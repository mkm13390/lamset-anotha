<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $purchase->purchase_number }} | لمسة أنوثة</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{--red:#e21b23;--bg:#f4f5f7;--card:#fff;--text:#171717;--muted:#777;--border:#e2e4e7}
        body{font-family:Arial,"Segoe UI",sans-serif;background:var(--bg);color:var(--text)}
        a{color:inherit;text-decoration:none}
        button,input,select{font:inherit}
        .container{width:min(1100px,94%);margin:25px auto 60px}
        .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
        .card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:18px;margin-bottom:14px}
        .notice{padding:12px;border-radius:8px;background:#d1fae5;color:#087443;margin-bottom:12px}
        .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
        .info{padding:12px;background:var(--bg);border-radius:9px}.info span{display:block;color:var(--muted);font-size:11px}.info strong{display:block;margin-top:5px}
        table{width:100%;border-collapse:collapse}th,td{padding:11px;border-bottom:1px solid var(--border);font-size:12px;text-align:start}
        th{background:var(--bg);color:var(--muted)}
        .btn{height:38px;padding:0 12px;border:0;border-radius:8px;background:var(--red);color:#fff;cursor:pointer}
        .field{margin-bottom:10px}.field label{display:block;font-size:12px;color:var(--muted);margin-bottom:5px}.field input,.field select{width:100%;height:40px;border:1px solid var(--border);border-radius:8px;padding:0 10px}
        .receive-grid{display:grid;grid-template-columns:1fr 140px;gap:8px;align-items:center}
        @media(max-width:700px){.grid{grid-template-columns:1fr 1fr}.top{flex-direction:column;align-items:stretch;gap:10px}}
    </style>
</head>
<body>
<div class="container">
    <div class="top">
        <div>
            <a href="{{ route('admin.purchases.index') }}" style="color:var(--red)">← العودة للمشتريات</a>
            <h1 style="margin-top:8px">{{ $purchase->purchase_number }}</h1>
        </div>
    </div>

    @if(session('success'))<div class="notice">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="card" style="color:#b91c1c">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

    <section class="card">
        <div class="grid">
            <div class="info"><span>المورد</span><strong>{{ $purchase->supplier->name ?? '—' }}</strong></div>
            <div class="info"><span>تاريخ الأمر</span><strong>{{ optional($purchase->order_date)->format('Y-m-d') }}</strong></div>
            <div class="info"><span>الحالة</span><strong>{{ $purchase->status }}</strong></div>
            <div class="info"><span>حالة الدفع</span><strong>{{ $purchase->payment_status }}</strong></div>
            <div class="info"><span>الإجمالي</span><strong>{{ number_format((float)$purchase->total,3) }} {{ $purchase->currency }}</strong></div>
            <div class="info"><span>المدفوع</span><strong>{{ number_format((float)$purchase->paid_amount,3) }}</strong></div>
            <div class="info"><span>المتبقي</span><strong>{{ number_format((float)$purchase->balance_due,3) }}</strong></div>
            <div class="info"><span>فاتورة المورد</span><strong>{{ $purchase->supplier_invoice_number ?: '—' }}</strong></div>
        </div>
    </section>

    <section class="card">
        <h3 style="margin-bottom:12px">الأصناف</h3>
        <div style="overflow:auto">
            <table>
                <thead><tr><th>الصنف</th><th>SKU</th><th>المطلوب</th><th>المستلم</th><th>التكلفة</th><th>الإجمالي</th></tr></thead>
                <tbody>
                @foreach($purchase->items as $item)
                    <tr>
                        <td>{{ $item->product_name_ar }}</td>
                        <td>{{ $item->sku ?: '—' }}</td>
                        <td>{{ $item->quantity_ordered }}</td>
                        <td>{{ $item->quantity_received }}</td>
                        <td>{{ number_format((float)$item->unit_cost,3) }}</td>
                        <td>{{ number_format((float)$item->line_total,3) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @if(!in_array($purchase->status,['received','cancelled'],true))
    <section class="card">
        <h3 style="margin-bottom:12px">استلام بضاعة</h3>
        <form method="POST" action="{{ route('admin.purchases.receive',$purchase) }}">
            @csrf @method('PATCH')
            @foreach($purchase->items as $item)
                @php($remaining=(int)$item->quantity_ordered-(int)$item->quantity_received)
                <div class="receive-grid">
                    <div>{{ $item->product_name_ar }} <small style="color:var(--muted)">المتبقي: {{ $remaining }}</small></div>
                    <input type="number" min="0" max="{{ $remaining }}" name="items[{{ $item->id }}][quantity_received]" value="0">
                </div>
                <br>
            @endforeach
            <button class="btn" type="submit">تسجيل الاستلام وتحديث المخزون</button>
        </form>
    </section>
    @endif

    <section class="card">
        <h3 style="margin-bottom:12px">تحديث الدفع</h3>
        <form method="POST" action="{{ route('admin.purchases.payment',$purchase) }}">
            @csrf @method('PATCH')
            <div class="field"><label>إجمالي المبلغ المدفوع حتى الآن</label><input name="paid_amount" type="number" min="0" max="{{ $purchase->total }}" step="0.001" value="{{ $purchase->paid_amount }}" required></div>
            <button class="btn" type="submit">تحديث الدفع</button>
        </form>
    </section>

    @if(!in_array($purchase->status,['partially_received','received'],true))
    <section class="card">
        <h3 style="margin-bottom:12px">تحديث الحالة</h3>
        <form method="POST" action="{{ route('admin.purchases.status',$purchase) }}">
            @csrf @method('PATCH')
            <div class="field"><label>الحالة</label>
                <select name="status">
                    <option value="draft" @selected($purchase->status==='draft')>مسودة</option>
                    <option value="ordered" @selected($purchase->status==='ordered')>مطلوب</option>
                    <option value="cancelled" @selected($purchase->status==='cancelled')>ملغى</option>
                </select>
            </div>
            <button class="btn" type="submit">حفظ الحالة</button>
        </form>
    </section>
    @endif
</div>
</body>
</html>