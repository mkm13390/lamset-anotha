<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $period->period_name }}</title>

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
            <h1>{{ $period->period_name }}</h1>
            <div class="muted">
                {{ optional($period->start_date)->format('Y-m-d') }}
                إلى
                {{ optional($period->end_date)->format('Y-m-d') }}
            </div>
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


    <div class="grid grid-4">
        <div class="card stat">
            <div class="kpi">الحالة</div>
            <b>{{ $period->status }}</b>
        </div>
        <div class="card stat">
            <div class="kpi">عدد الموظفين</div>
            <b>{{ $period->entries->count() }}</b>
        </div>
        <div class="card stat">
            <div class="kpi">إجمالي صافي الرواتب</div>
            <b>{{ number_format((float)$period->entries->sum('net_salary'),3) }} ر.ع</b>
        </div>
        <div class="card stat">
            <div class="kpi">إجمالي الاستقطاعات</div>
            <b>{{ number_format((float)$period->entries->sum(fn($e)=>(float)$e->deductions+(float)$e->advance_deduction),3) }} ر.ع</b>
        </div>
    </div>

    <div class="card">
        <div class="actions">
            @if(in_array($period->status,['draft','calculated']))
                <form method="post" action="{{ route('admin.finance.payroll.calculate',$period) }}">
                    @csrf
                    <button class="primary" type="submit">احتساب / إعادة احتساب</button>
                </form>
            @endif

            @if($period->status==='calculated')
                <form method="post" action="{{ route('admin.finance.payroll.approve',$period) }}">
                    @csrf
                    <button class="success-btn" type="submit">اعتماد المسير</button>
                </form>
            @endif
        </div>

        @if($period->status==='approved')
            <hr>
            @if($accounts->count())
            <form class="inline-form" method="post" action="{{ route('admin.finance.payroll.pay',$period) }}">
                @csrf
                <div class="field">
                    <label>حساب الصرف</label>
                    <select name="financial_account_id" required>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">
                                {{ $account->name }} · الرصيد {{ number_format((float)$account->current_balance,3) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>مرجع الدفع</label>
                    <input name="reference" placeholder="اختياري">
                </div>
                <button class="warning-btn" type="submit" onclick="return confirm('تأكيد صرف مسير الرواتب؟')">
                    صرف الرواتب
                </button>
            </form>
            @else
                <div class="errors">أنشئ حسابًا ماليًا نقديًا أو بنكيًا قبل صرف الرواتب.</div>
            @endif
        @endif
    </div>

    <div class="card">
        <h2 class="section-title">تفاصيل الرواتب</h2>
        <table>
            <thead>
                <tr>
                    <th>الموظف</th>
                    <th>أساسي</th>
                    <th>بدلات</th>
                    <th>مكافآت</th>
                    <th>عمولات</th>
                    <th>إضافي</th>
                    <th>خصومات</th>
                    <th>خصم سلفة</th>
                    <th>الإجمالي</th>
                    <th>الصافي</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
            @forelse($period->entries as $entry)
                <tr>
                    <td>
                        <strong>{{ $entry->employeeProfile?->user?->name }}</strong>
                        <div class="muted">{{ $entry->employeeProfile?->employee_code }}</div>
                    </td>
                    <td class="number">{{ number_format((float)$entry->basic_salary,3) }}</td>
                    <td class="number">{{ number_format((float)$entry->allowances,3) }}</td>
                    <td class="number">{{ number_format((float)$entry->bonuses,3) }}</td>
                    <td class="number">{{ number_format((float)$entry->commissions,3) }}</td>
                    <td class="number">{{ number_format((float)$entry->overtime,3) }}</td>
                    <td class="number">{{ number_format((float)$entry->deductions,3) }}</td>
                    <td class="number">{{ number_format((float)$entry->advance_deduction,3) }}</td>
                    <td class="number">{{ number_format((float)$entry->gross_salary,3) }}</td>
                    <td class="number"><strong>{{ number_format((float)$entry->net_salary,3) }}</strong></td>
                    <td><span class="badge">{{ $entry->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="11" class="empty">اضغط "احتساب" لإنشاء تفاصيل الرواتب.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2 class="section-title">معلومات الاعتماد</h2>
        <div class="grid grid-3">
            <div>
                <div class="kpi">أنشأها</div>
                <strong>{{ $period->creator?->name ?: '-' }}</strong>
            </div>
            <div>
                <div class="kpi">اعتمدها</div>
                <strong>{{ $period->approver?->name ?: '-' }}</strong>
            </div>
            <div>
                <div class="kpi">تاريخ الاعتماد</div>
                <strong>{{ optional($period->approved_at)->format('Y-m-d H:i') ?: '-' }}</strong>
            </div>
        </div>
    </div>
</div>
</body>
</html>
