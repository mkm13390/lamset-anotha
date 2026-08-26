<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = trim((string) $request->get('status', ''));
        $search = trim((string) $request->get('search', ''));

        $purchases = PurchaseOrder::query()
            ->with('supplier')
            ->withSum('items as ordered_quantity', 'quantity_ordered')
            ->withSum('items as received_quantity', 'quantity_received')
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('purchase_number', 'like', "%{$search}%")
                        ->orWhere(
                            'supplier_invoice_number',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                            $supplierQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere(
                                    'company_name',
                                    'like',
                                    "%{$search}%"
                                );
                        });
                });
            })
            ->latest('order_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $variants = ProductVariant::query()
            ->with('product')
            ->where('is_active', true)
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('sku')
            ->get();

        $stats = [
            'total' => PurchaseOrder::count(),
            'open' => PurchaseOrder::whereIn('status', [
                'draft',
                'ordered',
                'partially_received',
            ])->count(),
            'received' => PurchaseOrder::where('status', 'received')->count(),
            'balance_due' => (float) PurchaseOrder::sum('balance_due'),
        ];

        return view('admin.purchases.index', compact(
            'purchases',
            'suppliers',
            'variants',
            'stats',
            'status',
            'search'
        ));
    }

    public function show(PurchaseOrder $purchase): View
    {
        $purchase->load([
            'supplier',
            'user',
            'items.product',
            'items.variant',
        ]);

        return view('admin.purchases.show', compact('purchase'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],
            'order_date' => ['required', 'date'],
            'expected_date' => [
                'nullable',
                'date',
                'after_or_equal:order_date',
            ],
            'status' => [
                'required',
                Rule::in(['draft', 'ordered']),
            ],
            'currency' => ['required', 'string', 'max:10'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'shipping_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'supplier_invoice_number' => [
                'nullable',
                'string',
                'max:120',
            ],
            'reference' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:3000'],

            'items' => ['required', 'array', 'min:1'],
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
            'items.*.discount_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'items.*.tax_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'items.*.notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $purchase = DB::transaction(function () use ($validated) {
            $preparedItems = [];
            $subtotal = 0.0;

            foreach ($validated['items'] as $item) {
                $variant = ProductVariant::query()
                    ->with('product')
                    ->findOrFail($item['product_variant_id']);

                if (! $variant->product) {
                    throw ValidationException::withMessages([
                        'items' => 'أحد خيارات المنتجات غير مرتبط بمنتج صالح.',
                    ]);
                }

                $quantity = (int) $item['quantity'];
                $unitCost = round((float) $item['unit_cost'], 3);
                $itemDiscount = round(
                    (float) ($item['discount_amount'] ?? 0),
                    3
                );
                $itemTax = round(
                    (float) ($item['tax_amount'] ?? 0),
                    3
                );

                $gross = round($quantity * $unitCost, 3);

                if ($itemDiscount > $gross) {
                    throw ValidationException::withMessages([
                        'items' => 'خصم أحد الأصناف أكبر من قيمة الصنف.',
                    ]);
                }

                $lineTotal = round(
                    max($gross - $itemDiscount + $itemTax, 0),
                    3
                );

                $subtotal += $gross;

                $preparedItems[] = [
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'discount_amount' => $itemDiscount,
                    'tax_amount' => $itemTax,
                    'line_total' => $lineTotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            $subtotal = round($subtotal, 3);

            $discountAmount = round(
                (float) ($validated['discount_amount'] ?? 0),
                3
            );
            $shippingAmount = round(
                (float) ($validated['shipping_amount'] ?? 0),
                3
            );
            $taxAmount = round(
                (float) ($validated['tax_amount'] ?? 0),
                3
            );

            if ($discountAmount > $subtotal) {
                throw ValidationException::withMessages([
                    'discount_amount' =>
                        'خصم أمر الشراء لا يمكن أن يتجاوز الإجمالي الفرعي.',
                ]);
            }

            $total = round(
                max(
                    $subtotal
                    - $discountAmount
                    + $shippingAmount
                    + $taxAmount,
                    0
                ),
                3
            );

            $supplier = Supplier::query()
                ->lockForUpdate()
                ->findOrFail($validated['supplier_id']);

            $purchase = PurchaseOrder::create([
                'purchase_number' => $this->nextPurchaseNumber(),
                'supplier_id' => $supplier->id,
                'user_id' => auth()->id(),
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'status' => $validated['status'],
                'payment_status' => 'unpaid',
                'currency' => strtoupper($validated['currency']),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_amount' => $shippingAmount,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'paid_amount' => 0,
                'balance_due' => $total,
                'supplier_invoice_number' =>
                    $validated['supplier_invoice_number'] ?? null,
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($preparedItems as $prepared) {
                $variant = $prepared['variant'];
                $product = $variant->product;

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'product_name_ar' => $product->name_ar ?? 'منتج',
                    'product_name_en' => $product->name_en ?? null,
                    'sku' => $variant->sku ?? $product->sku,
                    'color_name_ar' => $variant->color_name_ar ?? null,
                    'color_name_en' => $variant->color_name_en ?? null,
                    'size' => $variant->size ?? null,
                    'quantity_ordered' => $prepared['quantity'],
                    'quantity_received' => 0,
                    'unit_cost' => $prepared['unit_cost'],
                    'discount_amount' => $prepared['discount_amount'],
                    'tax_amount' => $prepared['tax_amount'],
                    'line_total' => $prepared['line_total'],
                    'notes' => $prepared['notes'],
                ]);
            }

            $newSupplierBalance = round(
                (float) $supplier->current_balance + $total,
                3
            );

            $supplier->update([
                'current_balance' => $newSupplierBalance,
            ]);

            SupplierTransaction::create([
                'supplier_id' => $supplier->id,
                'purchase_order_id' => $purchase->id,
                'supplier_payment_id' => null,
                'user_id' => auth()->id(),
                'type' => 'purchase',
                'transaction_date' => $validated['order_date'],
                'debit' => $total,
                'credit' => 0,
                'balance_after' => $newSupplierBalance,
                'reference' => $purchase->purchase_number,
                'description' => 'إضافة أمر شراء إلى حساب المورد',
            ]);

            return $purchase;
        });

        return redirect()
            ->route('admin.purchases.show', $purchase)
            ->with(
                'success',
                'تم إنشاء أمر الشراء رقم '
                . $purchase->purchase_number
                . '.'
            );
    }

    public function updateStatus(
        Request $request,
        PurchaseOrder $purchase
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'ordered',
                    'cancelled',
                ]),
            ],
        ]);

        if (in_array(
            $purchase->status,
            ['partially_received', 'received'],
            true
        )) {
            throw ValidationException::withMessages([
                'status' =>
                    'لا يمكن تغيير أمر بدأ استلامه بهذه الطريقة.',
            ]);
        }

        $purchase->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'تم تحديث حالة أمر الشراء.');
    }

    public function receive(
        Request $request,
        PurchaseOrder $purchase
    ): RedirectResponse {
        if ($purchase->status === 'cancelled') {
            throw ValidationException::withMessages([
                'purchase' => 'لا يمكن استلام أمر شراء ملغى.',
            ]);
        }

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.quantity_received' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        DB::transaction(function () use (
            $validated,
            $purchase
        ) {
            $purchase = PurchaseOrder::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($purchase->id);

            foreach ($purchase->items as $item) {
                $receiveNow = (int) (
                    $validated['items'][$item->id]['quantity_received']
                    ?? 0
                );

                if ($receiveNow <= 0) {
                    continue;
                }

                $remaining = (int) $item->quantity_ordered
                    - (int) $item->quantity_received;

                if ($receiveNow > $remaining) {
                    throw ValidationException::withMessages([
                        "items.{$item->id}.quantity_received" =>
                            'الكمية المستلمة أكبر من الكمية المتبقية.',
                    ]);
                }

                if (! $item->product_variant_id) {
                    throw ValidationException::withMessages([
                        "items.{$item->id}.quantity_received" =>
                            'لا يمكن إضافة المخزون لأن خيار المنتج غير موجود.',
                    ]);
                }

                $variant = ProductVariant::query()
                    ->lockForUpdate()
                    ->findOrFail($item->product_variant_id);

                $stockBefore = (int) ($variant->stock_quantity ?? 0);
                $stockAfter = $stockBefore + $receiveNow;

                $variant->update([
                    'stock_quantity' => $stockAfter,
                ]);

                $item->update([
                    'quantity_received' =>
                        (int) $item->quantity_received + $receiveNow,
                ]);

                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'stock_in',
                    'quantity' => $receiveNow,
                    'quantity_before' => $stockBefore,
                    'quantity_after' => $stockAfter,
                    'unit_cost' => $item->unit_cost,
                    'reference' => $purchase->purchase_number,
                    'notes' => 'استلام بضاعة من أمر شراء',
                    'user_id' => auth()->id(),
                ]);
            }

            $purchase->load('items');

            $ordered = (int) $purchase->items->sum('quantity_ordered');
            $received = (int) $purchase->items->sum('quantity_received');

            $status = 'ordered';
            $receivedDate = null;

            if ($received > 0 && $received < $ordered) {
                $status = 'partially_received';
            } elseif ($ordered > 0 && $received >= $ordered) {
                $status = 'received';
                $receivedDate = now()->toDateString();
            }

            $purchase->update([
                'status' => $status,
                'received_date' => $receivedDate,
            ]);
        });

        return back()->with('success', 'تم تسجيل الكميات المستلمة وتحديث المخزون.');
    }

    public function updatePayment(
        Request $request,
        PurchaseOrder $purchase
    ): RedirectResponse {
        $validated = $request->validate([
            'paid_amount' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $paidAmount = round((float) $validated['paid_amount'], 3);
        $total = round((float) $purchase->total, 3);

        if ($paidAmount > $total) {
            throw ValidationException::withMessages([
                'paid_amount' =>
                    'المبلغ المدفوع لا يمكن أن يتجاوز إجمالي أمر الشراء.',
            ]);
        }

        $balanceDue = round(max($total - $paidAmount, 0), 3);

        $paymentStatus = 'unpaid';

        if ($paidAmount > 0 && $balanceDue > 0) {
            $paymentStatus = 'partial';
        } elseif ($paidAmount >= $total) {
            $paymentStatus = 'paid';
        }

        $purchase->update([
            'paid_amount' => $paidAmount,
            'balance_due' => $balanceDue,
            'payment_status' => $paymentStatus,
        ]);

        return back()->with('success', 'تم تحديث حالة دفع أمر الشراء.');
    }

    private function nextPurchaseNumber(): string
    {
        do {
            $number = 'PO-'
                . now()->format('YmdHis')
                . '-'
                . random_int(1000, 9999);
        } while (
            PurchaseOrder::where('purchase_number', $number)->exists()
        );

        return $number;
    }
}
