<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>جرد المخزون</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,"Segoe UI",sans-serif;background:#f4f5f7;color:#171717}
        a{text-decoration:none;color:inherit}
        button,input,select,textarea{font:inherit}
        .wrap{width:min(1180px,94%);margin:26px auto 60px}
        .top{margin-bottom:18px}.top h1{margin:7px 0}.back{color:#e21b23}.muted{color:#777}
        .card{background:#fff;border:1px solid #e3e5e8;border-radius:12px;padding:18px;margin-bottom:14px}
        .notice{padding:12px;border-radius:9px;margin-bottom:12px;background:#d1fae5;color:#087443}
        .error{background:#fee2e2;color:#b91c1c}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
        .field{margin-bottom:10px}.field label{display:block;font-size:12px;color:#777;margin-bottom:5px}
        .field input,.field select,.field textarea{width:100%;padding:10px;border:1px solid #dfe2e6;border-radius:8px;background:#fff}
        .full{grid-column:1/-1}.btn{border:0;border-radius:8px;background:#e21b23;color:#fff;padding:11px 16px;cursor:pointer}
        table{width:100%;border-collapse:collapse}th,td{padding:11px;border-bottom:1px solid #e6e7e9;text-align:start;font-size:12px}
        th{background:#f7f7f8;color:#6b7280}.table-wrap{overflow:auto}.badge{padding:5px 8px;border-radius:999px;background:#eef2ff;font-size:11px}
        .link{color:#e21b23;font-weight:700}
        @media(max-width:700px){.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <a class="back" href="{{ route('admin.inventory.index') }}">← العودة للمخزون</a>
        <h1>جرد المخزون</h1>
        <div class="muted">جرد شامل أو جزئي ثم اعتماد الفروقات وتحديث الرصيد.</div>
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
        <h3>جلسة جرد جديدة</h3>

        <form method="POST" action="{{ route('admin.inventory.counts.store') }}">
            @csrf

            <div class="grid">
                <div class="field">
                    <label>المخزن</label>
                    <select name="warehouse_id" required>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>نوع الجرد</label>
                    <select name="type" id="countType" required>
                        <option value="full">جرد شامل</option>
                        <option value="cycle">جرد جزئي</option>
                    </select>
                </div>

                <div class="field">
                    <label>تاريخ الجرد</label>
                    <input type="date" name="count_date" value="{{ now()->toDateString() }}" required>
                </div>

                <div class="field full" id="cycleNote" style="display:none">
                    <label>للجرد الجزئي</label>
                    <div class="muted">اختيار الأصناف سيضاف في خطوة الواجهة المتقدمة لاحقًا. حاليًا استخدم الجرد الشامل.</div>
                </div>

                <div class="field full">
                    <label>ملاحظات</label>
                    <textarea name="notes" style="min-height:80px"></textarea>
                </div>
            </div>

            <button class="btn" type="submit">إنشاء جلسة الجرد</button>
        </form>
    </section>

    <section class="card">
        <h3>سجل الجرد</h3>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>رقم الجرد</th>
                    <th>التاريخ</th>
                    <th>المخزن</th>
                    <th>النوع</th>
                    <th>الحالة</th>
                    <th>عدد الأصناف</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($counts as $count)
                    <tr>
                        <td>{{ $count->count_number }}</td>
                        <td>{{ optional($count->count_date)->format('Y-m-d') }}</td>
                        <td>{{ $count->warehouse->name ?? '—' }}</td>
                        <td>{{ $count->type === 'full' ? 'شامل' : 'جزئي' }}</td>
                        <td><span class="badge">{{ $count->status }}</span></td>
                        <td>{{ $count->items_count }}</td>
                        <td>
                            <a class="link" href="{{ route('admin.inventory.counts.show', $count) }}">
                                فتح
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;padding:30px;color:#777">لا توجد جلسات جرد حتى الآن.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:12px">{{ $counts->links() }}</div>
    </section>
</div>

<script>
    const type = document.getElementById('countType');
    const note = document.getElementById('cycleNote');

    function syncType() {
        note.style.display = type.value === 'cycle' ? 'block' : 'none';
    }

    type.addEventListener('change', syncType);
    syncType();
</script>
</body>
</html>
