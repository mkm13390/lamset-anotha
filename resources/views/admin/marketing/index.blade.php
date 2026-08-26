<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>التسويق</title>
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
<div class="top"><div><h1>التسويق الذكي</h1><div class="muted">العروض، الحملات، الشرائح والقياس</div></div>
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

<div class="grid g4">
<div class="card stat"><span class="muted">الحملات</span><b>{{ $stats['campaigns'] }}</b></div>
<div class="card stat"><span class="muted">العروض النشطة</span><b>{{ $stats['active_promotions'] }}</b></div>
<div class="card stat"><span class="muted">السلات المتروكة</span><b>{{ $stats['abandoned_carts'] }}</b></div>
<div class="card stat"><span class="muted">الإيراد المنسوب للحملات</span><b>{{ number_format($stats['attributed_revenue'],3) }} ر.ع</b></div>
</div>
<div class="grid g3">
<div class="card">
<h2 class="section-title">عرض جديد</h2>
<form method="post" action="{{ route('admin.marketing.promotions.store') }}">@csrf
<div class="field"><label>الاسم</label><input name="name_ar" required></div>
<div class="grid g2">
<div class="field"><label>نوع العرض</label><select name="promotion_type"><option value="automatic">تلقائي</option><option value="coupon">كوبون</option><option value="flash_deal">عرض خاطف</option><option value="loyalty">ولاء</option><option value="win_back">استرجاع عميل</option><option value="vip">VIP</option><option value="referral">إحالة</option></select></div>
<div class="field"><label>نوع المكافأة</label><select name="discount_type"><option value="percentage">نسبة</option><option value="fixed_amount">مبلغ ثابت</option><option value="free_shipping">شحن مجاني</option><option value="bonus_points">نقاط إضافية</option><option value="store_credit">رصيد متجر</option><option value="gift_item">هدية</option></select></div>
<div class="field"><label>القيمة</label><input name="discount_value" type="number" step="0.001" min="0" value="0" required></div>
<div class="field"><label>حد أدنى للسلة</label><input name="minimum_spend" type="number" step="0.001" min="0" value="0"></div>
<div class="field"><label>الكود</label><input name="code"></div>
<div class="field"><label>حد استخدام للعميل</label><input name="per_customer_limit" type="number" min="1"></div>
<div class="field"><label>يبدأ</label><input name="starts_at" type="datetime-local"></div>
<div class="field"><label>ينتهي</label><input name="ends_at" type="datetime-local"></div>
</div>
<button class="primary">إنشاء العرض</button></form>
</div>

<div class="card">
<h2 class="section-title">شريحة عملاء</h2>
<form method="post" action="{{ route('admin.marketing.segments.store') }}">@csrf
<div class="field"><label>الاسم</label><input name="name_ar" required></div>
<div class="field"><label>الكود</label><input name="code" required></div>
<div class="field"><label>النوع</label><select name="segment_type"><option value="manual">يدوي</option><option value="rule_based">قواعد</option><option value="ai">ذكاء اصطناعي</option></select></div>
<button class="primary">إنشاء الشريحة</button></form>
</div>

<div class="card">
<h2 class="section-title">حملة جديدة</h2>
<form method="post" action="{{ route('admin.marketing.campaigns.store') }}">@csrf
<div class="field"><label>اسم الحملة</label><input name="name" required></div>
<div class="field"><label>القناة</label><select name="channel"><option value="whatsapp">WhatsApp</option><option value="instagram">Instagram</option><option value="email">Email</option><option value="sms">SMS</option><option value="push">Push</option><option value="web">Web</option><option value="mixed">مختلط</option></select></div>
<div class="field"><label>الشريحة</label><select name="customer_segment_id"><option value="">كل العملاء/لاحقًا</option>@foreach($segments as $segment)<option value="{{ $segment->id }}">{{ $segment->name_ar }}</option>@endforeach</select></div>
<div class="field"><label>العرض</label><select name="promotion_id"><option value="">بدون</option>@foreach($promotions as $promotion)<option value="{{ $promotion->id }}">{{ $promotion->name_ar }}</option>@endforeach</select></div>
<div class="field"><label>الميزانية</label><input name="budget" type="number" step="0.001" min="0" value="0"></div>
<div class="field"><label>موعد التشغيل</label><input name="scheduled_at" type="datetime-local"></div>
<div class="field"><label>الرسالة</label><textarea name="message_ar"></textarea></div>
<button class="primary">إنشاء الحملة</button></form>
</div>
</div>

<div class="card"><h2 class="section-title">آخر العروض</h2><table><thead><tr><th>العرض</th><th>النوع</th><th>المكافأة</th><th>القيمة</th><th>الحالة</th></tr></thead><tbody>
@forelse($promotions as $p)<tr><td>{{ $p->name_ar }}</td><td>{{ $p->promotion_type }}</td><td>{{ $p->discount_type }}</td><td class="number">{{ number_format((float)$p->discount_value,3) }}</td><td><span class="badge">{{ $p->is_active ? 'مفعل' : 'متوقف' }}</span></td></tr>
@empty<tr><td colspan="5" class="empty">لا توجد عروض.</td></tr>@endforelse
</tbody></table></div>

<div class="card"><h2 class="section-title">آخر الحملات</h2><table><thead><tr><th>الحملة</th><th>القناة</th><th>الشريحة</th><th>العرض</th><th>الميزانية</th><th>الحالة</th></tr></thead><tbody>
@forelse($campaigns as $c)<tr><td>{{ $c->name }}</td><td>{{ $c->channel }}</td><td>{{ $c->segment?->name_ar ?: '-' }}</td><td>{{ $c->promotion?->name_ar ?: '-' }}</td><td class="number">{{ number_format((float)$c->budget,3) }}</td><td><span class="badge">{{ $c->status }}</span></td></tr>
@empty<tr><td colspan="6" class="empty">لا توجد حملات.</td></tr>@endforelse
</tbody></table></div>

<div class="card"><h2 class="section-title">تهيئة الذكاء الاصطناعي</h2><div class="muted" style="line-height:2">
تم تجهيز النظام لتجميع سلوك العميل، CLV، احتمالية الشراء، احتمالية الانقطاع، حساسية الخصم، أفضل قناة، أفضل وقت للإرسال وNext Best Action. هذه القيم لا تُختلق تلقائيًا الآن، بل ستُحسب لاحقًا عند تشغيل نماذج AI على بيانات حقيقية.
</div></div>
</div></body></html>