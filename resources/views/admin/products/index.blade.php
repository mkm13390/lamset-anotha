@extends('layouts.store')

@section('title', 'المنتجات | لمسة أنوثة')
@section('meta_description', 'تصفحي منتجات لمسة أنوثة من الحقائب والأحذية والإكسسوارات.')

@push('styles')
<style>
.catalog{padding:34px 0 60px}
.catalog-head{display:flex;justify-content:space-between;align-items:end;gap:16px;margin-bottom:20px}
.catalog-head h1{margin:0;font-size:34px}.catalog-head p{margin:5px 0 0;color:var(--sf-muted)}
.catalog-tools{display:grid;grid-template-columns:1fr auto auto;gap:10px;margin-bottom:18px;background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:16px;padding:12px}
.catalog-tools input,.catalog-tools select{min-height:44px;border:1px solid var(--sf-border);border-radius:10px;background:var(--sf-bg);color:var(--sf-text);padding:0 12px}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:18px;overflow:hidden;position:relative}
.card-image{aspect-ratio:1/1;background:var(--sf-surface-2);display:grid;place-items:center;overflow:hidden}
.card-image img{width:100%;height:100%;object-fit:cover}.card-image .fallback{font-size:56px}
.card-body{padding:14px}.card h2{font-size:15px;margin:0}.en{font-size:12px;color:var(--sf-muted);margin-top:2px}
.meta{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:12px}.price{font-weight:800}
.stock{font-size:11px;color:var(--sf-muted)}.soldout{color:#a23737}
.card-actions{display:grid;grid-template-columns:1fr auto;gap:8px;margin-top:12px}
.card-actions .sf-button{min-height:40px;padding:7px 12px}.heart{width:40px;height:40px;border-radius:10px;border:1px solid var(--sf-border);background:var(--sf-surface);color:var(--sf-text);cursor:pointer}
.results{color:var(--sf-muted);font-size:13px}.pager{margin-top:24px}.pager svg{width:18px}
@media(max-width:980px){.grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:760px){.catalog-tools{grid-template-columns:1fr 1fr}.catalog-tools input{grid-column:1/-1}.grid{grid-template-columns:repeat(2,1fr);gap:10px}.catalog-head{align-items:flex-start;flex-direction:column}}
</style>
@endpush

@section('content')
<section class="catalog">
    <div class="sf-container">
        <div class="catalog-head">
            <div>
                <h1 data-i18n-ar="كل المنتجات" data-i18n-en="All products">كل المنتجات</h1>
                <p data-i18n-ar="تصفحي المجموعة واختاري ما يناسبك." data-i18n-en="Browse the collection and find your pick.">
                    تصفحي المجموعة واختاري ما يناسبك.
                </p>
            </div>
            <div class="results">{{ $products->total() }} منتج</div>
        </div>

        <form method="get" class="catalog-tools">
            <input name="q" value="{{ request('q') }}" placeholder="بحث بالاسم، SKU، اللون أو المقاس">
            <select name="category">
                <option value="">كل الأقسام</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string)request('category') === (string)$category->id)>
                        {{ $category->name_ar ?? 'قسم' }}
                    </option>
                @endforeach
            </select>
            <select name="sort" onchange="this.form.submit()">
                <option value="newest" @selected(request('sort','newest')==='newest')>الأحدث</option>
                <option value="oldest" @selected(request('sort')==='oldest')>الأقدم</option>
                <option value="price_low" @selected(request('sort')==='price_low')>السعر: الأقل أولًا</option>
                <option value="price_high" @selected(request('sort')==='price_high')>السعر: الأعلى أولًا</option>
            </select>
        </form>

        <div class="grid">
            @forelse($products as $product)
                @php
                    $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                    $image = $primary?->image;
                    $imageUrl = $image ? (str_starts_with($image, 'http') ? $image : asset('storage/' . ltrim($image, '/'))) : null;
                    $activeVariants = $product->variants->where('is_active', true);
                    $variant = $activeVariants->first() ?? $product->variants->first();
                    $price = $variant?->price !== null ? (float)$variant->price : (float)($product->price ?? 0);
                    $stock = (int)$activeVariants->sum('stock_quantity');
                @endphp
                <article class="card">
                    <a class="card-image" href="{{ route('products.show', $product) }}">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" loading="lazy" alt="{{ $product->name_ar ?? 'منتج' }}">
                        @else
                            <span class="fallback">👜</span>
                        @endif
                    </a>

                    <div class="card-body">
                        <a href="{{ route('products.show', $product) }}">
                            <h2>{{ $product->name_ar ?? 'منتج' }}</h2>
                            @if($product->name_en)<div class="en">{{ $product->name_en }}</div>@endif
                        </a>

                        <div class="meta">
                            <span class="price">{{ number_format($price,3) }} OMR</span>
                            <span class="stock {{ $stock <= 0 ? 'soldout' : '' }}">
                                {{ $stock > 0 ? 'متوفر' : 'نفد المخزون' }}
                            </span>
                        </div>

                        <div class="card-actions">
                            <a class="sf-button sf-button--primary" href="{{ route('products.show', $product) }}">
                                عرض المنتج
                            </a>
                            @auth
                                <form method="post" action="{{ route('account.wishlist.toggle', $product) }}">
                                    @csrf
                                    <button class="heart" type="submit" aria-label="المفضلة">♡</button>
                                </form>
                            @else
                                <a class="heart" href="{{ route('login') }}" style="display:grid;place-items:center">♡</a>
                            @endauth
                        </div>
                    </div>
                </article>
            @empty
                <div class="sf-empty" style="grid-column:1/-1">
                    <div class="sf-empty__icon">⌕</div>
                    <strong>لم نجد منتجات مطابقة.</strong>
                    <div class="sf-muted">جرّبي عبارة بحث أو قسمًا مختلفًا.</div>
                </div>
            @endforelse
        </div>

        <div class="pager">{{ $products->links() }}</div>
    </div>
</section>
@endsection
