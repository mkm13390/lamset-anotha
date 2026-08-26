@extends('layouts.store')
@section('title','المرتجعات | لمسة أنوثة')
@push('styles')
<style>
.page{padding:32px 0 70px}.head{display:flex;justify-content:space-between;align-items:end;gap:12px;margin-bottom:18px}.head h1{margin:0}.card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:16px;padding:18px}
table{width:100%;border-collapse:collapse}th,td{text-align:start;padding:11px;border-bottom:1px solid var(--sf-border);font-size:13px}th{color:var(--sf-muted);font-size:11px}.badge{padding:5px 9px;border-radius:999px;background:var(--sf-surface-2);font-size:11px}
@media(max-width:700px){table{display:block;overflow-x:auto;white-space:nowrap}.head{align-items:flex-start;flex-direction:column}}
</style>
@endpush
@section('content')
<section class="page"><div class="sf-container">
<div class="head"><div><h1>طلباتي المرتجعة</h1><p class="sf-muted">تابعي طلبات الاسترداد والاستبدال ورصيد المتجر.</p></div><a class="sf-button sf-button--secondary" href="{{ route('account.index') }}">العودة للحساب</a></div>
<div class="card">
<table><thead><tr><th>رقم المرتجع</th><th>رقم الطلب</th><th>النوع</th><th>المبلغ</th><th>الحالة</th><th>التاريخ</th><th></th></tr></thead><tbody>
@forelse($returns as $r)
<tr>
<td>{{ $r->return_number }}</td>
<td>{{ $r->order?->order_number }}</td>
<td>{{ $r->request_type }}</td>
<td>{{ number_format((float)$r->requested_amount,3) }} OMR</td>
<td><span class="badge">{{ $r->status }}</span></td>
<td>{{ optional($r->requested_at)->format('Y-m-d') }}</td>
<td><a class="sf-button sf-button--secondary" href="{{ route('account.returns.show',$r) }}">عرض</a></td>
</tr>
@empty
<tr><td colspan="7"><div class="sf-empty"><strong>لا توجد طلبات إرجاع حتى الآن.</strong></div></td></tr>
@endforelse
</tbody></table>
@if(method_exists($returns,'links'))<div style="margin-top:15px">{{ $returns->links() }}</div>@endif
</div>
</div></section>
@endsection
