<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * عرض فاتورة الطلب اعتمادًا على البيانات المحفوظة داخل الطلب.
     */
    public function show(Request $request, Order $order)
    {
        /*
         |--------------------------------------------------------------
         | حماية الوصول إلى الفاتورة
         |--------------------------------------------------------------
         | - إذا كان الطلب تابعًا لمستخدم مسجل، يجب أن يكون هو صاحب الطلب.
         | - إذا كان الطلب Guest، نسمح بالوصول بعد إنشاء الطلب مباشرة
         |   اعتمادًا على order_id المحفوظ في Session.
         */
        if ($order->user_id) {
            if (
                !$request->user()
                || (int) $request->user()->id !== (int) $order->user_id
            ) {
                abort(403);
            }
        } else {
            $sessionOrderId = (int) $request->session()->get('order_id', 0);

            if ($sessionOrderId !== (int) $order->id) {
                abort(403);
            }
        }

        $order->load([
            'items',
            'user',
        ]);

        $subtotal = (float) ($order->subtotal ?? 0);
        $discountAmount = (float) ($order->discount_amount ?? 0);
        $shippingFee = (float) ($order->shipping_fee ?? 0);
        $total = (float) ($order->total ?? 0);

        return view('invoices.show', compact(
            'order',
            'subtotal',
            'discountAmount',
            'shippingFee',
            'total'
        ));
    }
}
