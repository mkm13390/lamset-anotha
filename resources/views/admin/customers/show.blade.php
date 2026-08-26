<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تفاصيل العميل | لمسة أنوثة</title>

    <link rel="icon" href="{{ asset('images/logo.jpeg') }}">

    <style>
        *{
            box-sizing:border-box;
            margin:0;
            padding:0
        }

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
            color:var(--text)
        }

        a{
            color:inherit;
            text-decoration:none
        }

        button{
            font:inherit
        }

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

        .nav a:hover,
        .nav a.active{
            background:var(--red)
        }

        .main{
            min-width:0
        }

        .top{
            height:74px;
            padding:0 3%;
            display:flex;
            align-items:center;
            justify-content:space-between;
            background:var(--card);
            border-bottom:1px solid var(--border)
        }

        .top h1{
            font-size:21px
        }

        .tools{
            display:flex;
            gap:8px
        }

        .tool{
            height:40px;
            padding:0 12px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--card);
            color:var(--text);
            cursor:pointer
        }

        .content{
            padding:25px 3% 55px
        }

        .back{
            display:inline-flex;
            align-items:center;
            gap:7px;
            margin-bottom:18px;
            color:var(--muted);
            font-size:13px
        }

        .back:hover{
            color:var(--red)
        }

        .profile-card{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:15px;
            box-shadow:var(--shadow);
            padding:22px;
            margin-bottom:18px
        }

        .profile-top{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px
        }

        .profile-main{
            display:flex;
            align-items:center;
            gap:15px
        }

        .avatar{
            width:68px;
            height:68px;
            border-radius:50%;
            display:grid;
            place-items:center;
            background:rgba(226,27,35,.12);
            color:var(--red);
            font-size:26px;
            font-weight:bold
        }

        .profile-main h2{
            font-size:22px
        }

        .profile-main p{
            color:var(--muted);
            font-size:13px;
            margin-top:5px
        }

        .profile-actions{
            display:flex;
            gap:8px;
            flex-wrap:wrap
        }

        .btn{
            min-height:41px;
            border-radius:8px;
            padding:0 14px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:6px;
            border:1px solid var(--border);
            background:var(--card);
            color:var(--text);
            cursor:pointer
        }

        .btn:hover{
            border-color:var(--red);
            color:var(--red)
        }

        .btn.red{
            background:var(--red);
            color:#fff;
            border-color:var(--red)
        }

        .btn.green{
            background:#168b52;
            color:#fff;
            border-color:#168b52
        }

        .status{
            display:inline-block;
            padding:6px 10px;
            border-radius:20px;
            font-size:11px;
            background:#d1fae5;
            color:#087443;
            margin-top:8px
        }

        .status.inactive{
            background:#eee;
            color:#777
        }

        .stats{
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:12px;
            margin-top:20px
        }

        .stat{
            background:var(--bg);
            border-radius:11px;
            padding:15px
        }

        .stat span{
            display:block;
            color:var(--muted);
            font-size:11px
        }

        .stat strong{
            display:block;
            font-size:22px;
            margin-top:8px
        }

        .grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:18px
        }

        .card{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:14px;
            box-shadow:var(--shadow);
            overflow:hidden
        }

        .card-head{
            padding:17px 18px;
            border-bottom:1px solid var(--border);
            display:flex;
            align-items:center;
            justify-content:space-between
        }

        .card-head h3{
            font-size:16px
        }

        .card-body{
            padding:18px
        }

        .detail-list{
            display:grid;
            gap:12px
        }

        .detail-row{
            display:flex;
            justify-content:space-between;
            gap:12px;
            padding-bottom:10px;
            border-bottom:1px solid var(--border);
            font-size:13px
        }

        .detail-row:last-child{
            border-bottom:0;
            padding-bottom:0
        }

        .detail-row span{
            color:var(--muted)
        }

        .points-balance{
            text-align:center;
            padding:20px 0
        }

        .points-balance strong{
            display:block;
            font-size:38px;
            color:#c28200
        }

        .points-balance span{
            display:block;
            color:var(--muted);
            font-size:12px;
            margin-top:5px
        }

        .section{
            margin-top:18px
        }

        .table-wrap{
            overflow:auto
        }

        table{
            width:100%;
            border-collapse:collapse;
            white-space:nowrap
        }

        th,td{
            text-align:start;
            padding:13px 14px;
            border-bottom:1px solid var(--border);
            font-size:12px
        }

        th{
            background:var(--bg);
            color:var(--muted);
            font-weight:normal
        }

        .order-number{
            color:var(--red);
            font-weight:bold
        }

        .amount{
            font-weight:bold
        }

        .order-status{
            display:inline-block;
            padding:5px 8px;
            border-radius:20px;
            font-size:10px;
            background:#eee;
            color:#555
        }

        .order-status.pending{
            background:#fff3cd;
            color:#7a5b00
        }

        .order-status.processing{
            background:#dbeafe;
            color:#1d4ed8
        }

        .order-status.shipped{
            background:#e0e7ff;
            color:#4338ca
        }

        .order-status.delivered{
            background:#d1fae5;
            color:#087443
        }

        .order-status.cancelled{
            background:#fee2e2;
            color:#b91c1c
        }

        .transaction-positive{
            color:#087443;
            font-weight:bold
        }

        .transaction-negative{
            color:#b91c1c;
            font-weight:bold
        }

        .empty{
            padding:35px;
            text-align:center;
            color:var(--muted);
            font-size:13px
        }

        @media(max-width:1000px){
            .stats{
                grid-template-columns:1fr 1fr
            }

            .grid{
                grid-template-columns:1fr
            }
        }

        @media(max-width:800px){
            .app{
                grid-template-columns:1fr
            }

            .side{
                display:none
            }
        }

        @media(max-width:600px){
            .profile-top{
                flex-direction:column;
                align-items:flex-start
            }

            .profile-actions{
                width:100%
            }

            .profile-actions .btn{
                flex:1
            }

            .stats{
                grid-template-columns:1fr
            }

            .top h1{
                font-size:17px
            }
        }
    </style>
</head>

<body>

@php
    $pointsBalance = (int) ($loyaltyAccount->points_balance ?? 0);
    $totalEarned = (int) ($loyaltyAccount->total_points_earned ?? 0);
    $totalRedeemed = (int) ($loyaltyAccount->total_points_redeemed ?? 0);

    $totalSpent = (float) ($customer->total_spent ?? 0);

    $phone = $customer->phone ?? '';

    $cleanPhone = preg_replace('/\D+/', '', $phone);
    $cleanPhone = preg_replace('/^968/', '', $cleanPhone);
@endphp

<div class="app">

    <aside class="side">

        <a class="logo" href="{{ url('/admin') }}">
            <img src="{{ asset('images/logo.jpeg') }}" alt="لمسة أنوثة">
        </a>

        <nav class="nav">
            <a href="{{ url('/admin') }}">
                ⌂
                <span data-t="dashboard">لوحة التحكم</span>
            </a>

            <a href="{{ url('/admin/orders') }}">
                ▤
                <span data-t="orders">الطلبات</span>
            </a>

            <a class="active" href="{{ route('admin.customers.index') }}">
                ♙
                <span data-t="customers">العملاء</span>
            </a>

            <a href="{{ url('/admin/products/import') }}">
                ⇧
                <span data-t="products">المنتجات</span>
            </a>

            <a href="{{ url('/admin/pos') }}">
                ▰
                <span data-t="pos">نقطة البيع</span>
            </a>
        </nav>

    </aside>

    <main class="main">

        <header class="top">

            <h1 data-t="customerDetails">
                تفاصيل العميل
            </h1>

            <div class="tools">
                <button class="tool" id="theme" type="button">☾</button>
                <button class="tool" id="language" type="button">English</button>
            </div>

        </header>

        <div class="content">

            <a class="back" href="{{ route('admin.customers.index') }}">
                ←
                <span data-t="backCustomers">العودة إلى العملاء</span>
            </a>

            @if(session('success'))
                <div style="margin-bottom:15px;padding:13px 15px;border-radius:9px;background:#d1fae5;color:#087443;border:1px solid #a7f3d0;font-size:13px;">
                    {{ session('success') }}
                </div>
            @endif

            <section class="profile-card">

                <div class="profile-top">

                    <div class="profile-main">

                        <div class="avatar">
                            {{ mb_substr($customer->name ?? 'ع', 0, 1) }}
                        </div>

                        <div>

                            <h2>
                                {{ $customer->name }}
                            </h2>

                            <p>
                                {{ $customer->email }}
                            </p>

                            <p>
                                {{ $phone ?: 'لا يوجد رقم هاتف' }}
                            </p>

                            @if($customer->is_active)
                                <span class="status" data-t="active">
                                    نشط
                                </span>
                            @else
                                <span class="status inactive" data-t="inactive">
                                    غير نشط
                                </span>
                            @endif

                        </div>

                    </div>

                    <div class="profile-actions">

                        @if($phone)
                            <a
                                class="btn green"
                                href="https://wa.me/968{{ $cleanPhone }}"
                                target="_blank"
                            >
                                ☏
                                <span data-t="whatsapp">واتساب</span>
                            </a>
                        @endif

                        <form
                            method="POST"
                            action="{{ route('admin.customers.toggle', $customer) }}"
                            onsubmit="return confirmToggle()"
                        >
                            @csrf
                            @method('PATCH')

                            <button class="btn red" type="submit">

                                @if($customer->is_active)
                                    ⛔
                                    <span data-t="disable">إيقاف الحساب</span>
                                @else
                                    ✅
                                    <span data-t="enable">تفعيل الحساب</span>
                                @endif

                            </button>
                        </form>

                    </div>

                </div>

                <div class="stats">

                    <div class="stat">
                        <span data-t="ordersCount">عدد الطلبات</span>
                        <strong>
                            {{ number_format($customer->orders_count ?? 0) }}
                        </strong>
                    </div>

                    <div class="stat">
                        <span data-t="totalSpent">إجمالي المشتريات</span>
                        <strong>
                            {{ number_format($totalSpent, 3) }} ر.ع
                        </strong>
                    </div>

                    <div class="stat">
                        <span data-t="pointsBalance">رصيد النقاط</span>
                        <strong>
                            {{ number_format($pointsBalance) }}
                        </strong>
                    </div>

                    <div class="stat">
                        <span data-t="memberSince">عضو منذ</span>
                        <strong style="font-size:16px">
                            {{ optional($customer->created_at)->format('Y-m-d') ?? '—' }}
                        </strong>
                    </div>

                </div>

            </section>

            <div class="grid">

                <section class="card">

                    <div class="card-head">
                        <h3 data-t="customerInfo">
                            بيانات العميل
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="detail-list">

                            <div class="detail-row">
                                <span data-t="name">الاسم</span>
                                <b>{{ $customer->name }}</b>
                            </div>

                            <div class="detail-row">
                                <span data-t="email">البريد الإلكتروني</span>
                                <b>{{ $customer->email }}</b>
                            </div>

                            <div class="detail-row">
                                <span data-t="phone">الهاتف</span>
                                <b>{{ $phone ?: '—' }}</b>
                            </div>

                            <div class="detail-row">
                                <span data-t="createdAt">تاريخ التسجيل</span>
                                <b>
                                    {{ optional($customer->created_at)->format('Y-m-d H:i') ?? '—' }}
                                </b>
                            </div>

                            <div class="detail-row">
                                <span data-t="accountStatus">حالة الحساب</span>

                                <b>
                                    @if($customer->is_active)
                                        <span data-t="active">نشط</span>
                                    @else
                                        <span data-t="inactive">غير نشط</span>
                                    @endif
                                </b>
                            </div>

                        </div>

                    </div>

                </section>

                <section class="card">

                    <div class="card-head">
                        <h3 data-t="loyaltySummary">
                            ملخص نقاط الولاء
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="points-balance">

                            <strong>
                                ★ {{ number_format($pointsBalance) }}
                            </strong>

                            <span data-t="availablePoints">
                                النقاط المتاحة حاليًا
                            </span>

                        </div>

                        <div class="detail-list">

                            <div class="detail-row">
                                <span data-t="totalEarned">
                                    إجمالي النقاط المكتسبة
                                </span>

                                <b>
                                    {{ number_format($totalEarned) }}
                                </b>
                            </div>

                            <div class="detail-row">
                                <span data-t="totalRedeemed">
                                    إجمالي النقاط المستخدمة
                                </span>

                                <b>
                                    {{ number_format($totalRedeemed) }}
                                </b>
                            </div>

                        </div>

                    </div>

                </section>

            </div>

            <section class="card section">

                <div class="card-head">
                    <h3 data-t="recentOrders">
                        طلبات العميل
                    </h3>

                    <span style="color:var(--muted);font-size:12px">
                        {{ number_format($customer->orders->count()) }}
                    </span>
                </div>

                <div class="table-wrap">

                    @if($customer->orders->count())

                        <table>

                            <thead>
                                <tr>
                                    <th data-t="orderNumber">رقم الطلب</th>
                                    <th data-t="date">التاريخ</th>
                                    <th data-t="itemsCount">المنتجات</th>
                                    <th data-t="payment">الدفع</th>
                                    <th data-t="status">الحالة</th>
                                    <th data-t="total">الإجمالي</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($customer->orders as $order)

                                    <tr>

                                        <td class="order-number">
                                            {{ $order->order_number ?? ('#'.$order->id) }}
                                        </td>

                                        <td>
                                            {{ optional($order->created_at)->format('Y-m-d H:i') ?? '—' }}
                                        </td>

                                        <td>
                                            {{ $order->items->sum('quantity') }}
                                        </td>

                                        <td>
                                            {{ $order->payment_method ?? '—' }}
                                        </td>

                                        <td>
                                            <span class="order-status {{ $order->status }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>

                                        <td class="amount">
                                            {{ number_format((float) $order->total, 3) }} ر.ع
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    @else

                        <div class="empty" data-t="noOrders">
                            لا توجد طلبات لهذا العميل حتى الآن.
                        </div>

                    @endif

                </div>

            </section>

            <section class="card section">

                <div class="card-head">
                    <h3 data-t="pointsHistory">
                        سجل نقاط الولاء
                    </h3>
                </div>

                <div class="table-wrap">

                    @if($loyaltyTransactions->count())

                        <table>

                            <thead>
                                <tr>
                                    <th data-t="date">التاريخ</th>
                                    <th data-t="type">النوع</th>
                                    <th data-t="description">الوصف</th>
                                    <th data-t="points">النقاط</th>
                                    <th data-t="balance">الرصيد بعد العملية</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($loyaltyTransactions as $transaction)

                                    <tr>

                                        <td>
                                            {{ optional($transaction->created_at)->format('Y-m-d H:i') ?? '—' }}
                                        </td>

                                        <td>
                                            {{ $transaction->type }}
                                        </td>

                                        <td>
                                            {{ $transaction->description ?? '—' }}
                                        </td>

                                        <td
                                            class="{{ $transaction->points >= 0 ? 'transaction-positive' : 'transaction-negative' }}"
                                        >
                                            {{ $transaction->points > 0 ? '+' : '' }}
                                            {{ number_format($transaction->points) }}
                                        </td>

                                        <td>
                                            {{ number_format($transaction->balance_after) }}
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    @else

                        <div class="empty" data-t="noTransactions">
                            لا توجد حركات نقاط لهذا العميل حتى الآن.
                        </div>

                    @endif

                </div>

            </section>

        </div>

    </main>

</div>

<script>
    const q = selector => document.querySelector(selector);
    const qa = selector => [...document.querySelectorAll(selector)];

    let lang = localStorage.adminLanguage || 'ar';
    let theme = localStorage.adminTheme || 'light';

    const tr = {
        ar: {
            dashboard: 'لوحة التحكم',
            orders: 'الطلبات',
            customers: 'العملاء',
            products: 'المنتجات',
            pos: 'نقطة البيع',
            customerDetails: 'تفاصيل العميل',
            backCustomers: 'العودة إلى العملاء',
            whatsapp: 'واتساب',
            disable: 'إيقاف الحساب',
            enable: 'تفعيل الحساب',
            active: 'نشط',
            inactive: 'غير نشط',
            ordersCount: 'عدد الطلبات',
            totalSpent: 'إجمالي المشتريات',
            pointsBalance: 'رصيد النقاط',
            memberSince: 'عضو منذ',
            customerInfo: 'بيانات العميل',
            name: 'الاسم',
            email: 'البريد الإلكتروني',
            phone: 'الهاتف',
            createdAt: 'تاريخ التسجيل',
            accountStatus: 'حالة الحساب',
            loyaltySummary: 'ملخص نقاط الولاء',
            availablePoints: 'النقاط المتاحة حاليًا',
            totalEarned: 'إجمالي النقاط المكتسبة',
            totalRedeemed: 'إجمالي النقاط المستخدمة',
            recentOrders: 'طلبات العميل',
            orderNumber: 'رقم الطلب',
            date: 'التاريخ',
            itemsCount: 'المنتجات',
            payment: 'الدفع',
            status: 'الحالة',
            total: 'الإجمالي',
            noOrders: 'لا توجد طلبات لهذا العميل حتى الآن.',
            pointsHistory: 'سجل نقاط الولاء',
            type: 'النوع',
            description: 'الوصف',
            points: 'النقاط',
            balance: 'الرصيد بعد العملية',
            noTransactions: 'لا توجد حركات نقاط لهذا العميل حتى الآن.'
        },

        en: {
            dashboard: 'Dashboard',
            orders: 'Orders',
            customers: 'Customers',
            products: 'Products',
            pos: 'Point of Sale',
            customerDetails: 'Customer Details',
            backCustomers: 'Back to Customers',
            whatsapp: 'WhatsApp',
            disable: 'Disable Account',
            enable: 'Enable Account',
            active: 'Active',
            inactive: 'Inactive',
            ordersCount: 'Orders',
            totalSpent: 'Total Spent',
            pointsBalance: 'Points Balance',
            memberSince: 'Member Since',
            customerInfo: 'Customer Information',
            name: 'Name',
            email: 'Email',
            phone: 'Phone',
            createdAt: 'Registration Date',
            accountStatus: 'Account Status',
            loyaltySummary: 'Loyalty Summary',
            availablePoints: 'Currently Available Points',
            totalEarned: 'Total Points Earned',
            totalRedeemed: 'Total Points Redeemed',
            recentOrders: 'Customer Orders',
            orderNumber: 'Order Number',
            date: 'Date',
            itemsCount: 'Items',
            payment: 'Payment',
            status: 'Status',
            total: 'Total',
            noOrders: 'This customer has no orders yet.',
            pointsHistory: 'Loyalty Points History',
            type: 'Type',
            description: 'Description',
            points: 'Points',
            balance: 'Balance After Transaction',
            noTransactions: 'This customer has no loyalty transactions yet.'
        }
    };

    function applyTheme(){
        document.body.classList.toggle(
            'dark',
            theme === 'dark'
        );

        q('#theme').textContent =
            theme === 'dark'
                ? '☀️'
                : '☾';
    }

    function applyLang(){
        document.documentElement.lang = lang;

        document.documentElement.dir =
            lang === 'ar'
                ? 'rtl'
                : 'ltr';

        q('#language').textContent =
            lang === 'ar'
                ? 'English'
                : 'العربية';

        qa('[data-t]').forEach(element => {
            const value = tr[lang][element.dataset.t];

            if(value){
                element.textContent = value;
            }
        });
    }

    q('#theme').onclick = () => {
        theme =
            theme === 'dark'
                ? 'light'
                : 'dark';

        localStorage.adminTheme = theme;

        applyTheme();
    };

    q('#language').onclick = () => {
        lang =
            lang === 'ar'
                ? 'en'
                : 'ar';

        localStorage.adminLanguage = lang;

        applyLang();
    };

    function confirmToggle(){
        return confirm(
            lang === 'ar'
                ? 'هل أنت متأكد من تغيير حالة حساب هذا العميل؟'
                : 'Are you sure you want to change this customer account status?'
        );
    }

    applyTheme();
    applyLang();
</script>

</body>
</html>