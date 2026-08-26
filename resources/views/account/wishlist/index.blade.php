@extends('layouts.store')
@section('title','المفضلة | لمسة أنوثة')
@push('styles')
<style>
.page{padding:34px 0 70px}.head{display:flex;justify-content:space-between;align-items:end;gap:14px;margin-bottom:18px}.head h1{margin:0}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:15px}.card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:17px;overflow:hidden}.img{aspect-ratio:1;background:var(--sf-surface-2);display:grid;place-items:center;overflow:hidden}.img img{width:100%;height:100%;object-fit:cover}.body{padding:13px}.body h2{font-size:15px;margin:0 0 5px}.muted{color:var(--sf-muted);font-size:12px}.row{display:flex;justify-content:space-between;gap:8px;align-items:center;margin-top:11px}.price{font-weight:800}.actions{display:grid;grid-template-columns:1fr auto;gap:7px;margin-top:11px}
@media(max-width:950px){.grid{grid-template-columns:repeat(3,1fr)}}@media(max-width:700px){.grid{grid-template-columns:repeat(2,1fr);gap:10px}.head{align-items:flex-start;flex-direction:column}}
</style>
@endpush
@section('content')
<section class="page"><div class="sf-container">
<div class="head"><div><h1>المفضلة</h1><p class="sf-muted">المنتجات التي حفظتها للرجوع إليها لاحقًا.</p></div><a class="sf-button sf-button--secondary" href="{{ route('account.index') }}">حسابي</a></div>
<div class="grid">
@forelse($wishlistItems as $item)
@php
$product=$item->product;
$primary=$product?->images?->firstWhere('is_primary',true) ?? $product?->images?->first();
$raw=$primary?->image;
$url=$raw ? (str_starts_with($raw,'http') ? $raw : asset('storage/'.ltrim($raw,'/'))) : null;
$variants=$product?->variants?->where('is_active',true) ?? collect();
$variant=$variants->first() ?? $product?->variants?->first();
$price=$variant?->price !== null ? (float)$variant->price : (float)($product?->price ?? 0);
$stock=(int)$variants->sum('stock_quantity');
@endphp
@if($product)
<article class="card">
<a class="img" href="{{ route('products.show',$product) }}">@if($url)<img src="{{ $url }}" loading="lazy" alt="{{ $product->name_ar ?? 'منتج' }}">@else 👜 @endif</a>
<div class="body">
<h2>{{ $product->name_ar ?? 'منتج' }}</h2>
@if($product->name_en)<div class="muted">{{ $product->name_en }}</div>@endif
<div class="row"><span class="price">{{ number_format($price,3) }} OMR</span><span class="muted">{{ $stock>0 ? 'متوفر' : 'نفد المخزون' }}</span></div>
<div class="actions">
<a class="sf-button sf-button--primary" href="{{ route('products.show',$product) }}">عرض المنتج</a>
<form method="POST" action="{{ route('account.wishlist.destroy',$item) }}">@csrf @method('DELETE')<button class="sf-button sf-button--secondary" type="submit">حذف</button></form>
</div>
</div>
</article>
@endif
@empty
<div class="sf-empty" style="grid-column:1/-1"><div class="sf-empty__icon">♡</div><strong>لا توجد منتجات في المفضلة.</strong><br><br><a class="sf-button sf-button--primary" href="{{ route('products.index') }}">تصفحي المنتجات</a></div>
@endforelse
</div>
<div style="margin-top:20px">{{ $wishlistItems->links() }}</div>
</div></section>
@endsection
