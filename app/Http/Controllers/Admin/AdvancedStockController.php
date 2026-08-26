<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\ProductVariantStock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdvancedStockController extends Controller
{
    public function index(Request $request): View
    {
        $warehouseId = $request->integer('warehouse_id');
        $search = trim((string) $request->get('search', ''));

        $warehouses = Warehouse::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $stocks = ProductVariantStock::query()
            ->with([
                'warehouse',
                'variant.product',
            ])
            ->when(
                $warehouseId,
                fn ($query) =>
                    $query->where('warehouse_id', $warehouseId)
            )
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->whereHas(
                        'variant',
                        function ($variantQuery) use ($search) {
                            $variantQuery->where(function ($q) use ($search) {
                                $q->where(
                                    'sku',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'color_name_ar',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'color_name_en',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'size',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'product',
                                    function ($productQuery) use ($search) {
                                        $productQuery
                                            ->where(
                                                'name_ar',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'name_en',
                                                'like',
                                                "%{$search}%"
                                            );
                                    }
                                );
                            });
                        }
                    );
                }
            )
            ->orderBy('warehouse_id')
            ->orderBy('product_variant_id')
            ->paginate(30)
            ->withQueryString();

        return view(
            'admin.inventory.stock-management.index',
            compact(
                'warehouses',
                'stocks',
                'warehouseId',
                'search'
            )
        );
    }

    public function updateSettings(
        Request $request,
        ProductVariantStock $stock
    ): RedirectResponse {
        $validated = $request->validate([
            'reorder_level' => [
                'required',
                'integer',
                'min:0',
            ],
            'reorder_quantity' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        $stock->update($validated);

        return back()->with(
            'success',
            'تم تحديث إعدادات إعادة الطلب.'
        );
    }

    public function adjustSpecialStock(
        Request $request,
        ProductVariantStock $stock
    ): RedirectResponse {
        $validated = $request->validate([
            'bucket' => [
                'required',
                'in:damaged,sample,reserved',
            ],
            'action' => [
                'required',
                'in:add,remove',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use ($validated, $stock) {
            $stock = ProductVariantStock::query()
                ->lockForUpdate()
                ->findOrFail($stock->id);

            $column = match ($validated['bucket']) {
                'damaged' => 'damaged_quantity',
                'sample' => 'sample_quantity',
                'reserved' => 'reserved_quantity',
            };

            $quantity = (int) $validated['quantity'];
            $current = (int) $stock->{$column};

            if ($validated['action'] === 'add') {
                $availableBefore = $stock->available_quantity;

                if ($quantity > $availableBefore) {
                    throw ValidationException::withMessages([
                        'quantity' =>
                            'الكمية المطلوبة أكبر من الكمية المتاحة.',
                    ]);
                }

                $newValue = $current + $quantity;
            } else {
                if ($quantity > $current) {
                    throw ValidationException::withMessages([
                        'quantity' =>
                            'الكمية المطلوب إرجاعها أكبر من الكمية الموجودة في هذا التصنيف.',
                    ]);
                }

                $newValue = $current - $quantity;
            }

            $stock->update([
                $column => $newValue,
            ]);

            $this->syncVariantAvailableStock(
                $stock->product_variant_id
            );

            $type = match ($validated['bucket']) {
                'damaged' => 'damaged',
                'sample' => 'sample',
                'reserved' => 'reserved',
            };

            StockMovement::create([
                'product_variant_id' =>
                    $stock->product_variant_id,
                'type' => $type,
                'quantity' => $quantity,
                'quantity_before' => $current,
                'quantity_after' => $newValue,
                'unit_cost' => null,
                'reference' =>
                    'WH-' . $stock->warehouse_id,
                'notes' =>
                    ($validated['action'] === 'add'
                        ? 'إضافة إلى '
                        : 'إرجاع من ')
                    . $this->bucketLabel(
                        $validated['bucket']
                    )
                    . (
                        ! empty($validated['notes'])
                            ? ' - ' . $validated['notes']
                            : ''
                    ),
                'user_id' => auth()->id(),
            ]);
        });

        return back()->with(
            'success',
            'تم تحديث حالة المخزون بنجاح.'
        );
    }

    private function syncVariantAvailableStock(
        int $variantId
    ): void {
        $available = ProductVariantStock::query()
            ->where('product_variant_id', $variantId)
            ->get()
            ->sum(
                fn ($stock) =>
                    max(
                        (int) $stock->quantity
                        - (int) $stock->reserved_quantity
                        - (int) $stock->damaged_quantity
                        - (int) $stock->sample_quantity,
                        0
                    )
            );

        ProductVariant::query()
            ->whereKey($variantId)
            ->update([
                'stock_quantity' => $available,
            ]);
    }

    private function bucketLabel(string $bucket): string
    {
        return match ($bucket) {
            'damaged' => 'التالف',
            'sample' => 'العينات',
            'reserved' => 'المحجوز',
        };
    }
}
