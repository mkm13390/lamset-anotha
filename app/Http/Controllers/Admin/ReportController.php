<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * عرض التقرير العام للطلبات والمبيعات.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $orders = $query
            ->latest()
            ->get();

        $summary = [
            'orders_count' => $orders->count(),
            'subtotal' => round((float) $orders->sum('subtotal'), 3),
            'discounts' => round((float) $orders->sum('discount_amount'), 3),
            'shipping' => round((float) $orders->sum('shipping_fee'), 3),
            'total_sales' => round((float) $orders->sum('total'), 3),

            'pending' => $orders->where('status', 'pending')->count(),
            'processing' => $orders->where('status', 'processing')->count(),
            'shipped' => $orders->where('status', 'shipped')->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),

            'payment_pending' => $orders
                ->where('payment_status', 'pending')
                ->count(),

            'payment_paid' => $orders
                ->where('payment_status', 'paid')
                ->count(),
        ];

        return view('admin.reports.index', compact(
            'orders',
            'summary'
        ));
    }
}
