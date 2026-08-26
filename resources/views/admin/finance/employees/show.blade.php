<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $employee->user?->name }} · الملف المالي</title>

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
            <h1>{{ $employee->user?->name }}</h1>
            <div class="muted">
                {{ $employee->employee_code }}
                @if($employee->job_title) · {{ $employee->job_title }} @endif
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
            <div class="kpi">الراتب الأساسي</div>
            <b>{{ number_format((float)$employee->basic_salary,3) }} ر.ع</b>
        </div>
        <div class="card stat">
            <div class="kpi">البدل الثابت</div>
            <b>{{ number_format((float)$employee->fixed_allowance,3) }} ر.ع</b>
        </div>
        <div class="card stat">
            <div class="kpi">السلف المتبقية</div>
            <b>{{ number_format((float)$employee->current_advance_balance,3) }} ر.ع</b>
        </div>
        <div class="card stat">
            <div class="kpi">الحالة</div>
            <b>{{ $employee->is_active ? 'مفعل' : 'متوقف' }}</b>
        </div>
    </div>

    <div class="grid grid-3">
        <div class="card">
            <h2 class="section-title">تعديل بيانات الموظف</h2>
            <form method="post" action="{{ route('admin.finance.employees.update',$employee) }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label>المسمى الوظيفي</label>
                    <input name="job_title" value="{{ old('job_title',$employee->job_title) }}">
                </div>

                <div class="field">
                    <label>تاريخ التعيين</label>
                    <input name="hire_date" type="date" value="{{ old('hire_date',optional($employee->hire_date)->format('Y-m-d')) }}">
                </div>

                <div class="field">
                    <label>الراتب الأساسي</label>
                    <input name="basic_salary" type="number" step="0.001" min="0" value="{{ old('basic_salary',$employee->basic_salary) }}" required>
                </div>

                <div class="field">
                    <label>بدل ثابت</label>
                    <input name="fixed_allowance" type="number" step="0.001" min="0" value="{{ old('fixed_allowance',$employee->fixed_allowance) }}">
                </div>

                <div class="field">
                    <label>نوع الراتب</label>
                    <select name="salary_type">
                        @foreach(['monthly'=>'شهري','daily'=>'يومي','hourly'=>'بالساعة'] as $key=>$label)
                            <option value="{{ $key }}" @selected($employee->salary_type===$key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="is_active" value="0">
                <div class="field">
                    <label>
                        <input style="width:auto" type="checkbox" name="is_active" value="1" @checked($employee->is_active)>
                        الموظف مفعل
                    </label>
                </div>

                <div class="field">
                    <label>ملاحظات</label>
                    <textarea name="notes">{{ old('notes',$employee->notes) }}</textarea>
                </div>

                <button class="primary" type="submit">حفظ التعديلات</button>
            </form>
        </div>

        <div class="card">
            <h2 class="section-title">إضافة سلفة</h2>
            <form method="post" action="{{ route('admin.finance.employees.advances.store',$employee) }}">
                @csrf
                <div class="field">
                    <label>المبلغ</label>
                    <input name="amount" type="number" step="0.001" min="0.001" required>
                </div>
                <div class="field">
                    <label>تاريخ السلفة</label>
                    <input name="advance_date" type="date" value="{{ now()->toDateString() }}" required>
                </div>
                <div class="field">
                    <label>أول تاريخ خصم</label>
                    <input name="first_deduction_date" type="date">
                </div>
                <div class="field">
                    <label>المرجع</label>
                    <input name="reference">
                </div>
                <div class="field">
                    <label>ملاحظات</label>
                    <textarea name="notes"></textarea>
                </div>
                <button class="warning-btn" type="submit">تسجيل السلفة</button>
            </form>
        </div>

        <div class="card">
            <h2 class="section-title">إضافة حركة على الموظف</h2>
            <form method="post" action="{{ route('admin.finance.employees.adjustments.store',$employee) }}">
                @csrf
                <div class="field">
                    <label>النوع</label>
                    <select name="type" required>
                        <option value="bonus">مكافأة</option>
                        <option value="deduction">خصم</option>
                        <option value="commission">عمولة</option>
                        <option value="overtime">إضافي</option>
                        <option value="allowance">بدل</option>
                        <option value="other_credit">إضافة أخرى</option>
                        <option value="other_debit">خصم آخر</option>
                    </select>
                </div>
                <div class="field">
                    <label>المبلغ</label>
                    <input name="amount" type="number" step="0.001" min="0.001" required>
                </div>
                <div class="field">
                    <label>تاريخ الاستحقاق</label>
                    <input name="effective_date" type="date" value="{{ now()->toDateString() }}" required>
                </div>
                <div class="field">
                    <label>الوصف</label>
                    <input name="description">
                </div>
                <div class="field">
                    <label>المرجع</label>
                    <input name="reference">
                </div>
                <label style="margin-bottom:12px">
                    <input style="width:auto" type="checkbox" name="is_recurring" value="1">
                    حركة متكررة
                </label>
                <button class="primary" type="submit">إضافة الحركة</button>
            </form>
        </div>
    </div>

    <div class="card">
        <h2 class="section-title">السلف</h2>
        <table>
            <thead><tr><th>التاريخ</th><th>المبلغ</th><th>المخصوم</th><th>المتبقي</th><th>الحالة</th><th>المرجع</th></tr></thead>
            <tbody>
            @forelse($employee->advances as $advance)
                <tr>
                    <td>{{ optional($advance->advance_date)->format('Y-m-d') }}</td>
                    <td class="number">{{ number_format((float)$advance->amount,3) }}</td>
                    <td class="number">{{ number_format((float)$advance->deducted_amount,3) }}</td>
                    <td class="number"><strong>{{ number_format((float)$advance->balance_amount,3) }}</strong></td>
                    <td><span class="badge">{{ $advance->status }}</span></td>
                    <td>{{ $advance->reference ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">لا توجد سلف.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2 class="section-title">المكافآت والخصومات والعمولات</h2>
        <table>
            <thead><tr><th>التاريخ</th><th>النوع</th><th>المبلغ</th><th>الوصف</th><th>تم ترحيلها؟</th></tr></thead>
            <tbody>
            @forelse($employee->adjustments as $adjustment)
                <tr>
                    <td>{{ optional($adjustment->effective_date)->format('Y-m-d') }}</td>
                    <td><span class="badge">{{ $adjustment->type }}</span></td>
                    <td class="number">{{ number_format((float)$adjustment->amount,3) }}</td>
                    <td>{{ $adjustment->description ?: '-' }}</td>
                    <td>{{ $adjustment->is_processed ? 'نعم' : 'لا' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">لا توجد حركات.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2 class="section-title">سجل الرواتب</h2>
        <table>
            <thead><tr><th>الفترة</th><th>الإجمالي</th><th>الصافي</th><th>الحالة</th><th>تاريخ الدفع</th></tr></thead>
            <tbody>
            @forelse($employee->payrollEntries as $entry)
                <tr>
                    <td>{{ $entry->payrollPeriod?->period_name }}</td>
                    <td class="number">{{ number_format((float)$entry->gross_salary,3) }}</td>
                    <td class="number"><strong>{{ number_format((float)$entry->net_salary,3) }}</strong></td>
                    <td><span class="badge">{{ $entry->status }}</span></td>
                    <td>{{ optional($entry->paid_at)->format('Y-m-d H:i') ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">لا توجد رواتب مسجلة.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
