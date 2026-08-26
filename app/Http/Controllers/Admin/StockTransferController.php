<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\ProductVariantStock;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockTransferController extends Controller
{
    public function index(): View
    {
        $warehouses = Warehouse::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $variants = ProductVariant::query()
            ->with('product')
            ->where('is_active', true)
            ->orderBy('sku')
            ->get();

        $transfers = StockTransfer::query()
            ->with([
                'fromWarehouse',
                'toWarehouse',
                'user',
                'items.variant.product',
            ])
            ->latest('transfer_date')
            ->latest('id')
            ->paginate(20);

        return view('admin.inventory.transfers.index', compact(
            'warehouses',
            'variants',
            'transfers'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from_warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
            ],
            'to_warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
                'different:from_warehouse_id',
            ],
            'transfer_date' => [
                'required',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.product_variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $from = Warehouse::query()
                ->lockForUpdate()
                ->findOrFail($validated['from_warehouse_id']);

            $to = Warehouse::query()
                ->lockForUpdate()
                ->findOrFail($validated['to_warehouse_id']);

            if (! $from->is_active || ! $to->is_active) {
                throw ValidationException::withMessages([
                    'from_warehouse_id' =>
                        'يجب أن يكون كلا المخزنين فعالين.',
                ]);
            }

            $transfer = StockTransfer::create([
                'transfer_number' => $this->nextTransferNumber(),
                'from_warehouse_id' => $from->id,
                'to_warehouse_id' => $to->id,
                'user_id' => auth()->id(),
                'status' => 'completed',
                'transfer_date' => $validated['transfer_date'],
                'received_date' => $validated['transfer_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $variantId = (int) $item['product_variant_id'];
                $quantity = (int) $item['quantity'];

                $sourceStock = ProductVariantStock::query()
                    ->where('warehouse_id', $from->id)
                    ->where('product_variant_id', $variantId)
                    ->lockForUpdate()
                    ->first();

                if (! $sourceStock) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'لا يوجد رصيد لهذا المنتج في المخزن المصدر.',
                    ]);
                }

                $available = $sourceStock->available_quantity;

                if ($quantity > $available) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'الكمية المطلوبة للتحويل أكبر من الكمية المتاحة.',
                    ]);
                }

                $destinationStock = ProductVariantStock::query()
                    ->where('warehouse_id', $to->id)
                    ->where('product_variant_id', $variantId)
                    ->lockForUpdate()
                    ->first();

                if (! $destinationStock) {
                    $destinationStock = ProductVariantStock::create([
                        'warehouse_id' => $to->id,
                        'product_variant_id' => $variantId,
                        'quantity' => 0,
                        'reserved_quantity' => 0,
                        'damaged_quantity' => 0,
                        'sample_quantity' => 0,
                        'reorder_level' => 0,
                        'reorder_quantity' => 0,
                    ]);
                }

                $sourceBefore = (int) $sourceStock->quantity;
                $destinationBefore = (int) $destinationStock->quantity;

                $sourceStock->update([
                    'quantity' => $sourceBefore - $quantity,
                ]);

                $destinationStock->update([
                    'quantity' => $destinationBefore + $quantity,
                ]);

                $transfer->items()->create([
                    'product_variant_id' => $variantId,
                    'quantity' => $quantity,
                ]);

                StockMovement::create([
                    'product_variant_id' => $variantId,
                    'type' => 'transfer_out',
                    'quantity' => $quantity,
                    'quantity_before' => $sourceBefore,
                    'quantity_after' => $sourceBefore - $quantity,
                    'unit_cost' => null,
                    'reference' => $transfer->transfer_number,
                    'notes' => 'تحويل من '
                        . $from->name
                        . ' إلى '
                        . $to->name,
                    'user_id' => auth()->id(),
                ]);

                StockMovement::create([
                    'product_variant_id' => $variantId,
                    'type' => 'transfer_in',
                    'quantity' => $quantity,
                    'quantity_before' => $destinationBefore,
                    'quantity_after' => $destinationBefore + $quantity,
                    'unit_cost' => null,
                    'reference' => $transfer->transfer_number,
                    'notes' => 'تحويل إلى '
                        . $to->name
                        . ' من '
                        . $from->name,
                    'user_id' => auth()->id(),
                ]);
            }
        });

        return back()->with(
            'success',
            'تم تحويل المخزون بنجاح.'
        );
    }

    private function nextTransferNumber(): string
    {
        do {
            $number = 'TR-'
                . now()->format('YmdHis')
                . '-'
                . random_int(1000, 9999);
        } while (
            StockTransfer::where(
                'transfer_number',
                $number
            )->exists()
        );

        return $number;
    }
}
