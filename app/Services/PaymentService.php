<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function createTransaction(
        Model $payable,
        PaymentGateway $gateway,
        float $amount,
        string $type = 'payment',
        array $meta = []
    ): PaymentTransaction {
        $amount = round($amount, 3);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'قيمة عملية الدفع يجب أن تكون أكبر من صفر.',
            ]);
        }

        do {
            $number = 'PAY-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));
        } while (PaymentTransaction::where('transaction_number', $number)->exists());

        return PaymentTransaction::create([
            'transaction_number' => $number,
            'payment_gateway_id' => $gateway->id,
            'user_id' => $meta['user_id'] ?? auth()->id(),
            'payable_type' => $payable->getMorphClass(),
            'payable_id' => $payable->getKey(),
            'type' => $type,
            'status' => $meta['status'] ?? 'pending',
            'amount' => $amount,
            'currency' => $meta['currency'] ?? $gateway->currency ?? 'OMR',
            'external_transaction_id' => $meta['external_transaction_id'] ?? null,
            'approval_code' => $meta['approval_code'] ?? null,
            'bank_reference' => $meta['bank_reference'] ?? null,
            'rrn' => $meta['rrn'] ?? null,
            'card_scheme' => $meta['card_scheme'] ?? null,
            'masked_card' => $meta['masked_card'] ?? null,
            'request_payload' => $meta['request_payload'] ?? null,
            'response_payload' => $meta['response_payload'] ?? null,
        ]);
    }

    public function markPaid(
        PaymentTransaction $transaction,
        array $meta = []
    ): PaymentTransaction {
        $transaction->update([
            'status' => 'paid',
            'paid_at' => now(),
            'external_transaction_id' => $meta['external_transaction_id']
                ?? $transaction->external_transaction_id,
            'approval_code' => $meta['approval_code']
                ?? $transaction->approval_code,
            'bank_reference' => $meta['bank_reference']
                ?? $transaction->bank_reference,
            'rrn' => $meta['rrn'] ?? $transaction->rrn,
            'card_scheme' => $meta['card_scheme']
                ?? $transaction->card_scheme,
            'masked_card' => $meta['masked_card']
                ?? $transaction->masked_card,
            'response_payload' => $meta['response_payload']
                ?? $transaction->response_payload,
        ]);

        return $transaction->refresh();
    }

    public function markFailed(
        PaymentTransaction $transaction,
        ?string $code = null,
        ?string $message = null,
        array $payload = []
    ): PaymentTransaction {
        $transaction->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_code' => $code,
            'failure_message' => $message,
            'response_payload' => $payload ?: $transaction->response_payload,
        ]);

        return $transaction->refresh();
    }

    public function registerRefund(
        PaymentTransaction $transaction,
        float $amount
    ): PaymentTransaction {
        $amount = round($amount, 3);

        return DB::transaction(function () use ($transaction, $amount) {
            $transaction = PaymentTransaction::query()
                ->lockForUpdate()
                ->findOrFail($transaction->id);

            $remaining = max(
                round(
                    (float) $transaction->amount
                    - (float) $transaction->refunded_amount,
                    3
                ),
                0
            );

            if ($amount <= 0 || $amount > $remaining) {
                throw ValidationException::withMessages([
                    'refund' => 'قيمة الاسترداد غير صالحة.',
                ]);
            }

            $newRefunded = round(
                (float) $transaction->refunded_amount + $amount,
                3
            );

            $transaction->update([
                'refunded_amount' => $newRefunded,
                'status' => $newRefunded >= (float) $transaction->amount
                    ? 'refunded'
                    : 'partially_refunded',
            ]);

            return $transaction->refresh();
        });
    }
}
