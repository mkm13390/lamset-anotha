<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إدارة المخزون المتقدم</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,"Segoe UI",sans-serif;background:#f4f5f7;color:#171717}
        a{text-decoration:none;color:inherit}
        button,input,select,textarea{font:inherit}
        .wrap{width:min(1280px,96%);margin:26px auto 60px}
        .top{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:16px}
        .top h1{margin:7px 0}.back{color:#e21b23}.muted{color:#777}
        .card{background:#fff;border:1px solid #e3e5e8;border-radius:12px;padding:16px;margin-bottom:14px}
        .notice{padding:12px;border-radius:9px;margin-bottom:12px;background:#d1fae5;color:#087443}
        .error{background:#fee2e2;color:#b91c1c}
        .filters{display:grid;grid-template-columns:1fr 1fr auto;gap:8px}
        .filters input,.filters select{padding:10px;border:1px solid #dfe2e6;border-radius:8px;background:#fff}
        .btn{border:0;border-radius:8px;background:#e21b23;color:#fff;padding:10px 14px;cursor:pointer}
        .btn.dark{background:#111}
        .table-wrap{overflow:auto}
        table{width:100%;border-collapse:collapse;min-width:1180px}
        th,td{padding:10px;border-bottom:1px solid #e6e7e9;text-align:start;font-size:12px;vertical-align:top}
        th{background:#f7f7f8;color:#6b7280}
        .pill{display:inline-block;padding:4px 7px;border-radius:999px;background:#eef2ff}
        .danger{color:#b91c1c;font-weight:700}
        .warn{color:#9a6700;font-weight:700}
        .good{color:#087443;font-weight:700}
        .mini-form{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px}
        .mini-form input,.mini-form select{padding:7px;border:1px solid #dfe2e6;border-radius:7px;max-width:120px}
        .mini-btn{border:0;border-radius:7px;padding:7px 9px;background:#111;color:#fff;cursor:pointer}
        .settings{display:grid;grid-template-columns:90px 90px auto;gap:6px}
        @media(max-width:800px){.filters{grid-template-columns:1fr}.top{flex-direction:column;align-items:stretch}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <div>
            <a class="back" href="{{ route('admin.inventory.index') }}">← العودة للمخزون</a>
            <h1>إدارة المخزون المتقدم</h1>
            <div class="muted">المتاح والمحجوز والتالف والعينات وإعادة الطلب لكل مخزن.</div>
        </div>

        <a class="btn dark" href="{{ route('admin.inventory.warehouses.index') }}">
            إدارة المخازن
        </a>
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

    <section class="card">
        <form class="filters" method="GET">
            <input
                name="search"
                value="{{ $search }}"
                placeholder="بحث بالمنتج أو SKU أو اللون أو المقاس"
            >

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

            <button class="btn" type="submit">بحث</button>
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
                    <th>إعادة الطلب</th>
                    <th>تعديل الحالة</th>
                </tr>
                </thead>
                <tbody>
                @forelse($stocks as $stock)
                    @php
                        $available = $stock->available_quantity;
                        $needsReorder =
                            (int)$stock->reorder_level > 0
                            && $available <= (int)$stock->reorder_level;
                    @endphp

                    <tr>
                        <td>
                            <span class="pill">{{ $stock->warehouse->name ?? '—' }}</span>
                        </td>

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

                        <td class="{{ $available <= 0 ? 'danger' : ($needsReorder ? 'warn' : 'good') }}">
                            {{ $available }}
                            @if($needsReorder)
                                <div>يحتاج إعادة طلب</div>
                            @endif
                        </td>

                        <td>
                            <form
                                class="settings"
                                method="POST"
                                action="{{ route('admin.inventory.stock.settings', $stock) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <input
                                    type="number"
                                    name="reorder_level"
                                    min="0"
                                    value="{{ $stock->reorder_level }}"
                                    title="حد إعادة الطلب"
                                >

                                <input
                                    type="number"
                                    name="reorder_quantity"
                                    min="0"
                                    value="{{ $stock->reorder_quantity }}"
                                    title="كمية إعادة الطلب"
                                >

                                <button class="mini-btn" type="submit">حفظ</button>
                            </form>
                        </td>

                        <td>
                            <form
                                class="mini-form"
                                method="POST"
                                action="{{ route('admin.inventory.stock.special', $stock) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <select name="bucket" required>
                                    <option value="damaged">تالف</option>
                                    <option value="sample">عينة</option>
                                    <option value="reserved">محجوز</option>
                                </select>

                                <select name="action" required>
                                    <option value="add">إضافة</option>
                                    <option value="remove">إرجاع</option>
                                </select>

                                <input type="number" name="quantity" min="1" value="1" required>

                                <button class="mini-btn" type="submit">تنفيذ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align:center;padding:35px;color:#777">
                            لا توجد أرصدة مطابقة.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:12px">{{ $stocks->links() }}</div>
    </section>
</div>
</body>
</html>
