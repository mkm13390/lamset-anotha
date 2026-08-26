<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\PosAuditLog;
use App\Models\PosReturn;
use App\Models\PosReturnItem;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\PosShift;
use App\Models\ProductVariant;
use App\Models\ProductVariantStock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosReturnService
{
    /**
     * إنشاء مرتجع أو جزء مرتجع من فاتورة POS.
     *
     * شكل $items المتوقع:
     * [
     *   [
     *     'pos_sale_item_id' => 1,
     *     'quantity' => 1,
     *     'stock_action' => 'restock|damaged|no_stock_change',
     *     'reason' => null,
     *   ],
     * ]
     */
    public function createReturn(
        PosSale $sale,
        PosShift $shift,
        array $items,
        array $data = []
    ): PosReturn {
        return DB::transaction(function () use ($sale, $shift, $items, $data) {
            $lockedShift = PosShift::query()
                ->lockForUpdate()
                ->findOrFail($shift->id);

            if (!$lockedShift->isOpen()) {
                throw ValidationException::withMessages([
                    'return' => 'يجب أن تكون هناك وردية مفتوحة لتنفيذ المرتجع.',
                ]);
            }

            if ($lockedShift->user_id !== auth()->id()) {
                throw ValidationException::withMessages([
                    'return' => 'الوردية الحالية لا تخص المستخدم الحالي.',
                ]);
            }

            $lockedSale = PosSale::query()
                ->with([
                    'items',
                    'payments',
                ])
                ->lockForUpdate()
                ->findOrFail($sale->id);

            if (!in_array($lockedSale->status, [
                'completed',
                'refunded',
            ], true)) {
                throw ValidationException::withMessages([
                    'return' => 'حالة الفاتورة لا تسمح بالمرتجع.',
                ]);
            }

            if (empty($items)) {
                throw ValidationException::withMessages([
                    'items' => 'اختر صنفًا واحدًا على الأقل للمرتجع.',
                ]);
            }

            $returnType = $data['return_type'] ?? 'refund';

            if (!in_array($returnType, [
                'refund',
                'exchange',
            ], true)) {
                throw ValidationException::withMessages([
                    'return_type' => 'نوع المرتجع غير صالح.',
                ]);
            }

            $refundMethod = $data['refund_method']
                ?? ($returnType === 'exchange' ? 'none' : 'original_method');

            if (!in_array($refundMethod, [
                'cash',
                'card',
                'bank_transfer',
                'store_credit',
                'original_method',
                'none',
            ], true)) {
                throw ValidationException::withMessages([
                    'refund_method' => 'طريقة رد المبلغ غير صالحة.',
                ]);
            }

            $defaultWarehouse = Warehouse::query()
                ->where('is_default', true)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (!$defaultWarehouse) {
                throw ValidationException::withMessages([
                    'inventory' => 'لا يوجد مخزن افتراضي مفعل.',
                ]);
            }

            $preparedItems = [];
            $subtotal = 0;

            foreach ($items as $index => $item) {
                $saleItemId = (int) ($item['pos_sale_item_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);

                if ($saleItemId <= 0 || $quantity <= 0) {
                    throw ValidationException::withMessages([
                        "items.$index" => 'بيانات أحد أصناف المرتجع غير صالحة.',
                    ]);
                }

                $saleItem = PosSaleItem::query()
                    ->lockForUpdate()
                    ->find($saleItemId);

                if (
                    !$saleItem
                    || (int) $saleItem->pos_sale_id !== (int) $lockedSale->id
                ) {
                    throw ValidationException::withMessages([
                        "items.$index" => 'أحد الأصناف لا ينتمي إلى الفاتورة المحددة.',
                    ]);
                }

                $remainingReturnable = max(
                    (int) $saleItem->quantity
                    - (int) ($saleItem->returned_quantity ?? 0),
                    0
                );

                if ($quantity > $remainingReturnable) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" =>
                            'الكمية المرتجعة أكبر من الكمية المتاحة للمرتجع.',
                    ]);
                }

                $unitPrice = round(
                    (float) $saleItem->unit_price,
                    3
                );

                $originalLineDiscount = round(
                    (float) ($saleItem->line_discount ?? 0),
                    3
                );

                $discountPerUnit = (int) $saleItem->quantity > 0
                    ? round(
                        $originalLineDiscount
                        / (int) $saleItem->quantity,
                        3
                    )
                    : 0;

                $returnedDiscount = round(
                    $discountPerUnit * $quantity,
                    3
                );

                $lineRefund = round(
                    ($unitPrice * $quantity)
                    - $returnedDiscount,
                    3
                );

                $stockAction = $item['stock_action'] ?? 'restock';

                if (!in_array($stockAction, [
                    'restock',
                    'damaged',
                    'no_stock_change',
                ], true)) {
                    throw ValidationException::withMessages([
                        "items.$index.stock_action" =>
                            'إجراء المخزون للمرتجع غير صالح.',
                    ]);
                }

                $subtotal += $lineRefund;

                $preparedItems[] = [
                    'sale_item' => $saleItem,
                    'product_variant_id' => $saleItem->product_variant_id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_discount' => $returnedDiscount,
                    'line_refund' => $lineRefund,
                    'stock_action' => $stockAction,
                    'reason' => $item['reason'] ?? null,
                ];
            }

            $subtotal = round($subtotal, 3);

            $deductionAmount = round(
                (float) ($data['deduction_amount'] ?? 0),
                3
            );

            if ($deductionAmount < 0 || $deductionAmount > $subtotal) {
                throw ValidationException::withMessages([
                    'deduction_amount' => 'قيمة الاستقطاع من المرتجع غير صالحة.',
                ]);
            }

            $refundAmount = $returnType === 'exchange'
                ? 0
                : round(
                    $subtotal - $deductionAmount,
                    3
                );

            do {
                $returnNumber = 'RET-'
                    . now()->format('YmdHis')
                    . '-'
                    . random_int(1000, 9999);
            } while (
                PosReturn::where('return_number', $returnNumber)->exists()
            );

            $return = PosReturn::create([
                'return_number' => $returnNumber,
                'pos_sale_id' => $lockedSale->id,
                'cash_register_id' => $lockedShift->cash_register_id,
                'pos_shift_id' => $lockedShift->id,
                'user_id' => auth()->id(),
                'return_type' => $returnType,
                'refund_method' => $refundMethod,
                'subtotal' => $subtotal,
                'deduction_amount' => $deductionAmount,
                'refund_amount' => $refundAmount,
                'reason' => $data['reason'] ?? null,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'returned_at' => now(),
            ]);

            foreach ($preparedItems as $prepared) {
                $saleItem = $prepared['sale_item'];

                PosReturnItem::create([
                    'pos_return_id' => $return->id,
                    'pos_sale_item_id' => $saleItem->id,
                    'product_variant_id' => $prepared['product_variant_id'],
                    'quantity' => $prepared['quantity'],
                    'unit_price' => $prepared['unit_price'],
                    'line_discount' => $prepared['line_discount'],
                    'line_refund' => $prepared['line_refund'],
                    'stock_action' => $prepared['stock_action'],
                    'reason' => $prepared['reason'],
                ]);

                $saleItem->update([
                    'returned_quantity' => (int) $saleItem->returned_quantity
                        + $prepared['quantity'],
                ]);

                if (
                    $prepared['product_variant_id']
                    && $prepared['stock_action'] !== 'no_stock_change'
                ) {
                    $this->applyStockReturn(
                        (int) $prepared['product_variant_id'],
                        $defaultWarehouse->id,
                        $prepared['quantity'],
                        $prepared['stock_action'],
                        $returnNumber
                    );
                }
            }

            if ($refundAmount > 0) {
                $this->applyRefund(
                    $lockedSale,
                    $lockedShift,
                    $refundMethod,
                    $refundAmount,
                    $returnNumber
                );
            }

            $allReturned = $lockedSale->items()
                ->get()
                ->every(function ($item) {
                    return (int) $item->returned_quantity
                        >= (int) $item->quantity;
                });

            if ($allReturned) {
                $lockedSale->update([
                    'status' => 'refunded',
                ]);
            }

            PosAuditLog::create([
                'user_id' => auth()->id(),
                'pos_shift_id' => $lockedShift->id,
                'pos_sale_id' => $lockedSale->id,
                'action' => $returnType === 'exchange'
                    ? 'exchange_return'
                    : 'sale_return',
                'reference' => $returnNumber,
                'description' => $returnType === 'exchange'
                    ? 'تسجيل أصناف كجزء من عملية استبدال'
                    : 'تسجيل مرتجع من نقطة البيع',
                'after_data' => [
                    'return_id' => $return->id,
                    'subtotal' => $subtotal,
                    'deduction_amount' => $deductionAmount,
                    'refund_amount' => $refundAmount,
                    'refund_method' => $refundMethod,
                    'items_count' => count($preparedItems),
                ],
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);

            return $return->load([
                'items.saleItem',
                'sale',
                'cashRegister',
                'shift',
                'user',
            ]);
        });
    }

    private function applyStockReturn(
        int $variantId,
        int $warehouseId,
        int $quantity,
        string $stockAction,
        string $reference
    ): void {
        $variant = ProductVariant::query()
            ->lockForUpdate()
            ->find($variantId);

        if (!$variant) {
            return;
        }

        $stock = ProductVariantStock::query()
            ->where('warehouse_id', $warehouseId)
            ->where('product_variant_id', $variantId)
            ->lockForUpdate()
            ->first();

        if (!$stock) {
            $stock = ProductVariantStock::create([
                'warehouse_id' => $warehouseId,
                'product_variant_id' => $variantId,
                'quantity' => 0,
                'reserved_quantity' => 0,
                'damaged_quantity' => 0,
                'sample_quantity' => 0,
                'reorder_level' => 0,
                'reorder_quantity' => 0,
            ]);
        }

        $globalBefore = (int) $variant->stock_quantity;

        if ($stockAction === 'restock') {
            $stock->quantity = (int) $stock->quantity + $quantity;
        }

        if ($stockAction === 'damaged') {
            $stock->quantity = (int) $stock->quantity + $quantity;
            $stock->damaged_quantity =
                (int) $stock->damaged_quantity + $quantity;
        }

        $stock->save();

        $globalAvailable = ProductVariantStock::query()
            ->where('product_variant_id', $variantId)
            ->get()
            ->sum(function ($item) {
                return max(
                    (int) $item->quantity
                    - (int) $item->reserved_quantity
                    - (int) $item->damaged_quantity
                    - (int) $item->sample_quantity,
                    0
                );
            });

        $variant->update([
            'stock_quantity' => $globalAvailable,
        ]);

        StockMovement::create([
            'product_variant_id' => $variantId,
            'type' => $stockAction === 'restock'
                ? 'return_in'
                : 'damaged_return',
            'quantity' => $quantity,
            'quantity_before' => $globalBefore,
            'quantity_after' => $globalAvailable,
            'unit_cost' => $variant->product?->cost_price,
            'reference' => $reference,
            'notes' => $stockAction === 'restock'
                ? 'إرجاع صنف صالح إلى المخزون من نقطة البيع'
                : 'إرجاع صنف تالف من نقطة البيع',
            'user_id' => auth()->id(),
        ]);
    }

    private function applyRefund(
        PosSale $sale,
        PosShift $shift,
        string $refundMethod,
        float $refundAmount,
        string $reference
    ): void {
        if ($refundMethod === 'none') {
            return;
        }

        if ($refundMethod === 'original_method') {
            $cashPaid = round(
                (float) $sale->payments()
                    ->where('payment_method', 'cash')
                    ->sum('amount'),
                3
            );

            if ($cashPaid > 0) {
                $cashRefund = min($refundAmount, $cashPaid);

                $this->refundCash(
                    $shift,
                    $cashRefund,
                    $reference
                );
            }

            return;
        }

        if ($refundMethod === 'cash') {
            $this->refundCash(
                $shift,
                $refundAmount,
                $reference
            );
        }
    }

    private function refundCash(
        PosShift $shift,
        float $amount,
        string $reference
    ): void {
        if ($amount <= 0) {
            return;
        }

        $register = CashRegister::query()
            ->lockForUpdate()
            ->findOrFail($shift->cash_register_id);

        if ((float) $register->current_balance < $amount) {
            throw ValidationException::withMessages([
                'refund' => 'رصيد الخزينة الحالي لا يكفي للمرتجع النقدي.',
            ]);
        }

        $newBalance = round(
            (float) $register->current_balance - $amount,
            3
        );

        $register->update([
            'current_balance' => $newBalance,
        ]);

        $shift->cash_refunds = round(
            (float) $shift->cash_refunds + $amount,
            3
        );

        $shift->expected_cash = round(
            (float) $shift->opening_cash
            + (float) $shift->cash_sales
            - (float) $shift->cash_refunds
            + (float) $shift->cash_in
            - (float) $shift->cash_out,
            3
        );

        $shift->save();

        CashMovement::create([
            'cash_register_id' => $register->id,
            'user_id' => auth()->id(),
            'type' => 'refund',
            'amount' => $amount,
            'balance_after' => $newBalance,
            'reference' => $reference,
            'description' => 'رد مبلغ نقدي من نقطة البيع',
        ]);
    }
}
