<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    private int $lowStockThreshold = 5;

    public function index()
    {
        $variants = ProductVariant::with([
            'product.category',
        ])
            ->orderBy('product_id')
            ->orderBy('sort_order')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | إحصائيات المخزون
        |--------------------------------------------------------------------------
        */

        $units = $variants->sum(function ($variant) {
            return (int) ($variant->stock_quantity ?? 0);
        });

        $value = $variants->sum(function ($variant) {
            $quantity = (int) ($variant->stock_quantity ?? 0);

            /*
             * سعر التكلفة محفوظ حاليًا على المنتج.
             * إذا لم توجد قيمة، نحسبها صفرًا ولا نستخدم سعر البيع.
             */
            $cost = (float) ($variant->product->cost_price ?? 0);

            return $quantity * $cost;
        });

        $lowStock = $variants
            ->filter(function ($variant) {
                $quantity = (int) ($variant->stock_quantity ?? 0);

                return $quantity > 0
                    && $quantity <= $this->lowStockThreshold;
            })
            ->values();

        $outOfStock = $variants
            ->filter(function ($variant) {
                return (int) ($variant->stock_quantity ?? 0) <= 0;
            })
            ->values();

        $stats = [
            'value' => $value,
            'units' => $units,
            'low' => $lowStock->count(),
            'out' => $outOfStock->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | أحدث حركات المخزون
        |--------------------------------------------------------------------------
        */

        $recentMovements = StockMovement::with([
            'variant.product',
        ])
            ->latest()
            ->limit(10)
            ->get();

        $movements = StockMovement::with([
            'variant.product',
        ])
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | المشتريات والموردون
        |--------------------------------------------------------------------------
        |
        | سيتم ربطهما في المرحلة التالية.
        |
        */

        $purchases = collect();
        $suppliers = collect();

        return view('admin.inventory.index', compact(
            'stats',
            'lowStock',
            'variants',
            'recentMovements',
            'movements',
            'purchases',
            'suppliers'
        ));
    }

    public function storeMovement(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],

            'type' => [
                'required',
                Rule::in([
                    'stock_in',
                    'stock_out',
                    'adjustment_in',
                    'adjustment_out',
                    'supplier_return',
                ]),
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'unit_cost' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            $variant = ProductVariant::query()
                ->lockForUpdate()
                ->findOrFail($validated['product_variant_id']);

            $before = (int) ($variant->stock_quantity ?? 0);
            $quantity = (int) $validated['quantity'];

            $increaseTypes = [
                'stock_in',
                'adjustment_in',
            ];

            $decreaseTypes = [
                'stock_out',
                'adjustment_out',
                'supplier_return',
            ];

            if (in_array($validated['type'], $increaseTypes, true)) {
                $after = $before + $quantity;
            } elseif (in_array($validated['type'], $decreaseTypes, true)) {
                $after = $before - $quantity;
            } else {
                abort(422, 'نوع حركة المخزون غير صالح.');
            }

            if ($after < 0) {
                abort(
                    422,
                    'لا يمكن تنفيذ الحركة لأن الكمية المطلوبة أكبر من المخزون المتاح.'
                );
            }

            $variant->stock_quantity = $after;
            $variant->save();

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => $validated['type'],
                'quantity' => $quantity,
                'quantity_before' => $before,
                'quantity_after' => $after,
                'unit_cost' => $validated['unit_cost'] ?? null,
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('admin.inventory.index')
            ->with('success', 'تم تحديث المخزون وتسجيل الحركة بنجاح.');
    }
}