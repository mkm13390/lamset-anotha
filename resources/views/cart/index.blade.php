@extends('layouts.store')

@section('title', 'سلة التسوق | لمسة أنوثة')

@push('styles')
<style>
.cart-page{padding:34px 0 70px}
.cart-head{display:flex;justify-content:space-between;align-items:end;gap:16px;margin-bottom:18px}
.cart-head h1{margin:0;font-size:34px}.cart-head p{margin:5px 0 0;color:var(--sf-muted)}
.cart-layout{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(300px,.72fr);gap:22px}
.cart-panel,.summary{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:18px}
.cart-panel-head{padding:18px 20px;border-bottom:1px solid var(--sf-border);display:flex;justify-content:space-between;gap:12px}
.cart-items{padding:0 20px}
.cart-item{display:grid;grid-template-columns:96px 1fr auto;gap:15px;align-items:center;padding:18px 0;border-bottom:1px solid var(--sf-border)}
.cart-item:last-child{border-bottom:0}.photo{width:96px;height:108px;border-radius:12px;background:var(--sf-surface-2);overflow:hidden;display:grid;place-items:center}
.photo img{width:100%;height:100%;object-fit:cover}.item-name{font-size:16px;font-weight:800}.meta{font-size:12px;color:var(--sf-muted);line-height:1.8;margin:5px 0}
.price{font-weight:800}.item-actions{display:flex;flex-direction:column;align-items:end;gap:9px}.qty-form{display:flex;align-items:center;gap:7px}
.qty-form input{width:70px;height:40px;border:1px solid var(--sf-border);border-radius:9px;background:var(--sf-bg);color:var(--sf-text);text-align:center}
.small-btn{height:40px;padding:0 12px;border:1px solid var(--sf-border);border-radius:9px;background:var(--sf-surface);color:var(--sf-text);cursor:pointer}
.remove{border:0;background:transparent;color:#a23737;cursor:pointer;font-size:12px}
.summary{padding:20px;position:sticky;top:145px;height:max-content}.summary h2{margin:0 0 15px}.line{display:flex;justify-content:space-between;gap:10px;padding:10px 0;color:var(--sf-muted)}
.line strong{color:var(--sf-text)}.discount{color:#1f7a4d}.discount strong{color:#1f7a4d}.total{border-top:1px solid var(--sf-border);margin-top:8px;padding-top:15px;font-size:18px}
.total strong{font-size:22px}.coupon{display:grid;grid-template-columns:1fr auto;gap:7px;margin-top:15px}.coupon input{min-width:0;height:44px;border:1px solid var(--sf-border);border-radius:10px;background:var(--sf-bg);color:var(--sf-text);padding:0 12px}
.coupon button{border:0;border-radius:10px;background:var(--sf-primary);color:var(--sf-primary-text);padding:0 15px;cursor:pointer}.coupon-active{margin-top:12px;padding:11px;border:1px solid #b9ddca;border-radius:10px;color:#1f7a4d;display:flex;justify-content:space-between;gap:10px}
.cart-actions{display:grid;gap:9px;margin-top:16px}.cart-actions .sf-button{width:100%}.empty-cart{text-align:center;padding:65px 20px}.empty-cart .icon{font-size:60px}
@media(max-width:850px){.cart-layout{grid-template-columns:1fr}.summary{position:static}}
@media(max-width:620px){.cart-item{grid-template-columns:76px 1fr}.photo{width:76px;height:88px}.item-actions{grid-column:1/-1;align-items:stretch}.qty-form{justify-content:space-between}.cart-head{align-items:flex-start;flex-direction:column}}
</style>
@endpush

@section('content')
@php
    $cartItems = $cart ?? [];
    $cartSubtotal = (float)($subtotal ?? 0);
    $cartDiscount = (float)($discount ?? 0);
    $cartTotal = (float)($total ?? max($cartSubtotal - $cartDiscount, 0));
    $cartCount = collect($cartItems)->sum(fn($item) => (int)($item['quantity'] ?? 0));
@endphp

<section class="cart-page">
    <div class="sf-container">
        <div class="cart-head">
            <div>
                <h1 data-i18n-ar="سلة التسوق" data-i18n-en="Shopping cart">سلة التسوق</h1>
                <p>{{ $cartCount }} منتج في السلة</p>
            </div>
            <a class="sf-button sf-button--secondary" href="{{ route('products.index') }}">متابعة التسوق</a>
        </div>

        @if(count($cartItems))
            <div class="cart-layout">
                <section class="cart-panel">
                    <div class="cart-panel-head">
                        <strong>منتجاتك</strong>
                        <span class="sf-muted">{{ $cartCount }} قطعة</span>
                    </div>

                    <div class="cart-items">
                        @foreach($cartItems as $item)
                            @php
                                $quantity = (int)($item['quantity'] ?? 1);
                                $price = (float)($item['price'] ?? 0);
                                $lineTotal = $price * $quantity;
                                $imagePath = null;
                                if (!empty($item['image'])) {
                                    $imagePath = str_starts_with($item['image'], 'http')
                                        ? $item['image']
                                        : asset('storage/' . ltrim($item['image'], '/'));
                                }
                            @endphp

                            <article class="cart-item">
                                <div class="photo">
                                    @if($imagePath)
                                        <img src="{{ $imagePath }}" alt="{{ $item['name_ar'] ?? 'منتج' }}">
                                    @else
                                        👜
                                    @endif
                                </div>

                                <div>
                                    <div class="item-name">{{ $item['name_ar'] ?? 'منتج' }}</div>
                                    <div class="meta">
                                        @if(!empty($item['sku']))SKU: {{ $item['sku'] }}<br>@endif
                                        @if(!empty($item['color_name_ar']))اللون: {{ $item['color_name_ar'] }}<br>@endif
                                        @if(!empty($item['size']))المقاس: {{ $item['size'] }}@endif
                                    </div>
                                    <div class="price">{{ number_format($lineTotal,3) }} OMR</div>
                                </div>

                                <div class="item-actions">
                                    <form class="qty-form" method="POST" action="{{ route('cart.update', $item['product_variant_id']) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number"
                                               name="quantity"
                                               min="1"
                                               max="{{ (int)($item['stock_quantity'] ?? 999) }}"
                                               value="{{ $quantity }}"
                                               required>
                                        <button class="small-btn" type="submit">تحديث</button>
                                    </form>

                                    <form method="POST" action="{{ route('cart.remove', $item['product_variant_id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="remove" type="submit">حذف من السلة</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <aside class="summary">
                    <h2>ملخص الطلب</h2>

                    <div class="line">
                        <span>المجموع الفرعي</span>
                        <strong>{{ number_format($cartSubtotal,3) }} OMR</strong>
                    </div>

                    @if($cartDiscount > 0)
                        <div class="line discount">
                            <span>الخصم</span>
                            <strong>-{{ number_format($cartDiscount,3) }} OMR</strong>
                        </div>
                    @endif

                    <div class="line">
                        <span>الشحن</span>
                        <strong>يُحسب عند إتمام الطلب</strong>
                    </div>

                    <div class="line total">
                        <span>الإجمالي الحالي</span>
                        <strong>{{ number_format($cartTotal,3) }} OMR</strong>
                    </div>

                    @if($coupon)
                        <div class="coupon-active">
                            <span>الكوبون: <strong>{{ $coupon->code }}</strong></span>
                            <form method="POST" action="{{ route('cart.coupon.remove') }}">
                                @csrf
                                @method('DELETE')
                                <button class="remove" type="submit">إزالة</button>
                            </form>
                        </div>
                    @else
                        <form class="coupon" method="POST" action="{{ route('cart.coupon.apply') }}">
                            @csrf
                            <input name="coupon_code" placeholder="رمز الخصم" required>
                            <button type="submit">تطبيق</button>
                        </form>
                    @endif

                    <div class="cart-actions">
                        <a class="sf-button sf-button--primary" href="{{ route('checkout.index') }}">إتمام الطلب</a>

                        <form method="POST" action="{{ route('cart.clear') }}">
                            @csrf
                            @method('DELETE')
                            <button class="sf-button sf-button--secondary" type="submit">إفراغ السلة</button>
                        </form>
                    </div>

                    <div class="sf-muted" style="text-align:center;margin-top:12px">🔒 طلبك وبياناتك محمية داخل النظام</div>
                </aside>
            </div>
        @else
            <div class="cart-panel empty-cart">
                <div class="icon">🛍</div>
                <h2>سلتك فارغة حاليًا</h2>
                <p class="sf-muted">تصفحي المنتجات وأضيفي القطع التي أعجبتك.</p>
                <a class="sf-button sf-button--primary" href="{{ route('products.index') }}">تصفحي المنتجات</a>
            </div>
        @endif
    </div>
</section>
@endsection
