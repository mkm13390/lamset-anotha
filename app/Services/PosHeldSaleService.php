<?php

namespace App\Services;

use App\Models\PosAuditLog;
use App\Models\PosHeldSale;
use App\Models\PosHeldSaleItem;
use App\Models\PosShift;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosHeldSaleService
{
    /**
     * تعليق فاتورة حالية بدون خصم المخزون وبدون تسجيل دفعة.
     */
    public function hold(
        PosShift $shift,
        array $items,
        array $data = []
    ): PosHeldSale {
        return DB::transaction(function () use ($shift, $items, $data) {
            $lockedShift = PosShift::query()
                ->lockForUpdate()
                ->findOrFail($shift->id);

            if (!$lockedShift->isOpen()) {
                throw ValidationException::withMessages([
                    'hold' => 'لا يمكن تعليق فاتورة بدون وردية مفتوحة.',
                ]);
            }

            if ($lockedShift->user_id !== auth()->id()) {
                throw ValidationException::withMessages([
                    'hold' => 'الوردية الحالية لا تخص المستخدم الحالي.',
                ]);
            }

            if (empty($items)) {
                throw ValidationException::withMessages([
                    'items' => 'أضف صنفًا واحدًا على الأقل قبل تعليق الفاتورة.',
                ]);
            }

            $preparedItems = [];
            $subtotal = 0;

            foreach ($items as $index => $item) {
                $variantId = (int) ($item['product_variant_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);

                if ($variantId <= 0 || $quantity <= 0) {
                    throw ValidationException::withMessages([
                        "items.$index" => 'بيانات أحد أصناف الفاتورة غير صالحة.',
                    ]);
                }

                $variant = ProductVariant::query()
                    ->with('product')
                    ->find($variantId);

                if (
                    !$variant
                    || !$variant->product
                    || !$variant->is_active
                    || !$variant->product->is_active
                ) {
                    throw ValidationException::withMessages([
                        "items.$index" => 'أحد المنتجات غير متاح حاليًا.',
                    ]);
                }

                $unitPrice = array_key_exists('unit_price', $item)
                    ? round((float) $item['unit_price'], 3)
                    : round(
                        $variant->price !== null
                            ? (float) $variant->price
                            : (float) ($variant->product->price ?? 0),
                        3
                    );

                $lineDiscount = round(
                    (float) ($item['line_discount'] ?? 0),
                    3
                );

                if ($lineDiscount < 0) {
                    throw ValidationException::withMessages([
                        "items.$index.line_discount" =>
                            'خصم الصنف لا يمكن أن يكون سالبًا.',
                    ]);
                }

                $gross = round($unitPrice * $quantity, 3);

                if ($lineDiscount > $gross) {
                    throw ValidationException::withMessages([
                        "items.$index.line_discount" =>
                            'خصم الصنف أكبر من قيمة الصنف.',
                    ]);
                }

                $lineTotal = round(
                    $gross - $lineDiscount,
                    3
                );

                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_discount' => $lineDiscount,
                    'line_total' => $lineTotal,
                ];
            }

            $subtotal = round($subtotal, 3);

            $invoiceDiscount = round(
                (float) ($data['discount_amount'] ?? 0),
                3
            );

            if ($invoiceDiscount < 0) {
                throw ValidationException::withMessages([
                    'discount_amount' => 'الخصم لا يمكن أن يكون سالبًا.',
                ]);
            }

            if ($invoiceDiscount > $subtotal) {
                throw ValidationException::withMessages([
                    'discount_amount' => 'الخصم أكبر من قيمة الفاتورة.',
                ]);
            }

            $total = round(
                $subtotal - $invoiceDiscount,
                3
            );

            do {
                $holdNumber = 'HOLD-'
                    . now()->format('YmdHis')
                    . '-'
                    . random_int(1000, 9999);
            } while (
                PosHeldSale::where('hold_number', $holdNumber)->exists()
            );

            $heldSale = PosHeldSale::create([
                'hold_number' => $holdNumber,
                'cash_register_id' => $lockedShift->cash_register_id,
                'pos_shift_id' => $lockedShift->id,
                'user_id' => auth()->id(),
                'customer_id' => $data['customer_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $invoiceDiscount,
                'total' => $total,
                'label' => $data['label'] ?? null,
                'notes' => $data['notes'] ?? null,
                'held_at' => now(),
            ]);

            foreach ($preparedItems as $preparedItem) {
                PosHeldSaleItem::create([
                    'pos_held_sale_id' => $heldSale->id,
                    'product_variant_id' =>
                        $preparedItem['product_variant_id'],
                    'quantity' => $preparedItem['quantity'],
                    'unit_price' => $preparedItem['unit_price'],
                    'line_discount' => $preparedItem['line_discount'],
                    'line_total' => $preparedItem['line_total'],
                ]);
            }

            PosAuditLog::create([
                'user_id' => auth()->id(),
                'pos_shift_id' => $lockedShift->id,
                'action' => 'hold_sale',
                'reference' => $holdNumber,
                'description' => 'تعليق فاتورة نقطة بيع',
                'after_data' => [
                    'held_sale_id' => $heldSale->id,
                    'total' => $total,
                    'items_count' => count($preparedItems),
                    'customer_name' =>
                        $data['customer_name'] ?? null,
                ],
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);

            return $heldSale->load([
                'items.variant.product',
                'cashRegister',
                'user',
            ]);
        });
    }

    /**
     * تحميل فاتورة معلقة للكاشير الحالي.
     */
    public function resume(PosHeldSale $heldSale): PosHeldSale
    {
        if ($heldSale->user_id !== auth()->id()) {
            throw ValidationException::withMessages([
                'hold' => 'لا يمكنك استرجاع فاتورة معلقة لمستخدم آخر.',
            ]);
        }

        $shift = PosShift::query()
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if (!$shift) {
            throw ValidationException::withMessages([
                'hold' => 'افتح وردية قبل استرجاع الفاتورة المعلقة.',
            ]);
        }

        return $heldSale->load([
            'items.variant.product',
            'cashRegister',
            'user',
        ]);
    }

    /**
     * حذف الفاتورة المعلقة بعد إتمامها أو إلغائها.
     */
    public function delete(PosHeldSale $heldSale): void
    {
        DB::transaction(function () use ($heldSale) {
            if ($heldSale->user_id !== auth()->id()) {
                throw ValidationException::withMessages([
                    'hold' => 'لا يمكنك حذف فاتورة معلقة لمستخدم آخر.',
                ]);
            }

            PosAuditLog::create([
                'user_id' => auth()->id(),
                'pos_shift_id' => $heldSale->pos_shift_id,
                'action' => 'delete_held_sale',
                'reference' => $heldSale->hold_number,
                'description' => 'حذف فاتورة معلقة',
                'before_data' => [
                    'held_sale_id' => $heldSale->id,
                    'total' => (float) $heldSale->total,
                ],
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);

            $heldSale->delete();
        });
    }
}
