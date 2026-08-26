@extends('layouts.store')
@section('title','تم استلام طلبك | لمسة أنوثة')
@push('styles')
<style>
.page{padding:50px 0 80px}.box{max-width:760px;margin:auto;background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:22px;padding:34px;text-align:center}.icon{width:76px;height:76px;border-radius:50%;display:grid;place-items:center;background:#ecfdf5;color:#166534;font-size:34px;margin:0 auto 18px}.box h1{margin:0 0 8px}.box p{color:var(--sf-muted)}.order-info{margin:22px 0;display:grid;grid-template-columns:repeat(3,1fr);gap:9px}.info{padding:13px;border:1px solid var(--sf-border);border-radius:12px}.info small{display:block;color:var(--sf-muted)}.actions{display:flex;justify-content:center;gap:9px;flex-wrap:wrap}
@media(max-width:600px){.order-info{grid-template-columns:1fr}.box{padding:24px 16px}}
</style>
@endpush
@section('content')
<section class="page"><div class="sf-container"><div class="box">
<div class="icon">✓</div>
<h1>تم استلام طلبك بنجاح</h1>
<p>شكرًا لكِ. تم تسجيل الطلب ويمكنك الاحتفاظ برقم الطلب للمتابعة.</p>
<div class="order-info">
<div class="info"><small>رقم الطلب</small><strong>#{{ $order->order_number }}</strong></div>
<div class="info"><small>الحالة</small><strong>{{ $order->status }}</strong></div>
<div class="info"><small>الإجمالي</small><strong>{{ number_format((float)$order->total,3) }} OMR</strong></div>
</div>
<div class="actions">
<a class="sf-button sf-button--primary" href="{{ route('orders.invoice',$order) }}">عرض الفاتورة</a>
@auth<a class="sf-button sf-button--secondary" href="{{ route('account.index') }}">حسابي</a>@endauth
<a class="sf-button sf-button--secondary" href="{{ route('products.index') }}">متابعة التسوق</a>
</div>
</div></div></section>
@endsection
