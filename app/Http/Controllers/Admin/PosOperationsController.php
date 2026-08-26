<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosHeldSale;
use App\Models\PosSale;
use App\Models\PosShift;
use App\Models\User;
use App\Services\PosCashDrawerService;
use App\Services\PosHeldSaleService;
use App\Services\PosReceiptService;
use App\Services\PosReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosOperationsController extends Controller
{
    public function __construct(
        private readonly PosHeldSaleService $heldSaleService,
        private readonly PosReturnService $returnService,
        private readonly PosReceiptService $receiptService,
        private readonly PosCashDrawerService $cashDrawerService,
    ) {
    }

    /**
     * بحث سريع عن العملاء من داخل POS.
     */
    public function customers(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $q = trim((string) ($validated['q'] ?? ''));
        $limit = (int) ($validated['limit'] ?? 15);

        $customers = User::query()
            ->where('role', 'customer')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%' . $q . '%')
                        ->orWhere('phone', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%');
                });
            })
            ->orderBy('name')
            ->limit($limit)
            ->get([
                'id',
                'name',
                'phone',
                'email',
            ]);

        return response()->json([
            'ok' => true,
            'customers' => $customers,
        ]);
    }

    /**
     * آخر المبيعات للكاشير الحالي.
     */
    public function salesHistory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $q = trim((string) ($validated['q'] ?? ''));
        $limit = (int) ($validated['limit'] ?? 30);

        $sales = PosSale::query()
            ->with([
                'payments:id,pos_sale_id,payment_method,amount,reference',
                'items:id,pos_sale_id,product_variant_id,quantity,returned_quantity,unit_price,line_discount,line_total',
            ])
            ->where('user_id', auth()->id())
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('sale_number', 'like', '%' . $q . '%')
                        ->orWhere('receipt_number', 'like', '%' . $q . '%')
                        ->orWhere('customer_name', 'like', '%' . $q . '%')
                        ->orWhere('customer_phone', 'like', '%' . $q . '%');
                });
            })
            ->latest('id')
            ->limit($limit)
            ->get();

        return response()->json([
            'ok' => true,
            'sales' => $sales,
        ]);
    }

    /**
     * قائمة الفواتير المعلقة للكاشير الحالي.
     */
    public function heldSales(): JsonResponse
    {
        $heldSales = PosHeldSale::query()
            ->with([
                'items.variant.product',
            ])
            ->where('user_id', auth()->id())
            ->latest('held_at')
            ->limit(100)
            ->get();

        return response()->json([
            'ok' => true,
            'held_sales' => $heldSales,
        ]);
    }

    /**
     * تعليق الفاتورة الحالية.
     */
    public function holdSale(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.line_discount' => ['nullable', 'numeric', 'min:0'],

            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'customer_name' => ['nullable', 'string', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'label' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $shift = $this->currentShiftOrFail();

        $heldSale = $this->heldSaleService->hold(
            $shift,
            $validated['items'],
            [
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'label' => $validated['label'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'تم تعليق الفاتورة بنجاح.',
            'held_sale' => $heldSale,
        ]);
    }

    /**
     * تحميل فاتورة معلقة إلى الواجهة.
     */
    public function resumeHeldSale(PosHeldSale $heldSale): JsonResponse
    {
        $heldSale = $this->heldSaleService->resume($heldSale);

        return response()->json([
            'ok' => true,
            'held_sale' => $heldSale,
        ]);
    }

    /**
     * حذف فاتورة معلقة.
     */
    public function deleteHeldSale(PosHeldSale $heldSale): JsonResponse
    {
        $this->heldSaleService->delete($heldSale);

        return response()->json([
            'ok' => true,
            'message' => 'تم حذف الفاتورة المعلقة.',
        ]);
    }

    /**
     * إنشاء مرتجع أو جزء من استبدال.
     */
    public function createReturn(
        Request $request,
        PosSale $sale
    ): JsonResponse {
        $validated = $request->validate([
            'return_type' => [
                'required',
                'in:refund,exchange',
            ],
            'refund_method' => [
                'nullable',
                'in:cash,card,bank_transfer,store_credit,original_method,none',
            ],
            'deduction_amount' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:500'],
            'reference' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:2000'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.pos_sale_item_id' => [
                'required',
                'integer',
                'exists:pos_sale_items,id',
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.stock_action' => [
                'nullable',
                'in:restock,damaged,no_stock_change',
            ],
            'items.*.reason' => ['nullable', 'string', 'max:500'],
        ]);

        $shift = $this->currentShiftOrFail();

        $return = $this->returnService->createReturn(
            $sale,
            $shift,
            $validated['items'],
            [
                'return_type' => $validated['return_type'],
                'refund_method' => $validated['refund_method'] ?? null,
                'deduction_amount' => $validated['deduction_amount'] ?? 0,
                'reason' => $validated['reason'] ?? null,
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => $validated['return_type'] === 'exchange'
                ? 'تم تسجيل أصناف الاستبدال.'
                : 'تم تنفيذ المرتجع بنجاح.',
            'return' => $return,
        ]);
    }

    /**
     * عرض الإيصال الحراري.
     */
    public function receipt(PosSale $sale): View
    {
        $data = $this->receiptService->getReceiptData($sale);

        return view('admin.pos.receipt', $data);
    }

    /**
     * تسجيل طباعة/إعادة طباعة الإيصال ثم عرض بياناته.
     */
    public function printReceipt(PosSale $sale): JsonResponse
    {
        $sale = $this->receiptService->markPrinted($sale);

        return response()->json([
            'ok' => true,
            'receipt_number' => $sale->receipt_number,
            'receipt_print_count' => $sale->receipt_print_count,
            'receipt_url' => route('admin.pos.receipt', $sale),
        ]);
    }

    /**
     * فتح درج النقد.
     *
     * هذا يسجل الطلب فقط. نبضة فتح الدرج الفعلية تعتمد على
     * تعريف طابعة الإيصالات/الدراور في الجهاز المحلي.
     */
    public function openDrawer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $shift = $this->currentShiftOrFail();

        $this->cashDrawerService->openDrawer(
            $shift,
            $validated['reason'] ?? null
        );

        return response()->json([
            'ok' => true,
            'message' => 'تم تسجيل طلب فتح درج النقد.',
        ]);
    }

    /**
     * Cash In.
     */
    public function cashIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $shift = $this->currentShiftOrFail();

        $this->cashDrawerService->cashIn(
            $shift,
            (float) $validated['amount'],
            $validated['reason'] ?? null
        );

        return response()->json([
            'ok' => true,
            'message' => 'تم تسجيل الإيداع النقدي.',
        ]);
    }

    /**
     * Cash Out.
     */
    public function cashOut(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $shift = $this->currentShiftOrFail();

        $this->cashDrawerService->cashOut(
            $shift,
            (float) $validated['amount'],
            $validated['reason'] ?? null
        );

        return response()->json([
            'ok' => true,
            'message' => 'تم تسجيل السحب النقدي.',
        ]);
    }

    /**
     * معلومات فاتورة واحدة للمرتجع/الاستبدال/السجل.
     */
    public function saleDetails(PosSale $sale): JsonResponse
    {
        $sale->load([
            'items.variant.product',
            'payments',
            'returns.items',
            'user',
            'cashRegister',
        ]);

        return response()->json([
            'ok' => true,
            'sale' => $sale,
        ]);
    }

    private function currentShiftOrFail(): PosShift
    {
        return PosShift::query()
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest('opened_at')
            ->firstOrFail();
    }
}
