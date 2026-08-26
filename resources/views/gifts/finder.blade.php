@extends('layouts.store')
@section('title','دليل الهدايا | لمسة أنوثة')
@push('styles')
<style>
.page{padding:40px 0 80px}.hero{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:22px;padding:26px;margin-bottom:20px}.hero h1{margin:0 0 7px}.finder{display:grid;grid-template-columns:1fr auto;gap:10px;max-width:520px;margin-top:18px}.finder input{height:46px;border:1px solid var(--sf-border);border-radius:10px;background:var(--sf-bg);color:var(--sf-text);padding:0 12px}.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:16px;overflow:hidden}.img{aspect-ratio:1;background:var(--sf-surface-2);display:grid;place-items:center}.img img{width:100%;height:100%;object-fit:cover}.body{padding:12px}.body h2{font-size:14px;margin:0 0 8px}
@media(max-width:900px){.grid{grid-template-columns:repeat(3,1fr)}}@media(max-width:650px){.grid{grid-template-columns:repeat(2,1fr);gap:9px}.finder{grid-template-columns:1fr}}
</style>
@endpush
@section('content')
<section class="page"><div class="sf-container">
<div class="hero"><h1>هدية حسب الميزانية 🎀</h1><p class="sf-muted">أدخلي ميزانيتك وسنعرض المنتجات التي تقع ضمنها.</p><form class="finder" method="get"><input type="number" step="0.001" min="{{ $minimum }}" name="budget" value="{{ $budget }}" required><button class="sf-button sf-button--primary">اعرضي الاقتراحات</button></form></div>
<h2>اقتراحات حتى {{ number_format((float)$budget,3) }} OMR</h2>
<div class="grid">
@forelse($products as $product)
@php
$raw=($product->images->firstWhere('is_primary',true) ?? $product->images->first())?->image;
$url=$raw ? (str_starts_with($raw,'http') ? $raw : asset('storage/'.ltrim($raw,'/'))) : null;
$prices=$product->variants->where('is_active',true)->pluck('price')->filter(fn($v)=>$v!==null)->map(fn($v)=>(float)$v);
$price=$prices->isNotEmpty() ? $prices->min() : (float)($product->price ?? 0);
@endphp
<a class="card" href="{{ route('products.show',$product) }}"><div class="img">@if($url)<img src="{{ $url }}" loading="lazy" alt="{{ $product->name_ar ?? 'منتج' }}">@else 👜 @endif</div><div class="body"><h2>{{ $product->name_ar ?? 'منتج' }}</h2><strong>{{ number_format($price,3) }} OMR</strong></div></a>
@empty
<div class="sf-empty" style="grid-column:1/-1"><strong>لا توجد منتجات ضمن هذه الميزانية حاليًا.</strong></div>
@endforelse
</div>
</div></section>
@endsection
