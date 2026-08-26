<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\PosAuditLog;
use App\Models\PosSale;
use App\Models\PosSalePayment;
use App\Models\PosShift;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosPaymentService
{
    /**
     * تسجيل دفعات عملية بيع احترافية.
     *
     * شكل $payments المتوقع:
     * [
     *   [
     *     'payment_method' => 'cash|card|bank_transfer|store_credit|other',
     *     'amount' => 10.000,
     *     'tendered_amount' => 20.000, // للنقدي فقط
     *     'reference' => null,
     *     'notes' => null,
     *   ],
     * ]
     */
    public function processSalePayments(
        PosSale $sale,
        PosShift $shift,
        array $payments
    ): array {
        return DB::transaction(function () use ($sale, $shift, $payments) {
            $lockedShift = PosShift::query()
                ->lockForUpdate()
                ->findOrFail($shift->id);

            if (!$lockedShift->isOpen()) {
                throw ValidationException::withMessages([
                    'payments' => 'تم إغلاق الوردية قبل إتمام الدفع.',
                ]);
            }

            $register = CashRegister::query()
                ->lockForUpdate()
                ->findOrFail($lockedShift->cash_register_id);

            if (!$register->is_active) {
                throw ValidationException::withMessages([
                    'payments' => 'خزينة الوردية غير مفعلة.',
                ]);
            }

            if (empty($payments)) {
                throw ValidationException::withMessages([
                    'payments' => 'أدخل طريقة دفع واحدة على الأقل.',
                ]);
            }

            $saleTotal = round((float) $sale->total, 3);

            if ($saleTotal < 0) {
                throw ValidationException::withMessages([
                    'payments' => 'إجمالي البيع غير صالح.',
                ]);
            }

            $normalized = [];
            $paidTotal = 0;
            $cashAmount = 0;
            $cashTendered = 0;
            $cashChange = 0;

            foreach ($payments as $index => $payment) {
                $method = $payment['payment_method'] ?? null;

                if (!in_array($method, [
                    'cash',
                    'card',
                    'bank_transfer',
                    'store_credit',
                    'other',
                ], true)) {
                    throw ValidationException::withMessages([
                        "payments.$index.payment_method" =>
                            'طريقة الدفع غير صالحة.',
                    ]);
                }

                $amount = round(
                    (float) ($payment['amount'] ?? 0),
                    3
                );

                if ($amount <= 0) {
                    throw ValidationException::withMessages([
                        "payments.$index.amount" =>
                            'قيمة الدفعة يجب أن تكون أكبر من صفر.',
                    ]);
                }

                $tendered = null;
                $change = 0;

                if ($method === 'cash') {
                    $tendered = round(
                        (float) (
                            $payment['tendered_amount']
                            ?? $amount
                        ),
                        3
                    );

                    if ($tendered < $amount) {
                        throw ValidationException::withMessages([
                            "payments.$index.tendered_amount" =>
                                'المبلغ النقدي المستلم أقل من قيمة الدفعة النقدية.',
                        ]);
                    }

                    $change = round(
                        $tendered - $amount,
                        3
                    );

                    $cashAmount += $amount;
                    $cashTendered += $tendered;
                    $cashChange += $change;
                }

                $paidTotal += $amount;

                $normalized[] = [
                    'payment_method' => $method,
                    'amount' => $amount,
                    'tendered_amount' => $tendered,
                    'change_amount' => $change,
                    'reference' => $payment['reference'] ?? null,
                    'notes' => $payment['notes'] ?? null,
                ];
            }

            $paidTotal = round($paidTotal, 3);

            if (abs($paidTotal - $saleTotal) > 0.0005) {
                throw ValidationException::withMessages([
                    'payments' =>
                        'مجموع الدفعات يجب أن يساوي إجمالي الفاتورة تمامًا.',
                ]);
            }

            foreach ($normalized as $payment) {
                PosSalePayment::create([
                    'pos_sale_id' => $sale->id,
                    'cash_register_id' => $register->id,
                    'pos_shift_id' => $lockedShift->id,
                    'user_id' => auth()->id(),
                    'payment_method' => $payment['payment_method'],
                    'amount' => $payment['amount'],
                    'refunded_amount' => 0,
                    'tendered_amount' => $payment['tendered_amount'],
                    'change_amount' => $payment['change_amount'],
                    'reference' => $payment['reference'],
                    'notes' => $payment['notes'],
                ]);
            }

            if ($cashAmount > 0) {
                $newBalance = round(
                    (float) $register->current_balance
                    + $cashAmount,
                    3
                );

                $register->update([
                    'current_balance' => $newBalance,
                ]);

                $lockedShift->cash_sales = round(
                    (float) $lockedShift->cash_sales
                    + $cashAmount,
                    3
                );

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
                    'type' => 'sale',
                    'amount' => $cashAmount,
                    'balance_after' => $newBalance,
                    'reference' => $sale->sale_number,
                    'description' =>
                        'تحصيل الجزء النقدي من بيع نقطة البيع',
                ]);
            }

            $sale->update([
                'cash_received' => round($cashTendered, 3),
                'change_due' => round($cashChange, 3),
            ]);

            PosAuditLog::create([
                'user_id' => auth()->id(),
                'pos_shift_id' => $lockedShift->id,
                'pos_sale_id' => $sale->id,
                'action' => 'sale_payment',
                'reference' => $sale->sale_number,
                'description' => 'تسجيل دفعات عملية بيع',
                'after_data' => [
                    'sale_total' => $saleTotal,
                    'paid_total' => $paidTotal,
                    'cash_amount' => round($cashAmount, 3),
                    'cash_tendered' => round($cashTendered, 3),
                    'change_due' => round($cashChange, 3),
                    'payments' => $normalized,
                ],
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);

            return [
                'paid_total' => $paidTotal,
                'cash_amount' => round($cashAmount, 3),
                'cash_received' => round($cashTendered, 3),
                'change_due' => round($cashChange, 3),
                'payments' => $normalized,
            ];
        });
    }
}
