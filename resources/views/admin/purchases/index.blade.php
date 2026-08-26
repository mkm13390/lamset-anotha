<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>أوامر الشراء | لمسة أنوثة</title>
    <link rel="icon" href="{{ asset('images/logo.jpeg') }}">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{--red:#e21b23;--bg:#f4f5f7;--card:#fff;--text:#171717;--muted:#777;--border:#e2e4e7;--shadow:0 8px 28px rgba(0,0,0,.07)}
        body.dark{--bg:#101010;--card:#191919;--text:#f5f5f5;--muted:#aaa;--border:#303030;--shadow:0 12px 35px rgba(0,0,0,.35)}
        body{font-family:Arial,"Segoe UI",sans-serif;background:var(--bg);color:var(--text)}
        a{color:inherit;text-decoration:none}
        button,input,select,textarea{font:inherit}
        .app{min-height:100vh;display:grid;grid-template-columns:245px 1fr}
        .side{background:#090909;color:#eee;padding:18px 13px}
        .logo{height:98px;display:flex;align-items:center;justify-content:center;border-bottom:1px solid #292929;margin-bottom:15px}
        .logo img{width:205px;height:86px;object-fit:contain}
        .nav a{display:flex;gap:10px;padding:12px;border-radius:9px;margin:3px 0;font-size:14px}
        .nav a:hover,.nav a.active{background:var(--red)}
        .main{min-width:0}
        .top{height:74px;padding:0 3%;display:flex;align-items:center;justify-content:space-between;background:var(--card);border-bottom:1px solid var(--border)}
        .content{padding:25px 3% 55px}
        .tool{height:40px;padding:0 12px;border:1px solid var(--border);border-radius:8px;background:var(--card);color:var(--text);cursor:pointer}
        .notice{padding:12px 14px;border-radius:9px;margin-bottom:14px;background:#d1fae5;color:#087443;border:1px solid #a7f3d0;font-size:13px}
        .error{background:#fee2e2;color:#b91c1c;border-color:#fecaca}
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:17px}
        .stat,.card{background:var(--card);border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow)}
        .stat{padding:17px}.stat span{color:var(--muted);font-size:12px}.stat strong{display:block;font-size:24px;margin-top:8px}
        .filters{display:grid;grid-template-columns:1fr 180px auto;gap:8px;margin-bottom:14px}
        .filters input,.filters select{height:42px;padding:0 10px;border:1px solid var(--border);border-radius:8px;background:var(--card);color:var(--text)}
        .btn-primary{height:42px;padding:0 16px;border:0;border-radius:8px;background:var(--red);color:#fff;cursor:pointer}
        .table-wrap{overflow:auto}
        table{width:100%;border-collapse:collapse;white-space:nowrap}
        th,td{text-align:start;padding:13px 14px;border-bottom:1px solid var(--border);font-size:12px;vertical-align:top}
        th{background:var(--bg);color:var(--muted);font-weight:normal}
        .badge{display:inline-block;padding:5px 8px;border-radius:20px;font-size:10px;background:#eee;color:#666}
        .badge.ok{background:#d1fae5;color:#087443}.badge.warn{background:#fff3cd;color:#856404}.badge.off{background:#fee2e2;color:#b91c1c}
        .form-card{margin-top:18px;padding:18px}
        .form-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
        .field.full{grid-column:1/-1}.field label{display:block;font-size:12px;margin-bottom:6px;color:var(--muted)}
        .field input,.field textarea,.field select{width:100%;border:1px solid var(--border);border-radius:8px;background:var(--bg);color:var(--text);padding:10px}
        .field textarea{min-height:80px;resize:vertical}
        .items{margin-top:12px}.item-row{display:grid;grid-template-columns:2fr 100px 120px 120px 120px auto;gap:8px;margin-bottom:8px}
        .item-row input,.item-row select{height:40px;padding:0 8px;border:1px solid var(--border);border-radius:8px;background:var(--bg);color:var(--text)}
        .remove{border:0;background:#fee2e2;color:#b91c1c;border-radius:8px;padding:0 10px;cursor:pointer}
        .secondary{height:38px;padding:0 12px;border:1px solid var(--border);border-radius:8px;background:var(--card);color:var(--text);cursor:pointer}
        .pager{padding:14px}
        @media(max-width:1000px){.stats{grid-template-columns:1fr 1fr}.form-grid{grid-template-columns:1fr 1fr}.item-row{grid-template-columns:1fr 1fr}}
        @media(max-width:800px){.app{grid-template-columns:1fr}.side{display:none}}
        @media(max-width:600px){.stats,.form-grid,.filters,.item-row{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="app">
    <aside class="side">
        <a class="logo" href="{{ route('admin.dashboard') }}"><img src="{{ asset('images/logo.jpeg') }}" alt="لمسة أنوثة"></a>
        <nav class="nav">
            <a href="{{ route('admin.dashboard') }}">⌂ لوحة التحكم</a>
            <a href="{{ route('admin.orders.index') }}">▤ الطلبات</a>
            <a href="{{ route('admin.inventory.index') }}">▦ المخزون</a>
            <a href="{{ route('admin.suppliers.index') }}">♧ الموردون</a>
            <a class="active" href="{{ route('admin.purchases.index') }}">🧾 المشتريات</a>
            <a href="{{ route('admin.pos.index') }}">▰ نقطة البيع</a>
        </nav>
    </aside>
    <main class="main">
        <header class="top"><h1>أوامر الشراء</h1><button class="tool" id="theme" type="button">☾</button></header>
        <div class="content">
            @if(session('success'))<div class="notice">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="notice error">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

            <section class="stats">
                <div class="stat"><span>إجمالي الأوامر</span><strong>{{ $stats['total'] ?? 0 }}</strong></div>
                <div class="stat"><span>مفتوحة</span><strong>{{ $stats['open'] ?? 0 }}</strong></div>
                <div class="stat"><span>مستلمة</span><strong>{{ $stats['received'] ?? 0 }}</strong></div>
                <div class="stat"><span>رصيد مستحق</span><strong>{{ number_format((float)($stats['balance_due'] ?? 0),3) }} ر.ع</strong></div>
            </section>

            <form class="filters" method="GET" action="{{ route('admin.purchases.index') }}">
                <input name="search" value="{{ $search }}" placeholder="بحث برقم الأمر أو المورد أو فاتورة المورد">
                <select name="status">
                    <option value="">كل الحالات</option>
                    @foreach(['draft'=>'مسودة','ordered'=>'مطلوب','partially_received'=>'استلام جزئي','received'=>'مستلم','cancelled'=>'ملغى'] as $key=>$label)
                        <option value="{{ $key }}" @selected($status===$key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="btn-primary" type="submit">بحث</button>
            </form>

            <section class="card">
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>رقم الأمر</th><th>المورد</th><th>التاريخ</th><th>الإجمالي</th><th>المستلم</th><th>الدفع</th><th>الحالة</th></tr></thead>
                        <tbody>
                        @forelse($purchases as $purchase)
                            @php
                                $statusLabels=['draft'=>'مسودة','ordered'=>'مطلوب','partially_received'=>'استلام جزئي','received'=>'مستلم','cancelled'=>'ملغى'];
                            @endphp
                            <tr>
                                <td><a style="color:var(--red)" href="{{ route('admin.purchases.show',$purchase) }}">{{ $purchase->purchase_number }}</a></td>
                                <td>{{ $purchase->supplier->name ?? '—' }}</td>
                                <td>{{ optional($purchase->order_date)->format('Y-m-d') }}</td>
                                <td>{{ number_format((float)$purchase->total,3) }} {{ $purchase->currency }}</td>
                                <td>{{ $purchase->received_quantity ?? 0 }} / {{ $purchase->ordered_quantity ?? 0 }}</td>
                                <td>{{ $purchase->payment_status }}</td>
                                <td><span class="badge {{ $purchase->status==='received'?'ok':($purchase->status==='cancelled'?'off':'warn') }}">{{ $statusLabels[$purchase->status] ?? $purchase->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center;padding:45px;color:var(--muted)">لا توجد أوامر شراء حتى الآن.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($purchases->hasPages())<div class="pager">{{ $purchases->links() }}</div>@endif
            </section>

            <section class="card form-card">
                <h3 style="margin-bottom:14px">إنشاء أمر شراء جديد</h3>

                <form method="POST" action="{{ route('admin.purchases.store') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="field"><label>المورد</label><select name="supplier_id" required><option value="">اختر المورد</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach</select></div>
                        <div class="field"><label>تاريخ الأمر</label><input type="date" name="order_date" value="{{ now()->toDateString() }}" required></div>
                        <div class="field"><label>التاريخ المتوقع</label><input type="date" name="expected_date"></div>
                        <div class="field"><label>الحالة</label><select name="status"><option value="draft">مسودة</option><option value="ordered">مطلوب</option></select></div>
                        <div class="field"><label>العملة</label><input name="currency" value="OMR" required></div>
                        <div class="field"><label>رقم فاتورة المورد</label><input name="supplier_invoice_number"></div>
                        <div class="field"><label>خصم الأمر</label><input name="discount_amount" type="number" step="0.001" min="0" value="0"></div>
                        <div class="field"><label>الشحن</label><input name="shipping_amount" type="number" step="0.001" min="0" value="0"></div>
                        <div class="field"><label>الضريبة</label><input name="tax_amount" type="number" step="0.001" min="0" value="0"></div>
                        <div class="field"><label>المرجع</label><input name="reference"></div>
                        <div class="field full"><label>ملاحظات</label><textarea name="notes"></textarea></div>
                    </div>

                    <div class="items">
                        <h4 style="margin-bottom:10px">الأصناف</h4>
                        <div id="itemsBox">
                            <div class="item-row">
                                <select name="items[0][product_variant_id]" required>
                                    <option value="">اختر المنتج / الخيار</option>
                                    @foreach($variants as $variant)
                                        <option value="{{ $variant->id }}">
                                            {{ optional($variant->product)->name_ar ?? 'منتج' }}
                                            — {{ $variant->sku }}
                                            @if($variant->color_name_ar) — {{ $variant->color_name_ar }} @endif
                                            @if($variant->size) / {{ $variant->size }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                <input name="items[0][quantity]" type="number" min="1" value="1" placeholder="الكمية" required>
                                <input name="items[0][unit_cost]" type="number" min="0" step="0.001" placeholder="التكلفة" required>
                                <input name="items[0][discount_amount]" type="number" min="0" step="0.001" value="0" placeholder="خصم">
                                <input name="items[0][tax_amount]" type="number" min="0" step="0.001" value="0" placeholder="ضريبة">
                                <button class="remove" type="button" onclick="removeRow(this)">حذف</button>
                            </div>
                        </div>
                        <button class="secondary" type="button" id="addItem">+ إضافة صنف</button>
                    </div>

                    <br>
                    <button class="btn-primary" type="submit">حفظ أمر الشراء</button>
                </form>
            </section>
        </div>
    </main>
</div>

<template id="itemTemplate">
    <div class="item-row">
        <select required>
            <option value="">اختر المنتج / الخيار</option>
            @foreach($variants as $variant)
                <option value="{{ $variant->id }}">
                    {{ optional($variant->product)->name_ar ?? 'منتج' }}
                    — {{ $variant->sku }}
                    @if($variant->color_name_ar) — {{ $variant->color_name_ar }} @endif
                    @if($variant->size) / {{ $variant->size }} @endif
                </option>
            @endforeach
        </select>
        <input type="number" min="1" value="1" placeholder="الكمية" required>
        <input type="number" min="0" step="0.001" placeholder="التكلفة" required>
        <input type="number" min="0" step="0.001" value="0" placeholder="خصم">
        <input type="number" min="0" step="0.001" value="0" placeholder="ضريبة">
        <button class="remove" type="button" onclick="removeRow(this)">حذف</button>
    </div>
</template>

<script>
let theme=localStorage.adminTheme||'light';
function applyTheme(){document.body.classList.toggle('dark',theme==='dark');document.getElementById('theme').textContent=theme==='dark'?'☀':'☾'}
document.getElementById('theme').onclick=()=>{theme=theme==='dark'?'light':'dark';localStorage.adminTheme=theme;applyTheme()};
applyTheme();

let itemIndex=1;
document.getElementById('addItem').onclick=()=>{
    const node=document.getElementById('itemTemplate').content.cloneNode(true);
    const row=node.querySelector('.item-row');
    const controls=row.querySelectorAll('select,input');
    controls[0].name=`items[${itemIndex}][product_variant_id]`;
    controls[1].name=`items[${itemIndex}][quantity]`;
    controls[2].name=`items[${itemIndex}][unit_cost]`;
    controls[3].name=`items[${itemIndex}][discount_amount]`;
    controls[4].name=`items[${itemIndex}][tax_amount]`;
    itemIndex++;
    document.getElementById('itemsBox').appendChild(node);
};
function removeRow(btn){
    const rows=document.querySelectorAll('#itemsBox .item-row');
    if(rows.length<=1)return;
    btn.closest('.item-row').remove();
}
</script>
</body>
</html>