<?php

namespace App\Services;

use App\Models\EmployeeAdjustment;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeProfile;
use App\Models\FinanceTransaction;
use App\Models\FinancialAccount;
use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollService
{
    public function calculatePeriod(PayrollPeriod $period): PayrollPeriod
    {
        return DB::transaction(function () use ($period) {
            $lockedPeriod = PayrollPeriod::query()
                ->lockForUpdate()
                ->findOrFail($period->id);

            if (!in_array($lockedPeriod->status, ['draft', 'calculated'], true)) {
                throw ValidationException::withMessages([
                    'payroll' => 'لا يمكن إعادة احتساب هذه الفترة في حالتها الحالية.',
                ]);
            }

            $employees = EmployeeProfile::query()
                ->where('is_active', true)
                ->with('user')
                ->orderBy('employee_code')
                ->get();

            foreach ($employees as $employee) {
                $adjustments = EmployeeAdjustment::query()
                    ->where('employee_profile_id', $employee->id)
                    ->where('effective_date', '>=', $lockedPeriod->start_date)
                    ->where('effective_date', '<=', $lockedPeriod->end_date)
                    ->where('is_processed', false)
                    ->get();

                $allowances = round(
                    (float) $employee->fixed_allowance
                    + (float) $adjustments->where('type', 'allowance')->sum('amount')
                    + (float) $adjustments->where('type', 'other_credit')->sum('amount'),
                    3
                );

                $bonuses = round(
                    (float) $adjustments->where('type', 'bonus')->sum('amount'),
                    3
                );

                $commissions = round(
                    (float) $adjustments->where('type', 'commission')->sum('amount'),
                    3
                );

                $overtime = round(
                    (float) $adjustments->where('type', 'overtime')->sum('amount'),
                    3
                );

                $deductions = round(
                    (float) $adjustments->where('type', 'deduction')->sum('amount')
                    + (float) $adjustments->where('type', 'other_debit')->sum('amount'),
                    3
                );

                $advanceDeduction = $this->suggestAdvanceDeduction($employee);

                $basic = round((float) $employee->basic_salary, 3);
                $gross = round(
                    $basic + $allowances + $bonuses + $commissions + $overtime,
                    3
                );
                $net = round(
                    max($gross - $deductions - $advanceDeduction, 0),
                    3
                );

                PayrollEntry::updateOrCreate(
                    [
                        'payroll_period_id' => $lockedPeriod->id,
                        'employee_profile_id' => $employee->id,
                    ],
                    [
                        'basic_salary' => $basic,
                        'allowances' => $allowances,
                        'bonuses' => $bonuses,
                        'commissions' => $commissions,
                        'overtime' => $overtime,
                        'deductions' => $deductions,
                        'advance_deduction' => $advanceDeduction,
                        'gross_salary' => $gross,
                        'net_salary' => $net,
                        'status' => 'draft',
                    ]
                );
            }

            $lockedPeriod->update([
                'status' => 'calculated',
            ]);

            return $lockedPeriod->refresh()->load('entries.employeeProfile.user');
        });
    }

    public function approvePeriod(PayrollPeriod $period): PayrollPeriod
    {
        return DB::transaction(function () use ($period) {
            $lockedPeriod = PayrollPeriod::query()
                ->with('entries')
                ->lockForUpdate()
                ->findOrFail($period->id);

            if ($lockedPeriod->status !== 'calculated') {
                throw ValidationException::withMessages([
                    'payroll' => 'يجب احتساب مسير الرواتب قبل اعتماده.',
                ]);
            }

            foreach ($lockedPeriod->entries as $entry) {
                $entry->update([
                    'status' => 'approved',
                ]);
            }

            $lockedPeriod->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return $lockedPeriod->refresh();
        });
    }

    public function payPeriod(
        PayrollPeriod $period,
        FinancialAccount $account,
        ?string $reference = null
    ): PayrollPeriod {
        return DB::transaction(function () use ($period, $account, $reference) {
            $lockedPeriod = PayrollPeriod::query()
                ->with('entries.employeeProfile')
                ->lockForUpdate()
                ->findOrFail($period->id);

            if ($lockedPeriod->status !== 'approved') {
                throw ValidationException::withMessages([
                    'payroll' => 'يجب اعتماد مسير الرواتب قبل صرفه.',
                ]);
            }

            $lockedAccount = FinancialAccount::query()
                ->lockForUpdate()
                ->findOrFail($account->id);

            if (!$lockedAccount->is_active) {
                throw ValidationException::withMessages([
                    'account' => 'الحساب المالي غير مفعل.',
                ]);
            }

            $totalNet = round(
                (float) $lockedPeriod->entries->sum('net_salary'),
                3
            );

            if ((float) $lockedAccount->current_balance < $totalNet) {
                throw ValidationException::withMessages([
                    'account' => 'رصيد الحساب المالي لا يكفي لصرف الرواتب.',
                ]);
            }

            foreach ($lockedPeriod->entries as $entry) {
                $entry->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payment_reference' => $reference,
                ]);

                if ((float) $entry->advance_deduction > 0) {
                    $this->applyAdvanceDeduction(
                        $entry->employeeProfile,
                        (float) $entry->advance_deduction
                    );
                }

                EmployeeAdjustment::query()
                    ->where('employee_profile_id', $entry->employee_profile_id)
                    ->where('effective_date', '>=', $lockedPeriod->start_date)
                    ->where('effective_date', '<=', $lockedPeriod->end_date)
                    ->where('is_processed', false)
                    ->update([
                        'is_processed' => true,
                    ]);
            }

            $newBalance = round(
                (float) $lockedAccount->current_balance - $totalNet,
                3
            );

            $lockedAccount->update([
                'current_balance' => $newBalance,
            ]);

            FinanceTransaction::create([
                'transaction_number' => $this->transactionNumber('SAL'),
                'financial_account_id' => $lockedAccount->id,
                'created_by' => auth()->id(),
                'transaction_type' => 'salary',
                'amount' => $totalNet,
                'transaction_date' => now()->toDateString(),
                'category' => 'رواتب',
                'reference' => $reference,
                'description' => 'صرف مسير الرواتب: ' . $lockedPeriod->period_name,
                'source_type' => PayrollPeriod::class,
                'source_id' => $lockedPeriod->id,
            ]);

            $lockedPeriod->update([
                'status' => 'paid',
                'payment_date' => now()->toDateString(),
                'paid_at' => now(),
            ]);

            return $lockedPeriod->refresh();
        });
    }

    private function suggestAdvanceDeduction(EmployeeProfile $employee): float
    {
        $activeAdvance = EmployeeAdvance::query()
            ->where('employee_profile_id', $employee->id)
            ->where('status', 'active')
            ->orderBy('advance_date')
            ->first();

        if (!$activeAdvance) {
            return 0;
        }

        return round(
            min(
                (float) $activeAdvance->balance_amount,
                (float) $employee->basic_salary
            ),
            3
        );
    }

    private function applyAdvanceDeduction(
        EmployeeProfile $employee,
        float $amount
    ): void {
        $remaining = round($amount, 3);

        $advances = EmployeeAdvance::query()
            ->where('employee_profile_id', $employee->id)
            ->where('status', 'active')
            ->orderBy('advance_date')
            ->lockForUpdate()
            ->get();

        foreach ($advances as $advance) {
            if ($remaining <= 0) {
                break;
            }

            $deduct = min(
                $remaining,
                (float) $advance->balance_amount
            );

            $newDeducted = round(
                (float) $advance->deducted_amount + $deduct,
                3
            );

            $newBalance = round(
                max((float) $advance->balance_amount - $deduct, 0),
                3
            );

            $advance->update([
                'deducted_amount' => $newDeducted,
                'balance_amount' => $newBalance,
                'status' => $newBalance <= 0 ? 'settled' : 'active',
            ]);

            $remaining = round($remaining - $deduct, 3);
        }
    }

    private function transactionNumber(string $prefix): string
    {
        do {
            $number = $prefix
                . '-'
                . now()->format('YmdHis')
                . '-'
                . random_int(1000, 9999);
        } while (
            FinanceTransaction::where('transaction_number', $number)->exists()
        );

        return $number;
    }
}
