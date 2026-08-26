@extends('layouts.store')
@section('title','طلب إرجاع | لمسة أنوثة')
@push('styles')
<style>
.page{padding:32px 0 70px}.head{display:flex;justify-content:space-between;align-items:end;gap:12px;margin-bottom:18px}.card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:16px;padding:18px;margin-bottom:12px}.grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.field label{display:block;font-size:12px;font-weight:700;margin-bottom:6px}.field input,.field select,.field textarea{width:100%;border:1px solid var(--sf-border);border-radius:10px;background:var(--sf-bg);color:var(--sf-text);padding:0 12px}.field input,.field select{height:44px}.field textarea{min-height:90px;padding-top:10px}.item{display:grid;grid-template-columns:auto 1.4fr .6fr .8fr;gap:12px;align-items:center}
@media(max-width:750px){.grid,.item{grid-template-columns:1fr}.head{align-items:flex-start;flex-direction:column}}
</style>
@endpush
@section('content')
<section class="page"><div class="sf-container">
<div class="head"><div><h1>طلب إرجاع أو استبدال</h1><p class="sf-muted">الطلب #{{ $order->order_number }}</p></div><a class="sf-button sf-button--secondary" href="{{ route('account.returns.index') }}">طلبات الإرجاع</a></div>

@if($policy)
<div class="card"><strong>{{ $policy->name_ar ?? 'سياسة الإرجاع' }}</strong><div class="sf-muted">مدة الإرجاع: {{ $policy->return_window_days }} يوم · مدة الاستبدال: {{ $policy->exchange_window_days }} يوم</div></div>
@endif

<form method="POST" action="{{ route('account.returns.store',$order) }}">
@csrf
@if($policy)<input type="hidden" name="return_policy_id" value="{{ $policy->id }}">@endif

<div class="card">
<div class="grid">
<div class="field"><label>نوع الطلب</label><select name="request_type" required><option value="refund">استرداد المبلغ</option><option value="exchange">استبدال</option><option value="store_credit">رصيد متجر</option></select></div>
<div class="field"><label>السبب العام</label><input name="reason_code" placeholder="مثال: المقاس غير مناسب"></div>
</div>
<div class="field" style="margin-top:12px"><label>ملاحظات</label><textarea name="customer_notes" placeholder="أضيفي أي تفاصيل تساعدنا في معالجة الطلب"></textarea></div>
</div>

@foreach($order->items as $i=>$item)
<div class="card item">
<div><label><input style="width:auto" type="checkbox" name="items[{{ $i }}][selected]" value="1"> اختيار</label><input type="hidden" name="items[{{ $i }}][order_item_id]" value="{{ $item->id }}"></div>
<div><strong>{{ $item->product_name_ar ?? $item->name ?? ('الصنف #'.$item->id) }}</strong><div class="sf-muted">الكمية الأصلية: {{ $item->quantity }}</div></div>
<div class="field"><label>الكمية</label><input name="items[{{ $i }}][quantity]" type="number" min="1" max="{{ $item->quantity }}" value="1"></div>
<div class="field"><label>حالة المنتج</label><select name="items[{{ $i }}][condition]"><option value="new">جديد</option><option value="unopened">غير مفتوح</option><option value="used">مستخدم</option><option value="damaged">متضرر</option><option value="defective">به عيب</option><option value="wrong_item">منتج خاطئ</option><option value="other">أخرى</option></select></div>
</div>
@endforeach

<button class="sf-button sf-button--primary" type="submit">إرسال الطلب</button>
</form>
</div></section>
@endsection
