<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>المدفوعات</title>
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
<div class="top"><div><h1>المدفوعات</h1><div class="muted">طرق الدفع والعمليات</div></div>
<div class="nav">
<a class="btn" href="{{ route('admin.shipping.index') }}">الشحن</a>
<a class="btn" href="{{ route('admin.payments.index') }}">المدفوعات</a>
<a class="btn" href="{{ route('admin.returns.index') }}">المرتجعات</a>
</div>
</div>

@if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
@if($errors->any())<div class="errors">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

<div class="grid g2"><div class="card"><h2 class="section-title">إضافة طريقة/بوابة دفع</h2><form method="post" action="{{ route('admin.payments.gateways.store') }}">@csrf
<div class="grid g2"><div class="field"><label>الاسم</label><input name="name_ar" required></div><div class="field"><label>الكود</label><input name="code" required></div>
<div class="field"><label>النوع</label><select name="gateway_type"><option value="cash">نقدي</option><option value="cod">عند الاستلام</option><option value="card">بطاقة</option><option value="bank_transfer">تحويل بنكي</option><option value="online_gateway">بوابة إلكترونية</option><option value="store_credit">رصيد متجر</option><option value="gift_card">بطاقة هدية</option><option value="other">أخرى</option></select></div>
<div class="field"><label>طريقة الربط</label><select name="integration_type"><option value="manual">يدوي</option><option value="redirect">Redirect</option><option value="api">API</option><option value="hosted_fields">Hosted Fields</option><option value="app_to_app">App to App</option></select></div></div>
<input type="hidden" name="currency" value="OMR">
<label><input style="width:auto" type="checkbox" name="supports_refund" value="1"> يدعم Refund</label><br>
<label><input style="width:auto" type="checkbox" name="supports_partial_refund" value="1"> يدعم Partial Refund</label><br><br><button class="primary">حفظ</button></form></div>
<div class="card"><h2 class="section-title">طرق الدفع الحالية</h2>@forelse($gateways as $gateway)<div style="padding:10px 0;border-bottom:1px solid #eee"><strong>{{ $gateway->name_ar }}</strong><div class="muted">{{ $gateway->gateway_type }} · {{ $gateway->integration_type }} · {{ $gateway->currency }}</div></div>@empty<div class="empty">لا توجد طرق دفع.</div>@endforelse</div></div>
<div class="card"><h2 class="section-title">سجل العمليات</h2><form method="get" class="inline" style="margin-bottom:12px"><div class="field"><label>الحالة</label><input name="status" value="{{ request('status') }}"></div><div class="field"><label>النوع</label><input name="type" value="{{ request('type') }}"></div><button class="primary">فلترة</button></form>
<table><thead><tr><th>الرقم</th><th>البوابة</th><th>النوع</th><th>الحالة</th><th>المبلغ</th><th>المسترد</th><th>المرجع</th><th>التاريخ</th></tr></thead><tbody>
@forelse($transactions as $tx)<tr><td>{{ $tx->transaction_number }}</td><td>{{ $tx->gateway?->name_ar ?: '-' }}</td><td>{{ $tx->type }}</td><td><span class="badge">{{ $tx->status }}</span></td><td class="number">{{ number_format((float)$tx->amount,3) }}</td><td class="number">{{ number_format((float)$tx->refunded_amount,3) }}</td><td>{{ $tx->bank_reference ?: $tx->external_transaction_id ?: '-' }}</td><td>{{ optional($tx->created_at)->format('Y-m-d H:i') }}</td></tr>
@empty<tr><td colspan="8" class="empty">لا توجد عمليات دفع.</td></tr>@endforelse
</tbody></table><div>{{ $transactions->links() }}</div></div>
</div></body></html>