<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>الباركود والملصقات</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Arial,"Segoe UI",sans-serif;background:#f4f5f7;color:#171717}
        a{text-decoration:none;color:inherit}
        button,input,select{font:inherit}
        .wrap{width:min(1220px,95%);margin:26px auto 60px}
        .top{display:flex;justify-content:space-between;gap:12px;align-items:center;margin-bottom:16px}
        .top h1{margin:7px 0}.back{color:#e21b23}.muted{color:#777}
        .card{background:#fff;border:1px solid #e3e5e8;border-radius:12px;padding:18px;margin-bottom:14px}
        .filters{display:grid;grid-template-columns:1fr auto;gap:8px}
        .filters input{padding:10px;border:1px solid #dfe2e6;border-radius:8px}
        .btn{border:0;border-radius:8px;background:#e21b23;color:#fff;padding:10px 14px;cursor:pointer}
        .btn.dark{background:#111}.btn.ghost{background:#fff;color:#e21b23;border:1px solid #e21b23}
        .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
        .item{border:1px solid #e3e5e8;border-radius:10px;padding:14px;background:#fff}
        .item h3{margin:0 0 7px;font-size:15px}.item small{color:#777}
        .label-preview{margin-top:12px;border:1px dashed #bbb;border-radius:8px;padding:14px;text-align:center}
        .bars{font-family:monospace;font-size:34px;letter-spacing:-4px;line-height:1}
        .actions{display:flex;gap:6px;margin-top:10px;flex-wrap:wrap}
        .qty{width:80px;padding:8px;border:1px solid #dfe2e6;border-radius:7px}
        .pagination{margin-top:12px}
        @media(max-width:900px){.grid{grid-template-columns:1fr 1fr}}
        @media(max-width:620px){.grid{grid-template-columns:1fr}.top{flex-direction:column;align-items:stretch}.filters{grid-template-columns:1fr}}
        @media print{
            body{background:#fff}
            .no-print,.top,.filters-card,.pagination{display:none!important}
            .wrap{width:100%;margin:0}
            .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6mm}
            .item{border:1px solid #000;page-break-inside:avoid}
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="top no-print">
        <div>
            <a class="back" href="{{ route('admin.inventory.index') }}">← العودة للمخزون</a>
            <h1>الباركود والملصقات</h1>
            <div class="muted">استخدام SKU كقيمة الباركود وتجهيز الملصقات للطباعة.</div>
        </div>

        <button class="btn dark" type="button" onclick="window.print()">طباعة الصفحة</button>
    </div>

    <section class="card filters-card no-print">
        <form class="filters" method="GET">
            <input
                name="search"
                value="{{ $search }}"
                placeholder="بحث بالمنتج أو SKU أو اللون أو المقاس"
            >
            <button class="btn" type="submit">بحث</button>
        </form>
    </section>

    <section class="grid">
        @forelse($variants as $variant)
            @php
                $price = $variant->price ?? $variant->product->price ?? 0;
                $sku = $variant->sku ?: 'NO-SKU-'.$variant->id;
            @endphp

            <article class="item">
                <h3>{{ $variant->product->name_ar ?? 'منتج' }}</h3>

                <small>
                    {{ $variant->color_name_ar ?? '' }}
                    {{ $variant->size ? ' / '.$variant->size : '' }}
                </small>

                <div class="label-preview">
                    <div class="bars">|||| ||| |||| | |||</div>
                    <strong>{{ $sku }}</strong>
                    <div style="margin-top:7px">{{ number_format((float)$price,3) }} ر.ع</div>
                </div>

                <div class="actions no-print">
                    <input
                        class="qty"
                        type="number"
                        min="1"
                        value="1"
                        id="copies-{{ $variant->id }}"
                        title="عدد النسخ"
                    >

                    <button
                        class="btn ghost"
                        type="button"
                        onclick="printCopies({{ $variant->id }})"
                    >
                        طباعة الملصق
                    </button>
                </div>
            </article>
        @empty
            <div class="card" style="grid-column:1/-1;text-align:center;color:#777">
                لا توجد منتجات مطابقة.
            </div>
        @endforelse
    </section>

    <div class="pagination no-print">
        {{ $variants->links() }}
    </div>
</div>

<script>
function printCopies(id) {
    const card = document.getElementById('copies-' + id).closest('.item');
    const count = Math.max(parseInt(document.getElementById('copies-' + id).value || 1), 1);

    const win = window.open('', '_blank', 'width=900,height=700');

    let labels = '';

    for (let i = 0; i < count; i++) {
        labels += `<div class="label">${card.querySelector('.label-preview').innerHTML}</div>`;
    }

    win.document.write(`
        <html dir="rtl">
        <head>
            <title>طباعة الملصقات</title>
            <style>
                body{font-family:Arial;margin:10mm}
                .sheet{display:grid;grid-template-columns:repeat(3,1fr);gap:5mm}
                .label{border:1px solid #000;padding:8mm;text-align:center;page-break-inside:avoid}
                .bars{font-family:monospace;font-size:34px;letter-spacing:-4px;line-height:1}
            </style>
        </head>
        <body>
            <div class="sheet">${labels}</div>
            <script>window.onload=()=>window.print()<\/script>
        </body>
        </html>
    `);

    win.document.close();
}
</script>
</body>
</html>
