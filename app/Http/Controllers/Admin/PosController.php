<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\PosHeldSale;
use App\Models\PosSale;
use App\Models\PosShift;
use App\Models\ProductVariant;
use App\Models\ProductVariantStock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\PosHeldSaleService;
use App\Services\PosPaymentService;
use App\Services\PosReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function __construct(
        private readonly PosPaymentService $paymentService,
        private readonly PosHeldSaleService $heldSaleService,
        private readonly PosReceiptService $receiptService,
    ) {
    }

    public function index()
    {
        $openShift = PosShift::query()
            ->with('cashRegister')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        $registers = CashRegister::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $variants = ProductVariant::query()
            ->with([
                'product.images',
                'product.category',
            ])
            ->where('is_active', true)
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('id')
            ->get();

        $recentSales = PosSale::query()
            ->with([
                'cashRegister',
                'user',
                'payments',
            ])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.pos.index', compact(
            'registers',
            'variants',
            'recentSales',
            'openShift'
        ));
    }

    public function store(Request $request)
    {
        $openShift = PosShift::query()
            ->with('cashRegister')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if (!$openShift) {
            return redirect()
                ->route('admin.pos.shifts.index')
                ->withErrors([
                    'shift' => 'يجب فتح وردية كاشير قبل تنفيذ أي عملية بيع.',
                ]);
        }

        $validated = $request->validate([
            'cash_register_id' => ['nullable', 'integer', 'exists:cash_registers,id'],

            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],

            'payment_method' => ['required', 'in:cash,card,transfer,mixed'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],

            'held_sale_id' => ['nullable', 'integer', 'exists:pos_held_sales,id'],
            'exchange_parent_sale_id' => ['nullable', 'integer', 'exists:pos_sales,id'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.line_discount' => ['nullable', 'numeric', 'min:0'],

            'payments' => ['nullable', 'array'],
            'payments.*.payment_method' => [
                'required_with:payments',
                'in:cash,card,bank_transfer,store_credit,other',
            ],
            'payments.*.amount' => ['required_with:payments', 'numeric', 'gt:0'],
            'payments.*.tendered_amount' => ['nullable', 'numeric', 'min:0'],
            'payments.*.reference' => ['nullable', 'string', 'max:150'],
            'payments.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $registerId = (int) $openShift->cash_register_id;

        if (
            !empty($validated['cash_register_id'])
            && (int) $validated['cash_register_id'] !== $registerId
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'cash_register_id' =>
                        'يجب استخدام خزينة الوردية المفتوحة الحالية.',
                ]);
        }

        $sale = DB::transaction(function () use (
            $validated,
            $openShift
        ) {
            $lockedShift = PosShift::query()
                ->lockForUpdate()
                ->findOrFail($openShift->id);

            if (!$lockedShift->isOpen()) {
                throw ValidationException::withMessages([
                    'shift' => 'تم إغلاق الوردية. افتح وردية جديدة قبل البيع.',
                ]);
            }

            if ((int) $lockedShift->user_id !== (int) auth()->id()) {
                throw ValidationException::withMessages([
                    'shift' => 'هذه الوردية لا تخص المستخدم الحالي.',
                ]);
            }

            $register = CashRegister::query()
                ->lockForUpdate()
                ->findOrFail($lockedShift->cash_register_id);

            if (!$register->is_active) {
                throw ValidationException::withMessages([
                    'cash_register_id' => 'خزينة الوردية غير مفعلة.',
                ]);
            }

            $warehouse = Warehouse::query()
                ->where('is_default', true)
                ->where('is_active', true)
                ->lockForUpdate()
                ->first();

            if (!$warehouse) {
                throw ValidationException::withMessages([
                    'inventory' => 'لا يوجد مخزن افتراضي مفعل لنقطة البيع.',
                ]);
            }

            $preparedItems = [];
            $subtotal = 0;

            foreach ($validated['items'] as $index => $item) {
                $variant = ProductVariant::query()
                    ->with('product')
                    ->lockForUpdate()
                    ->find($item['product_variant_id']);

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

                $warehouseStock = ProductVariantStock::query()
                    ->where('warehouse_id', $warehouse->id)
                    ->where('product_variant_id', $variant->id)
                    ->lockForUpdate()
                    ->first();

                if (!$warehouseStock) {
                    throw ValidationException::withMessages([
                        "items.$index" =>
                            'أحد المنتجات لا يملك رصيدًا في المخزن الافتراضي.',
                    ]);
                }

                $quantity = (int) $item['quantity'];
                $availableStock = (int) $warehouseStock->available_quantity;

                if ($quantity > $availableStock) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" =>
                            'الكمية المطلوبة أكبر من المخزون المتاح.',
                    ]);
                }

                $unitPrice = round(
                    $variant->price !== null
                        ? (float) $variant->price
                        : (float) ($variant->product->price ?? 0),
                    3
                );

                $gross = round($unitPrice * $quantity, 3);
                $lineDiscount = round(
                    (float) ($item['line_discount'] ?? 0),
                    3
                );

                if ($lineDiscount > $gross) {
                    throw ValidationException::withMessages([
                        "items.$index.line_discount" =>
                            'خصم الصنف أكبر من قيمة الصنف.',
                    ]);
                }

                $lineTotal = round($gross - $lineDiscount, 3);
                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'variant' => $variant,
                    'warehouse_stock' => $warehouseStock,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_discount' => $lineDiscount,
                    'line_total' => $lineTotal,
                ];
            }

            $subtotal = round($subtotal, 3);
            $discountAmount = round(
                (float) ($validated['discount_amount'] ?? 0),
                3
            );

            if ($discountAmount > $subtotal) {
                throw ValidationException::withMessages([
                    'discount_amount' =>
                        'قيمة خصم الفاتورة لا يمكن أن تتجاوز المجموع.',
                ]);
            }

            $total = round(max($subtotal - $discountAmount, 0), 3);

            if ($total <= 0) {
                throw ValidationException::withMessages([
                    'total' => 'إجمالي الفاتورة يجب أن يكون أكبر من صفر.',
                ]);
            }

            do {
                $saleNumber = 'POS-'
                    . now()->format('YmdHis')
                    . '-'
                    . random_int(1000, 9999);
            } while (PosSale::where('sale_number', $saleNumber)->exists());

            $sale = PosSale::create([
                'sale_number' => $saleNumber,
                'cash_register_id' => $register->id,
                'pos_shift_id' => $lockedShift->id,
                'user_id' => auth()->id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'exchange_parent_sale_id' =>
                    $validated['exchange_parent_sale_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'cash_received' => 0,
                'change_due' => 0,
                'receipt_print_count' => 0,
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
                'completed_at' => now(),
            ]);

            foreach ($preparedItems as $prepared) {
                $variant = $prepared['variant'];
                $product = $variant->product;
                $stock = $prepared['warehouse_stock'];
                $quantity = $prepared['quantity'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'product_name_ar' => $product->name_ar ?? 'منتج',
                    'product_name_en' => $product->name_en ?? null,
                    'sku' => $variant->sku ?? $product->sku,
                    'color_name_ar' => $variant->color_name_ar ?? null,
                    'color_name_en' => $variant->color_name_en ?? null,
                    'size' => $variant->size ?? null,
                    'quantity' => $quantity,
                    'returned_quantity' => 0,
                    'unit_price' => $prepared['unit_price'],
                    'line_discount' => $prepared['line_discount'],
                    'line_total' => $prepared['line_total'],
                ]);

                $stock->update([
                    'quantity' => (int) $stock->quantity - $quantity,
                ]);

                $globalAvailable = ProductVariantStock::query()
                    ->where('product_variant_id', $variant->id)
                    ->get()
                    ->sum(function ($row) {
                        return max(
                            (int) $row->quantity
                            - (int) $row->reserved_quantity
                            - (int) $row->damaged_quantity
                            - (int) $row->sample_quantity,
                            0
                        );
                    });

                $globalBefore = (int) $variant->stock_quantity;

                $variant->update([
                    'stock_quantity' => $globalAvailable,
                ]);

                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'stock_out',
                    'quantity' => $quantity,
                    'quantity_before' => $globalBefore,
                    'quantity_after' => $globalAvailable,
                    'unit_cost' => $product->cost_price ?? null,
                    'reference' => $sale->sale_number,
                    'notes' =>
                        'خصم مخزون بسبب بيع نقطة البيع من المخزن الافتراضي',
                    'user_id' => auth()->id(),
                ]);
            }

            $payments = $this->normalizePayments(
                $validated,
                $total
            );

            $this->paymentService->processSalePayments(
                $sale,
                $lockedShift,
                $payments
            );

            $this->receiptService->ensureReceiptNumber($sale);

            if (!empty($validated['held_sale_id'])) {
                $held = PosHeldSale::query()
                    ->where('id', $validated['held_sale_id'])
                    ->where('user_id', auth()->id())
                    ->first();

                if ($held) {
                    $this->heldSaleService->delete($held);
                }
            }

            return $sale->refresh();
        });

        return redirect()
            ->route('admin.pos.index')
            ->with('success',
                'تم حفظ عملية البيع بنجاح. رقم العملية: '
                . $sale->sale_number
            )
            ->with('pos_last_sale_id', $sale->id);
    }

    private function normalizePayments(
        array $validated,
        float $total
    ): array {
        if (!empty($validated['payments'])) {
            return array_values(array_filter(
                $validated['payments'],
                fn ($payment) =>
                    round((float) ($payment['amount'] ?? 0), 3) > 0
            ));
        }

        $method = $validated['payment_method'];

        if ($method === 'cash') {
            return [[
                'payment_method' => 'cash',
                'amount' => $total,
                'tendered_amount' => $total,
            ]];
        }

        if ($method === 'card') {
            return [[
                'payment_method' => 'card',
                'amount' => $total,
            ]];
        }

        if ($method === 'transfer') {
            return [[
                'payment_method' => 'bank_transfer',
                'amount' => $total,
            ]];
        }

        throw ValidationException::withMessages([
            'payments' => 'أدخل تفاصيل الدفع المختلط.',
        ]);
    }
}
