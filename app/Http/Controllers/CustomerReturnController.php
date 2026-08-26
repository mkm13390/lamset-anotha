<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnPolicy;
use App\Models\ReturnRequest;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class CustomerReturnController extends Controller
{
    public function __construct(
        private readonly ReturnService $returns
    ) {
    }

    public function index(Request $request)
    {
        $returns = ReturnRequest::query()
            ->with(['order', 'policy', 'items.orderItem'])
            ->where('user_id', $request->user()->id)
            ->latest('requested_at')
            ->paginate(20);

        return view('account.returns.index', compact('returns'));
    }

    public function create(Request $request, Order $order)
    {
        abort_unless(
            (int) $order->user_id === (int) $request->user()->id,
            403
        );

        $order->load('items');

        $policy = ReturnPolicy::query()
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('account.returns.create', compact(
            'order',
            'policy'
        ));
    }

    public function store(Request $request, Order $order)
    {
        abort_unless(
            (int) $order->user_id === (int) $request->user()->id,
            403
        );

        $validated = $request->validate([
            'request_type' => [
                'required',
                'in:refund,exchange,store_credit',
            ],
            'return_policy_id' => [
                'nullable',
                'exists:return_policies,id',
            ],
            'reason_code' => [
                'nullable',
                'string',
                'max:100',
            ],
            'customer_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.selected' => [
                'nullable',
                'boolean',
            ],
            'items.*.order_item_id' => [
                'required',
                'integer',
                'exists:order_items,id',
            ],
            'items.*.quantity' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'items.*.condition' => [
                'nullable',
                'in:unopened,new,used,damaged,defective,wrong_item,other',
            ],
            'items.*.reason_code' => [
                'nullable',
                'string',
                'max:100',
            ],
            'items.*.notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $items = collect($validated['items'])
            ->filter(fn ($item) => !empty($item['selected']))
            ->map(fn ($item) => [
                'order_item_id' => $item['order_item_id'],
                'quantity' => $item['quantity'] ?? 1,
                'condition' => $item['condition'] ?? 'new',
                'reason_code' => $item['reason_code']
                    ?? ($validated['reason_code'] ?? null),
                'notes' => $item['notes'] ?? null,
            ])
            ->values()
            ->all();

        if (empty($items)) {
            return back()
                ->withInput()
                ->withErrors([
                    'items' => 'اختر منتجًا واحدًا على الأقل.',
                ]);
        }

        foreach ($items as $item) {
            $belongsToOrder = $order->items()
                ->whereKey($item['order_item_id'])
                ->exists();

            abort_unless($belongsToOrder, 403);
        }

        $returnRequest = $this->returns->createRequest(
            $order,
            $request->user(),
            [
                'request_type' => $validated['request_type'],
                'return_policy_id' => $validated['return_policy_id'] ?? null,
                'reason_code' => $validated['reason_code'] ?? null,
                'customer_notes' => $validated['customer_notes'] ?? null,
            ],
            $items
        );

        return redirect()
            ->route('account.returns.show', $returnRequest)
            ->with('success', 'تم إرسال طلب الإرجاع/الاستبدال بنجاح.');
    }

    public function show(Request $request, ReturnRequest $returnRequest)
    {
        abort_unless(
            (int) $returnRequest->user_id === (int) $request->user()->id,
            403
        );

        $returnRequest->load([
            'order',
            'policy',
            'items.orderItem',
            'shipments.carrier',
            'refunds',
        ]);

        return view('account.returns.show', compact(
            'returnRequest'
        ));
    }
}
