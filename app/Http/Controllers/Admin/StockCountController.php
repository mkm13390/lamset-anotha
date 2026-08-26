<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\ProductVariantStock;
use App\Models\StockCount;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockCountController extends Controller
{
    public function index(): View
    {
        $warehouses = Warehouse::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $counts = StockCount::query()
            ->with([
                'warehouse',
                'user',
            ])
            ->withCount('items')
            ->latest('count_date')
            ->latest('id')
            ->paginate(20);

        return view('admin.inventory.counts.index', compact(
            'warehouses',
            'counts'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
            ],
            'type' => [
                'required',
                'in:full,cycle',
            ],
            'count_date' => [
                'required',
                'date',
            ],
            'product_variant_ids' => [
                'nullable',
                'array',
            ],
            'product_variant_ids.*' => [
                'integer',
                'exists:product_variants,id',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        if (
            $validated['type'] === 'cycle'
            && empty($validated['product_variant_ids'])
        ) {
            throw ValidationException::withMessages([
                'product_variant_ids' =>
                    'اختر منتجًا واحدًا على الأقل للجرد الجزئي.',
            ]);
        }

        DB::transaction(function () use ($validated) {
            $warehouse = Warehouse::query()
                ->findOrFail($validated['warehouse_id']);

            $count = StockCount::create([
                'count_number' => $this->nextCountNumber(),
                'warehouse_id' => $warehouse->id,
                'user_id' => auth()->id(),
                'type' => $validated['type'],
                'status' => 'counting',
                'count_date' => $validated['count_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $stocks = ProductVariantStock::query()
                ->where('warehouse_id', $warehouse->id)
                ->when(
                    $validated['type'] === 'cycle',
                    function ($query) use ($validated) {
                        $query->whereIn(
                            'product_variant_id',
                            $validated['product_variant_ids']
                        );
                    }
                )
                ->get();

            foreach ($stocks as $stock) {
                $count->items()->create([
                    'product_variant_id' =>
                        $stock->product_variant_id,
                    'system_quantity' =>
                        $stock->quantity,
                    'counted_quantity' => null,
                    'difference' => 0,
                    'notes' => null,
                ]);
            }
        });

        return back()->with(
            'success',
            'تم إنشاء جلسة الجرد.'
        );
    }

    public function show(StockCount $stockCount): View
    {
        $stockCount->load([
            'warehouse',
            'user',
            'items.variant.product',
        ]);

        return view(
            'admin.inventory.counts.show',
            compact('stockCount')
        );
    }

    public function complete(
        Request $request,
        StockCount $stockCount
    ): RedirectResponse {
        $stockCount->load('items');

        $rules = [];

        foreach ($stockCount->items as $item) {
            $rules[
                'counted_quantities.'
                . $item->id
            ] = [
                'required',
                'integer',
                'min:0',
            ];
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use (
            $validated,
            $stockCount
        ) {
            $count = StockCount::query()
                ->lockForUpdate()
                ->findOrFail($stockCount->id);

            if ($count->status === 'completed') {
                throw ValidationException::withMessages([
                    'count' => 'تم اعتماد هذا الجرد مسبقًا.',
                ]);
            }

            $count->load('items');

            foreach ($count->items as $item) {
                $counted = (int)
                    $validated['counted_quantities'][$item->id];

                $stock = ProductVariantStock::query()
                    ->where(
                        'warehouse_id',
                        $count->warehouse_id
                    )
                    ->where(
                        'product_variant_id',
                        $item->product_variant_id
                    )
                    ->lockForUpdate()
                    ->first();

                if (! $stock) {
                    $stock = ProductVariantStock::create([
                        'warehouse_id' =>
                            $count->warehouse_id,
                        'product_variant_id' =>
                            $item->product_variant_id,
                        'quantity' => 0,
                        'reserved_quantity' => 0,
                        'damaged_quantity' => 0,
                        'sample_quantity' => 0,
                        'reorder_level' => 0,
                        'reorder_quantity' => 0,
                    ]);
                }

                $before = (int) $stock->quantity;
                $difference = $counted - $before;

                $stock->update([
                    'quantity' => $counted,
                ]);

                $item->update([
                    'counted_quantity' => $counted,
                    'difference' => $difference,
                ]);

                if ($difference !== 0) {
                    StockMovement::create([
                        'product_variant_id' =>
                            $item->product_variant_id,
                        'type' =>
                            $difference > 0
                                ? 'adjustment_in'
                                : 'adjustment_out',
                        'quantity' => abs($difference),
                        'quantity_before' => $before,
                        'quantity_after' => $counted,
                        'unit_cost' => null,
                        'reference' =>
                            $count->count_number,
                        'notes' =>
                            'تسوية ناتجة عن الجرد في '
                            . $count->warehouse->name,
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            $count->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.inventory.counts.index')
            ->with(
                'success',
                'تم اعتماد الجرد وتحديث الكميات.'
            );
    }

    private function nextCountNumber(): string
    {
        do {
            $number = 'SC-'
                . now()->format('YmdHis')
                . '-'
                . random_int(1000, 9999);
        } while (
            StockCount::where(
                'count_number',
                $number
            )->exists()
        );

        return $number;
    }
}
