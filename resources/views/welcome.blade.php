@extends('layouts.store')

@section('title', 'لمسة أنوثة | حقائب وأحذية وإكسسوارات')
@section('meta_description', 'تسوقي أحدث الحقائب والأحذية والإكسسوارات من لمسة أنوثة.')

@push('styles')
<style>
.home-hero{padding:52px 0 30px}
.hero-box{min-height:430px;border-radius:28px;background:linear-gradient(135deg,#efe8e1,#f8f5f1);display:grid;grid-template-columns:1.05fr .95fr;overflow:hidden;border:1px solid var(--sf-border)}
html[data-theme="dark"] .hero-box{background:linear-gradient(135deg,#25211f,#1d1d1d)}
.hero-copy{padding:58px;display:flex;flex-direction:column;justify-content:center}
.hero-kicker{font-size:13px;color:var(--sf-muted);margin-bottom:10px}
.hero-copy h1{font-size:clamp(34px,5vw,68px);line-height:1.12;margin:0 0 18px}
.hero-copy p{max-width:560px;color:var(--sf-muted);font-size:16px;margin:0 0 26px}
.hero-actions{display:flex;gap:10px;flex-wrap:wrap}
.hero-visual{display:grid;place-items:center;min-height:360px;background:radial-gradient(circle at center,rgba(255,255,255,.65),transparent 60%)}
.hero-visual span{font-size:130px;filter:drop-shadow(0 15px 20px rgba(0,0,0,.08))}
.section{padding:34px 0}
.section-head{display:flex;justify-content:space-between;align-items:end;gap:18px;margin-bottom:18px}
.section-head h2{margin:0;font-size:28px}.section-head p{margin:4px 0 0;color:var(--sf-muted)}
.category-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
.category-card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:18px;padding:22px 14px;text-align:center;min-height:118px;display:flex;flex-direction:column;justify-content:center;gap:8px}
.category-card span{font-size:30px}.category-card small{color:var(--sf-muted)}
.category-card--wide{min-height:260px;padding:34px 22px;gap:12px}
.category-card--wide span{font-size:74px}
.category-card--wide strong{font-size:24px}
.category-card--wide em{font-style:normal;margin-top:6px;align-self:center;padding:11px 26px;border-radius:14px;background:var(--sf-primary);color:var(--sf-primary-text);font-weight:700}
.product-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.product-card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:18px;overflow:hidden}
.product-image{aspect-ratio:1/1;background:var(--sf-surface-2);display:grid;place-items:center;overflow:hidden}
.product-image img{width:100%;height:100%;object-fit:cover}.product-image span{font-size:58px}
.product-body{padding:14px}.product-body h3{font-size:15px;margin:0 0 4px}.product-body .en{font-size:12px;color:var(--sf-muted)}
.product-row{margin-top:12px;display:flex;align-items:center;justify-content:space-between;gap:10px}
.price{font-weight:800}.stock{font-size:11px;color:var(--sf-muted)}
.value-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.value-card{padding:22px;border:1px solid var(--sf-border);background:var(--sf-surface);border-radius:18px}.value-card strong{display:block;margin-bottom:5px}.value-card p{color:var(--sf-muted);margin:0;font-size:13px}
.gift-box{padding:34px;border-radius:24px;background:var(--sf-primary);color:var(--sf-primary-text);display:grid;grid-template-columns:1fr auto;align-items:center;gap:20px}
.gift-box h2{margin:0 0 7px}.gift-box p{margin:0;opacity:.78}.gift-box .sf-button{background:var(--sf-primary-text);color:var(--sf-primary)}
@media(max-width:980px){.hero-box{grid-template-columns:1fr}.hero-visual{min-height:220px}.product-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:620px){.home-hero{padding-top:20px}.hero-copy{padding:30px 22px}.hero-visual{display:none}.category-grid{grid-template-columns:1fr}.category-card--wide{min-height:190px;padding:22px 14px;gap:8px}.category-card--wide span{font-size:52px}.category-card--wide strong{font-size:18px}.category-card--wide em{padding:9px 18px;font-size:13px}.product-grid{grid-template-columns:repeat(2,1fr);gap:10px}.value-grid{grid-template-columns:1fr}.gift-box{grid-template-columns:1fr}.section-head{align-items:flex-start;flex-direction:column}.product-body{padding:10px}}
</style>
@endpush

@section('content')
<section class="home-hero">
    <div class="sf-container">
        <div class="hero-box">
            <div class="hero-copy">
                <div class="hero-kicker" data-i18n-ar="مختارات لمسة أنوثة" data-i18n-en="Lamset Anotha Edit">
                    مختارات لمسة أنوثة
                </div>
                <h1 data-i18n-ar="تفاصيل صغيرة تغيّر الإطلالة كاملة" data-i18n-en="Small details, complete the look">
                    تفاصيل صغيرة تغيّر الإطلالة كاملة
                </h1>
                <p data-i18n-ar="حقائب، أحذية وإكسسوارات مختارة لتسهّل عليكِ العثور على القطعة المناسبة."
                   data-i18n-en="Bags, shoes and accessories selected to make finding the right piece easier.">
                    حقائب، أحذية وإكسسوارات مختارة لتسهّل عليكِ العثور على القطعة المناسبة.
                </p>
                <div class="hero-actions">
                    <a class="sf-button sf-button--primary" href="{{ route('products.index') }}"
                       data-i18n-ar="تسوقي الآن" data-i18n-en="Shop now">تسوقي الآن</a>
                    <a class="sf-button sf-button--secondary" href="{{ route('products.index', ['sort' => 'newest']) }}"
                       data-i18n-ar="وصل حديثًا" data-i18n-en="New arrivals">وصل حديثًا</a>
                </div>
            </div>
            <div class="hero-visual"><span>👜</span></div>
        </div>
    </div>
</section>

@if($categories->isNotEmpty())
<section class="section">
    <div class="sf-container">
        <div class="category-grid">
            @foreach($categories as $category)
                <a class="category-card category-card--wide" href="{{ route('products.index', ['category' => $category->id]) }}">
                    <span>{{ $category->icon ?? '◇' }}</span>
                    <strong>{{ $category->name_ar ?? 'قسم' }}</strong>
                    @if(!empty($category->name_en))
                        <small>{{ $category->name_en }}</small>
                    @endif
                    <em data-i18n-ar="تسوقي الآن" data-i18n-en="Shop now">تسوقي الآن</em>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section">
    <div class="sf-container">
        <div class="section-head">
            <div>
                <h2 data-i18n-ar="وصل حديثًا" data-i18n-en="New arrivals">وصل حديثًا</h2>
                <p data-i18n-ar="أحدث المنتجات المضافة للمتجر." data-i18n-en="The newest products added to the store.">
                    أحدث المنتجات المضافة للمتجر.
                </p>
            </div>
            <a class="sf-button sf-button--secondary" href="{{ route('products.index') }}"
               data-i18n-ar="عرض الكل" data-i18n-en="View all">عرض الكل</a>
        </div>

        <div class="product-grid">
            @forelse($featuredProducts as $product)
                @php
                    $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                    $image = $primary?->image;
                    $imageUrl = $image ? (str_starts_with($image, 'http') ? $image : asset('storage/' . ltrim($image, '/'))) : null;
                    $variant = $product->variants->where('is_active', true)->first() ?? $product->variants->first();
                    $price = $variant?->price !== null ? (float)$variant->price : (float)($product->price ?? 0);
                    $stock = (int)$product->variants->where('is_active', true)->sum('stock_quantity');
                @endphp
                <article class="product-card">
                    <a href="{{ route('products.show', $product) }}" class="product-image">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->name_ar ?? 'منتج' }}" loading="lazy">
                        @else
                            <span>👜</span>
                        @endif
                    </a>
                    <div class="product-body">
                        <a href="{{ route('products.show', $product) }}">
                            <h3>{{ $product->name_ar ?? 'منتج' }}</h3>
                            @if($product->name_en)<div class="en">{{ $product->name_en }}</div>@endif
                        </a>
                        <div class="product-row">
                            <span class="price">{{ number_format($price, 3) }} OMR</span>
                            <span class="stock">{{ $stock > 0 ? 'متوفر' : 'غير متوفر' }}</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="sf-empty" style="grid-column:1/-1">
                    <div class="sf-empty__icon">👜</div>
                    <strong>سيتم عرض المنتجات هنا بعد إضافتها.</strong>
                </div>
            @endforelse
        </div>
    </div>
</section>

<section class="section">
    <div class="sf-container">
        <div class="value-grid">
            <div class="value-card"><strong>تسوق آمن</strong><p>بيانات الطلب والحساب محفوظة داخل النظام.</p></div>
            <div class="value-card"><strong>دعم واتساب</strong><p>يمكنك التواصل معنا بسرعة عند الحاجة.</p></div>
            <div class="value-card"><strong>إرجاع واستبدال</strong><p>طلبات الإرجاع والاستبدال مرتبطة بحساب العميل.</p></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="sf-container">
        <div class="gift-box">
            <div>
                <h2 data-i18n-ar="هدية بلا حيرة 🎁" data-i18n-en="A gift without the guesswork 🎁">هدية بلا حيرة 🎁</h2>
                <p data-i18n-ar="بطاقات الهدايا تبدأ من 5 ر.ع، مع إمكانية اختيار مبلغ مخصص."
                   data-i18n-en="Gift cards start from OMR 5, with a custom amount option.">
                    بطاقات الهدايا تبدأ من 5 ر.ع، مع إمكانية اختيار مبلغ مخصص.
                </p>
            </div>
            <a class="sf-button" href="{{ route('products.index') }}">استكشفي المتجر</a>
        </div>
    </div>
</section>
@endsection
