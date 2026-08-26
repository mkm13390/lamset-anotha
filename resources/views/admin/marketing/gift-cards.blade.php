<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>بطاقات الهدايا</title>
<style>
*{box-sizing:border-box}
:root{--bg:#f5f6f8;--card:#fff;--text:#1d2433;--muted:#6b7280;--line:#e2e6ed;--primary:#1f2937}
body{margin:0;font-family:Tahoma,Arial,sans-serif;background:var(--bg);color:var(--text)}
a{text-decoration:none;color:inherit}.wrap{max-width:1450px;margin:auto;padding:24px}
.top{display:flex;justify-content:space-between;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:20px}
.top h1{margin:0}.nav{display:flex;gap:8px;flex-wrap:wrap}
.btn,button{border:0;border-radius:10px;padding:10px 14px;cursor:pointer;font-weight:700;font-family:inherit}
.btn{background:#fff;border:1px solid var(--line)}.primary{background:var(--primary);color:#fff}
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
.number{white-space:nowrap;font-variant-numeric:tabular-nums}
@media(max-width:900px){.g4,.g3,.g2{grid-template-columns:1fr}.wrap{padding:14px}table{display:block;overflow-x:auto;white-space:nowrap}}
</style>
</head><body><div class="wrap">
<div class="top"><div><h1>بطاقات الهدايا</h1><div class="muted">إصدار وتتبع الأرصدة</div></div>
<div class="nav">
<a class="btn" href="{{ route('admin.marketing.index') }}">التسويق</a>
<a class="btn" href="{{ route('admin.marketing.loyalty.index') }}">الولاء</a>
<a class="btn" href="{{ route('admin.marketing.gift-cards.index') }}">بطاقات الهدايا</a>
<a class="btn" href="{{ route('admin.marketing.bundles.index') }}">الباقات</a>
</div>
</div>

@if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
@if($errors->any())
<div class="errors">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
@endif

<div class="grid g2"><div class="card"><h2 class="section-title">بطاقة جديدة</h2>
<form method="post" action="{{ route('admin.marketing.gift-cards.store') }}">@csrf
<div class="field"><label>القيمة</label><input name="initial_balance" type="number" step="0.001" min="0.001" required></div>
<div class="field"><label>اسم المستلم</label><input name="recipient_name"></div>
<div class="field"><label>هاتف المستلم</label><input name="recipient_phone"></div>
<div class="field"><label>تاريخ الانتهاء</label><input name="expires_at" type="datetime-local"></div>
<div class="field"><label>رسالة الإهداء</label><textarea name="message"></textarea></div>
<button class="primary">إصدار البطاقة</button></form></div>
<div class="card"><h2 class="section-title">ملاحظة تشغيلية</h2><div class="muted" style="line-height:2">بطاقات الهدايا أصبحت لها أكواد وأرصدة وحركات مستقلة. ربط استخدامها في Checkout وPOS يتم عند مرحلة دمج الدفع والتسويق مع مسارات البيع.</div></div></div>
<div class="card"><table><thead><tr><th>الكود</th><th>القيمة الأصلية</th><th>الرصيد</th><th>المستلم</th><th>الانتهاء</th><th>الحالة</th></tr></thead><tbody>
@forelse($cards as $card)<tr><td><strong>{{ $card->code }}</strong></td><td class="number">{{ number_format((float)$card->initial_balance,3) }}</td><td class="number">{{ number_format((float)$card->current_balance,3) }}</td><td>{{ $card->recipient_name ?: $card->assignedUser?->name ?: '-' }}</td><td>{{ optional($card->expires_at)->format('Y-m-d') ?: '-' }}</td><td><span class="badge">{{ $card->status }}</span></td></tr>
@empty<tr><td colspan="6" class="empty">لا توجد بطاقات هدايا.</td></tr>@endforelse
</tbody></table><div>{{ $cards->links() }}</div></div>
</div></body></html>