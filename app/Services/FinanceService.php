<?php

namespace App\Services;

use App\Models\FinanceTransaction;
use App\Models\FinancialAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinanceService
{
    public function createTransaction(array $data): FinanceTransaction
    {
        return DB::transaction(function () use ($data) {
            $account = FinancialAccount::query()
                ->lockForUpdate()
                ->findOrFail($data['financial_account_id']);

            if (!$account->is_active) {
                throw ValidationException::withMessages([
                    'financial_account_id' => 'الحساب المالي غير مفعل.',
                ]);
            }

            $amount = round((float) $data['amount'], 3);

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'المبلغ يجب أن يكون أكبر من صفر.',
                ]);
            }

            $type = $data['transaction_type'];

            $inflow = in_array($type, [
                'income',
                'advance_repayment',
                'transfer_in',
                'adjustment_in',
            ], true);

            $outflow = in_array($type, [
                'expense',
                'salary',
                'advance',
                'transfer_out',
                'adjustment_out',
            ], true);

            if (!$inflow && !$outflow) {
                throw ValidationException::withMessages([
                    'transaction_type' => 'نوع الحركة المالية غير صالح.',
                ]);
            }

            if ($outflow && (float) $account->current_balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'رصيد الحساب لا يكفي لتنفيذ الحركة.',
                ]);
            }

            $newBalance = $inflow
                ? round((float) $account->current_balance + $amount, 3)
                : round((float) $account->current_balance - $amount, 3);

            $account->update([
                'current_balance' => $newBalance,
            ]);

            $transaction = FinanceTransaction::create([
                'transaction_number' => $this->transactionNumber(),
                'financial_account_id' => $account->id,
                'created_by' => auth()->id(),
                'transaction_type' => $type,
                'amount' => $amount,
                'transaction_date' => $data['transaction_date'],
                'category' => $data['category'] ?? null,
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            return $transaction->load('account', 'creator');
        });
    }

    private function transactionNumber(): string
    {
        do {
            $number = 'FIN-'
                . now()->format('YmdHis')
                . '-'
                . random_int(1000, 9999);
        } while (
            FinanceTransaction::where('transaction_number', $number)->exists()
        );

        return $number;
    }
}
