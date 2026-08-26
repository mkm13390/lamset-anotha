<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\ProductVariantStock;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierReturn;
use App\Models\SupplierTransaction;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SupplierReturnController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $warehouses = Warehouse::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $purchases = PurchaseOrder::query()
            ->with('supplier')
            ->whereIn('status', [
                'ordered',
                'partially_received',
                'received',
            ])
            ->latest('order_date')
            ->get();

        $variants = ProductVariant::query()
            ->with('product')
            ->where('is_active', true)
            ->orderBy('sku')
            ->get();

        $returns = SupplierReturn::query()
            ->with([
                'supplier',
                'purchaseOrder',
                'warehouse',
                'user',
                'items.variant.product',
            ])
            ->latest('return_date')
            ->latest('id')
            ->paginate(20);

        return view(
            'admin.inventory.supplier-returns.index',
            compact(
                'suppliers',
                'warehouses',
                'purchases',
                'variants',
                'returns'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],
            'purchase_order_id' => [
                'nullable',
                'integer',
                'exists:purchase_orders,id',
            ],
            'warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
            ],
            'return_date' => [
                'required',
                'date',
            ],
            'reference' => [
                'nullable',
                'string',
                'max:150',
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
            'items.*.unit_cost' => [
                'required',
                'numeric',
                'min:0',
            ],
            'items.*.reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $supplier = Supplier::query()
                ->lockForUpdate()
                ->findOrFail($validated['supplier_id']);

            $warehouse = Warehouse::query()
                ->lockForUpdate()
                ->findOrFail($validated['warehouse_id']);

            if (! $warehouse->is_active) {
                throw ValidationException::withMessages([
                    'warehouse_id' =>
                        'المخزن المحدد غير فعال.',
                ]);
            }

            $purchase = null;

            if (! empty($validated['purchase_order_id'])) {
                $purchase = PurchaseOrder::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $validated['purchase_order_id']
                    );

                if (
                    (int) $purchase->supplier_id
                    !== (int) $supplier->id
                ) {
                    throw ValidationException::withMessages([
                        'purchase_order_id' =>
                            'أمر الشراء المحدد لا يتبع هذا المورد.',
                    ]);
                }
            }

            $preparedItems = [];
            $total = 0.0;

            foreach ($validated['items'] as $item) {
                $variantId = (int)
                    $item['product_variant_id'];

                $quantity = (int)
                    $item['quantity'];

                $unitCost = round(
                    (float) $item['unit_cost'],
                    3
                );

                $lineTotal = round(
                    $quantity * $unitCost,
                    3
                );

                $stock = ProductVariantStock::query()
                    ->where(
                        'warehouse_id',
                        $warehouse->id
                    )
                    ->where(
                        'product_variant_id',
                        $variantId
                    )
                    ->lockForUpdate()
                    ->first();

                if (! $stock) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'لا يوجد رصيد لهذا الصنف في المخزن المحدد.',
                    ]);
                }

                if ($quantity > $stock->available_quantity) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'كمية المرتجع أكبر من الكمية المتاحة في المخزن.',
                    ]);
                }

                $preparedItems[] = [
                    'stock' => $stock,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'line_total' => $lineTotal,
                    'reason' => $item['reason'] ?? null,
                ];

                $total += $lineTotal;
            }

            $total = round($total, 3);

            $supplierReturn = SupplierReturn::create([
                'return_number' =>
                    $this->nextReturnNumber(),
                'supplier_id' => $supplier->id,
                'purchase_order_id' => $purchase?->id,
                'warehouse_id' => $warehouse->id,
                'user_id' => auth()->id(),
                'return_date' =>
                    $validated['return_date'],
                'status' => 'completed',
                'total_amount' => $total,
                'reference' =>
                    $validated['reference'] ?? null,
                'notes' =>
                    $validated['notes'] ?? null,
            ]);

            foreach ($preparedItems as $prepared) {
                $stock = $prepared['stock'];

                $before = (int) $stock->quantity;
                $after = $before - $prepared['quantity'];

                $stock->update([
                    'quantity' => $after,
                ]);

                $supplierReturn->items()->create([
                    'product_variant_id' =>
                        $prepared['variant_id'],
                    'quantity' =>
                        $prepared['quantity'],
                    'unit_cost' =>
                        $prepared['unit_cost'],
                    'line_total' =>
                        $prepared['line_total'],
                    'reason' =>
                        $prepared['reason'],
                ]);

                StockMovement::create([
                    'product_variant_id' =>
                        $prepared['variant_id'],
                    'type' => 'supplier_return',
                    'quantity' =>
                        $prepared['quantity'],
                    'quantity_before' => $before,
                    'quantity_after' => $after,
                    'unit_cost' =>
                        $prepared['unit_cost'],
                    'reference' =>
                        $supplierReturn->return_number,
                    'notes' =>
                        'مرتجع إلى المورد '
                        . $supplier->name
                        . ' من المخزن '
                        . $warehouse->name,
                    'user_id' => auth()->id(),
                ]);

                $this->syncVariantAvailableStock(
                    $prepared['variant_id']
                );
            }

            $newSupplierBalance = round(
                (float) $supplier->current_balance
                - $total,
                3
            );

            $supplier->update([
                'current_balance' =>
                    $newSupplierBalance,
            ]);

            if ($purchase) {
                $newBalanceDue = round(
                    (float) $purchase->balance_due
                    - $total,
                    3
                );

                $newBalanceDue = max(
                    $newBalanceDue,
                    0
                );

                $paymentStatus = 'unpaid';

                if (
                    (float) $purchase->paid_amount > 0
                    && $newBalanceDue > 0
                ) {
                    $paymentStatus = 'partial';
                } elseif ($newBalanceDue <= 0) {
                    $paymentStatus = 'paid';
                }

                $purchase->update([
                    'balance_due' => $newBalanceDue,
                    'payment_status' =>
                        $paymentStatus,
                ]);
            }

            SupplierTransaction::create([
                'supplier_id' => $supplier->id,
                'purchase_order_id' => $purchase?->id,
                'supplier_payment_id' => null,
                'user_id' => auth()->id(),
                'type' => 'purchase_return',
                'transaction_date' =>
                    $validated['return_date'],
                'debit' => 0,
                'credit' => $total,
                'balance_after' =>
                    $newSupplierBalance,
                'reference' =>
                    $supplierReturn->return_number,
                'description' =>
                    'مرتجع مشتريات إلى المورد',
            ]);
        });

        return back()->with(
            'success',
            'تم تسجيل مرتجع المورد وتحديث المخزون وحساب المورد.'
        );
    }

    private function syncVariantAvailableStock(
        int $variantId
    ): void {
        $available = ProductVariantStock::query()
            ->where(
                'product_variant_id',
                $variantId
            )
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

    private function nextReturnNumber(): string
    {
        do {
            $number = 'SR-'
                . now()->format('YmdHis')
                . '-'
                . random_int(1000, 9999);
        } while (
            SupplierReturn::where(
                'return_number',
                $number
            )->exists()
        );

        return $number;
    }
}
