<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>مرتجعات الموردين</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,"Segoe UI",sans-serif;background:#f4f5f7;color:#171717}
        a{text-decoration:none;color:inherit}
        button,input,select,textarea{font:inherit}
        .wrap{width:min(1220px,95%);margin:26px auto 60px}
        .top{margin-bottom:18px}.top h1{margin:7px 0}.back{color:#e21b23}.muted{color:#777}
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
        .btn.add{background:#fff;color:#e21b23;border:1px solid #e21b23}
        .item-row{display:grid;grid-template-columns:1.6fr 110px 120px 1.2fr 42px;gap:8px;margin-bottom:8px}
        .remove{border:0;background:#fee2e2;color:#b91c1c;border-radius:8px;cursor:pointer}
        .table-wrap{overflow:auto}
        table{width:100%;border-collapse:collapse}
        th,td{padding:11px;border-bottom:1px solid #e6e7e9;text-align:start;font-size:12px;vertical-align:top}
        th{background:#f7f7f8;color:#6b7280}
        .badge{display:inline-block;padding:5px 8px;border-radius:999px;background:#eef2ff;font-size:11px}
        @media(max-width:850px){.grid{grid-template-columns:1fr}.item-row{grid-template-columns:1fr 90px 100px 1fr 42px}}
        @media(max-width:650px){.item-row{grid-template-columns:1fr}.remove{height:38px}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="top">
        <a class="back" href="{{ route('admin.inventory.index') }}">← العودة للمخزون</a>
        <h1>مرتجعات الموردين</h1>
        <div class="muted">إرجاع أصناف للمورد مع تحديث المخزون وحساب المورد تلقائيًا.</div>
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
        <h3>مرتجع جديد</h3>

        <form method="POST" action="{{ route('admin.inventory.supplier-returns.store') }}">
            @csrf

            <div class="grid">
                <div class="field">
                    <label>المورد</label>
                    <select name="supplier_id" id="supplierId" required>
                        <option value="">اختر المورد</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>أمر الشراء</label>
                    <select name="purchase_order_id" id="purchaseOrderId">
                        <option value="">بدون ربط بأمر شراء</option>
                        @foreach($purchases as $purchase)
                            <option
                                value="{{ $purchase->id }}"
                                data-supplier="{{ $purchase->supplier_id }}"
                            >
                                {{ $purchase->purchase_number }}
                                — {{ $purchase->supplier->name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>المخزن</label>
                    <select name="warehouse_id" required>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>تاريخ المرتجع</label>
                    <input type="date" name="return_date" value="{{ now()->toDateString() }}" required>
                </div>

                <div class="field">
                    <label>المرجع</label>
                    <input name="reference" placeholder="رقم فاتورة / مرجع">
                </div>

                <div class="field full">
                    <label>ملاحظات عامة</label>
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

                    <input
                        type="number"
                        name="items[0][quantity]"
                        min="1"
                        value="1"
                        placeholder="الكمية"
                        required
                    >

                    <input
                        type="number"
                        name="items[0][unit_cost]"
                        min="0"
                        step="0.001"
                        value="0"
                        placeholder="التكلفة"
                        required
                    >

                    <input
                        name="items[0][reason]"
                        placeholder="سبب المرتجع"
                    >

                    <button class="remove" type="button" onclick="removeRow(this)">×</button>
                </div>
            </div>

            <button class="btn add" type="button" onclick="addRow()">+ إضافة صنف</button>
            <button class="btn" type="submit">تسجيل المرتجع</button>
        </form>
    </section>

    <section class="card">
        <h3>سجل مرتجعات الموردين</h3>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>رقم المرتجع</th>
                    <th>التاريخ</th>
                    <th>المورد</th>
                    <th>أمر الشراء</th>
                    <th>المخزن</th>
                    <th>الإجمالي</th>
                    <th>عدد الأصناف</th>
                    <th>الحالة</th>
                </tr>
                </thead>
                <tbody>
                @forelse($returns as $return)
                    <tr>
                        <td>{{ $return->return_number }}</td>
                        <td>{{ optional($return->return_date)->format('Y-m-d') }}</td>
                        <td>{{ $return->supplier->name ?? '—' }}</td>
                        <td>{{ $return->purchaseOrder->purchase_number ?? '—' }}</td>
                        <td>{{ $return->warehouse->name ?? '—' }}</td>
                        <td>{{ number_format((float)$return->total_amount, 3) }} ر.ع</td>
                        <td>{{ $return->items->count() }}</td>
                        <td><span class="badge">{{ $return->status }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:30px;color:#777">
                            لا توجد مرتجعات موردين حتى الآن.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:12px">{{ $returns->links() }}</div>
    </section>
</div>

<script>
    let rowIndex = 1;

    const variantOptions = @json(
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

        let html = `<select name="items[${rowIndex}][product_variant_id]" required>
            <option value="">اختر الصنف</option>`;

        variantOptions.forEach(item => {
            html += `<option value="${item.id}">${item.name}</option>`;
        });

        html += `</select>
            <input type="number" name="items[${rowIndex}][quantity]" min="1" value="1" required>
            <input type="number" name="items[${rowIndex}][unit_cost]" min="0" step="0.001" value="0" required>
            <input name="items[${rowIndex}][reason]" placeholder="سبب المرتجع">
            <button class="remove" type="button" onclick="removeRow(this)">×</button>`;

        row.innerHTML = html;
        document.getElementById('items').appendChild(row);

        rowIndex++;
    }

    function removeRow(button) {
        const rows = document.querySelectorAll('.item-row');

        if (rows.length <= 1) {
            return;
        }

        button.closest('.item-row').remove();
    }

    const supplierId = document.getElementById('supplierId');
    const purchaseOrderId = document.getElementById('purchaseOrderId');

    function filterPurchases() {
        const selectedSupplier = supplierId.value;

        [...purchaseOrderId.options].forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden =
                selectedSupplier !== ''
                && option.dataset.supplier !== selectedSupplier;
        });

        const selected = purchaseOrderId.selectedOptions[0];

        if (
            selected
            && selected.value
            && selected.hidden
        ) {
            purchaseOrderId.value = '';
        }
    }

    supplierId.addEventListener('change', filterPurchases);
    filterPurchases();
</script>
</body>
</html>
