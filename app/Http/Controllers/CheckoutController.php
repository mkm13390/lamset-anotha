<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /**
     * عرض صفحة إتمام الطلب مع بيانات السلة الحالية.
     */
    public function index(Request $request)
    {
        $cartItems = $request->session()->get('cart', []);

        $subtotal = collect($cartItems)->sum(function ($item) {
            $price = (float) ($item['price'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            return $price * $quantity;
        });

        $coupon = null;
        $discountAmount = 0;

        $couponCode = $request->session()->get('coupon_code');

        if ($couponCode) {
            $coupon = Coupon::query()
                ->where('code', strtoupper(trim($couponCode)))
                ->first();

            if (
                !$coupon
                || !$coupon->isCurrentlyValid()
                || !$coupon->meetsMinimumOrder((float) $subtotal)
            ) {
                $request->session()->forget('coupon_code');
                $coupon = null;
            } else {
                $discountAmount = $coupon->calculateDiscount((float) $subtotal);
            }
        }

        $shippingFee = 0;
        $total = max(
            (float) $subtotal + $shippingFee - $discountAmount,
            0
        );

        return view('checkout.index', compact(
            'cartItems',
            'subtotal',
            'coupon',
            'discountAmount',
            'shippingFee',
            'total'
        ));
    }

    /**
     * حفظ الطلب الحقيقي.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'governorate' => ['required', 'string', 'max:100'],
            'wilayat' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'delivery' => ['required', 'in:standard,pickup'],
            'payment' => ['required', 'in:cod,transfer'],
        ]);

        $cart = $request->session()->get('cart', []);
        $couponCode = $request->session()->get('coupon_code');

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->withErrors([
                    'cart' => 'السلة فارغة. أضف منتجًا قبل إتمام الطلب.',
                ]);
        }

        $order = DB::transaction(function () use (
            $validated,
            $cart,
            $couponCode
        ) {
            $preparedItems = [];
            $subtotal = 0;

            foreach ($cart as $item) {
                $variantId = (int) ($item['product_variant_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);

                if ($variantId <= 0 || $quantity <= 0) {
                    throw ValidationException::withMessages([
                        'cart' => 'توجد بيانات غير صحيحة في السلة.',
                    ]);
                }

                $variant = ProductVariant::with('product')
                    ->lockForUpdate()
                    ->find($variantId);

                if (!$variant || !$variant->product) {
                    throw ValidationException::withMessages([
                        'cart' => 'أحد المنتجات لم يعد متاحًا.',
                    ]);
                }

                if (!$variant->is_active || !$variant->product->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => 'أحد المنتجات لم يعد متاحًا للبيع.',
                    ]);
                }

                $stockBefore = (int) ($variant->stock_quantity ?? 0);

                if ($quantity > $stockBefore) {
                    throw ValidationException::withMessages([
                        'cart' => 'الكمية المطلوبة لأحد المنتجات أكبر من الكمية المتوفرة.',
                    ]);
                }

                $unitPrice = $variant->price !== null
                    ? (float) $variant->price
                    : (float) ($variant->product->price ?? 0);

                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'stock_before' => $stockBefore,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'cart_item' => $item,
                ];
            }

            $subtotal = round($subtotal, 3);

            $shippingFee = 0;
            $discountAmount = 0;
            $appliedCouponCode = null;

            /*
             |--------------------------------------------------------------
             | إعادة التحقق من الكوبون من قاعدة البيانات
             |--------------------------------------------------------------
             | لا نعتمد على قيمة الخصم الموجودة في الواجهة أو Session.
             | يتم حساب الخصم هنا من جديد اعتمادًا على الأسعار الحقيقية
             | الموجودة في قاعدة البيانات وقت إنشاء الطلب.
             */
            if ($couponCode) {
                $coupon = Coupon::query()
                    ->where('code', strtoupper(trim($couponCode)))
                    ->lockForUpdate()
                    ->first();

                if (!$coupon) {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'رمز الكوبون لم يعد موجودًا.',
                    ]);
                }

                if (!$coupon->isCurrentlyValid()) {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'هذا الكوبون غير فعال أو انتهت صلاحيته.',
                    ]);
                }

                if (!$coupon->meetsMinimumOrder($subtotal)) {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'الحد الأدنى لاستخدام هذا الكوبون هو '
                            . number_format(
                                (float) $coupon->minimum_order_amount,
                                3
                            )
                            . ' ر.ع.',
                    ]);
                }

                $discountAmount = $coupon->calculateDiscount($subtotal);
                $appliedCouponCode = $coupon->code;
            }

            $total = max(
                $subtotal + $shippingFee - $discountAmount,
                0
            );

            $total = round($total, 3);

            do {
                $orderNumber = 'LA-'
                    . now()->format('YmdHis')
                    . '-'
                    . random_int(1000, 9999);
            } while (
                Order::where('order_number', $orderNumber)->exists()
            );

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'governorate' => $validated['governorate'],
                'wilayat' => $validated['wilayat'],
                'address' => $validated['address'],
                'notes' => $validated['notes'] ?? null,
                'delivery_method' => $validated['delivery'],
                'shipping_fee' => $shippingFee,
                'payment_method' => $validated['payment'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'currency' => 'OMR',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'coupon_code' => $appliedCouponCode,
                'total' => $total,
            ]);

            foreach ($preparedItems as $prepared) {
                $variant = $prepared['variant'];
                $product = $variant->product;
                $quantity = $prepared['quantity'];
                $stockBefore = $prepared['stock_before'];
                $stockAfter = $stockBefore - $quantity;
                $cartItem = $prepared['cart_item'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'product_name_ar' => $product->name_ar,
                    'product_name_en' => $product->name_en ?? null,
                    'sku' => $variant->sku ?? $product->sku,
                    'color_name_ar' => $variant->color_name_ar ?? null,
                    'color_name_en' => $variant->color_name_en ?? null,
                    'size' => $variant->size ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $prepared['unit_price'],
                    'line_total' => $prepared['line_total'],
                    'image' => $cartItem['image'] ?? null,
                ]);

                $variant->stock_quantity = $stockAfter;
                $variant->save();

                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'type' => 'stock_out',
                    'quantity' => $quantity,
                    'quantity_before' => $stockBefore,
                    'quantity_after' => $stockAfter,
                    'unit_cost' => $product->cost_price ?? null,
                    'reference' => $order->order_number,
                    'notes' => 'خصم مخزون بسبب طلب متجر',
                    'user_id' => auth()->id(),
                ]);
            }

            return $order;
        });

        $request->session()->forget([
            'cart',
            'coupon_code',
        ]);

        return redirect()
            ->route('orders.success')
            ->with('order_number', $order->order_number)
            ->with('order_id', $order->id);
    }
}
