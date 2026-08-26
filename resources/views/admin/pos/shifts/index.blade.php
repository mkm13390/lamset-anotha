<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورديات الكاشير</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,"Segoe UI",sans-serif;background:#f4f5f7;color:#171717}
        a{text-decoration:none;color:inherit}
        button,input,select,textarea{font:inherit}
        .wrap{width:min(1180px,94%);margin:28px auto 60px}
        .top{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:16px}
        .back{color:#e21b23}.muted{color:#777}
        .grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        .card{background:#fff;border:1px solid #e2e4e7;border-radius:12px;padding:18px;margin-bottom:14px}
        .field{margin-bottom:11px}
        .field label{display:block;font-size:12px;color:#777;margin-bottom:5px}
        .field input,.field select,.field textarea{width:100%;padding:10px;border:1px solid #dfe2e6;border-radius:8px;background:#fff}
        .field textarea{min-height:76px}
        .btn{border:0;border-radius:8px;background:#e21b23;color:#fff;padding:11px 16px;cursor:pointer}
        .btn.dark{background:#111}.btn.success{background:#087443}
        .notice{padding:12px;border-radius:8px;margin-bottom:12px}
        .notice.ok{background:#d1fae5;color:#087443}
        .notice.error{background:#fee2e2;color:#b91c1c}
        .status{display:inline-block;padding:5px 9px;border-radius:999px;background:#ecfdf5;color:#087443;font-size:11px}
        .stat{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-top:12px}
        .stat div{border:1px solid #eee;border-radius:9px;padding:12px}
        .stat span{display:block;color:#777;font-size:11px}
        .stat strong{display:block;font-size:20px;margin-top:5px}
        .table-wrap{overflow:auto}
        table{width:100%;border-collapse:collapse}
        th,td{padding:10px;border-bottom:1px solid #eee;text-align:start;font-size:12px;white-space:nowrap}
        th{background:#f7f7f8;color:#777}
        @media(max-width:800px){.grid{grid-template-columns:1fr}.top{flex-direction:column;align-items:stretch}}
    </style>
</head>
<body>
<div class="wrap">

    <div class="top">
        <div>
            <a class="back" href="{{ route('admin.pos.index') }}">← العودة إلى نقطة البيع</a>
            <h1>ورديات الكاشير</h1>
            <div class="muted">فتح وإغلاق الوردية مع مطابقة النقد المتوقع والفعلي.</div>
        </div>
    </div>

    @if(session('success'))
        <div class="notice ok">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="notice error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid">

        <section class="card">
            @if(!$openShift)
                <h2>فتح وردية جديدة</h2>

                <form method="POST" action="{{ route('admin.pos.shifts.open') }}">
                    @csrf

                    <div class="field">
                        <label>الخزينة</label>
                        <select name="cash_register_id" required>
                            <option value="">اختر الخزينة</option>
                            @foreach($registers as $register)
                                <option value="{{ $register->id }}">
                                    {{ $register->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>الرصيد الافتتاحي</label>
                        <input
                            type="number"
                            name="opening_cash"
                            min="0"
                            step="0.001"
                            value="{{ old('opening_cash', 0) }}"
                            required
                        >
                    </div>

                    <div class="field">
                        <label>ملاحظات</label>
                        <textarea name="opening_notes">{{ old('opening_notes') }}</textarea>
                    </div>

                    <button class="btn success" type="submit">
                        فتح الوردية
                    </button>
                </form>
            @else
                <h2>الوردية الحالية</h2>

                <p>
                    <span class="status">مفتوحة</span>
                    {{ $openShift->cashRegister->name ?? 'خزينة' }}
                </p>

                <div class="stat">
                    <div>
                        <span>الرصيد الافتتاحي</span>
                        <strong>{{ number_format((float)$openShift->opening_cash,3) }}</strong>
                    </div>

                    <div>
                        <span>النقد المتوقع</span>
                        <strong>{{ number_format((float)$openShift->expected_cash,3) }}</strong>
                    </div>

                    <div>
                        <span>مبيعات نقدية</span>
                        <strong>{{ number_format((float)$openShift->cash_sales,3) }}</strong>
                    </div>

                    <div>
                        <span>مرتجعات نقدية</span>
                        <strong>{{ number_format((float)$openShift->cash_refunds,3) }}</strong>
                    </div>
                </div>

                <hr style="border:0;border-top:1px solid #eee;margin:16px 0">

                <form
                    method="POST"
                    action="{{ route('admin.pos.shifts.close', $openShift) }}"
                >
                    @csrf

                    <div class="field">
                        <label>النقد الفعلي عند الإغلاق</label>
                        <input
                            type="number"
                            name="closing_cash"
                            min="0"
                            step="0.001"
                            required
                        >
                    </div>

                    <div class="field">
                        <label>ملاحظات الإغلاق</label>
                        <textarea name="closing_notes"></textarea>
                    </div>

                    <button class="btn dark" type="submit">
                        إغلاق الوردية
                    </button>
                </form>
            @endif
        </section>

        <section class="card">
            <h2>سجل الورديات</h2>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>الخزينة</th>
                        <th>الكاشير</th>
                        <th>الحالة</th>
                        <th>افتتاح</th>
                        <th>إغلاق</th>
                        <th>الفرق</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($recentShifts as $shift)
                        <tr>
                            <td>{{ $shift->cashRegister->name ?? '—' }}</td>
                            <td>{{ $shift->user->name ?? '—' }}</td>
                            <td>{{ $shift->status }}</td>
                            <td>{{ number_format((float)$shift->opening_cash,3) }}</td>
                            <td>{{ $shift->closing_cash !== null ? number_format((float)$shift->closing_cash,3) : '—' }}</td>
                            <td>{{ $shift->difference_amount !== null ? number_format((float)$shift->difference_amount,3) : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:28px;color:#777">
                                لا توجد ورديات حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>
</body>
</html>
