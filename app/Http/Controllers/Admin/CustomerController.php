<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * قائمة العملاء.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search'));

        $customers = User::query()
            ->where('role', 'customer')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),

            'active_customers' => User::where('role', 'customer')
                ->where('is_active', true)
                ->count(),

            'inactive_customers' => User::where('role', 'customer')
                ->where('is_active', false)
                ->count(),

            'customers_with_orders' => User::where('role', 'customer')
                ->whereHas('orders')
                ->count(),
        ];

        return view('admin.customers.index', compact(
            'customers',
            'stats',
            'search'
        ));
    }

    /**
     * تفاصيل العميل.
     */
    public function show(User $customer)
    {
        abort_unless($customer->role === 'customer', 404);

        $customer->load([
            'orders' => function ($query) {
                $query->latest();
            },
            'orders.items',
        ]);

        $customer->loadCount('orders');
        $customer->loadSum('orders as total_spent', 'total');

        $loyaltyAccount = \App\Models\LoyaltyAccount::where(
            'user_id',
            $customer->id
        )->first();

        $loyaltyTransactions = \App\Models\LoyaltyTransaction::where(
            'user_id',
            $customer->id
        )
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.customers.show', compact(
            'customer',
            'loyaltyAccount',
            'loyaltyTransactions'
        ));
    }

    /**
     * تفعيل أو إيقاف حساب العميل.
     */
    public function toggle(User $customer)
    {
        abort_unless($customer->role === 'customer', 404);

        $customer->is_active = ! $customer->is_active;
        $customer->save();

        return back()->with(
            'success',
            $customer->is_active
                ? 'تم تفعيل حساب العميل.'
                : 'تم إيقاف حساب العميل.'
        );
    }
}