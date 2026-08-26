@extends('layouts.store')
@section('title','عناويني | لمسة أنوثة')
@push('styles')
<style>
.page{padding:34px 0 70px}.head{display:flex;justify-content:space-between;align-items:end;gap:14px;margin-bottom:18px}.head h1{margin:0}.layout{display:grid;grid-template-columns:.8fr 1.2fr;gap:18px}.card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:17px;padding:18px}.field{margin-bottom:10px}.field label{display:block;font-size:12px;font-weight:700;margin-bottom:5px}.field input,.field textarea{width:100%;border:1px solid var(--sf-border);border-radius:9px;background:var(--sf-bg);color:var(--sf-text);padding:0 11px}.field input{height:42px}.field textarea{min-height:80px;padding-top:10px}.grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}.full{grid-column:1/-1}.address{border:1px solid var(--sf-border);border-radius:13px;padding:14px;margin-bottom:10px}.address.default{border-color:#9fc8b1}.address-head{display:flex;justify-content:space-between;gap:10px}.badge{font-size:10px;border-radius:999px;padding:4px 8px;background:#ecfdf5;color:#166534}.tools{display:flex;gap:7px;flex-wrap:wrap;margin-top:12px}.tools form{margin:0}
@media(max-width:850px){.layout{grid-template-columns:1fr}}@media(max-width:560px){.grid{grid-template-columns:1fr}.full{grid-column:auto}.head{align-items:flex-start;flex-direction:column}}
</style>
@endpush
@section('content')
<section class="page"><div class="sf-container">
<div class="head"><div><h1>عناويني</h1><p class="sf-muted">احفظي عناوين التوصيل لتسريع الطلبات القادمة.</p></div><a class="sf-button sf-button--secondary" href="{{ route('account.index') }}">العودة للحساب</a></div>
<div class="layout">
<form class="card" method="POST" action="{{ route('account.addresses.store') }}">@csrf
<h2>إضافة عنوان</h2>
<div class="grid">
<div class="field"><label>اسم العنوان</label><input name="label" placeholder="المنزل / العمل"></div>
<div class="field"><label>اسم المستلم</label><input name="full_name" value="{{ old('full_name',auth()->user()?->name) }}" required></div>
<div class="field"><label>رقم الهاتف</label><input name="phone" value="{{ old('phone',auth()->user()?->phone) }}" required></div>
<div class="field"><label>المحافظة</label><input name="governorate" value="{{ old('governorate') }}" required></div>
<div class="field"><label>الولاية</label><input name="wilayat" value="{{ old('wilayat') }}" required></div>
<div class="field"><label>المنطقة</label><input name="area" value="{{ old('area') }}"></div>
<div class="field"><label>الشارع</label><input name="street" value="{{ old('street') }}"></div>
<div class="field"><label>المبنى</label><input name="building" value="{{ old('building') }}"></div>
<div class="field"><label>رقم المنزل</label><input name="house_number" value="{{ old('house_number') }}"></div>
<div class="field"><label>الرمز البريدي</label><input name="postal_code" value="{{ old('postal_code') }}"></div>
<div class="field full"><label>تفاصيل إضافية</label><textarea name="address_details">{{ old('address_details') }}</textarea></div>
<div class="field full"><label style="display:flex;align-items:center;gap:7px"><input style="width:auto;height:auto" type="checkbox" name="is_default" value="1"> اجعليه العنوان الافتراضي</label></div>
</div>
<button class="sf-button sf-button--primary" type="submit">حفظ العنوان</button>
</form>

<div class="card">
<h2>العناوين المحفوظة</h2>
@forelse($addresses as $address)
<div class="address {{ $address->is_default ? 'default' : '' }}">
<div class="address-head"><div><strong>{{ $address->label ?: 'عنوان' }}</strong><div class="sf-muted">{{ $address->full_name }} · {{ $address->phone }}</div></div>@if($address->is_default)<span class="badge">افتراضي</span>@endif</div>
<p>{{ $address->governorate }} · {{ $address->wilayat }} @if($address->area) · {{ $address->area }} @endif</p>
<div class="sf-muted">{{ collect([$address->street,$address->building,$address->house_number,$address->address_details])->filter()->implode(' · ') }}</div>
<div class="tools">
@if(!$address->is_default)
<form method="POST" action="{{ route('account.addresses.default',$address) }}">@csrf @method('PATCH')<button class="sf-button sf-button--secondary" type="submit">تعيين افتراضي</button></form>
@endif
<form method="POST" action="{{ route('account.addresses.destroy',$address) }}">@csrf @method('DELETE')<button class="sf-button sf-button--secondary" type="submit">حذف</button></form>
</div>
</div>
@empty
<div class="sf-empty"><strong>لم تضيفي عنوانًا بعد.</strong></div>
@endforelse
</div>
</div></div></section>
@endsection
