<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $subtotal = $this->calculateSubtotal($cart);

        $coupon = null;
        $discount = 0;

        $couponCode = session()->get('coupon_code');

        if ($couponCode) {
            $coupon = Coupon::query()
                ->where('code', strtoupper(trim($couponCode)))
                ->first();

            if (
                !$coupon
                || !$coupon->isCurrentlyValid()
                || !$coupon->meetsMinimumOrder($subtotal)
            ) {
                session()->forget('coupon_code');
                $coupon = null;
            } else {
                $discount = $coupon->calculateDiscount($subtotal);
            }
        }

        $total = max($subtotal - $discount, 0);

        return view('cart.index', compact(
            'cart',
            'subtotal',
            'coupon',
            'discount',
            'total'
        ));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],
            'quantity' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'redirect_to_checkout' => [
                'nullable',
                'boolean',
            ],
        ]);

        $quantity = (int) ($validated['quantity'] ?? 1);

        $variant = ProductVariant::with([
            'product.images',
            'product.category',
        ])->findOrFail($validated['product_variant_id']);

        if (!$variant->product || !$variant->product->is_active) {
            return back()->withErrors([
                'cart' => 'هذا المنتج غير متاح حاليًا.',
            ]);
        }

        if (!$variant->is_active) {
            return back()->withErrors([
                'cart' => 'هذا الخيار غير متاح حاليًا.',
            ]);
        }

        $availableStock = (int) ($variant->stock_quantity ?? 0);

        if ($availableStock <= 0) {
            return back()->withErrors([
                'cart' => 'هذا الخيار نافد من المخزون.',
            ]);
        }

        $cart = session()->get('cart', []);

        $key = (string) $variant->id;

        $currentQuantity = isset($cart[$key])
            ? (int) ($cart[$key]['quantity'] ?? 0)
            : 0;

        $newQuantity = $currentQuantity + $quantity;

        if ($newQuantity > $availableStock) {
            return back()->withErrors([
                'cart' => 'الكمية المطلوبة أكبر من المخزون المتاح.',
            ]);
        }

        $image = $variant->image;

        if (!$image) {
            $primaryImage = $variant->product->images
                ->firstWhere('is_primary', true)
                ?? $variant->product->images->first();

            $image = $primaryImage?->image;
        }

        $price = $variant->price !== null
            ? (float) $variant->price
            : (float) ($variant->product->price ?? 0);

        $cart[$key] = [
            'product_id' => $variant->product_id,
            'product_variant_id' => $variant->id,
            'name_ar' => $variant->product->name_ar ?? 'منتج',
            'name_en' => $variant->product->name_en ?? '',
            'sku' => $variant->sku,
            'color_name_ar' => $variant->color_name_ar,
            'color_name_en' => $variant->color_name_en,
            'size' => $variant->size,
            'price' => $price,
            'quantity' => $newQuantity,
            'stock_quantity' => $availableStock,
            'image' => $image,
        ];

        session()->put('cart', $cart);

        if ($request->boolean('redirect_to_checkout')) {
            return redirect()
                ->route('checkout.index')
                ->with('success', 'تمت إضافة المنتج، ويمكنك الآن إتمام الطلب.');
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'تمت إضافة المنتج إلى السلة.');
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $quantity = (int) $validated['quantity'];

        $availableStock = (int) ($variant->stock_quantity ?? 0);

        if ($quantity > $availableStock) {
            return back()->withErrors([
                'cart' => 'الكمية المطلوبة أكبر من المخزون المتاح.',
            ]);
        }

        $cart = session()->get('cart', []);

        $key = (string) $variant->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $quantity;
            $cart[$key]['stock_quantity'] = $availableStock;

            session()->put('cart', $cart);
        }

        return back()->with('success', 'تم تحديث السلة.');
    }

    public function remove(ProductVariant $variant)
    {
        $cart = session()->get('cart', []);

        $key = (string) $variant->id;

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'تم حذف المنتج من السلة.');
    }

    public function clear()
    {
        session()->forget([
            'cart',
            'coupon_code',
        ]);

        return back()->with('success', 'تم إفراغ السلة.');
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => [
                'required',
                'string',
                'max:50',
            ],
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return back()->withErrors([
                'coupon_code' => 'لا يمكن تطبيق كوبون على سلة فارغة.',
            ]);
        }

        $subtotal = $this->calculateSubtotal($cart);

        $code = strtoupper(trim($validated['coupon_code']));

        $coupon = Coupon::query()
            ->where('code', $code)
            ->first();

        if (!$coupon) {
            return back()->withErrors([
                'coupon_code' => 'رمز الكوبون غير صحيح.',
            ]);
        }

        if (!$coupon->isCurrentlyValid()) {
            return back()->withErrors([
                'coupon_code' => 'هذا الكوبون غير فعال أو انتهت صلاحيته.',
            ]);
        }

        if (!$coupon->meetsMinimumOrder($subtotal)) {
            return back()->withErrors([
                'coupon_code' => 'الحد الأدنى لاستخدام هذا الكوبون هو '
                    . number_format(
                        (float) $coupon->minimum_order_amount,
                        3
                    )
                    . ' ر.ع.',
            ]);
        }

        session()->put('coupon_code', $coupon->code);

        return back()->with(
            'success',
            'تم تطبيق كوبون الخصم بنجاح.'
        );
    }

    public function removeCoupon()
    {
        session()->forget('coupon_code');

        return back()->with(
            'success',
            'تمت إزالة كوبون الخصم.'
        );
    }

    private function calculateSubtotal(array $cart): float
    {
        return round(
            collect($cart)->sum(function ($item) {
                return ((float) ($item['price'] ?? 0))
                    * ((int) ($item['quantity'] ?? 0));
            }),
            3
        );
    }
}
