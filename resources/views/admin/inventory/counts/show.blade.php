<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $stockCount->count_number }}</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,"Segoe UI",sans-serif;background:#f4f5f7;color:#171717}
        a{text-decoration:none;color:inherit}
        button,input{font:inherit}
        .wrap{width:min(1180px,94%);margin:26px auto 60px}
        .back{color:#e21b23}.muted{color:#777}.top h1{margin:8px 0}
        .card{background:#fff;border:1px solid #e3e5e8;border-radius:12px;padding:18px;margin-bottom:14px}
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
        .stat{background:#fff;border:1px solid #e3e5e8;border-radius:11px;padding:14px}
        .stat span{display:block;color:#777;font-size:11px}.stat strong{display:block;margin-top:6px}
        table{width:100%;border-collapse:collapse}th,td{padding:11px;border-bottom:1px solid #e6e7e9;text-align:start;font-size:12px}
        th{background:#f7f7f8;color:#6b7280}.table-wrap{overflow:auto}
        .qty{width:110px;padding:8px;border:1px solid #dfe2e6;border-radius:7px}
        .btn{border:0;border-radius:8px;background:#e21b23;color:#fff;padding:11px 16px;cursor:pointer}
        .done{background:#d1fae5;color:#087443;padding:12px;border-radius:9px}
        @media(max-width:700px){.stats{grid-template-columns:1fr 1fr}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <a class="back" href="{{ route('admin.inventory.counts.index') }}">← العودة للجرد</a>
        <h1>{{ $stockCount->count_number }}</h1>
        <div class="muted">{{ $stockCount->warehouse->name ?? '—' }}</div>
    </div>

    <section class="stats">
        <div class="stat"><span>نوع الجرد</span><strong>{{ $stockCount->type === 'full' ? 'شامل' : 'جزئي' }}</strong></div>
        <div class="stat"><span>الحالة</span><strong>{{ $stockCount->status }}</strong></div>
        <div class="stat"><span>التاريخ</span><strong>{{ optional($stockCount->count_date)->format('Y-m-d') }}</strong></div>
        <div class="stat"><span>عدد الأصناف</span><strong>{{ $stockCount->items->count() }}</strong></div>
    </section>

    <section class="card" style="margin-top:14px">
        @if($stockCount->status === 'completed')
            <div class="done">تم اعتماد هذا الجرد وتحديث كميات المخزون.</div>
        @else
            <form method="POST" action="{{ route('admin.inventory.counts.complete', $stockCount) }}">
                @csrf
                @method('PATCH')

                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>SKU</th>
                            <th>كمية النظام</th>
                            <th>الكمية الفعلية</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($stockCount->items as $item)
                            <tr>
                                <td>
                                    {{ $item->variant->product->name_ar ?? 'منتج' }}
                                    {{ $item->variant->color_name_ar ? ' - '.$item->variant->color_name_ar : '' }}
                                    {{ $item->variant->size ? ' - '.$item->variant->size : '' }}
                                </td>
                                <td>{{ $item->variant->sku ?? '—' }}</td>
                                <td>{{ $item->system_quantity }}</td>
                                <td>
                                    <input
                                        class="qty"
                                        type="number"
                                        min="0"
                                        name="counted_quantities[{{ $item->id }}]"
                                        value="{{ old('counted_quantities.'.$item->id, $item->system_quantity) }}"
                                        required
                                    >
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <button class="btn" type="submit" style="margin-top:14px">
                    اعتماد الجرد وتحديث المخزون
                </button>
            </form>
        @endif
    </section>
</div>
</body>
</html>
