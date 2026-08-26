<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\SupplierTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search', ''));

        $suppliers = Supplier::query()
            ->withCount('purchaseOrders')
            ->withSum('purchaseOrders as purchases_total', 'total')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('whatsapp', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere(
                            'commercial_registration',
                            'like',
                            "%{$search}%"
                        );
                });
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'total' => Supplier::count(),
            'active' => Supplier::where('is_active', true)->count(),
            'inactive' => Supplier::where('is_active', false)->count(),
            'balance' => (float) Supplier::sum('current_balance'),
        ];

        return view('admin.suppliers.index', compact(
            'suppliers',
            'stats',
            'search'
        ));
    }

    public function show(Supplier $supplier): View
    {
        $purchases = PurchaseOrder::query()
            ->where('supplier_id', $supplier->id)
            ->latest('order_date')
            ->latest('id')
            ->get();

        $payments = SupplierPayment::query()
            ->where('supplier_id', $supplier->id)
            ->with('purchaseOrder')
            ->latest('payment_date')
            ->latest('id')
            ->get();

        $transactions = SupplierTransaction::query()
            ->where('supplier_id', $supplier->id)
            ->with([
                'purchaseOrder',
                'payment',
            ])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $stats = [
            'purchases_total' => (float) $purchases->sum('total'),
            'paid_total' => (float) $payments->sum('amount'),
            'balance' => (float) $supplier->current_balance,
            'purchase_count' => $purchases->count(),
        ];

        return view('admin.suppliers.show', compact(
            'supplier',
            'purchases',
            'payments',
            'transactions',
            'stats'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateSupplier($request);

        $openingBalance = round(
            (float) ($data['opening_balance'] ?? 0),
            3
        );

        $supplier = Supplier::create([
            ...$data,
            'opening_balance' => $openingBalance,
            'current_balance' => $openingBalance,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($openingBalance > 0) {
            SupplierTransaction::create([
                'supplier_id' => $supplier->id,
                'purchase_order_id' => null,
                'supplier_payment_id' => null,
                'user_id' => auth()->id(),
                'type' => 'opening_balance',
                'transaction_date' => now()->toDateString(),
                'debit' => $openingBalance,
                'credit' => 0,
                'balance_after' => $openingBalance,
                'reference' => 'OPENING-' . $supplier->id,
                'description' => 'الرصيد الافتتاحي للمورد',
            ]);
        }

        return back()->with('success', 'تمت إضافة المورد بنجاح.');
    }

    public function update(
        Request $request,
        Supplier $supplier
    ): RedirectResponse {
        $data = $this->validateSupplier($request, $supplier);

        unset($data['opening_balance']);

        $supplier->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'تم تحديث بيانات المورد.');
    }

    public function toggle(Supplier $supplier): RedirectResponse
    {
        $supplier->update([
            'is_active' => ! $supplier->is_active,
        ]);

        return back()->with(
            'success',
            $supplier->is_active
                ? 'تم تفعيل المورد.'
                : 'تم إيقاف المورد.'
        );
    }

    private function validateSupplier(
        Request $request,
        ?Supplier $supplier = null
    ): array {
        return $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'company_name' => ['nullable', 'string', 'max:180'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => [
                'nullable',
                'email',
                'max:190',
                Rule::unique('suppliers', 'email')
                    ->ignore($supplier?->id),
            ],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'commercial_registration' => [
                'nullable',
                'string',
                'max:100',
            ],
            'country' => ['required', 'string', 'max:100'],
            'governorate' => ['nullable', 'string', 'max:100'],
            'wilayat' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:2000'],
            'opening_balance' => [
                $supplier ? 'sometimes' : 'nullable',
                'numeric',
                'min:0',
            ],
            'payment_terms_days' => [
                'nullable',
                'integer',
                'min:0',
                'max:3650',
            ],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);
    }
}
