<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>إدارة العملاء | لمسة أنوثة</title>

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
            color:var(--text)
        }

        a{
            color:inherit;
            text-decoration:none
        }

        button,input,select{
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

        .headline{
            display:flex;
            justify-content:space-between;
            gap:15px;
            align-items:center;
            margin-bottom:19px
        }

        .headline p{
            color:var(--muted);
            font-size:13px;
            margin-top:5px
        }

        .primary{
            min-height:43px;
            padding:0 16px;
            border:0;
            border-radius:8px;
            background:var(--red);
            color:#fff;
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            justify-content:center
        }

        .stats{
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:12px;
            margin-bottom:17px
        }

        .stat{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:12px;
            padding:17px;
            box-shadow:var(--shadow)
        }

        .stat span{
            color:var(--muted);
            font-size:12px
        }

        .stat strong{
            display:block;
            font-size:25px;
            margin-top:9px
        }

        .success-message{
            margin-bottom:15px;
            padding:13px 15px;
            border-radius:9px;
            background:#d1fae5;
            color:#087443;
            border:1px solid #a7f3d0;
            font-size:13px
        }

        .filters{
            display:grid;
            grid-template-columns:1fr 180px auto;
            gap:10px;
            padding:16px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:13px 13px 0 0
        }

        .filters input,
        .filters select{
            height:44px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--bg);
            color:var(--text);
            padding:0 12px;
            outline:0
        }

        .search-btn{
            height:44px;
            padding:0 18px;
            border:0;
            border-radius:8px;
            background:var(--red);
            color:#fff;
            cursor:pointer
        }

        .clear-btn{
            height:44px;
            padding:0 15px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--card);
            color:var(--text);
            display:inline-flex;
            align-items:center;
            justify-content:center
        }

        .table-card{
            background:var(--card);
            border:1px solid var(--border);
            border-top:0;
            border-radius:0 0 13px 13px;
            box-shadow:var(--shadow);
            overflow:hidden
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
            padding:14px 15px;
            border-bottom:1px solid var(--border);
            font-size:12px
        }

        th{
            background:var(--bg);
            color:var(--muted);
            font-weight:normal
        }

        .customer{
            display:flex;
            align-items:center;
            gap:10px
        }

        .avatar{
            width:39px;
            height:39px;
            display:grid;
            place-items:center;
            border-radius:50%;
            background:rgba(226,27,35,.12);
            color:var(--red);
            font-weight:bold
        }

        .customer small{
            display:block;
            color:var(--muted);
            margin-top:3px
        }

        .points{
            color:#c28200;
            font-weight:bold
        }

        .spent{
            color:var(--red);
            font-weight:bold
        }

        .status{
            display:inline-block;
            padding:6px 9px;
            border-radius:20px;
            font-size:10px;
            background:#d1fae5;
            color:#087443
        }

        .status.inactive{
            background:#eee;
            color:#777
        }

        .actions{
            display:flex;
            gap:6px;
            align-items:center
        }

        .icon-btn{
            width:36px;
            height:36px;
            border:1px solid var(--border);
            border-radius:7px;
            background:var(--card);
            color:var(--text);
            cursor:pointer;
            display:grid;
            place-items:center
        }

        .icon-btn:hover{
            border-color:var(--red);
            color:var(--red)
        }

        .toggle-btn{
            width:auto;
            padding:0 10px
        }

        .empty{
            text-align:center;
            padding:55px;
            color:var(--muted)
        }

        .empty span{
            display:block;
            font-size:42px;
            margin-bottom:10px
        }

        .pager{
            padding:14px 17px;
            color:var(--muted);
            font-size:12px
        }

        .pagination-wrap{
            padding:15px;
            border-top:1px solid var(--border)
        }

        .pagination-wrap nav{
            width:100%
        }

        .pagination-wrap svg{
            width:18px
        }

        .pagination-wrap .flex{
            display:flex
        }

        .pagination-wrap .items-center{
            align-items:center
        }

        .pagination-wrap .justify-between{
            justify-content:space-between
        }

        .pagination-wrap a,
        .pagination-wrap span{
            color:var(--text)
        }

        .modal{
            display:none;
            position:fixed;
            inset:0;
            z-index:100;
            background:rgba(0,0,0,.65);
            place-items:center;
            padding:18px
        }

        .modal.open{
            display:grid
        }

        .modal-box{
            width:min(650px,100%);
            max-height:92vh;
            overflow:auto;
            background:var(--card);
            border-radius:15px;
            padding:22px
        }

        .modal-head{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding-bottom:14px;
            border-bottom:1px solid var(--border)
        }

        .close{
            border:0;
            background:none;
            color:var(--text);
            font-size:25px;
            cursor:pointer
        }

        .profile{
            display:flex;
            align-items:center;
            gap:14px;
            padding:18px 0
        }

        .profile .avatar{
            width:58px;
            height:58px;
            font-size:21px
        }

        .profile p{
            color:var(--muted);
            margin-top:5px
        }

        .detail-grid{
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:11px
        }

        .detail{
            background:var(--bg);
            padding:13px;
            border-radius:9px
        }

        .detail small{
            display:block;
            color:var(--muted);
            margin-bottom:6px
        }

        .modal-actions{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:10px;
            margin-top:17px
        }

        .modal-action{
            min-height:44px;
            border-radius:9px;
            display:flex;
            align-items:center;
            justify-content:center;
            border:1px solid var(--border);
            background:var(--card)
        }

        .modal-action.primary-link{
            background:var(--red);
            color:#fff;
            border-color:var(--red)
        }

        @media(max-width:1000px){
            .stats{
                grid-template-columns:1fr 1fr
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

        @media(max-width:650px){
            .filters{
                grid-template-columns:1fr
            }
        }

        @media(max-width:560px){
            .stats{
                grid-template-columns:1fr
            }

            .headline{
                align-items:start;
                flex-direction:column
            }

            .primary{
                width:100%
            }

            .detail-grid,
            .modal-actions{
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
    $customerIds = collect($customers->items())->pluck('id');

    $loyaltyBalances = \App\Models\LoyaltyAccount::whereIn('user_id', $customerIds)
        ->pluck('points_balance', 'user_id');

    $totalPointsIssued = \App\Models\LoyaltyAccount::sum('total_points_earned');

    $newCustomersThisMonth = \App\Models\User::where('role', 'customer')
        ->whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->count();
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

            <a class="active" href="{{ url('/admin/customers') }}">
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

            <h1 data-t="customerManagement">
                إدارة العملاء
            </h1>

            <div class="tools">
                <button class="tool" id="theme">☾</button>
                <button class="tool" id="language">English</button>
            </div>

        </header>


        <div class="content">

            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif


            <section class="headline">

                <div>
                    <h2 data-t="allCustomers">
                        جميع العملاء
                    </h2>

                    <p data-t="intro">
                        متابعة العملاء وطلباتهم ونقاط لمسة.
                    </p>
                </div>

            </section>


            <section class="stats">

                <div class="stat">
                    <span data-t="totalCustomers">
                        إجمالي العملاء
                    </span>

                    <strong>
                        {{ number_format($stats['total_customers'] ?? 0) }}
                    </strong>
                </div>


                <div class="stat">
                    <span data-t="newCustomers">
                        عملاء هذا الشهر
                    </span>

                    <strong>
                        {{ number_format($newCustomersThisMonth) }}
                    </strong>
                </div>


                <div class="stat">
                    <span data-t="activeCustomers">
                        العملاء النشطون
                    </span>

                    <strong>
                        {{ number_format($stats['active_customers'] ?? 0) }}
                    </strong>
                </div>


                <div class="stat">
                    <span data-t="pointsIssued">
                        إجمالي النقاط الممنوحة
                    </span>

                    <strong>
                        {{ number_format($totalPointsIssued) }}
                    </strong>
                </div>

            </section>


            <form
                class="filters"
                method="GET"
                action="{{ route('admin.customers.index') }}"
            >

                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? request('search') }}"
                    data-placeholder-ar="بحث بالاسم أو الهاتف أو البريد..."
                    data-placeholder-en="Search name, phone or email..."
                    placeholder="بحث بالاسم أو الهاتف أو البريد..."
                >

                <button
                    class="search-btn"
                    type="submit"
                    data-t="search"
                >
                    🔍 بحث
                </button>

                @if(request()->filled('search'))
                    <a
                        class="clear-btn"
                        href="{{ route('admin.customers.index') }}"
                        data-t="clear"
                    >
                        ✕ مسح
                    </a>
                @else
                    <div></div>
                @endif

            </form>


            <section class="table-card">

                <div class="table-wrap">

                    <table>

                        <thead>
                            <tr>
                                <th data-t="customer">العميل</th>
                                <th data-t="phone">الهاتف</th>
                                <th data-t="ordersCount">الطلبات</th>
                                <th data-t="totalSpent">إجمالي المشتريات</th>
                                <th data-t="points">نقاط لمسة</th>
                                <th data-t="status">الحالة</th>
                                <th data-t="actions">الإجراءات</th>
                            </tr>
                        </thead>


                        <tbody>

                            @forelse($customers as $customer)

                                @php
                                    $customerStatus = $customer->is_active
                                        ? 'active'
                                        : 'inactive';

                                    $phone = $customer->phone ?? '';

                                    $pointsBalance = (int) (
                                        $loyaltyBalances[$customer->id] ?? 0
                                    );

                                    $totalSpent = (float) (
                                        $customer->total_spent ?? 0
                                    );
                                @endphp


                                <tr>

                                    <td>
                                        <div class="customer">

                                            <span class="avatar">
                                                {{ mb_substr($customer->name ?? 'ع', 0, 1) }}
                                            </span>

                                            <div>
                                                <b>
                                                    {{ $customer->name }}
                                                </b>

                                                <small>
                                                    {{ $customer->email }}
                                                </small>
                                            </div>

                                        </div>
                                    </td>


                                    <td>
                                        {{ $phone ?: '—' }}
                                    </td>


                                    <td>
                                        {{ number_format($customer->orders_count ?? 0) }}
                                    </td>


                                    <td class="spent">
                                        {{ number_format($totalSpent, 3) }} ر.ع
                                    </td>


                                    <td class="points">
                                        ★ {{ number_format($pointsBalance) }}
                                    </td>


                                    <td>

                                        @if($customer->is_active)

                                            <span class="status" data-t="active">
                                                نشط
                                            </span>

                                        @else

                                            <span class="status inactive" data-t="inactive">
                                                غير نشط
                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        <div class="actions">

                                            <button
                                                type="button"
                                                class="icon-btn view"
                                                title="معاينة"
                                                data-id="{{ $customer->id }}"
                                                data-name="{{ $customer->name }}"
                                                data-phone="{{ $phone }}"
                                                data-email="{{ $customer->email }}"
                                                data-orders="{{ $customer->orders_count ?? 0 }}"
                                                data-spent="{{ number_format($totalSpent, 3) }}"
                                                data-points="{{ $pointsBalance }}"
                                                data-status="{{ $customerStatus }}"
                                                data-details-url="{{ route('admin.customers.show', $customer) }}"
                                            >
                                                👁️
                                            </button>


                                            @if($phone)

                                                <a
                                                    class="icon-btn"
                                                    href="https://wa.me/968{{ preg_replace('/\D+/', '', preg_replace('/^968/', '', $phone)) }}"
                                                    target="_blank"
                                                    title="واتساب"
                                                >
                                                    ☏
                                                </a>

                                            @endif


                                            <form
                                                method="POST"
                                                action="{{ route('admin.customers.toggle', $customer) }}"
                                                onsubmit="return confirmToggle(this)"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="icon-btn toggle-btn"
                                                    title="{{ $customer->is_active ? 'إيقاف الحساب' : 'تفعيل الحساب' }}"
                                                >

                                                    @if($customer->is_active)
                                                        ⛔
                                                    @else
                                                        ✅
                                                    @endif

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7">

                                        <div class="empty">

                                            <span>♙</span>

                                            <p data-t="noCustomers">
                                                لا يوجد عملاء حتى الآن
                                            </p>

                                        </div>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                <div class="pager">

                    <span data-t="showing">
                        عدد العملاء في هذه الصفحة:
                    </span>

                    {{ $customers->count() }}

                    /

                    {{ $customers->total() }}

                </div>


                @if($customers->hasPages())

                    <div class="pagination-wrap">
                        {{ $customers->links() }}
                    </div>

                @endif

            </section>

        </div>

    </main>

</div>


<div class="modal" id="modal">

    <div class="modal-box">

        <div class="modal-head">

            <h2 data-t="customerDetails">
                تفاصيل العميل
            </h2>

            <button
                class="close"
                id="close"
                type="button"
            >
                ×
            </button>

        </div>


        <div class="profile">

            <span
                class="avatar"
                id="modalAvatar"
            >
                ع
            </span>

            <div>

                <h2 id="modalName"></h2>

                <p id="modalContact"></p>

            </div>

        </div>


        <div class="detail-grid">

            <div class="detail">
                <small data-t="ordersCount">
                    الطلبات
                </small>

                <b id="modalOrders"></b>
            </div>


            <div class="detail">
                <small data-t="totalSpent">
                    إجمالي المشتريات
                </small>

                <b id="modalSpent"></b>
            </div>


            <div class="detail">
                <small data-t="points">
                    نقاط لمسة
                </small>

                <b id="modalPoints"></b>
            </div>


            <div class="detail">
                <small data-t="status">
                    حالة الحساب
                </small>

                <b id="modalStatus"></b>
            </div>

        </div>


        <div class="modal-actions">

            <a
                href="#"
                class="modal-action primary-link"
                id="modalDetailsLink"
                data-t="fullDetails"
            >
                👤 عرض التفاصيل الكاملة
            </a>

            <button
                type="button"
                class="modal-action"
                id="modalCloseButton"
                data-t="close"
            >
                إغلاق
            </button>

        </div>

    </div>

</div>


<script>
    const q = selector => document.querySelector(selector);

    const qa = selector => [
        ...document.querySelectorAll(selector)
    ];


    let lang =
        localStorage.adminLanguage || 'ar';


    let theme =
        localStorage.adminTheme || 'light';


    const tr = {

        ar: {
            dashboard: 'لوحة التحكم',
            orders: 'الطلبات',
            customers: 'العملاء',
            products: 'المنتجات',
            pos: 'نقطة البيع',

            customerManagement: 'إدارة العملاء',
            allCustomers: 'جميع العملاء',

            intro:
                'متابعة العملاء وطلباتهم ونقاط لمسة.',

            totalCustomers:
                'إجمالي العملاء',

            newCustomers:
                'عملاء هذا الشهر',

            activeCustomers:
                'العملاء النشطون',

            pointsIssued:
                'إجمالي النقاط الممنوحة',

            customer:
                'العميل',

            phone:
                'الهاتف',

            ordersCount:
                'الطلبات',

            totalSpent:
                'إجمالي المشتريات',

            points:
                'نقاط لمسة',

            status:
                'الحالة',

            actions:
                'الإجراءات',

            active:
                'نشط',

            inactive:
                'غير نشط',

            noCustomers:
                'لا يوجد عملاء حتى الآن',

            customerDetails:
                'تفاصيل العميل',

            fullDetails:
                '👤 عرض التفاصيل الكاملة',

            search:
                '🔍 بحث',

            clear:
                '✕ مسح',

            showing:
                'عدد العملاء في هذه الصفحة:',

            close:
                'إغلاق'
        },


        en: {
            dashboard: 'Dashboard',
            orders: 'Orders',
            customers: 'Customers',
            products: 'Products',
            pos: 'Point of Sale',

            customerManagement:
                'Customer Management',

            allCustomers:
                'All Customers',

            intro:
                'Manage customers, orders and Lamset Points.',

            totalCustomers:
                'Total Customers',

            newCustomers:
                'New This Month',

            activeCustomers:
                'Active Customers',

            pointsIssued:
                'Total Points Issued',

            customer:
                'Customer',

            phone:
                'Phone',

            ordersCount:
                'Orders',

            totalSpent:
                'Total Spent',

            points:
                'Lamset Points',

            status:
                'Status',

            actions:
                'Actions',

            active:
                'Active',

            inactive:
                'Inactive',

            noCustomers:
                'No customers yet',

            customerDetails:
                'Customer Details',

            fullDetails:
                '👤 Full Customer Details',

            search:
                '🔍 Search',

            clear:
                '✕ Clear',

            showing:
                'Customers on this page:',

            close:
                'Close'
        }

    };


    function applyTheme() {

        document.body.classList.toggle(
            'dark',
            theme === 'dark'
        );

        q('#theme').textContent =
            theme === 'dark'
                ? '☀️'
                : '☾';

    }


    function applyLang() {

        document.documentElement.lang =
            lang;

        document.documentElement.dir =
            lang === 'ar'
                ? 'rtl'
                : 'ltr';


        q('#language').textContent =
            lang === 'ar'
                ? 'English'
                : 'العربية';


        qa('[data-t]').forEach(element => {

            const value =
                tr[lang][element.dataset.t];

            if(value){
                element.textContent = value;
            }

        });


        qa('[data-placeholder-ar]').forEach(element => {

            element.placeholder =
                lang === 'ar'
                    ? element.dataset.placeholderAr
                    : element.dataset.placeholderEn;

        });

    }


    qa('.view').forEach(button => {

        button.onclick = () => {

            const name =
                button.dataset.name || '—';

            const phone =
                button.dataset.phone || '—';

            const email =
                button.dataset.email || '—';

            const status =
                button.dataset.status === 'active'
                    ? tr[lang].active
                    : tr[lang].inactive;


            q('#modalAvatar').textContent =
                name.charAt(0);


            q('#modalName').textContent =
                name;


            q('#modalContact').textContent =
                phone + ' — ' + email;


            q('#modalOrders').textContent =
                button.dataset.orders;


            q('#modalSpent').textContent =
                button.dataset.spent + ' ر.ع';


            q('#modalPoints').textContent =
                '★ ' + button.dataset.points;


            q('#modalStatus').textContent =
                status;


            q('#modalDetailsLink').href =
                button.dataset.detailsUrl;


            q('#modal').classList.add('open');

        };

    });


    function closeModal(){

        q('#modal').classList.remove('open');

    }


    q('#close').onclick =
        closeModal;


    q('#modalCloseButton').onclick =
        closeModal;


    q('#modal').onclick = event => {

        if(event.target === q('#modal')){
            closeModal();
        }

    };


    q('#theme').onclick = () => {

        theme =
            theme === 'dark'
                ? 'light'
                : 'dark';

        localStorage.adminTheme =
            theme;

        applyTheme();

    };


    q('#language').onclick = () => {

        lang =
            lang === 'ar'
                ? 'en'
                : 'ar';

        localStorage.adminLanguage =
            lang;

        applyLang();

    };


    function confirmToggle(form){

        const message =
            lang === 'ar'
                ? 'هل أنت متأكد من تغيير حالة حساب هذا العميل؟'
                : 'Are you sure you want to change this customer account status?';

        return confirm(message);

    }


    applyTheme();

    applyLang();
</script>

</body>
</html>