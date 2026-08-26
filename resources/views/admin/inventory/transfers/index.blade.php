<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تحويلات المخزون</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,"Segoe UI",sans-serif;background:#f4f5f7;color:#171717}
        a{text-decoration:none;color:inherit}
        button,input,select,textarea{font:inherit}
        .wrap{width:min(1200px,94%);margin:26px auto 60px}
        .top{display:flex;justify-content:space-between;gap:15px;align-items:center;margin-bottom:18px}
        .top h1{margin:0 0 6px}
        .muted{color:#777}
        .back{color:#e21b23}
        .card{background:#fff;border:1px solid #e3e5e8;border-radius:12px;padding:18px;margin-bottom:14px}
        .notice{padding:12px;border-radius:9px;margin-bottom:12px;background:#d1fae5;color:#087443}
        .error{background:#fee2e2;color:#b91c1c}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
        .field{margin-bottom:10px}
        .field label{display:block;font-size:12px;color:#777;margin-bottom:5px}
        .field input,.field select,.field textarea{width:100%;padding:10px;border:1px solid #dfe2e6;border-radius:8px;background:#fff}
        .field textarea{min-height:78px}
        .full{grid-column:1/-1}
        .btn{border:0;border-radius:8px;background:#e21b23;color:#fff;padding:11px 16px;cursor:pointer}
        .item-row{display:grid;grid-template-columns:1fr 150px 42px;gap:8px;margin-bottom:8px}
        .remove{border:0;background:#fee2e2;color:#b91c1c;border-radius:8px;cursor:pointer}
        .add{border:1px solid #e21b23;background:#fff;color:#e21b23;border-radius:8px;padding:9px 12px;cursor:pointer}
        .table-wrap{overflow:auto}
        table{width:100%;border-collapse:collapse}
        th,td{padding:11px;border-bottom:1px solid #e6e7e9;text-align:start;font-size:12px;vertical-align:top}
        th{background:#f7f7f8;color:#6b7280}
        .badge{display:inline-block;padding:5px 8px;border-radius:999px;background:#eef2ff;font-size:11px}
        @media(max-width:700px){.grid{grid-template-columns:1fr}.item-row{grid-template-columns:1fr 100px 42px}.top{flex-direction:column;align-items:stretch}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <div>
            <a class="back" href="{{ route('admin.inventory.index') }}">← العودة للمخزون</a>
            <h1>تحويلات المخزون</h1>
            <div class="muted">نقل الكميات بين المخازن مع حفظ سجل الحركة.</div>
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

    <section class="card">
        <h3>تحويل جديد</h3>

        <form method="POST" action="{{ route('admin.inventory.transfers.store') }}">
            @csrf

            <div class="grid">
                <div class="field">
                    <label>من المخزن</label>
                    <select name="from_warehouse_id" required>
                        <option value="">اختر المخزن</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>إلى المخزن</label>
                    <select name="to_warehouse_id" required>
                        <option value="">اختر المخزن</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>تاريخ التحويل</label>
                    <input type="date" name="transfer_date" value="{{ now()->toDateString() }}" required>
                </div>

                <div class="field full">
                    <label>ملاحظات</label>
                    <textarea name="notes"></textarea>
                </div>
            </div>

            <h4>الأصناف</h4>

            <div id="items">
                <div class="item-row">
                    <select name="items[0][product_variant_id]" required>
                        <option value="">اختر الصنف</option>
                        @foreach($variants as $variant)
                            <option value="{{ $variant->id }}">
                                {{ $variant->product->name_ar ?? 'منتج' }}
                                {{ $variant->color_name_ar ? ' - '.$variant->color_name_ar : '' }}
                                {{ $variant->size ? ' - '.$variant->size : '' }}
                                {{ $variant->sku ? ' ['.$variant->sku.']' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <input type="number" name="items[0][quantity]" min="1" value="1" required>

                    <button class="remove" type="button" onclick="removeRow(this)">×</button>
                </div>
            </div>

            <button class="add" type="button" onclick="addRow()">+ إضافة صنف</button>
            <button class="btn" type="submit">تنفيذ التحويل</button>
        </form>
    </section>

    <section class="card">
        <h3>سجل التحويلات</h3>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>رقم التحويل</th>
                    <th>التاريخ</th>
                    <th>من</th>
                    <th>إلى</th>
                    <th>الحالة</th>
                    <th>عدد الأصناف</th>
                    <th>المستخدم</th>
                </tr>
                </thead>
                <tbody>
                @forelse($transfers as $transfer)
                    <tr>
                        <td>{{ $transfer->transfer_number }}</td>
                        <td>{{ optional($transfer->transfer_date)->format('Y-m-d') }}</td>
                        <td>{{ $transfer->fromWarehouse->name ?? '—' }}</td>
                        <td>{{ $transfer->toWarehouse->name ?? '—' }}</td>
                        <td><span class="badge">{{ $transfer->status }}</span></td>
                        <td>{{ $transfer->items->count() }}</td>
                        <td>{{ $transfer->user->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;padding:30px;color:#777">لا توجد تحويلات حتى الآن.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:12px">{{ $transfers->links() }}</div>
    </section>
</div>

<script>
    let index = 1;

    const options = @json(
        $variants->map(function ($variant) {
            $name = $variant->product->name_ar ?? 'منتج';

            if ($variant->color_name_ar) {
                $name .= ' - ' . $variant->color_name_ar;
            }

            if ($variant->size) {
                $name .= ' - ' . $variant->size;
            }

            if ($variant->sku) {
                $name .= ' [' . $variant->sku . ']';
            }

            return [
                'id' => $variant->id,
                'name' => $name,
            ];
        })->values()
    );

    function addRow() {
        const row = document.createElement('div');
        row.className = 'item-row';

        let html = `<select name="items[${index}][product_variant_id]" required>
            <option value="">اختر الصنف</option>`;

        options.forEach(item => {
            html += `<option value="${item.id}">${item.name}</option>`;
        });

        html += `</select>
            <input type="number" name="items[${index}][quantity]" min="1" value="1" required>
            <button class="remove" type="button" onclick="removeRow(this)">×</button>`;

        row.innerHTML = html;
        document.getElementById('items').appendChild(row);
        index++;
    }

    function removeRow(button) {
        const rows = document.querySelectorAll('.item-row');

        if (rows.length <= 1) {
            return;
        }

        button.closest('.item-row').remove();
    }
</script>
</body>
</html>
