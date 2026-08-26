<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>مسير الرواتب</title>

<style>
*{box-sizing:border-box}
:root{
    --bg:#f5f6f8;
    --card:#fff;
    --text:#1d2433;
    --muted:#6b7280;
    --line:#e2e6ed;
    --soft:#fafbfc;
    --primary:#1f2937;
    --success:#166534;
    --danger:#991b1b;
    --warning:#92400e;
}
body{margin:0;font-family:Tahoma,Arial,sans-serif;background:var(--bg);color:var(--text)}
a{text-decoration:none;color:inherit}
.wrap{max-width:1450px;margin:auto;padding:24px}
.top{display:flex;justify-content:space-between;gap:16px;align-items:center;margin-bottom:20px;flex-wrap:wrap}
.top h1{margin:0;font-size:26px}
.nav{display:flex;gap:8px;flex-wrap:wrap}
.btn,button{border:0;border-radius:10px;padding:10px 14px;cursor:pointer;font-weight:700;font-family:inherit}
.btn{background:#fff;border:1px solid var(--line)}
.primary{background:var(--primary);color:#fff}
.success-btn{background:var(--success);color:#fff}
.danger-btn{background:var(--danger);color:#fff}
.warning-btn{background:var(--warning);color:#fff}
.card{background:var(--card);border:1px solid var(--line);border-radius:16px;padding:16px;margin-bottom:16px}
.grid{display:grid;gap:14px}
.grid-4{grid-template-columns:repeat(4,minmax(0,1fr))}
.grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}
.grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}
.stat b{display:block;font-size:23px;margin-top:6px}
.muted{color:var(--muted);font-size:13px}
label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}
input,select,textarea{width:100%;border:1px solid #d8dde6;border-radius:10px;padding:10px;background:#fff;font-family:inherit}
textarea{min-height:80px;resize:vertical}
.field{margin-bottom:12px}
table{width:100%;border-collapse:collapse}
th,td{padding:11px 9px;border-bottom:1px solid #edf0f4;text-align:right;font-size:14px;vertical-align:top}
th{background:#fafbfc}
.badge{display:inline-block;padding:5px 9px;border-radius:999px;background:#eef2ff;font-size:12px}
.badge.good{background:#ecfdf5;color:#166534}
.badge.bad{background:#fef2f2;color:#991b1b}
.badge.warn{background:#fffbeb;color:#92400e}
.flash{padding:12px 14px;border-radius:12px;margin-bottom:14px;background:#ecfdf5;color:#166534}
.errors{padding:12px 14px;border-radius:12px;margin-bottom:14px;background:#fef2f2;color:#991b1b}
.section-title{margin:0 0 14px;font-size:18px}
.actions{display:flex;gap:8px;flex-wrap:wrap;align-items:end}
.empty{text-align:center;padding:30px;color:var(--muted)}
.kpi{font-size:12px;color:var(--muted);margin-bottom:5px}
.number{font-variant-numeric:tabular-nums;white-space:nowrap}
hr{border:0;border-top:1px solid var(--line);margin:16px 0}
.pagination{margin-top:14px}
.inline-form{display:flex;gap:8px;align-items:end;flex-wrap:wrap}
.inline-form .field{margin:0;min-width:170px;flex:1}
@media(max-width:900px){
    .grid-4,.grid-3,.grid-2{grid-template-columns:1fr}
    .wrap{padding:14px}
    table{display:block;overflow-x:auto;white-space:nowrap}
}
</style>

</head>
<body>
<div class="wrap">
    <div class="top">
        <div>
            <h1>مسير الرواتب</h1>
            <div class="muted">إنشاء الفترات، الاحتساب، الاعتماد والصرف</div>
        </div>
        
<div class="nav">
    <a class="btn" href="{{ route('admin.finance.index') }}">المالية</a>
    <a class="btn" href="{{ route('admin.finance.employees.index') }}">الموظفون</a>
    <a class="btn" href="{{ route('admin.finance.payroll.index') }}">مسير الرواتب</a>
</div>

    </div>

    
@if(session('success'))
    <div class="flash">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="errors">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif


    <div class="grid grid-2">
        <div class="card">
            <h2 class="section-title">فترة رواتب جديدة</h2>
            <form method="post" action="{{ route('admin.finance.payroll.store') }}">
                @csrf
                <div class="grid grid-2">
                    <div class="field">
                        <label>اسم الفترة</label>
                        <input name="period_name" placeholder="مثال: أغسطس 2026" required>
                    </div>
                    <div class="field">
                        <label>تاريخ الدفع المتوقع</label>
                        <input name="payment_date" type="date">
                    </div>
                    <div class="field">
                        <label>من</label>
                        <input name="start_date" type="date" required>
                    </div>
                    <div class="field">
                        <label>إلى</label>
                        <input name="end_date" type="date" required>
                    </div>
                </div>
                <div class="field">
                    <label>ملاحظات</label>
                    <textarea name="notes"></textarea>
                </div>
                <button class="primary" type="submit">إنشاء الفترة</button>
            </form>
        </div>

        <div class="card">
            <h2 class="section-title">تسلسل العمل</h2>
            <div class="muted" style="line-height:2">
                1. إنشاء الفترة<br>
                2. احتساب الرواتب<br>
                3. مراجعة التفاصيل<br>
                4. اعتماد المسير<br>
                5. اختيار الحساب المالي وصرف الرواتب
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="section-title">الفترات</h2>
        <table>
            <thead>
                <tr>
                    <th>الفترة</th>
                    <th>من</th>
                    <th>إلى</th>
                    <th>الموظفون</th>
                    <th>الحالة</th>
                    <th>تاريخ الدفع</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($periods as $period)
                <tr>
                    <td><strong>{{ $period->period_name }}</strong></td>
                    <td>{{ optional($period->start_date)->format('Y-m-d') }}</td>
                    <td>{{ optional($period->end_date)->format('Y-m-d') }}</td>
                    <td>{{ $period->entries_count }}</td>
                    <td><span class="badge">{{ $period->status }}</span></td>
                    <td>{{ optional($period->payment_date)->format('Y-m-d') ?: '-' }}</td>
                    <td><a class="btn" href="{{ route('admin.finance.payroll.show',$period) }}">فتح</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">لا توجد فترات رواتب.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $periods->links() }}</div>
    </div>
</div>
</body>
</html>
