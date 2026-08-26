<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إدارة المخازن</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,"Segoe UI",sans-serif;background:#f4f5f7;color:#171717}
        a{text-decoration:none;color:inherit}
        button,input,textarea{font:inherit}
        .wrap{width:min(1180px,94%);margin:26px auto 60px}
        .top{margin-bottom:18px}.top h1{margin:7px 0}.back{color:#e21b23}.muted{color:#777}
        .card{background:#fff;border:1px solid #e3e5e8;border-radius:12px;padding:18px;margin-bottom:14px}
        .notice{padding:12px;border-radius:9px;margin-bottom:12px;background:#d1fae5;color:#087443}
        .error{background:#fee2e2;color:#b91c1c}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
        .field{margin-bottom:10px}.field label{display:block;font-size:12px;color:#777;margin-bottom:5px}
        .field input,.field textarea{width:100%;padding:10px;border:1px solid #dfe2e6;border-radius:8px;background:#fff}
        .full{grid-column:1/-1}.btn{border:0;border-radius:8px;background:#e21b23;color:#fff;padding:11px 16px;cursor:pointer}
        .btn.secondary{background:#111}.btn.ghost{background:#fff;color:#e21b23;border:1px solid #e21b23}
        table{width:100%;border-collapse:collapse}th,td{padding:11px;border-bottom:1px solid #e6e7e9;text-align:start;font-size:12px}
        th{background:#f7f7f8;color:#6b7280}.table-wrap{overflow:auto}
        .badge{display:inline-block;padding:5px 8px;border-radius:999px;background:#eef2ff;font-size:11px}
        .badge.default{background:#fff3cd;color:#7a5b00}
        .badge.active{background:#d1fae5;color:#087443}
        .badge.inactive{background:#fee2e2;color:#b91c1c}
        .actions{display:flex;gap:6px;flex-wrap:wrap}
        @media(max-width:700px){.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <a class="back" href="{{ route('admin.inventory.index') }}">← العودة للمخزون</a>
        <h1>إدارة المخازن</h1>
        <div class="muted">إضافة المخازن وتحديد المخزن الافتراضي ومتابعة أرصدتها.</div>
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
        <h3>إضافة مخزن جديد</h3>

        <form method="POST" action="{{ route('admin.inventory.warehouses.store') }}">
            @csrf

            <div class="grid">
                <div class="field">
                    <label>اسم المخزن</label>
                    <input name="name" value="{{ old('name') }}" required>
                </div>

                <div class="field">
                    <label>الرمز</label>
                    <input name="code" value="{{ old('code') }}" placeholder="مثال: BRANCH2" required>
                </div>

                <div class="field">
                    <label>اسم الفرع</label>
                    <input name="branch_name" value="{{ old('branch_name') }}">
                </div>

                <div class="field">
                    <label>المحافظة</label>
                    <input name="governorate" value="{{ old('governorate') }}">
                </div>

                <div class="field">
                    <label>الولاية</label>
                    <input name="wilayat" value="{{ old('wilayat') }}">
                </div>

                <div class="field full">
                    <label>العنوان</label>
                    <textarea name="address">{{ old('address') }}</textarea>
                </div>
            </div>

            <button class="btn" type="submit">إضافة المخزن</button>
        </form>
    </section>

    <section class="card">
        <h3>المخازن الحالية</h3>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>المخزن</th>
                    <th>الرمز</th>
                    <th>الفرع</th>
                    <th>عدد الأصناف</th>
                    <th>إجمالي الكمية</th>
                    <th>المحجوز</th>
                    <th>التالف</th>
                    <th>العينات</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($warehouses as $warehouse)
                    <tr>
                        <td>
                            <strong>{{ $warehouse->name }}</strong>
                            @if($warehouse->is_default)
                                <div><span class="badge default">افتراضي</span></div>
                            @endif
                        </td>
                        <td>{{ $warehouse->code }}</td>
                        <td>{{ $warehouse->branch_name ?: '—' }}</td>
                        <td>{{ $warehouse->variant_stocks_count }}</td>
                        <td>{{ (int)($warehouse->total_quantity ?? 0) }}</td>
                        <td>{{ (int)($warehouse->reserved_quantity ?? 0) }}</td>
                        <td>{{ (int)($warehouse->damaged_quantity ?? 0) }}</td>
                        <td>{{ (int)($warehouse->sample_quantity ?? 0) }}</td>
                        <td>
                            <span class="badge {{ $warehouse->is_active ? 'active' : 'inactive' }}">
                                {{ $warehouse->is_active ? 'فعال' : 'موقوف' }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                @if(!$warehouse->is_default)
                                    <form method="POST" action="{{ route('admin.inventory.warehouses.default', $warehouse) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn secondary" type="submit">جعله افتراضيًا</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('admin.inventory.warehouses.toggle', $warehouse) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn ghost" type="submit">
                                        {{ $warehouse->is_active ? 'إيقاف' : 'تفعيل' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" style="text-align:center;padding:30px;color:#777">لا توجد مخازن.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
</body>
</html>
