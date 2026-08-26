<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $supplier->name }} | كشف حساب المورد</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --red:#e21b23;--bg:#f4f5f7;--card:#fff;--text:#171717;
            --muted:#777;--border:#e2e4e7;--green:#087443;--orange:#9a6700
        }
        body{font-family:Arial,"Segoe UI",sans-serif;background:var(--bg);color:var(--text)}
        a{color:inherit;text-decoration:none}
        button,input,select,textarea{font:inherit}
        .container{width:min(1180px,94%);margin:25px auto 60px}
        .top{display:flex;justify-content:space-between;align-items:center;gap:15px;margin-bottom:18px}
        .back{color:var(--red)}
        .card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:18px;margin-bottom:14px}
        .notice{padding:12px;border-radius:8px;background:#d1fae5;color:var(--green);margin-bottom:12px}
        .error{background:#fee2e2;color:#b91c1c}
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
        .stat{background:var(--card);border:1px solid var(--border);border-radius:11px;padding:16px}
        .stat span{display:block;color:var(--muted);font-size:12px}
        .stat strong{display:block;font-size:22px;margin-top:7px}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
        .info{padding:12px;background:var(--bg);border-radius:9px}
        .info span{display:block;color:var(--muted);font-size:11px}
        .info strong{display:block;margin-top:5px}
        table{width:100%;border-collapse:collapse}
        th,td{padding:11px;border-bottom:1px solid var(--border);font-size:12px;text-align:start;vertical-align:top}
        th{background:var(--bg);color:var(--muted)}
        .table-wrap{overflow:auto}
        .debit{color:#b91c1c;font-weight:700}
        .credit{color:var(--green);font-weight:700}
        .field{margin-bottom:10px}
        .field label{display:block;font-size:12px;color:var(--muted);margin-bottom:5px}
        .field input,.field select,.field textarea{width:100%;border:1px solid var(--border);border-radius:8px;background:#fff;padding:10px}
        .field textarea{min-height:80px;resize:vertical}
        .form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}
        .full{grid-column:1/-1}
        .btn{height:40px;padding:0 14px;border:0;border-radius:8px;background:var(--red);color:#fff;cursor:pointer}
        .section-title{margin-bottom:12px}
        @media(max-width:800px){.stats,.grid,.form-grid{grid-template-columns:1fr 1fr}}
        @media(max-width:560px){.stats,.grid,.form-grid{grid-template-columns:1fr}.top{flex-direction:column;align-items:stretch}}
    </style>
</head>
<body>
<div class="container">

    <div class="top">
        <div>
            <a class="back" href="{{ route('admin.suppliers.index') }}">← العودة للموردين</a>
            <h1 style="margin-top:8px">{{ $supplier->name }}</h1>
            <p style="color:var(--muted);margin-top:5px">
                {{ $supplier->company_name ?: 'كشف حساب المورد' }}
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="notice error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="stats">
        <div class="stat">
            <span>إجمالي المشتريات</span>
            <strong>{{ number_format((float)$stats['purchases_total'],3) }} ر.ع</strong>
        </div>
        <div class="stat">
            <span>إجمالي المدفوع</span>
            <strong>{{ number_format((float)$stats['paid_total'],3) }} ر.ع</strong>
        </div>
        <div class="stat">
            <span>الرصيد المستحق</span>
            <strong>{{ number_format((float)$stats['balance'],3) }} ر.ع</strong>
        </div>
        <div class="stat">
            <span>عدد أوامر الشراء</span>
            <strong>{{ $stats['purchase_count'] }}</strong>
        </div>
    </section>

    <section class="card" style="margin-top:14px">
        <h3 class="section-title">بيانات المورد</h3>
        <div class="grid">
            <div class="info"><span>الهاتف</span><strong>{{ $supplier->phone ?: '—' }}</strong></div>
            <div class="info"><span>واتساب</span><strong>{{ $supplier->whatsapp ?: '—' }}</strong></div>
            <div class="info"><span>البريد</span><strong>{{ $supplier->email ?: '—' }}</strong></div>
            <div class="info"><span>مدة السداد</span><strong>{{ $supplier->payment_terms_days }} يوم</strong></div>
        </div>
    </section>

    <section class="card">
        <h3 class="section-title">تسجيل دفعة جديدة</h3>

        <form method="POST" action="{{ route('admin.suppliers.payments.store', $supplier) }}">
            @csrf

            <div class="form-grid">
                <div class="field">
                    <label>أمر الشراء</label>
                    <select name="purchase_order_id">
                        <option value="">دفعة عامة للمورد</option>
                        @foreach($purchases->where('balance_due','>',0) as $purchase)
                            <option value="{{ $purchase->id }}">
                                {{ $purchase->purchase_number }}
                                — المتبقي {{ number_format((float)$purchase->balance_due,3) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>تاريخ الدفع</label>
                    <input name="payment_date" type="date" value="{{ now()->toDateString() }}" required>
                </div>

                <div class="field">
                    <label>المبلغ</label>
                    <input name="amount" type="number" min="0.001" step="0.001" required>
                </div>

                <div class="field">
                    <label>طريقة الدفع</label>
                    <select name="payment_method" required>
                        <option value="cash">نقدي</option>
                        <option value="card">بطاقة</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="cheque">شيك</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                <div class="field">
                    <label>المرجع</label>
                    <input name="reference" placeholder="رقم تحويل / شيك / مرجع">
                </div>

                <div class="field full">
                    <label>ملاحظات</label>
                    <textarea name="notes"></textarea>
                </div>
            </div>

            <button class="btn" type="submit">تسجيل الدفعة</button>
        </form>
    </section>

    <section class="card">
        <h3 class="section-title">كشف الحساب</h3>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>النوع</th>
                    <th>المرجع</th>
                    <th>مدين</th>
                    <th>دائن</th>
                    <th>الرصيد بعد الحركة</th>
                    <th>الوصف</th>
                </tr>
                </thead>
                <tbody>
                @forelse($transactions as $transaction)
                    @php
                        $types = [
                            'opening_balance' => 'رصيد افتتاحي',
                            'purchase' => 'شراء',
                            'payment' => 'دفعة',
                            'purchase_return' => 'مرتجع شراء',
                            'adjustment_debit' => 'تسوية مدينة',
                            'adjustment_credit' => 'تسوية دائنة',
                        ];
                    @endphp
                    <tr>
                        <td>{{ optional($transaction->transaction_date)->format('Y-m-d') }}</td>
                        <td>{{ $types[$transaction->type] ?? $transaction->type }}</td>
                        <td>{{ $transaction->reference ?: '—' }}</td>
                        <td class="debit">{{ (float)$transaction->debit > 0 ? number_format((float)$transaction->debit,3) : '—' }}</td>
                        <td class="credit">{{ (float)$transaction->credit > 0 ? number_format((float)$transaction->credit,3) : '—' }}</td>
                        <td><b>{{ number_format((float)$transaction->balance_after,3) }}</b></td>
                        <td>{{ $transaction->description ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:35px">لا توجد حركات في كشف الحساب حتى الآن.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card">
        <h3 class="section-title">الدفعات المسجلة</h3>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>رقم الدفعة</th>
                    <th>التاريخ</th>
                    <th>المبلغ</th>
                    <th>الطريقة</th>
                    <th>أمر الشراء</th>
                    <th>المرجع</th>
                </tr>
                </thead>
                <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_number }}</td>
                        <td>{{ optional($payment->payment_date)->format('Y-m-d') }}</td>
                        <td>{{ number_format((float)$payment->amount,3) }} ر.ع</td>
                        <td>{{ $payment->payment_method }}</td>
                        <td>{{ $payment->purchaseOrder->purchase_number ?? 'دفعة عامة' }}</td>
                        <td>{{ $payment->reference ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:35px">لا توجد دفعات مسجلة حتى الآن.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>
</body>
</html>