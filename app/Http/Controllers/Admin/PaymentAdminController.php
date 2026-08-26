<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class PaymentAdminController extends Controller
{
    public function index(Request $request)
    {
        $gateways = PaymentGateway::query()
            ->orderBy('name_ar')
            ->get();

        $transactions = PaymentTransaction::query()
            ->with(['gateway', 'user'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->toString());
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->string('type')->toString());
            })
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('admin.payments.index', compact(
            'gateways',
            'transactions'
        ));
    }

    public function storeGateway(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:160'],
            'name_en' => ['nullable', 'string', 'max:160'],
            'code' => ['required', 'string', 'max:100', 'unique:payment_gateways,code'],
            'gateway_type' => [
                'required',
                'in:cash,cod,card,bank_transfer,online_gateway,store_credit,gift_card,other',
            ],
            'integration_type' => [
                'required',
                'in:manual,redirect,api,hosted_fields,app_to_app',
            ],
            'supports_refund' => ['nullable', 'boolean'],
            'supports_partial_refund' => ['nullable', 'boolean'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        PaymentGateway::create([
            ...$validated,
            'supports_refund' => (bool) ($validated['supports_refund'] ?? false),
            'supports_partial_refund' => (bool) (
                $validated['supports_partial_refund'] ?? false
            ),
            'currency' => strtoupper($validated['currency'] ?? 'OMR'),
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إنشاء طريقة/بوابة الدفع.');
    }
}
