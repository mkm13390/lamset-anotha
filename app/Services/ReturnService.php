<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Refund;
use App\Models\ReturnPolicy;
use App\Models\ReturnRequest;
use App\Models\StoreCreditTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReturnService
{
    public function createRequest(
        Order $order,
        ?User $user,
        array $data,
        array $items
    ): ReturnRequest {
        return DB::transaction(function () use ($order, $user, $data, $items) {
            $policy = ReturnPolicy::query()
                ->where('is_active', true)
                ->find($data['return_policy_id'] ?? null)
                ?? ReturnPolicy::query()
                    ->where('is_active', true)
                    ->latest()
                    ->first();

            if (!$policy) {
                throw ValidationException::withMessages([
                    'return' => 'لا توجد سياسة إرجاع مفعلة.',
                ]);
            }

            do {
                $number = 'RET-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            } while (ReturnRequest::where('return_number', $number)->exists());

            $requestedAmount = 0;

            foreach ($items as $item) {
                $orderItem = $order->items()->findOrFail($item['order_item_id']);

                if ((int) $item['quantity'] > (int) $orderItem->quantity) {
                    throw ValidationException::withMessages([
                        'items' => 'كمية المرتجع أكبر من كمية الطلب.',
                    ]);
                }

                $unit = (float) (
                    $orderItem->unit_price
                    ?? (
                        (float) $orderItem->line_total
                        / max((int) $orderItem->quantity, 1)
                    )
                );

                $requestedAmount += $unit * (int) $item['quantity'];
            }

            $request = ReturnRequest::create([
                'return_number' => $number,
                'order_id' => $order->id,
                'user_id' => $user?->id,
                'return_policy_id' => $policy->id,
                'request_type' => $data['request_type'] ?? 'refund',
                'status' => 'requested',
                'reason_code' => $data['reason_code'] ?? null,
                'customer_notes' => $data['customer_notes'] ?? null,
                'requested_amount' => round($requestedAmount, 3),
                'requested_at' => now(),
            ]);

            foreach ($items as $item) {
                $orderItem = $order->items()->findOrFail($item['order_item_id']);

                $unit = (float) (
                    $orderItem->unit_price
                    ?? (
                        (float) $orderItem->line_total
                        / max((int) $orderItem->quantity, 1)
                    )
                );

                $request->items()->create([
                    'order_item_id' => $orderItem->id,
                    'quantity' => $item['quantity'],
                    'condition' => $item['condition'] ?? 'new',
                    'reason_code' => $item['reason_code']
                        ?? ($data['reason_code'] ?? null),
                    'notes' => $item['notes'] ?? null,
                    'unit_refund_amount' => round($unit, 3),
                    'approved_refund_amount' => 0,
                ]);
            }

            return $request->load([
                'order',
                'user',
                'policy',
                'items.orderItem',
            ]);
        });
    }

    public function approve(
        ReturnRequest $returnRequest,
        array $data = []
    ): ReturnRequest {
        return DB::transaction(function () use ($returnRequest, $data) {
            $returnRequest = ReturnRequest::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($returnRequest->id);

            $approvedAmount = 0;

            foreach ($returnRequest->items as $item) {
                $itemAmount = isset($data['items'][$item->id]['approved_refund_amount'])
                    ? (float) $data['items'][$item->id]['approved_refund_amount']
                    : (float) $item->unit_refund_amount * (int) $item->quantity;

                $item->update([
                    'approved_refund_amount' => round($itemAmount, 3),
                    'restock' => (bool) (
                        $data['items'][$item->id]['restock'] ?? false
                    ),
                    'mark_damaged' => (bool) (
                        $data['items'][$item->id]['mark_damaged'] ?? false
                    ),
                ]);

                $approvedAmount += $itemAmount;
            }

            $returnRequest->update([
                'status' => 'approved',
                'approved_amount' => round($approvedAmount, 3),
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_notes' => $data['admin_notes'] ?? $returnRequest->admin_notes,
            ]);

            return $returnRequest->refresh()->load('items');
        });
    }

    public function markReceived(ReturnRequest $returnRequest): ReturnRequest
    {
        $returnRequest->update([
            'status' => 'received',
            'received_at' => now(),
        ]);

        return $returnRequest->refresh();
    }

    public function completeStoreCredit(
        ReturnRequest $returnRequest,
        User $user,
        LoyaltyMarketingService $loyaltyService
    ): ReturnRequest {
        return DB::transaction(function () use (
            $returnRequest,
            $user,
            $loyaltyService
        ) {
            $amount = (float) $returnRequest->approved_amount;

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'return' => 'لا توجد قيمة معتمدة لإضافتها كرصيد متجر.',
                ]);
            }

            $loyaltyService->addStoreCredit(
                $user,
                $amount,
                'refund',
                $returnRequest->return_number,
                'رصيد متجر ناتج عن مرتجع'
            );

            $returnRequest->update([
                'status' => 'store_credited',
                'completed_at' => now(),
            ]);

            return $returnRequest->refresh();
        });
    }

    public function createRefundRecord(
        ReturnRequest $returnRequest,
        string $method,
        float $amount,
        ?int $paymentTransactionId = null,
        ?string $reference = null
    ): Refund {
        do {
            $number = 'RFD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));
        } while (Refund::where('refund_number', $number)->exists());

        return Refund::create([
            'refund_number' => $number,
            'return_request_id' => $returnRequest->id,
            'payment_transaction_id' => $paymentTransactionId,
            'user_id' => $returnRequest->user_id,
            'processed_by' => auth()->id(),
            'refund_method' => $method,
            'status' => 'pending',
            'amount' => round($amount, 3),
            'currency' => 'OMR',
            'reference' => $reference,
        ]);
    }
}
