<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>المالية</title>

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
            <h1>المالية</h1>
            <div class="muted">الحسابات، الإيرادات، المصروفات والحركات المالية</div>
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


    <div class="grid grid-3">
        <div class="card stat">
            <div class="kpi">إجمالي أرصدة الحسابات</div>
            <b>{{ number_format($stats['total_balance'],3) }} ر.ع</b>
        </div>
        <div class="card stat">
            <div class="kpi">إيرادات هذا الشهر</div>
            <b>{{ number_format($stats['income_month'],3) }} ر.ع</b>
        </div>
        <div class="card stat">
            <div class="kpi">مصروفات هذا الشهر</div>
            <b>{{ number_format($stats['expense_month'],3) }} ر.ع</b>
        </div>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <h2 class="section-title">إنشاء حساب مالي</h2>
            <form method="post" action="{{ route('admin.finance.accounts.store') }}">
                @csrf
                <div class="grid grid-2">
                    <div class="field">
                        <label>اسم الحساب</label>
                        <input name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="field">
                        <label>الكود</label>
                        <input name="code" value="{{ old('code') }}" required>
                    </div>
                    <div class="field">
                        <label>النوع</label>
                        <select name="type" required>
                            <option value="cash">نقدي</option>
                            <option value="bank">بنك</option>
                            <option value="card_clearing">تسوية بطاقات</option>
                            <option value="income">إيرادات</option>
                            <option value="expense">مصروفات</option>
                            <option value="liability">التزامات</option>
                            <option value="asset">أصول</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>الرصيد الافتتاحي</label>
                        <input name="opening_balance" type="number" step="0.001" value="{{ old('opening_balance',0) }}">
                    </div>
                    <div class="field">
                        <label>العملة</label>
                        <input name="currency" maxlength="3" value="{{ old('currency','OMR') }}">
                    </div>
                </div>
                <div class="field">
                    <label>ملاحظات</label>
                    <textarea name="notes">{{ old('notes') }}</textarea>
                </div>
                <button class="primary" type="submit">حفظ الحساب</button>
            </form>
        </div>

        <div class="card">
            <h2 class="section-title">تسجيل حركة مالية</h2>
            @if($accounts->count())
            <form method="post" action="{{ route('admin.finance.transactions.store') }}">
                @csrf
                <div class="grid grid-2">
                    <div class="field">
                        <label>الحساب</label>
                        <select name="financial_account_id" required>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->name }} · {{ number_format((float)$account->current_balance,3) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>نوع الحركة</label>
                        <select name="transaction_type" required>
                            <option value="income">إيراد</option>
                            <option value="expense">مصروف</option>
                            <option value="advance_repayment">سداد سلفة</option>
                            <option value="transfer_in">تحويل داخل</option>
                            <option value="transfer_out">تحويل خارج</option>
                            <option value="adjustment_in">تسوية إضافة</option>
                            <option value="adjustment_out">تسوية خصم</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>المبلغ</label>
                        <input name="amount" type="number" step="0.001" min="0.001" required>
                    </div>
                    <div class="field">
                        <label>التاريخ</label>
                        <input name="transaction_date" type="date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="field">
                        <label>التصنيف</label>
                        <input name="category">
                    </div>
                    <div class="field">
                        <label>المرجع</label>
                        <input name="reference">
                    </div>
                </div>
                <div class="field">
                    <label>الوصف</label>
                    <input name="description">
                </div>
                <div class="field">
                    <label>ملاحظات</label>
                    <textarea name="notes"></textarea>
                </div>
                <button class="primary" type="submit">تسجيل الحركة</button>
            </form>
            @else
                <div class="empty">أنشئ حسابًا ماليًا أولًا.</div>
            @endif
        </div>
    </div>

    <div class="card">
        <h2 class="section-title">الحسابات</h2>
        <table>
            <thead>
                <tr>
                    <th>الكود</th>
                    <th>الحساب</th>
                    <th>النوع</th>
                    <th>الرصيد الافتتاحي</th>
                    <th>الرصيد الحالي</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
            @forelse($accounts as $account)
                <tr>
                    <td>{{ $account->code }}</td>
                    <td>{{ $account->name }}</td>
                    <td>{{ $account->type }}</td>
                    <td class="number">{{ number_format((float)$account->opening_balance,3) }} {{ $account->currency }}</td>
                    <td class="number"><strong>{{ number_format((float)$account->current_balance,3) }} {{ $account->currency }}</strong></td>
                    <td><span class="badge {{ $account->is_active ? 'good' : 'bad' }}">{{ $account->is_active ? 'مفعل' : 'متوقف' }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">لا توجد حسابات مالية حتى الآن.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="top" style="margin-bottom:12px">
            <div>
                <h2 class="section-title" style="margin:0">سجل الحركات</h2>
                <div class="muted">فلترة حسب الحساب والنوع والتاريخ</div>
            </div>
        </div>

        <form method="get" class="inline-form" style="margin-bottom:14px">
            <div class="field">
                <label>الحساب</label>
                <select name="account_id">
                    <option value="">الكل</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" @selected(request('account_id')==$account->id)>
                            {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>النوع</label>
                <select name="type">
                    <option value="">الكل</option>
                    @foreach(['income','expense','salary','advance','advance_repayment','transfer_in','transfer_out','adjustment_in','adjustment_out'] as $type)
                        <option value="{{ $type }}" @selected(request('type')===$type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>من</label>
                <input type="date" name="from" value="{{ request('from') }}">
            </div>
            <div class="field">
                <label>إلى</label>
                <input type="date" name="to" value="{{ request('to') }}">
            </div>
            <button class="primary" type="submit">تطبيق</button>
            <a class="btn" href="{{ route('admin.finance.index') }}">مسح</a>
        </form>

        <table>
            <thead>
                <tr>
                    <th>الرقم</th>
                    <th>التاريخ</th>
                    <th>الحساب</th>
                    <th>النوع</th>
                    <th>المبلغ</th>
                    <th>التصنيف</th>
                    <th>المرجع</th>
                    <th>بواسطة</th>
                </tr>
            </thead>
            <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td>{{ $tx->transaction_number }}</td>
                    <td>{{ optional($tx->transaction_date)->format('Y-m-d') }}</td>
                    <td>{{ $tx->account?->name }}</td>
                    <td><span class="badge">{{ $tx->transaction_type }}</span></td>
                    <td class="number"><strong>{{ number_format((float)$tx->amount,3) }}</strong></td>
                    <td>{{ $tx->category ?: '-' }}</td>
                    <td>{{ $tx->reference ?: '-' }}</td>
                    <td>{{ $tx->creator?->name ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty">لا توجد حركات مالية.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="pagination">{{ $transactions->links() }}</div>
    </div>
</div>
</body>
</html>
