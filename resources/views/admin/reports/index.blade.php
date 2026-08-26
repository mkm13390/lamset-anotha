<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقارير | لمسة أنوثة</title>
    <link rel="icon" href="{{ asset('images/logo.jpeg') }}">

    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --red:#e21b23;
            --bg:#f4f5f7;
            --card:#fff;
            --text:#171717;
            --muted:#777;
            --border:#e2e4e7;
            --shadow:0 8px 28px rgba(0,0,0,.07)
        }
        body.dark{
            --bg:#101010;
            --card:#191919;
            --text:#f5f5f5;
            --muted:#aaa;
            --border:#303030;
            --shadow:0 12px 35px rgba(0,0,0,.35)
        }
        body{
            font-family:Arial,"Segoe UI",sans-serif;
            background:var(--bg);
            color:var(--text);
            transition:.25s
        }
        a{color:inherit;text-decoration:none}
        button,input,select{font:inherit}
        .app{
            min-height:100vh;
            display:grid;
            grid-template-columns:245px 1fr
        }
        .side{
            background:#090909;
            color:#eee;
            padding:18px 13px
        }
        .logo{
            height:98px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-bottom:1px solid #292929;
            margin-bottom:15px
        }
        .logo img{
            width:205px;
            height:86px;
            object-fit:contain
        }
        .nav a{
            display:flex;
            gap:10px;
            padding:12px;
            border-radius:9px;
            margin:3px 0;
            font-size:14px
        }
        .nav a:hover,.nav a.active{background:var(--red)}
        .main{min-width:0}
        .top{
            height:74px;
            padding:0 3%;
            display:flex;
            align-items:center;
            justify-content:space-between;
            background:var(--card);
            border-bottom:1px solid var(--border)
        }
        .top h1{font-size:21px}
        .tools{display:flex;gap:8px}
        .tool{
            height:40px;
            padding:0 12px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--card);
            color:var(--text);
            cursor:pointer
        }
        .content{padding:25px 3% 55px}
        .headline{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:15px;
            margin-bottom:20px
        }
        .headline p{
            color:var(--muted);
            font-size:13px;
            margin-top:5px
        }
        .filters{
            display:grid;
            grid-template-columns:repeat(4,1fr) auto;
            gap:10px;
            padding:16px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:13px;
            margin-bottom:17px;
            box-shadow:var(--shadow)
        }
        .filters input,.filters select{
            height:44px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--bg);
            color:var(--text);
            padding:0 12px;
            outline:0
        }
        .filter-btn{
            height:44px;
            padding:0 18px;
            border:0;
            border-radius:8px;
            background:var(--red);
            color:#fff;
            cursor:pointer
        }
        .stats{
            display:grid;
            grid-template-columns:repeat(5,1fr);
            gap:11px;
            margin-bottom:17px
        }
        .stat{
            padding:16px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:12px;
            box-shadow:var(--shadow)
        }
        .stat span{
            color:var(--muted);
            font-size:11px
        }
        .stat strong{
            display:block;
            font-size:23px;
            margin-top:8px
        }
        .mini{
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:11px;
            margin-bottom:17px
        }
        .mini-card{
            padding:15px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:12px;
            box-shadow:var(--shadow)
        }
        .mini-card span{
            color:var(--muted);
            font-size:11px
        }
        .mini-card strong{
            display:block;
            margin-top:7px;
            font-size:20px
        }
        .table-card{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:13px;
            box-shadow:var(--shadow);
            overflow:hidden
        }
        .table-head{
            padding:16px 18px;
            border-bottom:1px solid var(--border)
        }
        .table-wrap{overflow:auto}
        table{
            width:100%;
            border-collapse:collapse;
            white-space:nowrap
        }
        th,td{
            text-align:start;
            padding:14px 15px;
            border-bottom:1px solid var(--border);
            font-size:12px
        }
        th{
            background:var(--bg);
            color:var(--muted);
            font-weight:normal
        }
        .status{
            display:inline-block;
            padding:6px 10px;
            border-radius:20px;
            font-size:10px
        }
        .pending{background:#fff3cd;color:#856404}
        .processing{background:#dbeafe;color:#1d4ed8}
        .shipped{background:#e0e7ff;color:#4338ca}
        .delivered{background:#d1fae5;color:#087443}
        .cancelled{background:#fee2e2;color:#b91c1c}
        .empty{
            text-align:center;
            padding:50px;
            color:var(--muted)
        }
        .toolbar{
            display:flex;
            gap:8px;
            flex-wrap:wrap
        }
        .secondary{
            height:42px;
            padding:0 14px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--card);
            color:var(--text);
            cursor:pointer
        }
        @media(max-width:1100px){
            .stats{grid-template-columns:repeat(3,1fr)}
            .mini{grid-template-columns:repeat(2,1fr)}
            .filters{grid-template-columns:repeat(2,1fr)}
        }
        @media(max-width:800px){
            .app{grid-template-columns:1fr}
            .side{display:none}
            .stats{grid-template-columns:1fr 1fr}
            .mini{grid-template-columns:1fr 1fr}
            .filters{grid-template-columns:1fr}
        }
        @media(max-width:520px){
            .stats,.mini{grid-template-columns:1fr}
            .headline{flex-direction:column;align-items:start}
        }
        @media print{
            .side,.top,.filters,.toolbar{display:none!important}
            .app{display:block}
            .content{padding:0}
            body{background:#fff}
            .table-card,.stat,.mini-card{box-shadow:none}
        }
    </style>
</head>

<body>

<div class="app">

    <aside class="side">
        <a class="logo" href="{{ url('/admin') }}">
            <img src="{{ asset('images/logo.jpeg') }}" alt="لمسة أنوثة">
        </a>

        <nav class="nav">
            <a href="{{ url('/admin') }}">⌂ لوحة التحكم</a>
            <a href="{{ url('/admin/orders') }}">▤ الطلبات</a>
            <a href="{{ url('/admin/products/import') }}">⇧ المنتجات</a>
            <a href="{{ url('/admin/coupons') }}">٪ الكوبونات</a>
            <a href="{{ url('/admin/pos') }}">▰ نقطة البيع</a>
            <a href="#">♙ العملاء</a>
            <a class="active" href="{{ route('admin.reports.index') }}">▥ التقارير</a>
        </nav>
    </aside>

    <main class="main">

        <header class="top">
            <h1>التقارير</h1>

            <div class="tools">
                <button class="tool" id="theme" type="button">☾</button>
            </div>
        </header>

        <div class="content">

            <section class="headline">
                <div>
                    <h2>تقرير المبيعات والطلبات</h2>
                    <p>ملخص حقيقي يعتمد على بيانات الطلبات المحفوظة في قاعدة البيانات.</p>
                </div>

                <div class="toolbar">
                    <button class="secondary" type="button" onclick="window.print()">
                        طباعة التقرير
                    </button>

                    <a class="secondary" href="{{ route('admin.reports.index') }}">
                        إعادة ضبط الفلاتر
                    </a>
                </div>
            </section>

            <form class="filters" method="GET" action="{{ route('admin.reports.index') }}">

                <input
                    type="date"
                    name="from"
                    value="{{ request('from') }}"
                    title="من تاريخ"
                >

                <input
                    type="date"
                    name="to"
                    value="{{ request('to') }}"
                    title="إلى تاريخ"
                >

                <select name="status">
                    <option value="">جميع حالات الطلب</option>
                    <option value="pending" @selected(request('status') === 'pending')>بانتظار التأكيد</option>
                    <option value="processing" @selected(request('status') === 'processing')>جاري التجهيز</option>
                    <option value="shipped" @selected(request('status') === 'shipped')>تم الشحن</option>
                    <option value="delivered" @selected(request('status') === 'delivered')>تم التوصيل</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
                </select>

                <select name="payment_status">
                    <option value="">جميع حالات الدفع</option>
                    <option value="pending" @selected(request('payment_status') === 'pending')>بانتظار الدفع</option>
                    <option value="paid" @selected(request('payment_status') === 'paid')>مدفوع</option>
                </select>

                <button class="filter-btn" type="submit">
                    تطبيق
                </button>
            </form>

            <section class="stats">

                <div class="stat">
                    <span>عدد الطلبات</span>
                    <strong>{{ $summary['orders_count'] ?? 0 }}</strong>
                </div>

                <div class="stat">
                    <span>المجموع الفرعي</span>
                    <strong>{{ number_format((float)($summary['subtotal'] ?? 0),3) }} ر.ع</strong>
                </div>

                <div class="stat">
                    <span>إجمالي الخصومات</span>
                    <strong>{{ number_format((float)($summary['discounts'] ?? 0),3) }} ر.ع</strong>
                </div>

                <div class="stat">
                    <span>رسوم التوصيل</span>
                    <strong>{{ number_format((float)($summary['shipping'] ?? 0),3) }} ر.ع</strong>
                </div>

                <div class="stat">
                    <span>إجمالي المبيعات</span>
                    <strong>{{ number_format((float)($summary['total_sales'] ?? 0),3) }} ر.ع</strong>
                </div>

            </section>

            <section class="mini">

                <div class="mini-card">
                    <span>بانتظار التأكيد</span>
                    <strong>{{ $summary['pending'] ?? 0 }}</strong>
                </div>

                <div class="mini-card">
                    <span>جاري التجهيز</span>
                    <strong>{{ $summary['processing'] ?? 0 }}</strong>
                </div>

                <div class="mini-card">
                    <span>تم الشحن</span>
                    <strong>{{ $summary['shipped'] ?? 0 }}</strong>
                </div>

                <div class="mini-card">
                    <span>تم التوصيل</span>
                    <strong>{{ $summary['delivered'] ?? 0 }}</strong>
                </div>

                <div class="mini-card">
                    <span>ملغي</span>
                    <strong>{{ $summary['cancelled'] ?? 0 }}</strong>
                </div>

                <div class="mini-card">
                    <span>مدفوع</span>
                    <strong>{{ $summary['payment_paid'] ?? 0 }}</strong>
                </div>

                <div class="mini-card">
                    <span>بانتظار الدفع</span>
                    <strong>{{ $summary['payment_pending'] ?? 0 }}</strong>
                </div>

                <div class="mini-card">
                    <span>متوسط قيمة الطلب</span>
                    <strong>
                        @php
                            $count = (int)($summary['orders_count'] ?? 0);
                            $sales = (float)($summary['total_sales'] ?? 0);
                            $average = $count > 0 ? $sales / $count : 0;
                        @endphp
                        {{ number_format($average,3) }} ر.ع
                    </strong>
                </div>

            </section>

            <section class="table-card">

                <div class="table-head">
                    <h3>تفاصيل الطلبات</h3>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>العميل</th>
                            <th>التاريخ</th>
                            <th>المجموع الفرعي</th>
                            <th>الخصم</th>
                            <th>التوصيل</th>
                            <th>الإجمالي</th>
                            <th>الدفع</th>
                            <th>حالة الطلب</th>
                        </tr>
                        </thead>

                        <tbody>

                        @forelse($orders as $order)

                            @php
                                $customerName = trim(
                                    ($order->first_name ?? '') . ' ' .
                                    ($order->last_name ?? '')
                                );

                                if ($customerName === '') {
                                    $customerName = optional($order->user)->name ?? '—';
                                }
                            @endphp

                            <tr>
                                <td>
                                    <strong>
                                        #{{ $order->order_number ?? $order->id }}
                                    </strong>
                                </td>

                                <td>{{ $customerName }}</td>

                                <td>
                                    {{ optional($order->created_at)->format('Y-m-d H:i') }}
                                </td>

                                <td>
                                    {{ number_format((float)($order->subtotal ?? 0),3) }} ر.ع
                                </td>

                                <td>
                                    {{ number_format((float)($order->discount_amount ?? 0),3) }} ر.ع
                                </td>

                                <td>
                                    {{ number_format((float)($order->shipping_fee ?? 0),3) }} ر.ع
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format((float)($order->total ?? 0),3) }} ر.ع
                                    </strong>
                                </td>

                                <td>
                                    {{ $order->payment_status ?? 'pending' }}
                                </td>

                                <td>
                                    <span class="status {{ $order->status ?? 'pending' }}">
                                        {{ $order->status ?? 'pending' }}
                                    </span>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="9">
                                    <div class="empty">
                                        لا توجد طلبات مطابقة للفلاتر الحالية.
                                    </div>
                                </td>
                            </tr>

                        @endforelse

                        </tbody>
                    </table>
                </div>

            </section>

        </div>
    </main>
</div>

<script>
const themeButton = document.getElementById('theme');

let theme = localStorage.adminTheme || 'light';

function applyTheme(){
    document.body.classList.toggle('dark', theme === 'dark');
    themeButton.textContent = theme === 'dark' ? '☀' : '☾';
}

themeButton.onclick = () => {
    theme = theme === 'dark' ? 'light' : 'dark';
    localStorage.adminTheme = theme;
    applyTheme();
};

applyTheme();
</script>

</body>
</html>
