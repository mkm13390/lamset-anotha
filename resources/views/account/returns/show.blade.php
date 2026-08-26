@extends('layouts.store')
@section('title',($returnRequest->return_number ?? 'مرتجع').' | لمسة أنوثة')
@push('styles')
<style>
.page{padding:32px 0 70px}.head{display:flex;justify-content:space-between;align-items:end;gap:12px;margin-bottom:18px}.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:14px}.card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:16px;padding:18px}.stat b{display:block;font-size:21px;margin-top:5px}table{width:100%;border-collapse:collapse}th,td{text-align:start;padding:11px;border-bottom:1px solid var(--sf-border);font-size:13px}th{color:var(--sf-muted);font-size:11px}
.timeline{display:grid;gap:8px}.event{padding:10px;border-inline-start:3px solid var(--sf-border);background:var(--sf-bg);border-radius:8px}.event small{color:var(--sf-muted)}
@media(max-width:700px){.stats{grid-template-columns:1fr}.head{align-items:flex-start;flex-direction:column}table{display:block;overflow-x:auto;white-space:nowrap}}
</style>
@endpush
@section('content')
<section class="page"><div class="sf-container">
<div class="head"><div><h1>{{ $returnRequest->return_number }}</h1><p class="sf-muted">طلب مرتبط بالطلب #{{ $returnRequest->order?->order_number }}</p></div><a class="sf-button sf-button--secondary" href="{{ route('account.returns.index') }}">العودة للمرتجعات</a></div>

<div class="stats">
<div class="card stat"><span class="sf-muted">الحالة</span><b>{{ $returnRequest->status }}</b></div>
<div class="card stat"><span class="sf-muted">المبلغ المطلوب</span><b>{{ number_format((float)$returnRequest->requested_amount,3) }} OMR</b></div>
<div class="card stat"><span class="sf-muted">المبلغ المعتمد</span><b>{{ number_format((float)$returnRequest->approved_amount,3) }} OMR</b></div>
</div>

<div class="card">
<h2>المنتجات</h2>
<table><thead><tr><th>المنتج</th><th>الكمية</th><th>الحالة</th><th>المبلغ المعتمد</th></tr></thead><tbody>
@foreach($returnRequest->items as $item)
<tr>
<td>{{ $item->orderItem?->product_name_ar ?? $item->orderItem?->name ?? ('#'.$item->order_item_id) }}</td>
<td>{{ $item->quantity }}</td>
<td>{{ $item->condition }}</td>
<td>{{ number_format((float)$item->approved_refund_amount,3) }} OMR</td>
</tr>
@endforeach
</tbody></table>
</div>

@if($shipment)
<div class="card" style="margin-top:14px">
<h2>شحنة المرتجع</h2>
<div class="timeline">
<div class="event"><strong>{{ $shipment->status }}</strong><br><small>{{ $shipment->tracking_number ?? 'لا يوجد رقم تتبع بعد' }}</small></div>
</div>
</div>
@endif

@if($returnRequest->customer_notes)
<div class="card" style="margin-top:14px"><h2>ملاحظاتك</h2><p>{{ $returnRequest->customer_notes }}</p></div>
@endif
</div></section>
@endsection
