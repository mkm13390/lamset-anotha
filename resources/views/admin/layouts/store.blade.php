<!doctype html>
<html lang="{{ app()->getLocale() === 'en' ? 'en' : 'ar' }}"
      dir="{{ app()->getLocale() === 'en' ? 'ltr' : 'rtl' }}"
      data-storefront>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('storefront.brand.name_ar', 'لمسة أنوثة'))</title>
    <meta name="description" content="@yield('meta_description', 'متجر لمسة أنوثة للحقائب والأحذية والإكسسوارات')">
    @yield('meta')
    <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
    @stack('styles')
</head>
<body>
@php
    $layoutCart = session('cart', []);
    $layoutCartCount = collect($layoutCart)->sum(fn ($item) => (int) ($item['quantity'] ?? 0));
    $brandName = config('storefront.brand.name_ar', 'لمسة أنوثة');
    $whatsapp = config('storefront.brand.whatsapp', '96895426555');
    $instagram = config('storefront.brand.instagram', 'mkm13390');
    $businessName = config('storefront.legal.business_name');
    $commercialRegistration = config('storefront.legal.commercial_registration');
    $licenseNumber = config('storefront.legal.license_number');
@endphp

<div class="sf-announcement" data-announcement>
    <div class="sf-container sf-announcement__inner">
        <span data-i18n-ar="تسوقي بسهولة، ونتابع طلبك خطوة بخطوة"
              data-i18n-en="Shop easily and track your order step by step">
            تسوقي بسهولة، ونتابع طلبك خطوة بخطوة
        </span>
        <button class="sf-icon-button sf-announcement__close" type="button" data-close-announcement>×</button>
    </div>
</div>

<header class="sf-header">
    <div class="sf-container sf-header__main">
        <button class="sf-icon-button sf-header__menu" type="button" data-mobile-menu-open>☰</button>

        <a class="sf-logo" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.jpeg') }}" alt="{{ $brandName }}" width="154" height="56">
        </a>

        <form class="sf-search" action="{{ url('/products') }}" method="GET">
            <span class="sf-search__icon">⌕</span>
            <input name="q"
                   value="{{ request('q') }}"
                   autocomplete="off"
                   data-placeholder-ar="ابحثي عن شنطة، حذاء أو إكسسوار..."
                   data-placeholder-en="Search bags, shoes or accessories..."
                   placeholder="ابحثي عن شنطة، حذاء أو إكسسوار...">
            <button type="submit" data-i18n-ar="بحث" data-i18n-en="Search">بحث</button>
        </form>

        <div class="sf-header__actions">
            <button class="sf-action" type="button" data-theme-toggle><span data-theme-icon>☾</span></button>
            <button class="sf-action sf-language" type="button" data-language-toggle>EN</button>

            @auth
                <a class="sf-action" href="{{ route('account.index') }}" aria-label="حسابي">♡</a>
            @else
                <a class="sf-action" href="{{ route('login') }}" aria-label="تسجيل الدخول">♡</a>
            @endauth

            <button class="sf-action sf-cart-trigger" type="button" data-mini-cart-open>
                🛍
                <span class="sf-count" data-cart-count>{{ $layoutCartCount }}</span>
            </button>
        </div>
    </div>

    <nav class="sf-nav">
        <div class="sf-container sf-nav__inner">
            <a href="{{ url('/') }}" data-i18n-ar="الرئيسية" data-i18n-en="Home">الرئيسية</a>
            <a href="{{ route('products.index') }}" data-i18n-ar="المنتجات" data-i18n-en="Products">المنتجات</a>
            <a href="{{ url('/products?sort=newest') }}" data-i18n-ar="وصل حديثًا" data-i18n-en="New arrivals">وصل حديثًا</a>
            <a href="{{ url('/products?sort=best_selling') }}" data-i18n-ar="الأكثر طلبًا" data-i18n-en="Best sellers">الأكثر طلبًا</a>
            @auth
                <a href="{{ route('account.wishlist.index') }}" data-i18n-ar="المفضلة" data-i18n-en="Wishlist">المفضلة</a>
            @endauth
            <a href="{{ url('/#footer') }}" data-i18n-ar="تواصل معنا" data-i18n-en="Contact">تواصل معنا</a>
        </div>
    </nav>
</header>

<main class="sf-main">
    @if(session('success'))
        <div class="sf-container"><div class="sf-notice sf-notice--success">{{ session('success') }}</div></div>
    @endif

    @if($errors->any())
        <div class="sf-container">
            <div class="sf-notice sf-notice--error">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        </div>
    @endif

    @yield('content')
</main>

<footer id="footer" class="sf-footer">
    <div class="sf-container sf-footer__grid">
        <section>
            <a class="sf-logo sf-footer__logo" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.jpeg') }}" alt="{{ $brandName }}" width="150" height="54">
            </a>
            <p data-i18n-ar="حقائب، أحذية وإكسسوارات مختارة بعناية."
               data-i18n-en="Bags, shoes and accessories selected with care.">
                حقائب، أحذية وإكسسوارات مختارة بعناية.
            </p>

            @if($businessName || $commercialRegistration || $licenseNumber)
                <div class="sf-legal-info">
                    @if($businessName)
                        <span>{{ $businessName }}</span>
                    @endif
                    @if($commercialRegistration)
                        <span>
                            <b data-i18n-ar="السجل التجاري:" data-i18n-en="Commercial Registration:">السجل التجاري:</b>
                            {{ $commercialRegistration }}
                        </span>
                    @endif
                    @if($licenseNumber)
                        <span>
                            <b data-i18n-ar="رقم الترخيص:" data-i18n-en="License No.:">رقم الترخيص:</b>
                            {{ $licenseNumber }}
                        </span>
                    @endif
                </div>
            @endif
        </section>

        <section>
            <h3 data-i18n-ar="تسوقي" data-i18n-en="Shop">تسوقي</h3>
            <a href="{{ route('products.index') }}" data-i18n-ar="كل المنتجات" data-i18n-en="All products">كل المنتجات</a>
            <a href="{{ route('cart.index') }}" data-i18n-ar="السلة" data-i18n-en="Cart">السلة</a>
            @auth
                <a href="{{ route('account.index') }}" data-i18n-ar="حسابي" data-i18n-en="My account">حسابي</a>
            @endauth
        </section>

        <section>
            <h3 data-i18n-ar="خدمة العملاء" data-i18n-en="Customer care">خدمة العملاء</h3>
            @auth
                <a href="{{ route('account.returns.index') }}" data-i18n-ar="الإرجاع والاستبدال" data-i18n-en="Returns & exchanges">الإرجاع والاستبدال</a>
            @endauth
            <a href="{{ url('/#shipping') }}" data-i18n-ar="الشحن والتوصيل" data-i18n-en="Shipping & delivery">الشحن والتوصيل</a>
        </section>

        <section>
            <h3 data-i18n-ar="تواصلي معنا" data-i18n-en="Contact us">تواصلي معنا</h3>
            <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener">WhatsApp</a>
            <a href="https://www.instagram.com/{{ $instagram }}" target="_blank" rel="noopener">Instagram</a>
        </section>
    </div>

    <div class="sf-container sf-footer__bottom">
        <p data-i18n-ar="جميع الحقوق محفوظة © 2026 لمسة أنوثة"
           data-i18n-en="All rights reserved © 2026 Lamset Anotha">
            جميع الحقوق محفوظة © 2026 لمسة أنوثة
        </p>
        <span>OMR · 🇴🇲</span>
    </div>
</footer>

<a class="sf-whatsapp" href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" aria-label="WhatsApp">◉</a>

<div class="sf-drawer-overlay" data-mobile-menu-overlay hidden></div>
<aside class="sf-drawer sf-drawer--menu" data-mobile-menu hidden>
    <div class="sf-drawer__head">
        <strong>{{ $brandName }}</strong>
        <button class="sf-icon-button" type="button" data-mobile-menu-close>×</button>
    </div>
    <nav class="sf-drawer__links">
        <a href="{{ url('/') }}">الرئيسية</a>
        <a href="{{ route('products.index') }}">المنتجات</a>
        <a href="{{ route('cart.index') }}">السلة</a>
        @auth
            <a href="{{ route('account.index') }}">حسابي</a>
            <a href="{{ route('account.wishlist.index') }}">المفضلة</a>
            <a href="{{ route('account.returns.index') }}">المرتجعات</a>
        @else
            <a href="{{ route('login') }}">تسجيل الدخول</a>
        @endauth
    </nav>
</aside>

<div class="sf-drawer-overlay" data-mini-cart-overlay hidden></div>
<aside class="sf-drawer sf-drawer--cart" data-mini-cart hidden>
    <div class="sf-drawer__head">
        <div>
            <strong data-i18n-ar="سلة التسوق" data-i18n-en="Shopping cart">سلة التسوق</strong>
            <div class="sf-muted">
                <span data-cart-count>{{ $layoutCartCount }}</span>
                <span data-i18n-ar="منتج" data-i18n-en="items">منتج</span>
            </div>
        </div>
        <button class="sf-icon-button" type="button" data-mini-cart-close>×</button>
    </div>

    <div class="sf-mini-cart__body">
        @if($layoutCartCount > 0)
            <p data-i18n-ar="منتجاتك محفوظة في السلة."
               data-i18n-en="Your items are saved in the cart.">منتجاتك محفوظة في السلة.</p>
        @else
            <div class="sf-empty">
                <div class="sf-empty__icon">🛍</div>
                <strong data-i18n-ar="سلتك فارغة" data-i18n-en="Your cart is empty">سلتك فارغة</strong>
            </div>
        @endif
    </div>

    <div class="sf-mini-cart__footer">
        <a class="sf-button sf-button--secondary" href="{{ route('cart.index') }}"
           data-i18n-ar="عرض السلة" data-i18n-en="View cart">عرض السلة</a>

        @if($layoutCartCount > 0)
            <a class="sf-button sf-button--primary" href="{{ route('checkout.index') }}"
               data-i18n-ar="إتمام الطلب" data-i18n-en="Checkout">إتمام الطلب</a>
        @endif
    </div>
</aside>

<nav class="sf-mobile-bottom" aria-label="Mobile navigation">
    <a href="{{ url('/') }}"><span>⌂</span><small data-i18n-ar="الرئيسية" data-i18n-en="Home">الرئيسية</small></a>
    <a href="{{ route('products.index') }}"><span>⌕</span><small data-i18n-ar="تصفح" data-i18n-en="Browse">تصفح</small></a>
    @auth
        <a href="{{ route('account.wishlist.index') }}"><span>♡</span><small data-i18n-ar="المفضلة" data-i18n-en="Wishlist">المفضلة</small></a>
    @else
        <a href="{{ route('login') }}"><span>♡</span><small data-i18n-ar="دخول" data-i18n-en="Login">دخول</small></a>
    @endauth
    <button type="button" data-mini-cart-open>
        <span>🛍</span>
        <small data-i18n-ar="السلة" data-i18n-en="Cart">السلة</small>
        <b class="sf-mobile-count" data-cart-count>{{ $layoutCartCount }}</b>
    </button>
</nav>

<script>
window.StorefrontConfig = {
    language: @json(config('storefront.storefront.default_language', 'ar')),
    theme: @json(config('storefront.storefront.default_theme', 'light')),
    currency: @json(config('storefront.storefront.default_currency', 'OMR'))
};
</script>
<script src="{{ asset('js/storefront.js') }}"></script>
@stack('scripts')
</body>
</html>
