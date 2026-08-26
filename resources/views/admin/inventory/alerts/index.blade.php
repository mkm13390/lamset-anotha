<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تنبيهات المخزون</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,"Segoe UI",sans-serif;background:#f4f5f7;color:#171717}
        a{text-decoration:none;color:inherit}
        button,select{font:inherit}
        .wrap{width:min(1180px,94%);margin:26px auto 60px}
        .top{margin-bottom:18px}.top h1{margin:7px 0}.back{color:#e21b23}.muted{color:#777}
        .stats{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:14px}
        .stat{background:#fff;border:1px solid #e3e5e8;border-radius:11px;padding:16px}
        .stat span{display:block;color:#777;font-size:12px}.stat strong{display:block;font-size:24px;margin-top:6px}
        .card{background:#fff;border:1px solid #e3e5e8;border-radius:12px;padding:18px;margin-bottom:14px}
        .filter{display:flex;gap:8px;align-items:center}
        .filter select{padding:10px;border:1px solid #dfe2e6;border-radius:8px;min-width:220px}
        .btn{border:0;border-radius:8px;background:#e21b23;color:#fff;padding:10px 14px;cursor:pointer}
        .table-wrap{overflow:auto}
        table{width:100%;border-collapse:collapse}
        th,td{padding:11px;border-bottom:1px solid #e6e7e9;text-align:start;font-size:12px;vertical-align:top}
        th{background:#f7f7f8;color:#6b7280}
        .danger{color:#b91c1c;font-weight:700}.warn{color:#9a6700;font-weight:700}.good{color:#087443;font-weight:700}
        .pill{display:inline-block;padding:5px 8px;border-radius:999px;background:#eef2ff;font-size:11px}
        @media(max-width:700px){.stats{grid-template-columns:1fr}.filter{flex-direction:column;align-items:stretch}.filter select{min-width:0}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <a class="back" href="{{ route('admin.inventory.index') }}">← العودة للمخزون</a>
        <h1>تنبيهات إعادة الطلب</h1>
        <div class="muted">الأصناف التي وصلت إلى حد إعادة الطلب أو نفدت حسب المخزن.</div>
    </div>

    <section class="stats">
        <div class="stat">
            <span>أصناف تحتاج إعادة طلب</span>
            <strong>{{ $reorderCount }}</strong>
        </div>

        <div class="stat">
            <span>أصناف نافدة</span>
            <strong>{{ $outOfStockCount }}</strong>
        </div>
    </section>

    <section class="card">
        <form class="filter" method="GET">
            <select name="warehouse_id">
                <option value="">كل المخازن</option>
                @foreach($warehouses as $warehouse)
                    <option
                        value="{{ $warehouse->id }}"
                        @selected((int)$warehouseId === (int)$warehouse->id)
                    >
                        {{ $warehouse->name }}
                    </option>
                @endforeach
            </select>

            <button class="btn" type="submit">تصفية</button>
        </form>
    </section>

    <section class="card">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>المخزن</th>
                    <th>المنتج</th>
                    <th>SKU</th>
                    <th>الإجمالي</th>
                    <th>المحجوز</th>
                    <th>التالف</th>
                    <th>العينات</th>
                    <th>المتاح</th>
                    <th>حد إعادة الطلب</th>
                    <th>كمية إعادة الطلب</th>
                    <th>الحالة</th>
                </tr>
                </thead>
                <tbody>
                @forelse($alerts as $stock)
                    @php
                        $available = $stock->available_quantity;
                        $isOut = $available <= 0;
                    @endphp

                    <tr>
                        <td><span class="pill">{{ $stock->warehouse->name ?? '—' }}</span></td>

                        <td>
                            <strong>{{ $stock->variant->product->name_ar ?? 'منتج' }}</strong>
                            <div class="muted">
                                {{ $stock->variant->color_name_ar ?? '' }}
                                {{ $stock->variant->size ? ' / '.$stock->variant->size : '' }}
                            </div>
                        </td>

                        <td>{{ $stock->variant->sku ?? '—' }}</td>
                        <td>{{ $stock->quantity }}</td>
                        <td>{{ $stock->reserved_quantity }}</td>
                        <td>{{ $stock->damaged_quantity }}</td>
                        <td>{{ $stock->sample_quantity }}</td>
                        <td class="{{ $isOut ? 'danger' : 'warn' }}">{{ $available }}</td>
                        <td>{{ $stock->reorder_level }}</td>
                        <td>{{ $stock->reorder_quantity }}</td>

                        <td>
                            @if($isOut)
                                <span class="danger">نافد</span>
                            @else
                                <span class="warn">إعادة طلب</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align:center;padding:35px;color:#777">
                            لا توجد تنبيهات إعادة طلب حاليًا.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:12px">{{ $alerts->links() }}</div>
    </section>
</div>
</body>
</html>
