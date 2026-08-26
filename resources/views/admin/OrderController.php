<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * عرض جميع الطلبات في لوحة الإدارة.
     */
    public function index()
    {
        $orders = Order::query()
            ->with(['user', 'items'])
            ->latest()
            ->get();

        $counts = [
            'all' => $orders->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'processing' => $orders->where('status', 'processing')->count(),
            'shipped' => $orders->where('status', 'shipped')->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    /**
     * تحديث حالة الطلب.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,processing,shipped,delivered,cancelled',
            ],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }
}
