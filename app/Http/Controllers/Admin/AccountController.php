<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('account.index', [
            'orders' => $orders,
            'ordersCount' => $orders->count(),
            'wishlistCount' => 0,
            'points' => 0,
            'activeSection' => session('account_section', 'dashboard'),
            'trackingOrder' => session('tracking_order'),
        ]);
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:100'],
        ]);

        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->where('order_number', trim($validated['order_number']))
            ->first();

        if (!$order) {
            return redirect()
                ->route('account.index')
                ->with('account_section', 'tracking')
                ->withErrors([
                    'order_number' => 'لم يتم العثور على طلب بهذا الرقم في حسابك.',
                ]);
        }

        return redirect()
            ->route('account.index')
            ->with('account_section', 'tracking')
            ->with('tracking_order', [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'total' => (float) $order->total,
                'created_at' => optional($order->created_at)->format('Y-m-d H:i'),
            ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => [
                'required',
                'string',
                'max:30',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => strtolower($validated['email']),
        ]);

        return redirect()
            ->route('account.index')
            ->with('account_section', 'profile')
            ->with('success', 'تم تحديث بيانات الحساب بنجاح.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('account.index')
            ->with('account_section', 'profile')
            ->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }
}
