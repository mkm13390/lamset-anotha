@extends('layouts.store')
@php
$pages=[
'about'=>['title'=>'من نحن','text'=>'لمسة أنوثة متجر متخصص في الحقائب والأحذية والإكسسوارات والمنتجات النسائية. نعمل على تقديم تجربة شراء واضحة وسهلة مع متابعة الطلبات وخدمة العملاء.'],
'contact'=>['title'=>'تواصل معنا','text'=>'يسعدنا استقبال استفساراتك عبر واتساب وإنستغرام.'],
'shipping'=>['title'=>'الشحن والتوصيل','text'=>'تختلف رسوم ومدة التوصيل حسب المنطقة وخدمة الشحن المتاحة. يظهر التفصيل النهائي للعميل أثناء معالجة الطلب حسب عنوانه.'],
'returns'=>['title'=>'الإرجاع والاستبدال','text'=>'يمكن للعملاء المسجلين تقديم طلب إرجاع أو استبدال من الحساب وفق سياسة الإرجاع الفعالة وشروط حالة المنتج.'],
];
$current=$pages[$page] ?? $pages['about'];
@endphp
@section('title',$current['title'].' | لمسة أنوثة')
@push('styles')
<style>.page{padding:44px 0 80px}.box{max-width:850px;margin:auto;background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:20px;padding:30px}.box h1{margin-top:0}.box p{color:var(--sf-muted);line-height:2}.links{display:flex;gap:8px;flex-wrap:wrap;margin-top:20px}.legal{margin-top:26px;padding-top:18px;border-top:1px solid var(--sf-border);font-size:12px;color:var(--sf-muted)}</style>
@endpush
@section('content')
<section class="page"><div class="sf-container"><div class="box">
<h1>{{ $current['title'] }}</h1><p>{{ $current['text'] }}</p>
@if($page==='contact')
<div class="links"><a class="sf-button sf-button--primary" target="_blank" href="https://wa.me/{{ config('storefront.brand.whatsapp','96895426555') }}">WhatsApp</a><a class="sf-button sf-button--secondary" target="_blank" href="https://www.instagram.com/{{ config('storefront.brand.instagram','mkm13390') }}">Instagram</a></div>
@endif
@if(config('storefront.legal.business_name') || config('storefront.legal.commercial_registration') || config('storefront.legal.license_number'))
<div class="legal">
@if(config('storefront.legal.business_name'))<div>{{ config('storefront.legal.business_name') }}</div>@endif
@if(config('storefront.legal.commercial_registration'))<div>السجل التجاري: {{ config('storefront.legal.commercial_registration') }}</div>@endif
@if(config('storefront.legal.license_number'))<div>رقم الترخيص: {{ config('storefront.legal.license_number') }}</div>@endif
</div>
@endif
</div></div></section>
@endsection
