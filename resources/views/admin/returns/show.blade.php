<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $returnRequest->return_number }}</title>
<style>
*{box-sizing:border-box}
:root{--bg:#f5f6f8;--card:#fff;--text:#1d2433;--muted:#6b7280;--line:#e2e6ed;--primary:#1f2937}
body{margin:0;font-family:Tahoma,Arial,sans-serif;background:var(--bg);color:var(--text)}
a{text-decoration:none;color:inherit}
.wrap{max-width:1450px;margin:auto;padding:24px}
.top{display:flex;justify-content:space-between;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:20px}
.top h1{margin:0}
.nav{display:flex;gap:8px;flex-wrap:wrap}
.btn,button{border:0;border-radius:10px;padding:10px 14px;cursor:pointer;font-weight:700;font-family:inherit}
.btn{background:#fff;border:1px solid var(--line)}
.primary{background:var(--primary);color:#fff}
.success-btn{background:#166534;color:#fff}
.warning-btn{background:#92400e;color:#fff}
.card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:16px;margin-bottom:16px}
.grid{display:grid;gap:14px}.g4{grid-template-columns:repeat(4,minmax(0,1fr))}
.g3{grid-template-columns:repeat(3,minmax(0,1fr))}.g2{grid-template-columns:repeat(2,minmax(0,1fr))}
.stat b{display:block;font-size:22px;margin-top:6px}.muted{color:var(--muted);font-size:13px}
.field{margin-bottom:12px}label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}
input,select,textarea{width:100%;border:1px solid #d8dde6;border-radius:10px;padding:10px;font-family:inherit;background:#fff}
textarea{min-height:90px;resize:vertical}table{width:100%;border-collapse:collapse}
th,td{padding:10px 8px;border-bottom:1px solid #edf0f4;text-align:right;font-size:14px;vertical-align:top}
th{background:#fafbfc}.badge{display:inline-block;padding:5px 9px;border-radius:999px;background:#eef2ff;font-size:12px}
.flash{padding:12px 14px;border-radius:12px;margin-bottom:14px;background:#ecfdf5;color:#166534}
.errors{padding:12px 14px;border-radius:12px;margin-bottom:14px;background:#fef2f2;color:#991b1b}
.section-title{margin:0 0 14px;font-size:18px}.empty{text-align:center;padding:28px;color:var(--muted)}
.number{white-space:nowrap;font-variant-numeric:tabular-nums}.inline{display:flex;gap:8px;flex-wrap:wrap;align-items:end}
@media(max-width:900px){.g4,.g3,.g2{grid-template-columns:1fr}.wrap{padding:14px}table{display:block;overflow-x:auto;white-space:nowrap}}
</style>
</head><body><div class="wrap">
<div class="top"><div><h1>{{ $returnRequest->return_number }}</h1><div class="muted">طلب {{ $returnRequest->order?->order_number }} · {{ $returnRequest->request_type }}</div></div>
<div class="nav">
<a class="btn" href="{{ route('admin.shipping.index') }}">الشحن</a>
<a class="btn" href="{{ route('admin.payments.index') }}">المدفوعات</a>
<a class="btn" href="{{ route('admin.returns.index') }}">المرتجعات</a>
</div>
</div>

@if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
@if($errors->any())<div class="errors">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

<div class="grid g4"><div class="card stat"><span class="muted">الحالة</span><b>{{ $returnRequest->status }}</b></div><div class="card stat"><span class="muted">المطلوب</span><b>{{ number_format((float)$returnRequest->requested_amount,3) }}</b></div><div class="card stat"><span class="muted">المعتمد</span><b>{{ number_format((float)$returnRequest->approved_amount,3) }}</b></div><div class="card stat"><span class="muted">العميل</span><b style="font-size:16px">{{ $returnRequest->user?->name ?: '-' }}</b></div></div>
<div class="card"><h2 class="section-title">المنتجات</h2><form method="post" action="{{ route('admin.returns.approve',$returnRequest) }}">@csrf
<table><thead><tr><th>الصنف</th><th>الكمية</th><th>الحالة</th><th>قيمة الوحدة</th><th>المبلغ المعتمد</th><th>إرجاع للمخزون</th><th>تالف</th></tr></thead><tbody>
@foreach($returnRequest->items as $item)<tr><td>{{ $item->orderItem?->product_name_ar ?? $item->orderItem?->name ?? ('#'.$item->order_item_id) }}</td><td>{{ $item->quantity }}</td><td>{{ $item->condition }}</td><td>{{ number_format((float)$item->unit_refund_amount,3) }}</td><td><input name="items[{{ $item->id }}][approved_refund_amount]" type="number" step="0.001" min="0" value="{{ $item->approved_refund_amount ?: ((float)$item->unit_refund_amount * $item->quantity) }}"></td><td><input style="width:auto" name="items[{{ $item->id }}][restock]" type="checkbox" value="1" @checked($item->restock)></td><td><input style="width:auto" name="items[{{ $item->id }}][mark_damaged]" type="checkbox" value="1" @checked($item->mark_damaged)></td></tr>@endforeach
</tbody></table><div class="field"><label>ملاحظات الإدارة</label><textarea name="admin_notes">{{ $returnRequest->admin_notes }}</textarea></div><button class="success-btn">اعتماد المرتجع</button></form></div>

<div class="grid g2">
<div class="card"><h2 class="section-title">استلام المرتجع</h2><form method="post" action="{{ route('admin.returns.received',$returnRequest) }}">@csrf<button class="primary">تسجيل الاستلام</button></form></div>
<div class="card"><h2 class="section-title">تنفيذ الاسترداد</h2><form method="post" action="{{ route('admin.returns.refund',$returnRequest) }}">@csrf
<div class="field"><label>الطريقة</label><select name="refund_method"><option value="original_payment">طريقة الدفع الأصلية</option><option value="cash">نقدي</option><option value="bank_transfer">تحويل بنكي</option><option value="store_credit">رصيد متجر</option><option value="gift_card">Gift Card</option><option value="other">أخرى</option></select></div>
<div class="field"><label>القيمة</label><input name="amount" type="number" step="0.001" min="0.001" value="{{ $returnRequest->approved_amount }}" required></div>
<div class="field"><label>عملية الدفع الأصلية</label><select name="payment_transaction_id"><option value="">بدون</option>@foreach($paymentTransactions as $tx)<option value="{{ $tx->id }}">{{ $tx->transaction_number }} · {{ number_format((float)$tx->amount,3) }}</option>@endforeach</select></div>
<div class="field"><label>المرجع</label><input name="reference"></div><button class="warning-btn">تنفيذ Refund</button></form></div>
</div>

<div class="card"><h2 class="section-title">سجل الاستردادات</h2><table><thead><tr><th>الرقم</th><th>الطريقة</th><th>القيمة</th><th>الحالة</th><th>التاريخ</th></tr></thead><tbody>@forelse($returnRequest->refunds as $refund)<tr><td>{{ $refund->refund_number }}</td><td>{{ $refund->refund_method }}</td><td>{{ number_format((float)$refund->amount,3) }}</td><td>{{ $refund->status }}</td><td>{{ optional($refund->processed_at)->format('Y-m-d H:i') ?: '-' }}</td></tr>@empty<tr><td colspan="5" class="empty">لا توجد استردادات.</td></tr>@endforelse</tbody></table></div>
</div></body></html>