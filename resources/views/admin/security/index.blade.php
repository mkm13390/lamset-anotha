<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>الأمان والصلاحيات</title>
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
<div class="top"><div><h1>الأمان والصلاحيات</h1><div class="muted">صلاحيات دقيقة، Feature Flags وسجلات النشاط</div></div>
<div class="nav">
<a class="btn" href="{{ route('admin.security.index') }}">الأمان</a>
<a class="btn" href="{{ route('admin.approvals.index') }}">الموافقات</a>
<a class="btn" href="{{ route('admin.health.index') }}">صحة النظام</a>
<a class="btn" href="{{ route('admin.report-center.index') }}">مركز التقارير</a>
<a class="btn" href="{{ route('admin.search.index') }}">البحث</a>
<a class="btn" href="{{ route('admin.backups.index') }}">النسخ الاحتياطية</a>
</div>
</div>
@if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
@if($errors->any())<div class="errors">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

<div class="grid g2">
<div class="card"><h2 class="section-title">صلاحيات مستخدم</h2>
<form method="get"><div class="inline"><div class="field" style="min-width:280px"><label>المستخدم</label><select name="user_id"><option value="">اختر</option>@foreach($users as $u)<option value="{{ $u->id }}" @selected($selectedUser?->id===$u->id)>{{ $u->name }} · {{ $u->role }}</option>@endforeach</select></div><button class="primary">عرض</button></div></form>
@if($selectedUser)
<table><thead><tr><th>الصلاحية</th><th>المجموعة</th><th>الحالة الحالية</th><th>تجاوز مخصص</th></tr></thead><tbody>
@foreach($selectedUserPermissions as $p)<tr><td>{{ $p['name'] }}<div class="muted">{{ $p['code'] }}</div></td><td>{{ $p['group_name'] }}</td><td><span class="badge">{{ $p['allowed'] ? 'مسموح' : 'غير مسموح' }}</span></td><td>
<form method="post" action="{{ route('admin.security.user-permissions.update',[$selectedUser,$p['id']]) }}">@csrf
<div class="inline"><select name="effect"><option value="inherit">حسب الدور</option><option value="allow">سماح</option><option value="deny">منع</option></select><input style="max-width:170px" type="datetime-local" name="expires_at"><button>حفظ</button></div></form>
</td></tr>@endforeach
</tbody></table>@endif
</div>

<div class="card"><h2 class="section-title">Feature Flags</h2>
@foreach($flags as $flag)<form method="post" action="{{ route('admin.security.features.update',$flag) }}" style="border-bottom:1px solid #eee;padding:10px 0">@csrf
<div><strong>{{ $flag->name }}</strong><div class="muted">{{ $flag->description }}</div></div>
<div class="inline" style="margin-top:8px"><select name="is_enabled"><option value="1" @selected($flag->is_enabled)>مفعل</option><option value="0" @selected(!$flag->is_enabled)>متوقف</option></select><select name="scope"><option value="global" @selected($flag->scope==='global')>global</option><option value="role" @selected($flag->scope==='role')>role</option><option value="user" @selected($flag->scope==='user')>user</option><option value="branch" @selected($flag->scope==='branch')>branch</option></select><button>تحديث</button></div>
</form>@endforeach
</div></div>

<div class="grid g2">
<div class="card"><h2 class="section-title">آخر الأنشطة</h2><table><thead><tr><th>المستخدم</th><th>الإجراء</th><th>الوحدة</th><th>الوقت</th></tr></thead><tbody>
@forelse($recentActivity as $a)<tr><td>{{ $a->user?->name ?: '-' }}</td><td>{{ $a->action }}</td><td>{{ $a->module ?: '-' }}</td><td>{{ optional($a->occurred_at)->format('Y-m-d H:i') }}</td></tr>@empty<tr><td colspan="4" class="empty">لا يوجد سجل نشاط بعد.</td></tr>@endforelse
</tbody></table></div>
<div class="card"><h2 class="section-title">سجل الدخول</h2><table><thead><tr><th>المستخدم</th><th>الحدث</th><th>IP</th><th>مشبوه؟</th><th>الوقت</th></tr></thead><tbody>
@forelse($recentLogins as $l)<tr><td>{{ $l->user?->name ?: $l->email_or_identifier ?: '-' }}</td><td>{{ $l->event_type }}</td><td>{{ $l->ip_address ?: '-' }}</td><td>{{ $l->is_suspicious ? 'نعم' : 'لا' }}</td><td>{{ optional($l->occurred_at)->format('Y-m-d H:i') }}</td></tr>@empty<tr><td colspan="5" class="empty">لا يوجد سجل دخول بعد.</td></tr>@endforelse
</tbody></table></div></div>
</div></body></html>