<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إدارة الطلبات | لمسة أنوثة</title>
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
        .export{
            height:43px;
            padding:0 16px;
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
        .stat span{color:var(--muted);font-size:11px}
        .stat strong{
            display:block;
            font-size:23px;
            margin-top:8px
        }
        .filters{
            display:grid;
            grid-template-columns:1fr 180px 160px;
            gap:10px;
            padding:16px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:13px 13px 0 0
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
        .table-card{
            background:var(--card);
            border:1px solid var(--border);
            border-top:0;
            border-radius:0 0 13px 13px;
            box-shadow:var(--shadow);
            overflow:hidden
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
        .order-no{color:var(--red);font-weight:bold}
        .customer small{
            display:block;
            color:var(--muted);
            margin-top:4px
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
        .actions{display:flex;gap:6px}
        .icon-btn{
            width:34px;
            height:34px;
            border:1px solid var(--border);
            border-radius:7px;
            background:var(--card);
            color:var(--text);
            cursor:pointer
        }
        .icon-btn:hover{border-color:var(--red);color:var(--red)}
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
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:14px 17px;
            color:var(--muted);
            font-size:12px
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
        .modal.open{display:grid}
        .modal-box{
            width:min(720px,100%);
            max-height:90vh;
            overflow:auto;
            background:var(--card);
            border-radius:15px;
            padding:22px
        }
        .modal-head{
            display:flex;
            justify-content:space-between;
            align-items:center;
            border-bottom:1px solid var(--border);
            padding-bottom:14px
        }
        .close{
            border:0;
            background:none;
            color:var(--text);
            font-size:25px;
            cursor:pointer
        }
        .detail-grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:12px;
            margin:18px 0
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
        .update-row{
            display:flex;
            gap:9px
        }
        .update-row select{
            flex:1;
            height:44px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--bg);
            color:var(--text);
            padding:0 10px
        }
        .save{
            border:0;
            border-radius:8px;
            background:var(--red);
            color:#fff;
            padding:0 16px;
            cursor:pointer
        }
        .notice{
            margin-bottom:15px;
            padding:11px 13px;
            border-radius:8px;
            font-size:12px
        }
        .notice.success{
            background:#d1fae5;
            color:#087443
        }
        .notice.error{
            background:#fee2e2;
            color:#b91c1c
        }
        .order-items{
            margin:18px 0;
            border-top:1px solid var(--border)
        }
        .order-items h3{margin:16px 0 8px}
        .item-line{
            display:flex;
            justify-content:space-between;
            gap:12px;
            padding:10px 0;
            border-bottom:1px solid var(--border);
            font-size:12px
        }
        .item-line small{color:var(--muted)}
        @media(max-width:1050px){
            .stats{grid-template-columns:repeat(3,1fr)}
        }
        @media(max-width:800px){
            .app{grid-template-columns:1fr}
            .side{display:none}
            .filters{grid-template-columns:1fr}
            .stats{grid-template-columns:1fr 1fr}
        }
        @media(max-width:520px){
            .stats{grid-template-columns:1fr}
            .headline{align-items:start;flex-direction:column}
            .export{width:100%}
            .detail-grid{grid-template-columns:1fr}
            .top h1{font-size:17px}
            .update-row{flex-direction:column}
            .save{height:44px}
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
            <a href="{{ url('/admin') }}">⌂ <span data-t="dashboard">لوحة التحكم</span></a>
            <a class="active" href="{{ route('admin.orders.index') }}">▤ <span data-t="orders">الطلبات</span></a>
            <a href="{{ url('/admin/products/import') }}">⇧ <span data-t="products">المنتجات</span></a>
            <a href="{{ url('/admin/pos') }}">▰ <span data-t="pos">نقطة البيع</span></a>
            <a href="{{ url('/admin/customers') }}">♙ <span data-t="customers">العملاء</span></a>
            <a href="{{ url('/admin/finance') }}">▥ <span data-t="reports">التقارير</span></a>
        </nav>
    </aside>

    <main class="main">

        <header class="top">
            <h1 data-t="ordersManagement">إدارة الطلبات</h1>

            <div class="tools">
                <button class="tool" id="theme" type="button">☾</button>
                <button class="tool" id="language" type="button">English</button>
            </div>
        </header>

        <div class="content">

            @if(session('success'))
                <div class="notice success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="notice error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <section class="headline">
                <div>
                    <h2 data-t="allOrders">جميع الطلبات</h2>
                    <p data-t="intro">متابعة الطلبات وتحديث حالتها والتواصل مع العملاء.</p>
                </div>

                <button class="export" id="export" type="button" data-t="export">↓ تصدير Excel</button>
            </section>

            <section class="stats">
                <div class="stat">
                    <span data-t="all">الكل</span>
                    <strong>{{ $counts['all'] ?? 0 }}</strong>
                </div>

                <div class="stat">
                    <span data-t="pending">بانتظار التأكيد</span>
                    <strong>{{ $counts['pending'] ?? 0 }}</strong>
                </div>

                <div class="stat">
                    <span data-t="processing">جاري التجهيز</span>
                    <strong>{{ $counts['processing'] ?? 0 }}</strong>
                </div>

                <div class="stat">
                    <span data-t="shipped">تم الشحن</span>
                    <strong>{{ $counts['shipped'] ?? 0 }}</strong>
                </div>

                <div class="stat">
                    <span data-t="delivered">تم التوصيل</span>
                    <strong>{{ $counts['delivered'] ?? 0 }}</strong>
                </div>
            </section>

            <div class="filters">
                <input
                    id="search"
                    data-placeholder-ar="بحث برقم الطلب أو اسم العميل أو الهاتف..."
                    data-placeholder-en="Search order, customer or phone..."
                    placeholder="بحث برقم الطلب أو اسم العميل أو الهاتف..."
                >

                <select id="status">
                    <option value="all" data-t="allStatuses">جميع الحالات</option>
                    <option value="pending" data-t="pending">بانتظار التأكيد</option>
                    <option value="processing" data-t="processing">جاري التجهيز</option>
                    <option value="shipped" data-t="shipped">تم الشحن</option>
                    <option value="delivered" data-t="delivered">تم التوصيل</option>
                    <option value="cancelled" data-t="cancelled">ملغي</option>
                </select>

                <input id="date" type="date">
            </div>

            <section class="table-card">

                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th data-t="orderNo">رقم الطلب</th>
                            <th data-t="customer">العميل</th>
                            <th data-t="date">التاريخ</th>
                            <th data-t="amount">الإجمالي</th>
                            <th data-t="payment">الدفع</th>
                            <th data-t="statusLabel">الحالة</th>
                            <th data-t="actions">الإجراءات</th>
                        </tr>
                        </thead>

                        <tbody id="rows">

                        @forelse(($orders ?? []) as $order)

                            @php
                                $status = $order->status ?? 'pending';

                                $phone = $order->phone
                                    ?? optional($order->user)->phone
                                    ?? '';

                                $name = trim(
                                    ($order->first_name ?? '') . ' ' .
                                    ($order->last_name ?? '')
                                );

                                if ($name === '') {
                                    $name = optional($order->user)->name ?? '—';
                                }

                                $searchValue = mb_strtolower(
                                    ($order->order_number ?? $order->id)
                                    . ' ' . $name
                                    . ' ' . $phone
                                );

                                $whatsappPhone = preg_replace('/\D+/', '', $phone);

                                if ($whatsappPhone && !str_starts_with($whatsappPhone, '968')) {
                                    $whatsappPhone = '968' . ltrim($whatsappPhone, '0');
                                }
                            @endphp

                            <tr
                                data-search="{{ $searchValue }}"
                                data-status="{{ $status }}"
                                data-date="{{ optional($order->created_at)->format('Y-m-d') }}"
                            >
                                <td class="order-no">
                                    #{{ $order->order_number ?? $order->id }}
                                </td>

                                <td class="customer">
                                    <b>{{ $name }}</b>
                                    <small>{{ $phone }}</small>
                                </td>

                                <td>
                                    {{ optional($order->created_at)->format('Y-m-d') }}
                                </td>

                                <td>
                                    <b>{{ number_format((float)($order->total ?? 0), 3) }} ر.ع</b>
                                </td>

                                <td>
                                    {{ $order->payment_method ?? '—' }}
                                </td>

                                <td>
                                    <span class="status {{ $status }}">
                                        {{ $status }}
                                    </span>
                                </td>

                                <td>
                                    <div class="actions">

                                        <button
                                            class="icon-btn view"
                                            type="button"
                                            title="عرض"
                                            data-id="{{ $order->id }}"
                                            data-number="{{ $order->order_number ?? $order->id }}"
                                            data-name="{{ $name }}"
                                            data-phone="{{ $phone }}"
                                            data-total="{{ number_format((float)($order->total ?? 0),3) }}"
                                            data-status="{{ $status }}"
                                            data-payment="{{ $order->payment_method ?? '—' }}"
                                            data-governorate="{{ $order->governorate ?? '—' }}"
                                            data-wilayat="{{ $order->wilayat ?? '—' }}"
                                            data-address="{{ $order->address ?? '—' }}"
                                        >
                                            👁
                                        </button>

                                        @if($whatsappPhone)
                                            <a
                                                class="icon-btn"
                                                style="display:grid;place-items:center"
                                                href="https://wa.me/{{ $whatsappPhone }}"
                                                target="_blank"
                                                title="WhatsApp"
                                            >
                                                ☏
                                            </a>
                                        @endif

                                        <button
                                            class="icon-btn print"
                                            type="button"
                                            title="طباعة"
                                        >
                                            ▣
                                        </button>

                                    </div>
                                </td>
                            </tr>

                        @empty

                            <tr id="emptyRow">
                                <td colspan="7">
                                    <div class="empty">
                                        <span>▤</span>
                                        <p data-t="noOrders">لا توجد طلبات حتى الآن</p>
                                    </div>
                                </td>
                            </tr>

                        @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="pager">
                    <span id="resultCount"></span>
                    <span data-t="pageOne">الصفحة 1</span>
                </div>

            </section>

        </div>
    </main>
</div>

<div class="modal" id="modal">
    <div class="modal-box">

        <div class="modal-head">
            <h2>
                <span data-t="orderDetails">تفاصيل الطلب</span>
                <span id="modalId"></span>
            </h2>

            <button class="close" id="close" type="button">×</button>
        </div>

        <div class="detail-grid">
            <div class="detail">
                <small data-t="customer">العميل</small>
                <b id="modalName"></b>
            </div>

            <div class="detail">
                <small data-t="phone">الهاتف</small>
                <b id="modalPhone"></b>
            </div>

            <div class="detail">
                <small data-t="amount">الإجمالي</small>
                <b id="modalTotal"></b>
            </div>

            <div class="detail">
                <small data-t="statusLabel">الحالة</small>
                <b id="modalStatus"></b>
            </div>

            <div class="detail">
                <small data-t="payment">الدفع</small>
                <b id="modalPayment"></b>
            </div>

            <div class="detail">
                <small>المحافظة / الولاية</small>
                <b id="modalLocation"></b>
            </div>

            <div class="detail" style="grid-column:1/-1">
                <small>العنوان</small>
                <b id="modalAddress"></b>
            </div>
        </div>

        <form
            class="update-row"
            id="statusForm"
            method="POST"
            action=""
        >
            @csrf
            @method('PATCH')

            <select name="status" id="newStatus">
                <option value="pending" data-t="pending">بانتظار التأكيد</option>
                <option value="processing" data-t="processing">جاري التجهيز</option>
                <option value="shipped" data-t="shipped">تم الشحن</option>
                <option value="delivered" data-t="delivered">تم التوصيل</option>
                <option value="cancelled" data-t="cancelled">ملغي</option>
            </select>

            <button class="save" type="submit" data-t="update">
                تحديث الحالة
            </button>
        </form>

    </div>
</div>

<script>
const q=s=>document.querySelector(s);
const qa=s=>[...document.querySelectorAll(s)];

let lang=localStorage.adminLanguage||'ar';
let theme=localStorage.adminTheme||'light';

const tr={
    ar:{
        dashboard:'لوحة التحكم',
        orders:'الطلبات',
        products:'المنتجات',
        pos:'نقطة البيع',
        customers:'العملاء',
        reports:'التقارير',
        ordersManagement:'إدارة الطلبات',
        allOrders:'جميع الطلبات',
        intro:'متابعة الطلبات وتحديث حالتها والتواصل مع العملاء.',
        export:'↓ تصدير Excel',
        all:'الكل',
        pending:'بانتظار التأكيد',
        processing:'جاري التجهيز',
        shipped:'تم الشحن',
        delivered:'تم التوصيل',
        cancelled:'ملغي',
        allStatuses:'جميع الحالات',
        orderNo:'رقم الطلب',
        customer:'العميل',
        date:'التاريخ',
        amount:'الإجمالي',
        payment:'الدفع',
        statusLabel:'الحالة',
        actions:'الإجراءات',
        noOrders:'لا توجد طلبات حتى الآن',
        pageOne:'الصفحة 1',
        orderDetails:'تفاصيل الطلب',
        phone:'الهاتف',
        update:'تحديث الحالة',
        results:n=>'النتائج: '+n,
        exportDemo:'تصدير Excel مؤجل لمرحلة التقارير.'
    },
    en:{
        dashboard:'Dashboard',
        orders:'Orders',
        products:'Products',
        pos:'Point of Sale',
        customers:'Customers',
        reports:'Reports',
        ordersManagement:'Order Management',
        allOrders:'All Orders',
        intro:'Track orders, update status and contact customers.',
        export:'↓ Export Excel',
        all:'All',
        pending:'Pending',
        processing:'Processing',
        shipped:'Shipped',
        delivered:'Delivered',
        cancelled:'Cancelled',
        allStatuses:'All statuses',
        orderNo:'Order no.',
        customer:'Customer',
        date:'Date',
        amount:'Total',
        payment:'Payment',
        statusLabel:'Status',
        actions:'Actions',
        noOrders:'No orders yet',
        pageOne:'Page 1',
        orderDetails:'Order Details',
        phone:'Phone',
        update:'Update status',
        results:n=>'Results: '+n,
        exportDemo:'Excel export is deferred to the reports stage.'
    }
};

function applyTheme(){
    document.body.classList.toggle('dark',theme==='dark');
    q('#theme').textContent=theme==='dark'?'☀':'☾';
}

function applyLang(){
    document.documentElement.lang=lang;
    document.documentElement.dir=lang==='ar'?'rtl':'ltr';
    q('#language').textContent=lang==='ar'?'English':'العربية';

    qa('[data-t]').forEach(e=>{
        let v=tr[lang][e.dataset.t];
        if(v)e.textContent=v;
    });

    qa('[data-placeholder-ar]').forEach(e=>{
        e.placeholder=e.dataset[lang==='ar'?'placeholderAr':'placeholderEn'];
    });

    filter();
}

function filter(){
    let s=q('#search').value.toLowerCase();
    let st=q('#status').value;
    let d=q('#date').value;
    let n=0;

    qa('#rows tr[data-search]').forEach(r=>{
        let show=
            r.dataset.search.includes(s)
            && (st==='all'||r.dataset.status===st)
            && (!d||r.dataset.date===d);

        r.style.display=show?'':'none';

        if(show)n++;
    });

    q('#resultCount').textContent=tr[lang].results(n);
}

q('#search').oninput=filter;
q('#status').onchange=filter;
q('#date').onchange=filter;

qa('.view').forEach(b=>{
    b.onclick=()=>{
        q('#modalId').textContent='#'+b.dataset.number;
        q('#modalName').textContent=b.dataset.name;
        q('#modalPhone').textContent=b.dataset.phone;
        q('#modalTotal').textContent=b.dataset.total+' ر.ع';
        q('#modalStatus').textContent=b.dataset.status;
        q('#modalPayment').textContent=b.dataset.payment;
        q('#modalLocation').textContent=
            b.dataset.governorate+' / '+b.dataset.wilayat;
        q('#modalAddress').textContent=b.dataset.address;
        q('#newStatus').value=b.dataset.status;

        q('#statusForm').action=
            '{{ url('/admin/orders') }}/'
            +b.dataset.id+
            '/status';

        q('#modal').classList.add('open');
    };
});

q('#close').onclick=()=>q('#modal').classList.remove('open');

q('#modal').onclick=e=>{
    if(e.target===q('#modal')){
        q('#modal').classList.remove('open');
    }
};

qa('.print').forEach(b=>{
    b.onclick=()=>window.print();
});

q('#export').onclick=()=>alert(tr[lang].exportDemo);

q('#theme').onclick=()=>{
    theme=theme==='dark'?'light':'dark';
    localStorage.adminTheme=theme;
    applyTheme();
};

q('#language').onclick=()=>{
    lang=lang==='ar'?'en':'ar';
    localStorage.adminLanguage=lang;
    applyLang();
};

applyTheme();
applyLang();
</script>

</body>
</html>
