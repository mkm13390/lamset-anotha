<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\PosAuditLog;
use App\Models\PosShift;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosCashDrawerService
{
    /**
     * فتح درج النقد بدون تغيير الرصيد.
     *
     * يسجل فقط حركة فتح الدرج للمراجعة الأمنية.
     */
    public function openDrawer(
        PosShift $shift,
        ?string $reason = null
    ): void {
        DB::transaction(function () use ($shift, $reason) {
            $lockedShift = PosShift::query()
                ->lockForUpdate()
                ->findOrFail($shift->id);

            if (!$lockedShift->isOpen()) {
                throw ValidationException::withMessages([
                    'drawer' => 'لا يمكن فتح درج النقد بدون وردية مفتوحة.',
                ]);
            }

            if ($lockedShift->user_id !== auth()->id()) {
                throw ValidationException::withMessages([
                    'drawer' => 'الوردية الحالية لا تخص المستخدم الحالي.',
                ]);
            }

            $register = CashRegister::query()
                ->lockForUpdate()
                ->findOrFail($lockedShift->cash_register_id);

            PosAuditLog::create([
                'user_id' => auth()->id(),
                'pos_shift_id' => $lockedShift->id,
                'action' => 'open_cash_drawer',
                'reference' => 'REGISTER-' . $register->id,
                'description' => $reason
                    ?: 'فتح درج النقد من نقطة البيع',
                'after_data' => [
                    'cash_register_id' => $register->id,
                    'current_balance' =>
                        (float) $register->current_balance,
                ],
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);
        });
    }

    /**
     * إدخال نقدي للدرج أثناء الوردية.
     */
    public function cashIn(
        PosShift $shift,
        float $amount,
        ?string $reason = null
    ): void {
        $this->cashMovement(
            $shift,
            $amount,
            'cash_in',
            $reason ?: 'إيداع نقدي في درج الكاشير'
        );
    }

    /**
     * إخراج نقدي من الدرج أثناء الوردية.
     */
    public function cashOut(
        PosShift $shift,
        float $amount,
        ?string $reason = null
    ): void {
        $this->cashMovement(
            $shift,
            $amount,
            'cash_out',
            $reason ?: 'سحب نقدي من درج الكاشير'
        );
    }

    private function cashMovement(
        PosShift $shift,
        float $amount,
        string $type,
        string $reason
    ): void {
        $amount = round($amount, 3);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'المبلغ يجب أن يكون أكبر من صفر.',
            ]);
        }

        DB::transaction(function () use (
            $shift,
            $amount,
            $type,
            $reason
        ) {
            $lockedShift = PosShift::query()
                ->lockForUpdate()
                ->findOrFail($shift->id);

            if (!$lockedShift->isOpen()) {
                throw ValidationException::withMessages([
                    'drawer' => 'لا توجد وردية مفتوحة.',
                ]);
            }

            if ($lockedShift->user_id !== auth()->id()) {
                throw ValidationException::withMessages([
                    'drawer' => 'الوردية الحالية لا تخص المستخدم الحالي.',
                ]);
            }

            $register = CashRegister::query()
                ->lockForUpdate()
                ->findOrFail($lockedShift->cash_register_id);

            $currentBalance = round(
                (float) $register->current_balance,
                3
            );

            if (
                $type === 'cash_out'
                && $amount > $currentBalance
            ) {
                throw ValidationException::withMessages([
                    'amount' => 'المبلغ أكبر من الرصيد الحالي في الخزينة.',
                ]);
            }

            $newBalance = $type === 'cash_in'
                ? round($currentBalance + $amount, 3)
                : round($currentBalance - $amount, 3);

            $register->update([
                'current_balance' => $newBalance,
            ]);

            if ($type === 'cash_in') {
                $lockedShift->cash_in = round(
                    (float) $lockedShift->cash_in + $amount,
                    3
                );
            } else {
                $lockedShift->cash_out = round(
                    (float) $lockedShift->cash_out + $amount,
                    3
                );
            }

            $lockedShift->expected_cash = round(
                (float) $lockedShift->opening_cash
                + (float) $lockedShift->cash_sales
                - (float) $lockedShift->cash_refunds
                + (float) $lockedShift->cash_in
                - (float) $lockedShift->cash_out,
                3
            );

            $lockedShift->save();

            CashMovement::create([
                'cash_register_id' => $register->id,
                'user_id' => auth()->id(),
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => 'SHIFT-' . $lockedShift->id,
                'description' => $reason,
            ]);

            PosAuditLog::create([
                'user_id' => auth()->id(),
                'pos_shift_id' => $lockedShift->id,
                'action' => $type,
                'reference' => 'SHIFT-' . $lockedShift->id,
                'description' => $reason,
                'after_data' => [
                    'amount' => $amount,
                    'balance_after' => $newBalance,
                    'expected_cash' =>
                        (float) $lockedShift->expected_cash,
                ],
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);
        });
    }
}
