<?php

namespace App\Services;

use App\Models\PaymentTerminal;
use App\Models\PosAuditLog;
use App\Models\PosSale;
use App\Models\PosSalePayment;
use App\Models\PosShift;
use App\Models\PosTerminalTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentTerminalService
{
    /**
     * إنشاء طلب دفع بنكي وربطه بالفاتورة.
     *
     * ملاحظة مهمة:
     * هذه الطبقة لا تفترض بروتوكول بنك مسقط.
     * الاتصال الفعلي يضاف لاحقًا كـ Driver بعد معرفة موديل الجهاز
     * وبروتوكول ECR/API المقدم من البنك أو مزود الماكينة.
     */
    public function startSale(
        PaymentTerminal $terminal,
        PosSale $sale,
        PosShift $shift,
        float $amount
    ): PosTerminalTransaction {
        return DB::transaction(function () use (
            $terminal,
            $sale,
            $shift,
            $amount
        ) {
            $amount = round($amount, 3);

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'terminal' => 'مبلغ عملية البطاقة يجب أن يكون أكبر من صفر.',
                ]);
            }

            $lockedTerminal = PaymentTerminal::query()
                ->lockForUpdate()
                ->findOrFail($terminal->id);

            if (!$lockedTerminal->is_active) {
                throw ValidationException::withMessages([
                    'terminal' => 'ماكينة الدفع البنكية غير مفعلة.',
                ]);
            }

            $lockedShift = PosShift::query()
                ->lockForUpdate()
                ->findOrFail($shift->id);

            if (!$lockedShift->isOpen()) {
                throw ValidationException::withMessages([
                    'terminal' => 'لا توجد وردية مفتوحة.',
                ]);
            }

            if ($lockedShift->user_id !== auth()->id()) {
                throw ValidationException::withMessages([
                    'terminal' => 'الوردية الحالية لا تخص المستخدم الحالي.',
                ]);
            }

            if (
                $lockedTerminal->cash_register_id
                && (int) $lockedTerminal->cash_register_id
                    !== (int) $lockedShift->cash_register_id
            ) {
                throw ValidationException::withMessages([
                    'terminal' => 'ماكينة الدفع غير مرتبطة بخزينة الوردية الحالية.',
                ]);
            }

            do {
                $transactionNumber = 'TERM-'
                    . now()->format('YmdHis')
                    . '-'
                    . random_int(1000, 9999);
            } while (
                PosTerminalTransaction::where(
                    'transaction_number',
                    $transactionNumber
                )->exists()
            );

            $transaction = PosTerminalTransaction::create([
                'transaction_number' => $transactionNumber,
                'payment_terminal_id' => $lockedTerminal->id,
                'pos_sale_id' => $sale->id,
                'pos_shift_id' => $lockedShift->id,
                'user_id' => auth()->id(),
                'transaction_type' => 'sale',
                'amount' => $amount,
                'status' => 'pending',
                'request_payload' => [
                    'sale_number' => $sale->sale_number,
                    'amount' => $amount,
                    'currency' => 'OMR',
                    'terminal_id' => $lockedTerminal->terminal_id,
                    'integration_type' => $lockedTerminal->integration_type,
                ],
            ]);

            PosAuditLog::create([
                'user_id' => auth()->id(),
                'pos_shift_id' => $lockedShift->id,
                'pos_sale_id' => $sale->id,
                'action' => 'terminal_payment_started',
                'reference' => $transactionNumber,
                'description' => 'إنشاء طلب دفع عبر ماكينة البنك',
                'after_data' => [
                    'terminal_id' => $lockedTerminal->id,
                    'amount' => $amount,
                    'integration_type' =>
                        $lockedTerminal->integration_type,
                ],
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);

            return $transaction;
        });
    }

    /**
     * تجهيز العملية للإرسال إلى Driver المزود الحقيقي.
     *
     * لا يتم الادعاء بوجود تكامل فعلي قبل تركيب Driver بنك مسقط.
     */
    public function markAsSent(
        PosTerminalTransaction $transaction,
        ?array $requestPayload = null
    ): PosTerminalTransaction {
        if ($transaction->isFinal()) {
            throw ValidationException::withMessages([
                'terminal' => 'عملية الدفع البنكية منتهية بالفعل.',
            ]);
        }

        $transaction->update([
            'status' => 'sent',
            'request_payload' => $requestPayload
                ?: $transaction->request_payload,
            'sent_at' => now(),
        ]);

        return $transaction->refresh();
    }

    /**
     * تسجيل موافقة ماكينة البنك.
     *
     * يتم استدعاؤها لاحقًا من Driver بنك مسقط بعد نجاح العملية فعليًا.
     */
    public function approve(
        PosTerminalTransaction $transaction,
        array $response = []
    ): PosTerminalTransaction {
        return DB::transaction(function () use ($transaction, $response) {
            $lockedTransaction = PosTerminalTransaction::query()
                ->lockForUpdate()
                ->findOrFail($transaction->id);

            if ($lockedTransaction->isFinal()) {
                throw ValidationException::withMessages([
                    'terminal' => 'عملية الدفع البنكية منتهية بالفعل.',
                ]);
            }

            $lockedTransaction->update([
                'status' => 'approved',
                'approval_code' => $response['approval_code'] ?? null,
                'bank_reference' => $response['bank_reference'] ?? null,
                'rrn' => $response['rrn'] ?? null,
                'masked_card' => $response['masked_card'] ?? null,
                'card_scheme' => $response['card_scheme'] ?? null,
                'response_payload' => $response,
                'completed_at' => now(),
            ]);

            PosAuditLog::create([
                'user_id' => auth()->id(),
                'pos_shift_id' => $lockedTransaction->pos_shift_id,
                'pos_sale_id' => $lockedTransaction->pos_sale_id,
                'action' => 'terminal_payment_approved',
                'reference' => $lockedTransaction->transaction_number,
                'description' => 'تمت الموافقة على عملية ماكينة البنك',
                'after_data' => [
                    'amount' => (float) $lockedTransaction->amount,
                    'approval_code' =>
                        $lockedTransaction->approval_code,
                    'bank_reference' =>
                        $lockedTransaction->bank_reference,
                    'rrn' => $lockedTransaction->rrn,
                ],
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);

            return $lockedTransaction->refresh();
        });
    }

    /**
     * تسجيل رفض/إلغاء/انتهاء مهلة/خطأ.
     */
    public function fail(
        PosTerminalTransaction $transaction,
        string $status,
        ?string $reason = null,
        array $response = []
    ): PosTerminalTransaction {
        if (!in_array($status, [
            'declined',
            'cancelled',
            'timeout',
            'error',
        ], true)) {
            throw ValidationException::withMessages([
                'terminal' => 'حالة فشل عملية البنك غير صالحة.',
            ]);
        }

        return DB::transaction(function () use (
            $transaction,
            $status,
            $reason,
            $response
        ) {
            $lockedTransaction = PosTerminalTransaction::query()
                ->lockForUpdate()
                ->findOrFail($transaction->id);

            if ($lockedTransaction->isFinal()) {
                throw ValidationException::withMessages([
                    'terminal' => 'عملية الدفع البنكية منتهية بالفعل.',
                ]);
            }

            $lockedTransaction->update([
                'status' => $status,
                'failure_reason' => $reason,
                'response_payload' => $response,
                'completed_at' => now(),
            ]);

            PosAuditLog::create([
                'user_id' => auth()->id(),
                'pos_shift_id' => $lockedTransaction->pos_shift_id,
                'pos_sale_id' => $lockedTransaction->pos_sale_id,
                'action' => 'terminal_payment_' . $status,
                'reference' => $lockedTransaction->transaction_number,
                'description' => $reason
                    ?: 'لم تكتمل عملية ماكينة البنك',
                'after_data' => [
                    'status' => $status,
                    'amount' => (float) $lockedTransaction->amount,
                    'response' => $response,
                ],
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);

            return $lockedTransaction->refresh();
        });
    }

    /**
     * ربط العملية البنكية بدفعة POS بعد اعتمادها.
     */
    public function attachPayment(
        PosTerminalTransaction $transaction,
        PosSalePayment $payment
    ): PosTerminalTransaction {
        if (!$transaction->isApproved()) {
            throw ValidationException::withMessages([
                'terminal' => 'لا يمكن ربط دفعة قبل موافقة ماكينة البنك.',
            ]);
        }

        if (
            (int) $transaction->pos_sale_id
            !== (int) $payment->pos_sale_id
        ) {
            throw ValidationException::withMessages([
                'terminal' => 'الدفعة لا تنتمي إلى نفس الفاتورة.',
            ]);
        }

        $transaction->update([
            'pos_sale_payment_id' => $payment->id,
        ]);

        return $transaction->refresh();
    }

    /**
     * Driver بنك مسقط سيستدعي هذه الطبقة لاحقًا.
     * الآن نرفض الإرسال الفعلي حتى لا ننشئ تكاملًا وهميًا.
     */
    public function sendToPhysicalTerminal(
        PosTerminalTransaction $transaction
    ): never {
        throw ValidationException::withMessages([
            'terminal' =>
                'الاتصال الفعلي بماكينة بنك مسقط يحتاج موديل الجهاز وبروتوكول ECR/API المعتمد من البنك أو مزود الماكينة.',
        ]);
    }
}
