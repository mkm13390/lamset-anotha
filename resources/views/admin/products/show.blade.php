@extends('layouts.store')

@section('title', ($product->name_ar ?? 'منتج') . ' | لمسة أنوثة')
@section('meta_description', $product->description_ar ?? $product->name_ar ?? 'منتج من لمسة أنوثة')

@push('styles')
<style>
.product-page{padding:30px 0 60px}
.crumb{font-size:12px;color:var(--sf-muted);margin-bottom:16px}.crumb a:hover{color:var(--sf-text)}
.product-layout{display:grid;grid-template-columns:1.05fr .95fr;gap:34px}
.gallery{display:grid;grid-template-columns:90px 1fr;gap:12px}
.thumbs{display:flex;flex-direction:column;gap:8px}.thumb{border:1px solid var(--sf-border);border-radius:12px;overflow:hidden;background:var(--sf-surface);padding:0;cursor:pointer;aspect-ratio:1}
.thumb img{width:100%;height:100%;object-fit:cover}.main-image{aspect-ratio:1/1;background:var(--sf-surface-2);border:1px solid var(--sf-border);border-radius:20px;overflow:hidden;display:grid;place-items:center}
.main-image img{width:100%;height:100%;object-fit:cover}.fallback{font-size:90px}
.details{padding:8px 0}.category{font-size:12px;color:var(--sf-muted)}.details h1{font-size:34px;line-height:1.25;margin:6px 0}.name-en{color:var(--sf-muted);font-size:14px}
.product-price{font-size:25px;font-weight:900;margin:18px 0}.availability{font-size:13px;margin-bottom:18px}.ok{color:#1f7a4d}.no{color:#a23737}
.description{color:var(--sf-muted);font-size:14px;line-height:1.9;padding:16px 0;border-top:1px solid var(--sf-border)}
.purchase-box{margin-top:16px;padding:16px;border:1px solid var(--sf-border);border-radius:16px;background:var(--sf-surface)}
.field{margin-bottom:12px}.field label{display:block;font-size:12px;font-weight:700;margin-bottom:6px}.field select,.field input{width:100%;min-height:44px;border:1px solid var(--sf-border);border-radius:10px;background:var(--sf-bg);color:var(--sf-text);padding:0 12px}
.qty-row{display:grid;grid-template-columns:120px 1fr;gap:10px}.purchase-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px}
.secondary-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}.secondary-actions form{margin:0}
.info-strip{margin-top:18px;display:grid;grid-template-columns:repeat(3,1fr);gap:8px}.info-item{padding:12px;border:1px solid var(--sf-border);border-radius:12px;font-size:12px;color:var(--sf-muted);text-align:center}
.related{margin-top:54px}.related h2{margin-bottom:16px}.related-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.related-card{border:1px solid var(--sf-border);border-radius:16px;overflow:hidden;background:var(--sf-surface)}.related-card .img{aspect-ratio:1;background:var(--sf-surface-2);display:grid;place-items:center}.related-card img{width:100%;height:100%;object-fit:cover}.related-card .body{padding:12px}.related-card h3{font-size:14px;margin:0 0 8px}
.mobile-buy{display:none}
@media(max-width:900px){.product-layout{grid-template-columns:1fr}.related-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:620px){.gallery{grid-template-columns:1fr}.thumbs{order:2;flex-direction:row;overflow-x:auto}.thumb{min-width:70px;width:70px}.details h1{font-size:27px}.purchase-actions{grid-template-columns:1fr}.info-strip{grid-template-columns:1fr}.mobile-buy{position:fixed;display:block;left:0;right:0;bottom:66px;z-index:68;background:var(--sf-surface);border-top:1px solid var(--sf-border);padding:9px 12px}.mobile-buy .sf-button{width:100%}.product-page{padding-bottom:110px}}
</style>
@endpush

@section('content')
@php
    $images = $product->images;
    $primary = $images->firstWhere('is_primary', true) ?? $images->first();
    $mainRaw = $primary?->image;
    $mainUrl = $mainRaw ? (str_starts_with($mainRaw, 'http') ? $mainRaw : asset('storage/' . ltrim($mainRaw, '/'))) : null;

    $variants = $product->variants->where('is_active', true)->values();
    $defaultVariant = $variants->first();
    $basePrice = $defaultVariant?->price !== null ? (float)$defaultVariant->price : (float)($product->price ?? 0);
    $totalStock = (int)$variants->sum('stock_quantity');
@endphp

<section class="product-page">
    <div class="sf-container">
        <div class="crumb">
            <a href="{{ url('/') }}">الرئيسية</a> /
            <a href="{{ route('products.index') }}">المنتجات</a> /
            <span>{{ $product->name_ar ?? 'منتج' }}</span>
        </div>

        <div class="product-layout">
            <div class="gallery">
                <div class="thumbs">
                    @foreach($images as $image)
                        @php
                            $raw = $image->image;
                            $url = $raw ? (str_starts_with($raw, 'http') ? $raw : asset('storage/' . ltrim($raw, '/'))) : null;
                        @endphp
                        @if($url)
                            <button class="thumb" type="button" data-gallery-image="{{ $url }}">
                                <img src="{{ $url }}" alt="{{ $product->name_ar ?? 'منتج' }}">
                            </button>
                        @endif
                    @endforeach
                </div>

                <div class="main-image">
                    @if($mainUrl)
                        <img id="mainProductImage" src="{{ $mainUrl }}" alt="{{ $product->name_ar ?? 'منتج' }}">
                    @else
                        <span class="fallback">👜</span>
                    @endif
                </div>
            </div>

            <div class="details">
                @if($product->category)
                    <div class="category">{{ $product->category->name_ar ?? '' }}</div>
                @endif

                <h1>{{ $product->name_ar ?? 'منتج' }}</h1>
                @if($product->name_en)<div class="name-en">{{ $product->name_en }}</div>@endif

                <div class="product-price" id="displayPrice">{{ number_format($basePrice,3) }} OMR</div>

                <div class="availability {{ $totalStock > 0 ? 'ok' : 'no' }}">
                    {{ $totalStock > 0 ? 'متوفر في المخزون' : 'غير متوفر حاليًا' }}
                </div>

                @if(!empty($product->description_ar))
                    <div class="description">{{ $product->description_ar }}</div>
                @elseif(!empty($product->description_en))
                    <div class="description">{{ $product->description_en }}</div>
                @endif

                <div class="purchase-box">
                    @if($variants->isNotEmpty())
                        <form method="post" action="{{ route('cart.add') }}" id="addToCartForm">
                            @csrf

                            <div class="field">
                                <label>اختاري اللون / المقاس</label>
                                <select name="product_variant_id" id="variantSelect" required>
                                    @foreach($variants as $variant)
                                        @php
                                            $vPrice = $variant->price !== null ? (float)$variant->price : (float)($product->price ?? 0);
                                            $vStock = (int)($variant->stock_quantity ?? 0);
                                            $label = collect([
                                                $variant->color_name_ar,
                                                $variant->size ? 'مقاس ' . $variant->size : null,
                                                $variant->sku ? 'SKU ' . $variant->sku : null,
                                            ])->filter()->implode(' · ');
                                        @endphp
                                        <option value="{{ $variant->id }}"
                                                data-price="{{ number_format($vPrice,3,'.','') }}"
                                                data-stock="{{ $vStock }}"
                                                @disabled($vStock <= 0)>
                                            {{ $label ?: 'الخيار ' . $variant->id }}
                                            {{ $vStock <= 0 ? ' - نفد' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="qty-row">
                                <div class="field">
                                    <label>الكمية</label>
                                    <input type="number" name="quantity" value="1" min="1" id="quantityInput">
                                </div>
                                <div class="field">
                                    <label>المخزون المتاح</label>
                                    <input type="text" id="stockDisplay" value="{{ (int)($defaultVariant?->stock_quantity ?? 0) }}" readonly>
                                </div>
                            </div>

                            <div class="purchase-actions">
                                <button class="sf-button sf-button--primary" type="submit" @disabled($totalStock <= 0)>
                                    أضيفي للسلة
                                </button>
                                <button class="sf-button sf-button--secondary" type="submit" name="buy_now" value="1" @disabled($totalStock <= 0)>
                                    شراء الآن
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="sf-empty">
                            <strong>لا توجد خيارات متاحة لهذا المنتج حاليًا.</strong>
                        </div>
                    @endif

                    <div class="secondary-actions">
                        @auth
                            <form method="post" action="{{ route('account.wishlist.toggle', $product) }}">
                                @csrf
                                <button class="sf-button sf-button--secondary" type="submit">♡ أضيفي للمفضلة</button>
                            </form>
                        @else
                            <a class="sf-button sf-button--secondary" href="{{ route('login') }}">♡ أضيفي للمفضلة</a>
                        @endauth

                        <a class="sf-button sf-button--secondary"
                           target="_blank"
                           rel="noopener"
                           href="https://wa.me/{{ config('storefront.brand.whatsapp','96895426555') }}?text={{ urlencode('مرحبًا، أريد الاستفسار عن المنتج: ' . ($product->name_ar ?? 'منتج')) }}">
                            WhatsApp
                        </a>
                    </div>
                </div>

                <div class="info-strip">
                    <div class="info-item">دفع آمن</div>
                    <div class="info-item">إرجاع واستبدال حسب السياسة</div>
                    <div class="info-item">دعم مباشر عبر واتساب</div>
                </div>
            </div>
        </div>

        @if($relatedProducts->isNotEmpty())
            <section class="related">
                <h2>قد يعجبك أيضًا</h2>
                <div class="related-grid">
                    @foreach($relatedProducts as $related)
                        @php
                            $rImage = ($related->images->firstWhere('is_primary', true) ?? $related->images->first())?->image;
                            $rUrl = $rImage ? (str_starts_with($rImage,'http') ? $rImage : asset('storage/' . ltrim($rImage,'/'))) : null;
                            $rVariant = $related->variants->where('is_active',true)->first() ?? $related->variants->first();
                            $rPrice = $rVariant?->price !== null ? (float)$rVariant->price : (float)($related->price ?? 0);
                        @endphp
                        <a class="related-card" href="{{ route('products.show',$related) }}">
                            <div class="img">
                                @if($rUrl)<img src="{{ $rUrl }}" loading="lazy" alt="{{ $related->name_ar ?? 'منتج' }}">@else 👜 @endif
                            </div>
                            <div class="body">
                                <h3>{{ $related->name_ar ?? 'منتج' }}</h3>
                                <strong>{{ number_format($rPrice,3) }} OMR</strong>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</section>

@if($variants->isNotEmpty() && $totalStock > 0)
<div class="mobile-buy">
    <button type="button" class="sf-button sf-button--primary" onclick="document.getElementById('addToCartForm').requestSubmit()">
        أضيفي للسلة
    </button>
</div>
@endif
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-gallery-image]').forEach(button => {
    button.addEventListener('click', () => {
        const img = document.getElementById('mainProductImage');
        if (img) img.src = button.dataset.galleryImage;
    });
});

const select = document.getElementById('variantSelect');
const price = document.getElementById('displayPrice');
const stock = document.getElementById('stockDisplay');
const qty = document.getElementById('quantityInput');

function syncVariant() {
    if (!select) return;
    const option = select.options[select.selectedIndex];
    const currentStock = Number(option?.dataset.stock || 0);
    if (price) price.textContent = Number(option?.dataset.price || 0).toFixed(3) + ' OMR';
    if (stock) stock.value = currentStock;
    if (qty) qty.max = Math.max(currentStock, 1);
}
select?.addEventListener('change', syncVariant);
syncVariant();

const form = document.getElementById('addToCartForm');
form?.addEventListener('submit', event => {
    const submitter = event.submitter;
    if (submitter && submitter.name === 'buy_now') {
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'redirect_to_checkout';
        hidden.value = '1';
        form.appendChild(hidden);
    }
});
</script>
@endpush
