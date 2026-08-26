<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة الطلب {{ $order->order_number }} | لمسة أنوثة</title>
    <link rel="icon" href="{{ asset('images/logo.jpeg') }}">

    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --red:#e21b23;
            --bg:#f5f5f5;
            --card:#fff;
            --text:#171717;
            --muted:#777;
            --border:#e4e4e4;
            --shadow:0 12px 35px rgba(0,0,0,.08)
        }
        body.dark{
            --bg:#101010;
            --card:#191919;
            --text:#f5f5f5;
            --muted:#aaa;
            --border:#303030;
            --shadow:0 14px 40px rgba(0,0,0,.35)
        }
        body{
            font-family:Arial,"Segoe UI",sans-serif;
            background:var(--bg);
            color:var(--text)
        }
        a{text-decoration:none;color:inherit}
        button{font:inherit}
        .wrap{
            width:min(980px,94%);
            margin:35px auto 60px
        }
        .toolbar{
            display:flex;
            justify-content:space-between;
            gap:10px;
            margin-bottom:16px
        }
        .btn{
            min-height:42px;
            padding:0 16px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--card);
            color:var(--text);
            cursor:pointer
        }
        .btn.primary{
            border-color:var(--red);
            background:var(--red);
            color:#fff
        }
        .invoice{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:16px;
            box-shadow:var(--shadow);
            overflow:hidden
        }
        .head{
            display:flex;
            justify-content:space-between;
            gap:25px;
            padding:28px;
            border-bottom:1px solid var(--border)
        }
        .brand img{
            width:210px;
            height:90px;
            object-fit:contain
        }
        .invoice-title{
            text-align:left
        }
        html[dir="rtl"] .invoice-title{
            text-align:right
        }
        .invoice-title h1{
            font-size:30px;
            margin-bottom:10px
        }
        .invoice-title p{
            color:var(--muted);
            font-size:13px;
            line-height:1.8
        }
        .legal-business{
            margin-top:14px;
            padding-top:12px;
            border-top:1px dashed var(--border);
            color:var(--muted);
            font-size:12px;
            line-height:1.9
        }
        .legal-business strong{color:var(--text)}
        .section{
            padding:24px 28px;
            border-bottom:1px solid var(--border)
        }
        .section h2{
            font-size:18px;
            margin-bottom:15px
        }
        .info-grid{
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:12px
        }
        .info{
            background:var(--bg);
            border-radius:10px;
            padding:13px
        }
        .info small{
            display:block;
            color:var(--muted);
            margin-bottom:5px
        }
        table{
            width:100%;
            border-collapse:collapse
        }
        th,td{
            padding:13px 10px;
            text-align:start;
            border-bottom:1px solid var(--border);
            font-size:13px
        }
        th{
            color:var(--muted);
            background:var(--bg);
            font-weight:normal
        }
        .summary{
            width:min(420px,100%);
            margin-inline-start:auto;
            padding:24px 28px
        }
        .line{
            display:flex;
            justify-content:space-between;
            gap:15px;
            padding:10px 0;
            color:var(--muted)
        }
        .line strong{
            color:var(--text)
        }
        .discount strong{
            color:#087443
        }
        .total{
            margin-top:8px;
            padding-top:16px;
            border-top:1px solid var(--border);
            font-size:18px
        }
        .total strong{
            color:var(--red);
            font-size:22px
        }
        .footer{
            padding:18px 28px 26px;
            color:var(--muted);
            text-align:center;
            font-size:12px
        }
        @media(max-width:700px){
            .head{flex-direction:column}
            .invoice-title{text-align:start!important}
            .info-grid{grid-template-columns:1fr}
            .toolbar{flex-wrap:wrap}
            .toolbar .btn{flex:1}
            th,td{font-size:11px;padding:10px 6px}
        }
        @media print{
            body{background:#fff}
            .wrap{width:100%;margin:0}
            .toolbar{display:none}
            .invoice{
                border:0;
                box-shadow:none;
                border-radius:0
            }
        }
    </style>
</head>
<body>

@php
    $customerName = trim(
        ($order->first_name ?? '') . ' ' .
        ($order->last_name ?? '')
    );

    if ($customerName === '') {
        $customerName = optional($order->user)->name ?? '—';
    }

    $businessName = config('storefront.legal.business_name');
    $commercialRegistration = config('storefront.legal.commercial_registration');
    $licenseNumber = config('storefront.legal.license_number');
@endphp

<div class="wrap">

    <div class="toolbar">
        <a class="btn" href="{{ url()->previous() }}">رجوع</a>

        <div style="display:flex;gap:8px">
            <button class="btn" id="theme" type="button">☾</button>
            <button class="btn primary" type="button" onclick="window.print()">
                طباعة الفاتورة
            </button>
        </div>
    </div>

    <section class="invoice">

        <div class="head">

            <div class="brand">
                <img src="{{ asset('images/logo.jpeg') }}" alt="لمسة أنوثة">
            </div>

            <div class="invoice-title">
                <h1>فاتورة إلكترونية</h1>

                <p>
                    رقم الطلب:
                    <strong>{{ $order->order_number }}</strong>
                    <br>
                    التاريخ:
                    {{ optional($order->created_at)->format('Y-m-d H:i') }}
                    <br>
                    العملة:
                    {{ $order->currency ?? 'OMR' }}
                </p>

                @if($businessName || $commercialRegistration || $licenseNumber)
                    <div class="legal-business">
                        @if($businessName)
                            <div><strong>{{ $businessName }}</strong></div>
                        @endif
                        @if($commercialRegistration)
                            <div>
                                السجل التجاري:
                                <strong>{{ $commercialRegistration }}</strong>
                            </div>
                        @endif
                        @if($licenseNumber)
                            <div>
                                رقم الترخيص:
                                <strong>{{ $licenseNumber }}</strong>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>

        <div class="section">
            <h2>بيانات العميل</h2>

            <div class="info-grid">

                <div class="info">
                    <small>الاسم</small>
                    <strong>{{ $customerName }}</strong>
                </div>

                <div class="info">
                    <small>الهاتف</small>
                    <strong>{{ $order->phone ?? '—' }}</strong>
                </div>

                <div class="info">
                    <small>البريد الإلكتروني</small>
                    <strong>{{ $order->email ?? '—' }}</strong>
                </div>

                <div class="info">
                    <small>المحافظة / الولاية</small>
                    <strong>
                        {{ $order->governorate ?? '—' }}
                        /
                        {{ $order->wilayat ?? '—' }}
                    </strong>
                </div>

                <div class="info" style="grid-column:1/-1">
                    <small>العنوان</small>
                    <strong>{{ $order->address ?? '—' }}</strong>
                </div>

            </div>
        </div>

        <div class="section">
            <h2>تفاصيل الطلب</h2>

            <div style="overflow:auto">
                <table>
                    <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>SKU</th>
                        <th>اللون</th>
                        <th>المقاس</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($order->items as $item)

                        <tr>
                            <td>{{ $item->product_name_ar ?? 'منتج' }}</td>
                            <td>{{ $item->sku ?? '—' }}</td>
                            <td>{{ $item->color_name_ar ?? '—' }}</td>
                            <td>{{ $item->size ?? '—' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format((float)$item->unit_price,3) }} ر.ع</td>
                            <td>{{ number_format((float)$item->line_total,3) }} ر.ع</td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--muted)">
                                لا توجد عناصر محفوظة لهذا الطلب.
                            </td>
                        </tr>

                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="summary">

            <div class="line">
                <span>المجموع الفرعي</span>
                <strong>{{ number_format($subtotal,3) }} ر.ع</strong>
            </div>

            @if(!empty($order->coupon_code))
                <div class="line">
                    <span>الكوبون</span>
                    <strong>{{ $order->coupon_code }}</strong>
                </div>
            @endif

            <div class="line discount">
                <span>الخصم</span>
                <strong>-{{ number_format($discountAmount,3) }} ر.ع</strong>
            </div>

            <div class="line">
                <span>التوصيل</span>
                <strong>{{ number_format($shippingFee,3) }} ر.ع</strong>
            </div>

            <div class="line">
                <span>طريقة الدفع</span>
                <strong>{{ $order->payment_method ?? '—' }}</strong>
            </div>

            <div class="line">
                <span>حالة الدفع</span>
                <strong>{{ $order->payment_status ?? 'pending' }}</strong>
            </div>

            <div class="line total">
                <span>الإجمالي النهائي</span>
                <strong>{{ number_format($total,3) }} ر.ع</strong>
            </div>

        </div>

        <div class="footer">
            شكرًا لتسوقك من لمسة أنوثة
        </div>

    </section>

</div>

<script>
let theme = localStorage.siteTheme || 'light';
const button = document.getElementById('theme');

function applyTheme(){
    document.body.classList.toggle('dark', theme === 'dark');
    button.textContent = theme === 'dark' ? '☀' : '☾';
}

button.onclick = () => {
    theme = theme === 'dark' ? 'light' : 'dark';
    localStorage.siteTheme = theme;
    applyTheme();
};

applyTheme();
</script>

</body>
</html>
