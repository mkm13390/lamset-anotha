<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>الولاء</title>
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
<div class="top"><div><h1>الولاء وVIP</h1><div class="muted">النقاط، المستوى ورصيد المتجر</div></div>
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

<div class="card">
<form method="get"><div class="grid g2"><div class="field"><label>بحث عن عميل</label><input name="q" value="{{ request('q') }}" placeholder="الاسم أو الهاتف أو البريد"></div><div style="display:flex;align-items:end"><button class="primary">بحث</button></div></div></form>
</div>
<div class="card"><table><thead><tr><th>العميل</th><th>المستوى</th><th>النقاط</th><th>إجمالي الإنفاق</th><th>رصيد المتجر</th><th>كود الإحالة</th><th>إجراءات</th></tr></thead><tbody>
@forelse($profiles as $profile)
<tr>
<td><strong>{{ $profile->user?->name }}</strong><div class="muted">{{ $profile->user?->phone }}</div></td>
<td><span class="badge">{{ $profile->tier?->name_ar ?: '-' }}</span></td>
<td>{{ $profile->points_balance }}</td>
<td class="number">{{ number_format((float)$profile->lifetime_spend,3) }}</td>
<td class="number">{{ number_format((float)$profile->store_credit_balance,3) }}</td>
<td>{{ $profile->referral_code }}</td>
<td>
<form method="post" action="{{ route('admin.marketing.loyalty.points',$profile->user_id) }}" style="display:inline-block">@csrf<input type="hidden" name="points" value="10"><button type="submit">+10 نقاط</button></form>
<form method="post" action="{{ route('admin.marketing.loyalty.store-credit',$profile->user_id) }}" style="display:inline-block">@csrf<input type="hidden" name="amount" value="1"><button type="submit">+1 ر.ع رصيد</button></form>
</td>
</tr>
@empty<tr><td colspan="7" class="empty">لا توجد ملفات ولاء بعد. ستظهر هنا عند إنشاء ملفات العملاء أو ربط الشراء بالولاء.</td></tr>@endforelse
</tbody></table><div>{{ $profiles->links() }}</div></div>
</div></body></html>