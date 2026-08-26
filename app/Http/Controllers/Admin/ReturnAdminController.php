<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\Refund;
use App\Models\ReturnPolicy;
use App\Models\ReturnRequest;
use App\Services\LoyaltyMarketingService;
use App\Services\PaymentService;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class ReturnAdminController extends Controller
{
    public function __construct(
        private readonly ReturnService $returns,
        private readonly PaymentService $payments,
        private readonly LoyaltyMarketingService $loyalty
    ) {
    }

    public function index(Request $request)
    {
        $returns = ReturnRequest::query()
            ->with(['order', 'user', 'policy', 'reviewer'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->toString());
            })
            ->latest('requested_at')
            ->paginate(40)
            ->withQueryString();

        $policies = ReturnPolicy::query()
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('admin.returns.index', compact(
            'returns',
            'policies'
        ));
    }

    public function show(ReturnRequest $returnRequest)
    {
        $returnRequest->load([
            'order.items',
            'user',
            'policy',
            'replacementOrder',
            'reviewer',
            'items.orderItem',
            'shipments.carrier',
            'refunds.paymentTransaction',
        ]);

        $paymentTransactions = PaymentTransaction::query()
            ->where('payable_type', $returnRequest->order->getMorphClass())
            ->where('payable_id', $returnRequest->order_id)
            ->whereIn('status', [
                'paid',
                'partially_refunded',
            ])
            ->latest()
            ->get();

        return view('admin.returns.show', compact(
            'returnRequest',
            'paymentTransactions'
        ));
    }

    public function approve(Request $request, ReturnRequest $returnRequest)
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.approved_refund_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.restock' => ['nullable', 'boolean'],
            'items.*.mark_damaged' => ['nullable', 'boolean'],
        ]);

        $this->returns->approve($returnRequest, $validated);

        return back()->with('success', 'تم اعتماد طلب المرتجع.');
    }

    public function markReceived(ReturnRequest $returnRequest)
    {
        $this->returns->markReceived($returnRequest);

        return back()->with('success', 'تم تسجيل استلام المرتجع.');
    }

    public function refund(Request $request, ReturnRequest $returnRequest)
    {
        $validated = $request->validate([
            'refund_method' => [
                'required',
                'in:original_payment,cash,bank_transfer,store_credit,gift_card,other',
            ],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_transaction_id' => [
                'nullable',
                'exists:payment_transactions,id',
            ],
            'reference' => ['nullable', 'string', 'max:180'],
        ]);

        if ($validated['refund_method'] === 'store_credit') {
            $user = $returnRequest->user;

            if (!$user) {
                return back()->withErrors([
                    'refund' => 'لا يمكن إضافة رصيد متجر بدون عميل مرتبط.',
                ]);
            }

            $this->returns->completeStoreCredit(
                $returnRequest,
                $user,
                $this->loyalty
            );

            return back()->with('success', 'تم تحويل قيمة المرتجع إلى رصيد متجر.');
        }

        $refund = $this->returns->createRefundRecord(
            $returnRequest,
            $validated['refund_method'],
            (float) $validated['amount'],
            $validated['payment_transaction_id'] ?? null,
            $validated['reference'] ?? null
        );

        if (!empty($validated['payment_transaction_id'])) {
            $transaction = PaymentTransaction::findOrFail(
                $validated['payment_transaction_id']
            );

            $this->payments->registerRefund(
                $transaction,
                (float) $validated['amount']
            );
        }

        $refund->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        $returnRequest->update([
            'status' => 'refunded',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'تم تسجيل الاسترداد.');
    }

    public function storePolicy(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:180'],
            'name_en' => ['nullable', 'string', 'max:180'],
            'return_window_days' => ['required', 'integer', 'min:0'],
            'exchange_window_days' => ['required', 'integer', 'min:0'],
            'allow_refund' => ['nullable', 'boolean'],
            'allow_exchange' => ['nullable', 'boolean'],
            'allow_store_credit' => ['nullable', 'boolean'],
            'customer_pays_return_shipping' => ['nullable', 'boolean'],
            'require_original_packaging' => ['nullable', 'boolean'],
            'terms_ar' => ['nullable', 'string'],
            'terms_en' => ['nullable', 'string'],
        ]);

        ReturnPolicy::create([
            ...$validated,
            'allow_refund' => (bool) ($validated['allow_refund'] ?? false),
            'allow_exchange' => (bool) ($validated['allow_exchange'] ?? false),
            'allow_store_credit' => (bool) ($validated['allow_store_credit'] ?? false),
            'customer_pays_return_shipping' => (bool) (
                $validated['customer_pays_return_shipping'] ?? false
            ),
            'require_original_packaging' => (bool) (
                $validated['require_original_packaging'] ?? false
            ),
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إنشاء سياسة الإرجاع.');
    }
}
