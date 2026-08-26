<?php

namespace App\Services;

use App\Models\PosAuditLog;
use App\Models\PosSale;
use Illuminate\Support\Facades\DB;

class PosReceiptService
{
    public function ensureReceiptNumber(PosSale $sale): PosSale
    {
        if ($sale->receipt_number) {
            return $sale;
        }

        do {
            $receiptNumber = 'RCPT-'
                . now()->format('YmdHis')
                . '-'
                . random_int(1000, 9999);
        } while (
            PosSale::where('receipt_number', $receiptNumber)->exists()
        );

        $sale->update([
            'receipt_number' => $receiptNumber,
        ]);

        return $sale->refresh();
    }

    public function markPrinted(PosSale $sale): PosSale
    {
        return DB::transaction(function () use ($sale) {
            $lockedSale = PosSale::query()
                ->lockForUpdate()
                ->findOrFail($sale->id);

            $this->ensureReceiptNumber($lockedSale);
            $lockedSale->refresh();

            $beforeCount = (int) $lockedSale->receipt_print_count;
            $afterCount = $beforeCount + 1;

            $lockedSale->update([
                'receipt_print_count' => $afterCount,
            ]);

            PosAuditLog::create([
                'user_id' => auth()->id(),
                'pos_shift_id' => $lockedSale->pos_shift_id,
                'pos_sale_id' => $lockedSale->id,
                'action' => $beforeCount === 0
                    ? 'print_receipt'
                    : 'reprint_receipt',
                'reference' => $lockedSale->receipt_number
                    ?: $lockedSale->sale_number,
                'description' => $beforeCount === 0
                    ? 'طباعة إيصال نقطة البيع'
                    : 'إعادة طباعة إيصال نقطة البيع',
                'before_data' => [
                    'receipt_print_count' => $beforeCount,
                ],
                'after_data' => [
                    'receipt_print_count' => $afterCount,
                ],
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);

            return $lockedSale->refresh();
        });
    }

    public function getReceiptData(PosSale $sale): array
    {
        $sale = $this->ensureReceiptNumber($sale);

        $sale->load([
            'items',
            'payments',
            'cashRegister',
            'user',
        ]);

        return [
            'sale' => $sale,
            'receipt_number' => $sale->receipt_number,
            'sale_number' => $sale->sale_number,
            'customer_name' => $sale->customer_name ?: 'عميل نقدي',
            'customer_phone' => $sale->customer_phone,
            'subtotal' => (float) $sale->subtotal,
            'discount_amount' => (float) $sale->discount_amount,
            'total' => (float) $sale->total,
            'cash_received' => (float) $sale->cash_received,
            'change_due' => (float) $sale->change_due,
            'items' => $sale->items,
            'payments' => $sale->payments,
            'cashier' => $sale->user?->name,
            'register' => $sale->cashRegister?->name,
            'completed_at' => $sale->completed_at ?: $sale->created_at,
        ];
    }
}
