<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>البحث الإداري</title>
<style>
*{box-sizing:border-box}
:root{--bg:#f5f6f8;--card:#fff;--text:#1f2937;--muted:#6b7280;--line:#e5e7eb;--primary:#111827}
body{margin:0;background:var(--bg);color:var(--text);font-family:Tahoma,Arial,sans-serif}
a{text-decoration:none;color:inherit}.wrap{max-width:1450px;margin:auto;padding:24px}
.top{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:20px}
.top h1{margin:0}.nav{display:flex;gap:8px;flex-wrap:wrap}
.btn,button{border:0;border-radius:10px;padding:10px 14px;font-family:inherit;font-weight:700;cursor:pointer}
.btn{background:#fff;border:1px solid var(--line)}.primary{background:var(--primary);color:#fff}
.good{background:#166534;color:#fff}.warn{background:#92400e;color:#fff}.danger{background:#991b1b;color:#fff}
.card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:16px;margin-bottom:16px}
.grid{display:grid;gap:14px}.g4{grid-template-columns:repeat(4,minmax(0,1fr))}
.g3{grid-template-columns:repeat(3,minmax(0,1fr))}.g2{grid-template-columns:repeat(2,minmax(0,1fr))}
.muted{color:var(--muted);font-size:13px}.stat b{display:block;font-size:22px;margin-top:6px}
.field{margin-bottom:12px}label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}
input,select,textarea{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:10px;background:#fff;font-family:inherit}
textarea{min-height:90px}.badge{display:inline-block;padding:5px 9px;border-radius:999px;background:#eef2ff;font-size:12px}
table{width:100%;border-collapse:collapse}th,td{padding:10px 8px;border-bottom:1px solid #edf0f4;text-align:right;vertical-align:top;font-size:14px}
th{background:#fafafa}.flash{padding:12px 14px;background:#ecfdf5;color:#166534;border-radius:10px;margin-bottom:14px}
.errors{padding:12px 14px;background:#fef2f2;color:#991b1b;border-radius:10px;margin-bottom:14px}
.section-title{margin:0 0 14px}.inline{display:flex;gap:8px;align-items:end;flex-wrap:wrap}.empty{text-align:center;padding:24px;color:var(--muted)}
@media(max-width:900px){.g4,.g3,.g2{grid-template-columns:1fr}.wrap{padding:14px}table{display:block;overflow-x:auto;white-space:nowrap}}
</style>
</head><body><div class="wrap">
<div class="top"><div><h1>البحث الإداري الموحد</h1><div class="muted">بحث عبر فهرس الإدارة</div></div>
<div class="nav">
<a class="btn" href="{{ route('admin.security.index') }}">الأمان</a>
<a class="btn" href="{{ route('admin.approvals.index') }}">الموافقات</a>
<a class="btn" href="{{ route('admin.health.index') }}">صحة النظام</a>
<a class="btn" href="{{ route('admin.report-center.index') }}">مركز التقارير</a>
<a class="btn" href="{{ route('admin.search.index') }}">البحث</a>
<a class="btn" href="{{ route('admin.backups.index') }}">النسخ الاحتياطية</a>
</div>
</div>
<div class="card"><form method="get"><div class="inline"><div class="field" style="flex:1"><label>عبارة البحث</label><input name="q" value="{{ $term }}" autofocus></div><button class="primary">بحث</button></div></form></div>
<div class="card">@forelse($results as $r)<div style="padding:12px 0;border-bottom:1px solid #eee"><strong>{{ $r->title }}</strong><div class="muted">{{ $r->subtitle }}</div>@if($r->route_name)<div style="margin-top:8px"><a class="btn" href="{{ route($r->route_name,$r->route_parameters ?? []) }}">فتح</a></div>@endif</div>@empty<div class="empty">{{ $term ? 'لا توجد نتائج في الفهرس.' : 'اكتب عبارة للبحث.' }}</div>@endforelse</div>
</div></body></html>