<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نقطة البيع | لمسة أنوثة</title>
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
        button,input,select,textarea{font:inherit}
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
        .content{padding:24px 3% 55px}
        .notice{
            padding:11px 13px;
            border-radius:8px;
            margin-bottom:15px;
            font-size:12px
        }
        .notice.success{background:#d1fae5;color:#087443}
        .notice.error{background:#fee2e2;color:#b91c1c}
        .pos-grid{
            display:grid;
            grid-template-columns:minmax(0,1.55fr) minmax(340px,.75fr);
            gap:18px
        }
        .card{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:14px;
            box-shadow:var(--shadow)
        }
        .card-head{
            padding:16px 18px;
            border-bottom:1px solid var(--border)
        }
        .card-head h2{font-size:18px}
        .card-head p{
            color:var(--muted);
            font-size:12px;
            margin-top:5px
        }
        .search-wrap{
            padding:14px 16px;
            display:grid;
            grid-template-columns:1fr 180px;
            gap:10px;
            border-bottom:1px solid var(--border)
        }
        .search-wrap input,.search-wrap select{
            height:44px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--bg);
            color:var(--text);
            padding:0 12px;
            outline:0
        }
        .products{
            padding:16px;
            display:grid;
            grid-template-columns:repeat(3,minmax(0,1fr));
            gap:12px;
            max-height:690px;
            overflow:auto
        }
        .product{
            border:1px solid var(--border);
            border-radius:12px;
            overflow:hidden;
            background:var(--card)
        }
        .product-image{
            height:145px;
            background:var(--bg);
            display:grid;
            place-items:center;
            overflow:hidden;
            font-size:38px
        }
        .product-image img{
            width:100%;
            height:100%;
            object-fit:cover
        }
        .product-body{padding:11px}
        .product-body h3{
            font-size:14px;
            line-height:1.5;
            min-height:42px
        }
        .meta{
            color:var(--muted);
            font-size:11px;
            line-height:1.6;
            margin:6px 0
        }
        .product-row{
            display:flex;
            justify-content:space-between;
            gap:8px;
            align-items:center;
            margin-top:9px
        }
        .price{
            color:var(--red);
            font-weight:bold;
            font-size:13px
        }
        .add{
            height:34px;
            border:0;
            border-radius:7px;
            padding:0 10px;
            background:var(--red);
            color:#fff;
            cursor:pointer
        }
        .add:disabled{
            opacity:.45;
            cursor:not-allowed
        }
        .cart-panel{
            position:sticky;
            top:95px;
            overflow:hidden
        }
        .cart-items{
            max-height:310px;
            overflow:auto;
            padding:4px 16px
        }
        .cart-empty{
            text-align:center;
            padding:35px 15px;
            color:var(--muted);
            font-size:13px
        }
        .cart-item{
            display:grid;
            grid-template-columns:1fr auto;
            gap:10px;
            padding:12px 0;
            border-bottom:1px solid var(--border)
        }
        .cart-item h4{
            font-size:13px;
            margin-bottom:4px
        }
        .cart-item small{
            display:block;
            color:var(--muted);
            font-size:10px;
            line-height:1.5
        }
        .cart-item-total{
            color:var(--red);
            font-weight:bold;
            font-size:12px;
            margin-top:5px
        }
        .qty{
            display:flex;
            align-items:center;
            gap:5px
        }
        .qty button{
            width:29px;
            height:29px;
            border:1px solid var(--border);
            border-radius:6px;
            background:var(--card);
            color:var(--text);
            cursor:pointer
        }
        .qty span{
            min-width:25px;
            text-align:center;
            font-size:12px
        }
        .remove{
            margin-top:5px;
            width:100%;
            border:0;
            background:none;
            color:var(--red);
            cursor:pointer;
            font-size:10px
        }
        .checkout-form{
            padding:16px;
            border-top:1px solid var(--border)
        }
        .field{
            display:grid;
            gap:5px;
            margin-bottom:10px
        }
        .field label{
            color:var(--muted);
            font-size:11px
        }
        .field input,.field select,.field textarea{
            width:100%;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--bg);
            color:var(--text);
            padding:0 11px;
            outline:0
        }
        .field input,.field select{height:40px}
        .field textarea{
            min-height:66px;
            padding-top:10px;
            resize:vertical
        }
        .two{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:9px
        }
        .summary{
            margin-top:12px;
            border-top:1px solid var(--border);
            padding-top:10px
        }
        .line{
            display:flex;
            justify-content:space-between;
            padding:7px 0;
            color:var(--muted);
            font-size:12px
        }
        .line strong{color:var(--text)}
        .line.total{
            border-top:1px solid var(--border);
            margin-top:5px;
            padding-top:12px;
            font-size:16px
        }
        .line.total strong{
            color:var(--red);
            font-size:18px
        }
        .pay{
            width:100%;
            height:46px;
            border:0;
            border-radius:8px;
            background:var(--red);
            color:#fff;
            cursor:pointer;
            margin-top:10px
        }
        .pay:disabled{
            opacity:.45;
            cursor:not-allowed
        }
        .recent{
            margin-top:18px
        }
        .table-wrap{overflow:auto}
        table{
            width:100%;
            border-collapse:collapse;
            white-space:nowrap
        }
        th,td{
            text-align:start;
            padding:12px 14px;
            border-bottom:1px solid var(--border);
            font-size:11px
        }
        th{
            background:var(--bg);
            color:var(--muted);
            font-weight:normal
        }
        .empty{
            padding:32px;
            text-align:center;
            color:var(--muted)
        }
        .shift-strip{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            padding:12px 16px;
            margin-bottom:14px;
            border-radius:11px;
            border:1px solid var(--border);
            background:var(--card)
        }
        .shift-strip.open{border-color:#86efac}
        .shift-strip.closed{border-color:#fecaca}
        .shift-info{
            display:flex;
            align-items:center;
            gap:10px;
            flex-wrap:wrap
        }
        .shift-badge{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:6px 10px;
            border-radius:999px;
            font-size:11px;
            font-weight:bold
        }
        .shift-badge.open{background:#dcfce7;color:#166534}
        .shift-badge.closed{background:#fee2e2;color:#991b1b}
        .shift-link{
            height:38px;
            padding:0 13px;
            display:inline-flex;
            align-items:center;
            border-radius:8px;
            background:#111;
            color:#fff;
            font-size:12px
        }
        .shortcut-bar{
            display:grid;
            grid-template-columns:repeat(7,minmax(0,1fr));
            gap:7px;
            margin-bottom:14px
        }
        .shortcut{
            border:1px solid var(--border);
            background:var(--card);
            color:var(--text);
            border-radius:9px;
            padding:9px 7px;
            text-align:center;
            font-size:11px;
            cursor:pointer
        }
        .shortcut b{
            display:block;
            color:var(--red);
            font-size:12px;
            margin-bottom:3px
        }
        .shortcut:hover{border-color:var(--red)}
        .pos-lock{
            padding:26px;
            text-align:center;
            border:1px solid #fecaca;
            background:#fff1f2;
            color:#991b1b;
            border-radius:12px;
            margin-bottom:15px
        }
        body.dark .pos-lock{background:#2b1113}
        .pos-lock h2{margin-bottom:8px}
        .pos-lock p{font-size:13px;line-height:1.7;margin-bottom:14px}
        .pos-lock a{
            display:inline-flex;
            align-items:center;
            height:42px;
            padding:0 16px;
            border-radius:8px;
            background:var(--red);
            color:#fff
        }
        .register-readonly{
            min-height:40px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:8px;
            padding:0 11px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--bg);
            font-size:12px
        }
        @media(max-width:1150px){
            .products{grid-template-columns:repeat(2,minmax(0,1fr))}
            .shortcut-bar{grid-template-columns:repeat(4,minmax(0,1fr))}
        }
        @media(max-width:900px){
            .app{grid-template-columns:1fr}
            .side{display:none}
            .pos-grid{grid-template-columns:1fr}
            .cart-panel{position:static}
        }
        @media(max-width:560px){
            .products{grid-template-columns:1fr 1fr}
            .search-wrap{grid-template-columns:1fr}
            .two{grid-template-columns:1fr}
            .shortcut-bar{grid-template-columns:repeat(2,minmax(0,1fr))}
            .shift-strip{align-items:flex-start;flex-direction:column}
        }
        @media(max-width:410px){
            .products{grid-template-columns:1fr}
        }

        .selected-cart-item{
            outline:2px solid var(--red);
            outline-offset:-2px;
        }
        .payment-box,.action-panel{
            border:1px solid var(--line);
            border-radius:14px;
            padding:12px;
            background:var(--soft);
            display:grid;
            gap:10px;
        }
        .payment-grid{
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:10px;
        }
        .payment-grid.three{
            grid-template-columns:repeat(3,minmax(0,1fr));
        }
        .small-help{
            color:var(--muted);
            font-size:12px;
            line-height:1.6;
        }
        .change-box{
            padding:10px 12px;
            border-radius:12px;
            border:1px dashed var(--line);
            display:flex;
            justify-content:space-between;
            align-items:center;
            font-weight:800;
        }
        .modal-backdrop{
            position:fixed;
            inset:0;
            z-index:9998;
            background:rgba(0,0,0,.52);
            display:none;
            align-items:center;
            justify-content:center;
            padding:18px;
        }
        .modal-backdrop.show{display:flex}
        .pos-modal{
            width:min(760px,100%);
            max-height:88vh;
            overflow:auto;
            background:var(--card);
            border:1px solid var(--line);
            border-radius:20px;
            box-shadow:0 24px 70px rgba(0,0,0,.25);
            padding:18px;
        }
        .modal-head{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            margin-bottom:14px;
        }
        .modal-head h3{margin:0}
        .modal-close{
            border:1px solid var(--line);
            background:var(--soft);
            color:var(--text);
            border-radius:10px;
            padding:7px 11px;
            cursor:pointer;
        }
        .modal-list{display:grid;gap:8px}
        .modal-item{
            border:1px solid var(--line);
            border-radius:12px;
            padding:10px;
            display:flex;
            gap:10px;
            justify-content:space-between;
            align-items:center;
        }
        .modal-item button{
            border:0;
            border-radius:9px;
            padding:8px 12px;
            cursor:pointer;
        }
        .toolbar-mini{
            display:flex;
            gap:8px;
            flex-wrap:wrap;
            margin-bottom:10px;
        }
        .toolbar-mini button{
            border:1px solid var(--line);
            background:var(--soft);
            color:var(--text);
            border-radius:10px;
            padding:8px 11px;
            cursor:pointer;
        }
        .toast-pos{
            position:fixed;
            z-index:10000;
            left:18px;
            bottom:18px;
            min-width:240px;
            max-width:420px;
            background:#111;
            color:#fff;
            border-radius:12px;
            padding:12px 14px;
            display:none;
            box-shadow:0 18px 50px rgba(0,0,0,.25);
        }
        .toast-pos.show{display:block}
        .terminal-note{
            font-size:12px;
            padding:9px 10px;
            border:1px dashed var(--line);
            border-radius:10px;
            color:var(--muted);
        }
        @media(max-width:700px){
            .payment-grid,.payment-grid.three{grid-template-columns:1fr}
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
            <a class="active" href="{{ route('admin.pos.index') }}">▰ نقطة البيع</a>
            <a href="#">♙ العملاء</a>
            <a href="{{ url('/admin/reports') }}">▥ التقارير</a>
        </nav>
    </aside>

    <main class="main">

        <header class="top">
            <h1>نقطة البيع POS</h1>

            <div class="tools">
                <button class="tool" id="theme" type="button">☾</button>
            </div>
        </header>

        <div class="content">

            @if(session('success'))
                <div class="notice success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="notice error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if($openShift)
                <div class="shift-strip open">
                    <div class="shift-info">
                        <span class="shift-badge open">● وردية مفتوحة</span>
                        <strong>{{ $openShift->cashRegister->name ?? 'الخزينة' }}</strong>
                        <span class="muted">
                            بدأت {{ optional($openShift->opened_at)->format('H:i') }}
                        </span>
                        <span class="muted">
                            النقد المتوقع:
                            {{ number_format((float)$openShift->expected_cash,3) }} ر.ع
                        </span>
                    </div>

                    <a class="shift-link" href="{{ route('admin.pos.shifts.index') }}">
                        إدارة الوردية
                    </a>
                </div>

                <div class="shortcut-bar">
                    <button class="shortcut" type="button" data-shortcut-action="search">
                        <b>F1</b>بحث سريع
                    </button>
                    <button class="shortcut" type="button" data-shortcut-action="barcode">
                        <b>Ctrl+B</b>باركود
                    </button>
                    <button class="shortcut" type="button" data-shortcut-action="discount">
                        <b>F5</b>الخصم
                    </button>
                    <button class="shortcut" type="button" data-shortcut-action="cash">
                        <b>F6</b>نقدي
                    </button>
                    <button class="shortcut" type="button" data-shortcut-action="card">
                        <b>F7</b>بطاقة
                    </button>
                    <button class="shortcut" type="button" data-shortcut-action="mixed">
                        <b>F8</b>مختلط
                    </button>
                    <button class="shortcut" type="button" data-shortcut-action="complete">
                        <b>Ctrl+Enter</b>إتمام البيع
                    </button>
                </div>
            @else
                <div class="pos-lock">
                    <h2>نقطة البيع مقفلة</h2>
                    <p>
                        افتح وردية كاشير وحدد الخزينة والرصيد الافتتاحي قبل بدء البيع.
                    </p>
                    <a href="{{ route('admin.pos.shifts.index') }}">
                        فتح وردية الآن
                    </a>
                </div>
            @endif

            <div class="pos-grid" @if(!$openShift) style="opacity:.45;pointer-events:none" @endif>

                <section class="card">

                    <div class="card-head">
                        <h2>اختيار المنتجات</h2>
                        <p>اختر المنتجات والكميات لإضافتها إلى عملية البيع.</p>
                    </div>

                    <div class="search-wrap">
                        <input
                            id="productSearch"
                            type="text"
                            placeholder="بحث بالاسم أو SKU أو اللون أو المقاس..."
                        >

                        <select id="stockFilter">
                            <option value="all">جميع المنتجات</option>
                            <option value="available">المتوفر فقط</option>
                            <option value="out">النافد فقط</option>
                        </select>
                    </div>

                    <div class="products" id="products">

                        @forelse($variants as $variant)

                            @php
                                $product = $variant->product;
                                $stock = (int)($variant->stock_quantity ?? 0);

                                $image = $variant->image;

                                if (!$image && $product) {
                                    $primary = $product->images
                                        ->firstWhere('is_primary', true)
                                        ?? $product->images->first();

                                    $image = $primary?->image;
                                }

                                $imagePath = null;

                                if ($image) {
                                    $imagePath = str_starts_with($image, 'http')
                                        ? $image
                                        : asset('storage/' . ltrim($image, '/'));
                                }

                                $price = $variant->price !== null
                                    ? (float)$variant->price
                                    : (float)($product->price ?? 0);

                                $searchText = mb_strtolower(
                                    ($product->name_ar ?? '')
                                    . ' ' . ($product->name_en ?? '')
                                    . ' ' . ($variant->sku ?? '')
                                    . ' ' . ($variant->color_name_ar ?? '')
                                    . ' ' . ($variant->color_name_en ?? '')
                                    . ' ' . ($variant->size ?? '')
                                );
                            @endphp

                            <article
                                class="product"
                                data-search="{{ $searchText }}"
                                data-sku="{{ mb_strtolower((string)($variant->sku ?? '')) }}"
                                data-stock="{{ $stock }}"
                            >
                                <div class="product-image">
                                    @if($imagePath)
                                        <img
                                            src="{{ $imagePath }}"
                                            alt="{{ $product->name_ar ?? 'منتج' }}"
                                        >
                                    @else
                                        🛍️
                                    @endif
                                </div>

                                <div class="product-body">
                                    <h3>{{ $product->name_ar ?? 'منتج' }}</h3>

                                    <div class="meta">
                                        @if($variant->sku)
                                            SKU: {{ $variant->sku }}<br>
                                        @endif

                                        @if($variant->color_name_ar)
                                            اللون: {{ $variant->color_name_ar }}<br>
                                        @endif

                                        @if($variant->size)
                                            المقاس: {{ $variant->size }}<br>
                                        @endif

                                        المخزون: {{ $stock }}
                                    </div>

                                    <div class="product-row">
                                        <span class="price">
                                            {{ number_format($price,3) }} ر.ع
                                        </span>

                                        <button
                                            class="add"
                                            type="button"
                                            data-id="{{ $variant->id }}"
                                            data-name="{{ $product->name_ar ?? 'منتج' }}"
                                            data-sku="{{ $variant->sku }}"
                                            data-price="{{ $price }}"
                                            data-stock="{{ $stock }}"
                                            @disabled($stock <= 0)
                                        >
                                            إضافة
                                        </button>
                                    </div>
                                </div>
                            </article>

                        @empty

                            <div class="empty">
                                لا توجد خيارات منتجات متاحة حاليًا.
                            </div>

                        @endforelse

                    </div>

                </section>

                <aside class="card cart-panel">

                    <div class="card-head">
                        <h2>عملية البيع الحالية</h2>
                        <p id="cartCount">0 منتج</p>
                    </div>

                    <div class="cart-items" id="cartItems">
                        <div class="cart-empty" id="cartEmpty">
                            لم تتم إضافة أي منتجات بعد.
                        </div>
                    </div>

                    <form
                        class="checkout-form"
                        id="saleForm"
                        method="POST"
                        action="{{ route('admin.pos.store') }}"
                    >
                        @csrf

                        <div id="hiddenItems"></div>

                        <div class="field">
                            <label>خزينة الوردية</label>

                            @if($openShift)
                                <input
                                    type="hidden"
                                    name="cash_register_id"
                                    id="cashRegister"
                                    value="{{ $openShift->cash_register_id }}"
                                >

                                <div class="register-readonly">
                                    <strong>{{ $openShift->cashRegister->name ?? 'الخزينة' }}</strong>
                                    <span>
                                        {{ number_format((float)($openShift->cashRegister->current_balance ?? 0),3) }} ر.ع
                                    </span>
                                </div>
                            @else
                                <input type="hidden" id="cashRegister" value="">
                                <div class="register-readonly">
                                    <span>لا توجد وردية مفتوحة</span>
                                </div>
                            @endif
                        </div>

                        <input
                            type="hidden"
                            name="customer_id"
                            id="customerId"
                            value="{{ old('customer_id') }}"
                        >
                        <input type="hidden" name="held_sale_id" id="heldSaleId" value="">
                        <input type="hidden" name="exchange_parent_sale_id" id="exchangeParentSaleId" value="">

                        <div class="two">

                            <div class="field">
                                <label>اسم العميل</label>
                                <input
                                    type="text"
                                    name="customer_name"
                                    id="customerName"
                                    value="{{ old('customer_name') }}"
                                    placeholder="اختياري"
                                >
                            </div>

                            <div class="field">
                                <label>الهاتف</label>
                                <input
                                    type="text"
                                    name="customer_phone"
                                    id="customerPhone"
                                    value="{{ old('customer_phone') }}"
                                    placeholder="اختياري"
                                >
                            </div>

                        </div>

                        <div class="two">

                            <div class="field">
                                <label>طريقة الدفع</label>

                                <select name="payment_method" id="paymentMethod" required>
                                    <option value="cash" @selected(old('payment_method') === 'cash')>
                                        نقدي
                                    </option>
                                    <option value="card" @selected(old('payment_method') === 'card')>
                                        بطاقة
                                    </option>
                                    <option value="transfer" @selected(old('payment_method') === 'transfer')>
                                        تحويل
                                    </option>
                                    <option value="mixed" @selected(old('payment_method') === 'mixed')>
                                        مختلط
                                    </option>
                                </select>
                            </div>

                            <div class="field">
                                <label>الخصم</label>
                                <input
                                    id="discount"
                                    type="number"
                                    name="discount_amount"
                                    value="{{ old('discount_amount', 0) }}"
                                    min="0"
                                    step="0.001"
                                >
                            </div>

                        </div>

                        <div class="payment-box" id="paymentDetails">
                            <div id="cashPaymentPanel">
                                <div class="field">
                                    <label>المبلغ النقدي المستلم</label>
                                    <input
                                        id="cashReceived"
                                        type="number"
                                        min="0"
                                        step="0.001"
                                        placeholder="0.000"
                                    >
                                </div>
                                <div class="change-box">
                                    <span>الباقي للعميل</span>
                                    <span><b id="changeDue">0.000</b> ر.ع</span>
                                </div>
                            </div>

                            <div id="cardPaymentPanel" style="display:none">
                                <div class="field">
                                    <label>مرجع البطاقة</label>
                                    <input
                                        id="cardReference"
                                        type="text"
                                        maxlength="150"
                                        placeholder="اختياري حاليًا"
                                    >
                                </div>
                                <div class="terminal-note">
                                    ربط بنك مسقط مهيأ داخل النظام. الإرسال المباشر
                                    للماكينة يفعّل بعد معرفة موديل الجهاز وبروتوكول ECR المعتمد.
                                </div>
                            </div>

                            <div id="transferPaymentPanel" style="display:none">
                                <div class="field">
                                    <label>مرجع التحويل</label>
                                    <input
                                        id="transferReference"
                                        type="text"
                                        maxlength="150"
                                        placeholder="رقم المرجع أو التحويل"
                                    >
                                </div>
                            </div>

                            <div id="mixedPaymentPanel" style="display:none">
                                <div class="payment-grid three">
                                    <div class="field">
                                        <label>نقدي</label>
                                        <input id="mixedCash" type="number" min="0" step="0.001" value="0">
                                    </div>
                                    <div class="field">
                                        <label>بطاقة</label>
                                        <input id="mixedCard" type="number" min="0" step="0.001" value="0">
                                    </div>
                                    <div class="field">
                                        <label>تحويل</label>
                                        <input id="mixedTransfer" type="number" min="0" step="0.001" value="0">
                                    </div>
                                </div>
                                <div class="payment-grid">
                                    <div class="field">
                                        <label>النقد المستلم فعليًا</label>
                                        <input id="mixedCashTendered" type="number" min="0" step="0.001" value="0">
                                    </div>
                                    <div class="field">
                                        <label>مرجع البطاقة/التحويل</label>
                                        <input id="mixedReference" type="text" maxlength="150">
                                    </div>
                                </div>
                                <div class="change-box">
                                    <span>المتبقي للتوزيع</span>
                                    <span><b id="mixedRemaining">0.000</b> ر.ع</span>
                                </div>
                            </div>

                            <div id="hiddenPayments"></div>
                        </div>

                        <div class="field">
                            <label>ملاحظات</label>
                            <textarea
                                name="notes"
                                placeholder="اختياري"
                            >{{ old('notes') }}</textarea>
                        </div>

                        <div class="summary">

                            <div class="line">
                                <span>المجموع الفرعي</span>
                                <strong>
                                    <span id="subtotal">0.000</span> ر.ع
                                </strong>
                            </div>

                            <div class="line">
                                <span>الخصم</span>
                                <strong>
                                    <span id="discountView">0.000</span> ر.ع
                                </strong>
                            </div>

                            <div class="line total">
                                <span>الإجمالي</span>
                                <strong>
                                    <span id="total">0.000</span> ر.ع
                                </strong>
                            </div>

                        </div>

                        <button class="pay" id="submitSale" type="submit" disabled>
                            حفظ عملية البيع
                        </button>

                    </form>

                </aside>

            </div>

            <section class="card recent">

                <div class="card-head">
                    <h2>آخر عمليات البيع</h2>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>رقم العملية</th>
                            <th>العميل</th>
                            <th>طريقة الدفع</th>
                            <th>الإجمالي</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                        </thead>

                        <tbody>

                        @forelse($recentSales as $sale)

                            <tr>
                                <td>
                                    <strong>{{ $sale->sale_number }}</strong>
                                </td>

                                <td>
                                    {{ $sale->customer_name ?: 'عميل نقدي' }}
                                </td>

                                <td>
                                    {{ $sale->payment_method }}
                                </td>

                                <td>
                                    {{ number_format((float)$sale->total,3) }} ر.ع
                                </td>

                                <td>
                                    {{ $sale->status }}
                                </td>

                                <td>
                                    {{ optional($sale->created_at)->format('Y-m-d H:i') }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6">
                                    <div class="empty">
                                        لا توجد عمليات بيع حتى الآن.
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
const q = s => document.querySelector(s);
const qa = s => [...document.querySelectorAll(s)];
const csrf = @json(csrf_token());

const routes = {
    customers: @json(route('admin.pos.customers.search')),
    history: @json(route('admin.pos.sales.history')),
    held: @json(route('admin.pos.held.index')),
    hold: @json(route('admin.pos.held.store')),
    heldResumeBase: @json(url('/admin/pos/held-sales')),
    saleBase: @json(url('/admin/pos/sales')),
    drawer: @json(route('admin.pos.drawer.open'))
};

let theme = localStorage.adminTheme || 'light';
const cart = new Map();
let selectedCartId = null;
let lastSaleId = @json(session('pos_last_sale_id'));
let activeReturnMode = 'refund';

function applyTheme(){
    document.body.classList.toggle('dark', theme === 'dark');
    q('#theme').textContent = theme === 'dark' ? '☀' : '☾';
}
q('#theme').onclick = () => {
    theme = theme === 'dark' ? 'light' : 'dark';
    localStorage.adminTheme = theme;
    applyTheme();
};

function toast(message){
    const el = q('#posToast');
    el.textContent = message;
    el.classList.add('show');
    clearTimeout(window.__posToast);
    window.__posToast = setTimeout(() => el.classList.remove('show'), 2600);
}

function money(v){ return Number(v || 0).toFixed(3); }

function filterProducts(){
    const search = q('#productSearch').value.trim().toLowerCase();
    const stockFilter = q('#stockFilter').value;
    qa('.product').forEach(product => {
        const text = product.dataset.search || '';
        const stock = Number(product.dataset.stock || 0);
        const visible =
            text.includes(search)
            && (
                stockFilter === 'all'
                || (stockFilter === 'available' && stock > 0)
                || (stockFilter === 'out' && stock <= 0)
            );
        product.style.display = visible ? '' : 'none';
    });
}
q('#productSearch').oninput = filterProducts;
q('#stockFilter').onchange = filterProducts;

function addButtonToCart(button){
    const id = String(button.dataset.id);
    const stock = Number(button.dataset.stock || 0);
    if (stock <= 0) return;

    if (cart.has(id)) {
        const item = cart.get(id);
        if (item.quantity < item.stock) item.quantity++;
    } else {
        cart.set(id, {
            id,
            name: button.dataset.name,
            sku: button.dataset.sku || '',
            price: Number(button.dataset.price || 0),
            stock,
            quantity: 1,
            lineDiscount: 0
        });
    }
    selectedCartId = id;
    renderCart();
}

qa('.add').forEach(button => {
    button.onclick = () => addButtonToCart(button);
});

q('#productSearch').addEventListener('keydown', event => {
    if (event.key !== 'Enter') return;
    event.preventDefault();

    const value = q('#productSearch').value.trim().toLowerCase();
    if (!value) return;

    const exactProduct = qa('.product').find(product =>
        (product.dataset.sku || '') === value
    );

    if (!exactProduct) {
        toast('لم يتم العثور على SKU مطابق.');
        return;
    }

    const button = exactProduct.querySelector('.add');
    if (button && !button.disabled) {
        addButtonToCart(button);
        q('#productSearch').select();
    }
});

function cartSubtotal(){
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += Math.max(
            (item.price * item.quantity) - Number(item.lineDiscount || 0),
            0
        );
    });
    return Number(subtotal.toFixed(3));
}

function currentTotal(){
    const subtotal = cartSubtotal();
    const discount = Math.min(
        Math.max(Number(q('#discount').value || 0), 0),
        subtotal
    );
    return Number(Math.max(subtotal - discount, 0).toFixed(3));
}

function renderCart(){
    const container = q('#cartItems');
    const hidden = q('#hiddenItems');

    container.innerHTML = '';
    hidden.innerHTML = '';

    let totalQuantity = 0;
    let index = 0;

    if (cart.size === 0) {
        selectedCartId = null;
        container.innerHTML = `
            <div class="cart-empty">لم تتم إضافة أي منتجات بعد.</div>
        `;
    }

    cart.forEach(item => {
        totalQuantity += item.quantity;

        const gross = item.price * item.quantity;
        const net = Math.max(gross - Number(item.lineDiscount || 0), 0);

        const row = document.createElement('div');
        row.className = 'cart-item'
            + (String(item.id) === String(selectedCartId)
                ? ' selected-cart-item'
                : '');
        row.dataset.cartId = item.id;

        row.innerHTML = `
            <div>
                <h4>${escapeHtml(item.name)}</h4>
                <small>
                    ${item.sku ? 'SKU: ' + escapeHtml(item.sku) + '<br>' : ''}
                    ${money(item.price)} ر.ع للوحدة
                    ${Number(item.lineDiscount || 0) > 0
                        ? '<br>خصم الصنف: ' + money(item.lineDiscount) + ' ر.ع'
                        : ''}
                </small>
                <div class="cart-item-total">${money(net)} ر.ع</div>
            </div>
            <div>
                <div class="qty">
                    <button type="button" data-action="plus" data-id="${item.id}">+</button>
                    <span>${item.quantity}</span>
                    <button type="button" data-action="minus" data-id="${item.id}">−</button>
                </div>
                <button class="remove" type="button" data-action="remove" data-id="${item.id}">
                    حذف
                </button>
            </div>
        `;
        row.onclick = event => {
            if (event.target.closest('button')) return;
            selectedCartId = String(item.id);
            renderCart();
        };

        container.appendChild(row);

        hidden.insertAdjacentHTML('beforeend', `
            <input type="hidden"
                name="items[${index}][product_variant_id]"
                value="${item.id}">
            <input type="hidden"
                name="items[${index}][quantity]"
                value="${item.quantity}">
            <input type="hidden"
                name="items[${index}][line_discount]"
                value="${Number(item.lineDiscount || 0).toFixed(3)}">
        `);
        index++;
    });

    const subtotal = cartSubtotal();
    const discount = Math.min(
        Math.max(Number(q('#discount').value || 0), 0),
        subtotal
    );
    const total = Math.max(subtotal - discount, 0);

    q('#cartCount').textContent = totalQuantity + ' منتج';
    q('#subtotal').textContent = money(subtotal);
    q('#discountView').textContent = money(discount);
    q('#total').textContent = money(total);
    q('#submitSale').disabled = cart.size === 0;

    bindCartButtons();
    updatePaymentUI();
}

function bindCartButtons(){
    qa('[data-action]').forEach(button => {
        button.onclick = () => {
            const id = String(button.dataset.id);
            if (!cart.has(id)) return;

            selectedCartId = id;
            const item = cart.get(id);

            if (button.dataset.action === 'plus' && item.quantity < item.stock) {
                item.quantity++;
            }
            if (button.dataset.action === 'minus' && item.quantity > 1) {
                item.quantity--;
            }
            if (button.dataset.action === 'remove') {
                cart.delete(id);
                if (selectedCartId === id) selectedCartId = null;
            }
            renderCart();
        };
    });
}

function selectCartRelative(direction){
    const ids = [...cart.keys()];
    if (!ids.length) return;

    let index = ids.indexOf(String(selectedCartId));
    if (index < 0) index = 0;
    else index = (index + direction + ids.length) % ids.length;

    selectedCartId = ids[index];
    renderCart();
    q(`[data-cart-id="${selectedCartId}"]`)?.scrollIntoView({
        block:'nearest'
    });
}

function changeSelectedQty(delta){
    if (!selectedCartId || !cart.has(selectedCartId)) return;
    const item = cart.get(selectedCartId);
    const next = item.quantity + delta;
    if (next < 1 || next > item.stock) return;
    item.quantity = next;
    renderCart();
}

function removeSelected(){
    if (!selectedCartId || !cart.has(selectedCartId)) return;
    cart.delete(selectedCartId);
    selectedCartId = null;
    renderCart();
}

function itemDiscount(){
    if (!selectedCartId || !cart.has(selectedCartId)) {
        toast('اختر صنفًا من السلة أولًا.');
        return;
    }
    const item = cart.get(selectedCartId);
    const max = item.price * item.quantity;
    const value = prompt(
        'خصم الصنف من 0 إلى ' + money(max),
        Number(item.lineDiscount || 0).toFixed(3)
    );
    if (value === null) return;

    const discount = Number(value);
    if (!Number.isFinite(discount) || discount < 0 || discount > max) {
        toast('قيمة الخصم غير صالحة.');
        return;
    }
    item.lineDiscount = Number(discount.toFixed(3));
    renderCart();
}

q('#discount').oninput = renderCart;
q('#paymentMethod').onchange = updatePaymentUI;

['cashReceived','mixedCash','mixedCard','mixedTransfer','mixedCashTendered']
    .forEach(id => {
        q('#' + id)?.addEventListener('input', updatePaymentUI);
    });

function updatePaymentUI(){
    const method = q('#paymentMethod').value;
    const total = currentTotal();

    q('#cashPaymentPanel').style.display = method === 'cash' ? '' : 'none';
    q('#cardPaymentPanel').style.display = method === 'card' ? '' : 'none';
    q('#transferPaymentPanel').style.display = method === 'transfer' ? '' : 'none';
    q('#mixedPaymentPanel').style.display = method === 'mixed' ? '' : 'none';

    const received = Number(q('#cashReceived').value || 0);
    q('#changeDue').textContent = money(Math.max(received - total, 0));

    const mixedSum =
        Number(q('#mixedCash').value || 0)
        + Number(q('#mixedCard').value || 0)
        + Number(q('#mixedTransfer').value || 0);

    q('#mixedRemaining').textContent = money(total - mixedSum);
}

function buildPaymentInputs(){
    const holder = q('#hiddenPayments');
    holder.innerHTML = '';

    const method = q('#paymentMethod').value;
    const total = currentTotal();
    let payments = [];

    if (method === 'cash') {
        const received = Number(q('#cashReceived').value || total);
        if (received < total) {
            throw new Error('المبلغ النقدي المستلم أقل من إجمالي الفاتورة.');
        }
        payments.push({
            payment_method:'cash',
            amount:total,
            tendered_amount:received
        });
    }

    if (method === 'card') {
        payments.push({
            payment_method:'card',
            amount:total,
            reference:q('#cardReference').value.trim()
        });
    }

    if (method === 'transfer') {
        payments.push({
            payment_method:'bank_transfer',
            amount:total,
            reference:q('#transferReference').value.trim()
        });
    }

    if (method === 'mixed') {
        const cash = Number(q('#mixedCash').value || 0);
        const card = Number(q('#mixedCard').value || 0);
        const transfer = Number(q('#mixedTransfer').value || 0);
        const sum = Number((cash + card + transfer).toFixed(3));

        if (Math.abs(sum - total) > 0.0005) {
            throw new Error(
                'مجموع الدفعات المختلطة يجب أن يساوي إجمالي الفاتورة.'
            );
        }

        if (cash > 0) {
            const tendered = Number(q('#mixedCashTendered').value || cash);
            if (tendered < cash) {
                throw new Error('النقد المستلم أقل من الجزء النقدي.');
            }
            payments.push({
                payment_method:'cash',
                amount:cash,
                tendered_amount:tendered
            });
        }
        if (card > 0) {
            payments.push({
                payment_method:'card',
                amount:card,
                reference:q('#mixedReference').value.trim()
            });
        }
        if (transfer > 0) {
            payments.push({
                payment_method:'bank_transfer',
                amount:transfer,
                reference:q('#mixedReference').value.trim()
            });
        }
    }

    payments.forEach((payment, index) => {
        Object.entries(payment).forEach(([key, value]) => {
            holder.insertAdjacentHTML('beforeend', `
                <input type="hidden"
                    name="payments[${index}][${key}]"
                    value="${escapeHtml(value)}">
            `);
        });
    });
}

async function api(url, options = {}){
    const response = await fetch(url, {
        headers:{
            'Accept':'application/json',
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':csrf,
            ...(options.headers || {})
        },
        ...options
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        const errors = data.errors
            ? Object.values(data.errors).flat().join('\n')
            : (data.message || 'تعذر تنفيذ العملية.');
        throw new Error(errors);
    }
    return data;
}

function currentCartPayload(){
    return [...cart.values()].map(item => ({
        product_variant_id:Number(item.id),
        quantity:Number(item.quantity),
        unit_price:Number(item.price),
        line_discount:Number(item.lineDiscount || 0)
    }));
}

async function holdCurrentSale(){
    if (!cart.size) {
        toast('لا توجد أصناف لتعليقها.');
        return;
    }

    const data = await api(routes.hold, {
        method:'POST',
        body:JSON.stringify({
            items:currentCartPayload(),
            customer_id:q('#customerId').value || null,
            customer_name:q('#customerName').value || null,
            customer_phone:q('#customerPhone').value || null,
            discount_amount:Number(q('#discount').value || 0),
            notes:q('textarea[name="notes"]').value || null
        })
    });

    newSale(false);
    toast('تم تعليق الفاتورة: ' + data.held_sale.hold_number);
}

async function openHeld(){
    const data = await api(routes.held);
    const box = q('#heldList');
    box.innerHTML = '';

    if (!data.held_sales.length) {
        box.innerHTML = '<div class="empty">لا توجد فواتير معلقة.</div>';
    }

    data.held_sales.forEach(sale => {
        const row = document.createElement('div');
        row.className = 'modal-item';
        row.innerHTML = `
            <div>
                <strong>${escapeHtml(sale.hold_number)}</strong>
                <div class="small-help">
                    ${escapeHtml(sale.customer_name || 'عميل نقدي')}
                    · ${money(sale.total)} ر.ع
                </div>
            </div>
            <button type="button">استرجاع</button>
        `;
        row.querySelector('button').onclick = () => resumeHeld(sale.id);
        box.appendChild(row);
    });

    showModal('heldModal');
}

async function resumeHeld(id){
    const data = await api(`${routes.heldResumeBase}/${id}/resume`);
    const sale = data.held_sale;

    cart.clear();
    sale.items.forEach(item => {
        const variant = item.variant || {};
        const product = variant.product || {};
        cart.set(String(item.product_variant_id), {
            id:String(item.product_variant_id),
            name:product.name_ar || 'منتج',
            sku:variant.sku || '',
            price:Number(item.unit_price || 0),
            stock:Number(variant.stock_quantity || item.quantity || 1),
            quantity:Number(item.quantity || 1),
            lineDiscount:Number(item.line_discount || 0)
        });
    });

    q('#customerId').value = sale.customer_id || '';
    q('#customerName').value = sale.customer_name || '';
    q('#customerPhone').value = sale.customer_phone || '';
    q('#discount').value = sale.discount_amount || 0;
    q('textarea[name="notes"]').value = sale.notes || '';
    q('#heldSaleId').value = sale.id;

    hideModal('heldModal');
    renderCart();
    toast('تم تحميل الفاتورة المعلقة.');
}

async function openCustomers(){
    q('#customerQuery').value = '';
    q('#customerList').innerHTML =
        '<div class="small-help">اكتب اسم العميل أو رقم الهاتف.</div>';
    showModal('customerModal');
    await searchCustomers();
}

async function searchCustomers(){
    const query = q('#customerQuery').value.trim();
    const url = routes.customers + '?q=' + encodeURIComponent(query);
    const data = await api(url);
    const box = q('#customerList');
    box.innerHTML = '';

    data.customers.forEach(customer => {
        const row = document.createElement('div');
        row.className = 'modal-item';
        row.innerHTML = `
            <div>
                <strong>${escapeHtml(customer.name || '')}</strong>
                <div class="small-help">
                    ${escapeHtml(customer.phone || '')}
                    ${customer.email ? ' · ' + escapeHtml(customer.email) : ''}
                </div>
            </div>
            <button type="button">اختيار</button>
        `;
        row.querySelector('button').onclick = () => {
            q('#customerId').value = customer.id;
            q('#customerName').value = customer.name || '';
            q('#customerPhone').value = customer.phone || '';
            hideModal('customerModal');
            toast('تم اختيار العميل.');
        };
        box.appendChild(row);
    });

    if (!data.customers.length) {
        box.innerHTML = '<div class="empty">لا توجد نتائج.</div>';
    }
}

async function openHistory(mode = null){
    activeReturnMode = mode || 'refund';
    const data = await api(routes.history);
    const box = q('#historyList');
    box.innerHTML = '';

    data.sales.forEach(sale => {
        const row = document.createElement('div');
        row.className = 'modal-item';
        row.innerHTML = `
            <div>
                <strong>${escapeHtml(sale.sale_number)}</strong>
                <div class="small-help">
                    ${escapeHtml(sale.customer_name || 'عميل نقدي')}
                    · ${money(sale.total)} ر.ع
                    · ${escapeHtml(sale.status)}
                </div>
            </div>
            <div class="toolbar-mini">
                <button type="button" data-op="receipt">إيصال</button>
                <button type="button" data-op="return">
                    ${activeReturnMode === 'exchange' ? 'استبدال' : 'مرتجع'}
                </button>
            </div>
        `;
        row.querySelector('[data-op="receipt"]').onclick =
            () => openReceipt(sale.id);
        row.querySelector('[data-op="return"]').onclick =
            () => openReturnSale(sale.id, activeReturnMode);
        box.appendChild(row);
    });

    if (!data.sales.length) {
        box.innerHTML = '<div class="empty">لا توجد عمليات بيع.</div>';
    }
    showModal('historyModal');
}

async function openReturnSale(saleId, mode){
    const data = await api(`${routes.saleBase}/${saleId}/details`);
    const sale = data.sale;

    q('#returnSaleId').value = sale.id;
    q('#returnMode').value = mode;
    q('#returnTitle').textContent =
        mode === 'exchange' ? 'استبدال أصناف' : 'مرتجع';

    const box = q('#returnItems');
    box.innerHTML = '';

    sale.items.forEach(item => {
        const returnable = Math.max(
            Number(item.quantity || 0)
            - Number(item.returned_quantity || 0),
            0
        );
        if (returnable <= 0) return;

        const row = document.createElement('div');
        row.className = 'modal-item';
        row.innerHTML = `
            <div style="flex:1">
                <strong>${escapeHtml(item.product_name_ar || 'منتج')}</strong>
                <div class="small-help">
                    المباع: ${item.quantity}
                    · المتاح للمرتجع: ${returnable}
                    · ${money(item.unit_price)} ر.ع
                </div>
                <div class="payment-grid" style="margin-top:8px">
                    <div class="field">
                        <label>الكمية</label>
                        <input class="return-qty"
                            data-item-id="${item.id}"
                            type="number" min="0" max="${returnable}"
                            value="0">
                    </div>
                    <div class="field">
                        <label>المخزون</label>
                        <select class="return-stock"
                            data-item-id="${item.id}">
                            <option value="restock">إعادة للمخزون</option>
                            <option value="damaged">تالف</option>
                            <option value="no_stock_change">بدون تغيير</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
        box.appendChild(row);
    });

    hideModal('historyModal');
    showModal('returnModal');
}

async function submitReturn(){
    const saleId = q('#returnSaleId').value;
    const mode = q('#returnMode').value;

    const items = qa('.return-qty')
        .map(input => {
            const qty = Number(input.value || 0);
            if (qty <= 0) return null;
            const id = input.dataset.itemId;
            return {
                pos_sale_item_id:Number(id),
                quantity:qty,
                stock_action:q(`.return-stock[data-item-id="${id}"]`).value
            };
        })
        .filter(Boolean);

    if (!items.length) {
        toast('اختر كمية للمرتجع.');
        return;
    }

    const refundMethod = mode === 'exchange'
        ? 'none'
        : q('#refundMethod').value;

    const data = await api(`${routes.saleBase}/${saleId}/return`, {
        method:'POST',
        body:JSON.stringify({
            return_type:mode,
            refund_method:refundMethod,
            deduction_amount:Number(q('#returnDeduction').value || 0),
            reason:q('#returnReason').value || null,
            items
        })
    });

    hideModal('returnModal');

    if (mode === 'exchange') {
        q('#exchangeParentSaleId').value = saleId;
        toast('تم تسجيل الأصناف المستبدلة. أضف الأصناف الجديدة الآن.');
    } else {
        toast(data.message || 'تم المرتجع.');
    }
}

function openReceipt(id){
    lastSaleId = id;
    window.open(
        `${routes.saleBase}/${id}/receipt`,
        '_blank',
        'width=460,height=760'
    );
}

async function printLastReceipt(){
    if (!lastSaleId) {
        toast('لا توجد فاتورة أخيرة للطباعة.');
        return;
    }
    const data = await api(
        `${routes.saleBase}/${lastSaleId}/receipt/print`,
        {method:'POST', body:JSON.stringify({})}
    );
    window.open(data.receipt_url, '_blank', 'width=460,height=760');
}

async function openDrawer(){
    await api(routes.drawer, {
        method:'POST',
        body:JSON.stringify({
            reason:'فتح درج النقد من اختصار POS'
        })
    });
    toast('تم تسجيل طلب فتح درج النقد.');
}

function newSale(confirmFirst = true){
    if (
        confirmFirst
        && cart.size
        && !confirm('مسح الفاتورة الحالية وبدء فاتورة جديدة؟')
    ) {
        return;
    }

    cart.clear();
    selectedCartId = null;
    q('#customerId').value = '';
    q('#customerName').value = '';
    q('#customerPhone').value = '';
    q('#heldSaleId').value = '';
    q('#exchangeParentSaleId').value = '';
    q('#discount').value = '0';
    q('textarea[name="notes"]').value = '';
    q('#paymentMethod').value = 'cash';
    q('#cashReceived').value = '';
    q('#mixedCash').value = '0';
    q('#mixedCard').value = '0';
    q('#mixedTransfer').value = '0';
    q('#mixedCashTendered').value = '0';
    renderCart();
}

function editQuantity(){
    if (!selectedCartId || !cart.has(selectedCartId)) {
        toast('اختر صنفًا أولًا.');
        return;
    }
    const item = cart.get(selectedCartId);
    const value = prompt(
        'الكمية من 1 إلى ' + item.stock,
        item.quantity
    );
    if (value === null) return;
    const qty = Number(value);
    if (!Number.isInteger(qty) || qty < 1 || qty > item.stock) {
        toast('الكمية غير صالحة.');
        return;
    }
    item.quantity = qty;
    renderCart();
}

function runShortcut(action){
    const actions = {
        search:() => {
            q('#productSearch').focus();
            q('#productSearch').select();
        },
        barcode:() => {
            q('#productSearch').focus();
            q('#productSearch').select();
        },
        customer:openCustomers,
        hold:holdCurrentSale,
        held:openHeld,
        discount:() => {
            q('#discount').focus();
            q('#discount').select();
        },
        cash:() => {
            q('#paymentMethod').value='cash';
            updatePaymentUI();
            q('#cashReceived').focus();
        },
        card:() => {
            q('#paymentMethod').value='card';
            updatePaymentUI();
            q('#cardReference').focus();
        },
        mixed:() => {
            q('#paymentMethod').value='mixed';
            updatePaymentUI();
            q('#mixedCash').focus();
        },
        return:() => openHistory('refund'),
        exchange:() => openHistory('exchange'),
        print:printLastReceipt,
        drawer:openDrawer,
        new:() => newSale(true),
        history:() => openHistory(null),
        itemDiscount,
        invoiceDiscount:() => {
            q('#discount').focus();
            q('#discount').select();
        },
        quantity:editQuantity,
        complete:() => {
            if (!q('#submitSale').disabled) q('#saleForm').requestSubmit();
        }
    };
    actions[action]?.();
}

qa('[data-shortcut-action]').forEach(button => {
    button.onclick = () => runShortcut(button.dataset.shortcutAction);
});

document.addEventListener('keydown', event => {
    const key = event.key;
    const lower = key.toLowerCase();
    const tag = document.activeElement?.tagName?.toLowerCase();
    const typing = ['input','textarea','select'].includes(tag);

    if (key === 'Escape') {
        qa('.modal-backdrop.show').forEach(modal =>
            modal.classList.remove('show')
        );
        return;
    }

    if (event.ctrlKey && event.shiftKey && lower === 'd') {
        event.preventDefault(); runShortcut('invoiceDiscount'); return;
    }

    if (event.ctrlKey) {
        const ctrlMap = {
            b:'barcode',
            p:'print',
            h:'history',
            c:'customer',
            d:'itemDiscount',
            q:'quantity',
            r:'return',
            e:'exchange',
            s:'hold',
            l:'held'
        };
        if (ctrlMap[lower]) {
            event.preventDefault();
            runShortcut(ctrlMap[lower]);
            return;
        }
        if (key === 'Enter') {
            event.preventDefault();
            runShortcut('complete');
            return;
        }
    }

    if (event.altKey) {
        const altMap = {
            '1':'cash',
            '2':'card',
            '3':() => {
                q('#paymentMethod').value='transfer';
                updatePaymentUI();
                q('#transferReference').focus();
            }
        };
        if (altMap[key]) {
            event.preventDefault();
            typeof altMap[key] === 'function'
                ? altMap[key]()
                : runShortcut(altMap[key]);
            return;
        }
    }

    if (!typing) {
        if (key === 'ArrowUp') {
            event.preventDefault(); selectCartRelative(-1); return;
        }
        if (key === 'ArrowDown') {
            event.preventDefault(); selectCartRelative(1); return;
        }
        if (key === 'Delete') {
            event.preventDefault(); removeSelected(); return;
        }
        if (key === '+' || key === '=') {
            event.preventDefault(); changeSelectedQty(1); return;
        }
        if (key === '-') {
            event.preventDefault(); changeSelectedQty(-1); return;
        }

        const fMap = {
            F1:'search',
            F2:'customer',
            F3:'hold',
            F4:'held',
            F5:'discount',
            F6:'cash',
            F7:'card',
            F8:'mixed',
            F9:'return',
            F10:'print',
            F11:'drawer',
            F12:'new'
        };
        if (fMap[key]) {
            event.preventDefault();
            runShortcut(fMap[key]);
        }
    }
});

q('#customerQuery').addEventListener('input', () => {
    clearTimeout(window.__customerSearch);
    window.__customerSearch = setTimeout(searchCustomers, 250);
});

q('#saleForm').onsubmit = event => {
    const hasOpenShift = @json((bool)$openShift);

    if (!hasOpenShift) {
        event.preventDefault();
        alert('افتح وردية كاشير أولًا.');
        return;
    }

    if (cart.size === 0) {
        event.preventDefault();
        alert('أضف منتجًا واحدًا على الأقل.');
        return;
    }

    try {
        buildPaymentInputs();
    } catch (error) {
        event.preventDefault();
        alert(error.message);
    }
};

function showModal(id){ q('#' + id).classList.add('show'); }
function hideModal(id){ q('#' + id).classList.remove('show'); }

qa('[data-modal-close]').forEach(button => {
    button.onclick = () => hideModal(button.dataset.modalClose);
});
qa('.modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', event => {
        if (event.target === backdrop) backdrop.classList.remove('show');
    });
});

q('#submitReturn').onclick = submitReturn;

function escapeHtml(value){
    return String(value ?? '')
        .replaceAll('&','&amp;')
        .replaceAll('<','&lt;')
        .replaceAll('>','&gt;')
        .replaceAll('"','&quot;')
        .replaceAll("'","&#039;");
}

applyTheme();
renderCart();
</script>


<div class="modal-backdrop" id="customerModal">
    <div class="pos-modal">
        <div class="modal-head">
            <h3>اختيار العميل</h3>
            <button class="modal-close" type="button" data-modal-close="customerModal">إغلاق</button>
        </div>
        <div class="field">
            <input id="customerQuery" type="text" placeholder="الاسم أو الهاتف أو البريد">
        </div>
        <div class="modal-list" id="customerList"></div>
    </div>
</div>

<div class="modal-backdrop" id="heldModal">
    <div class="pos-modal">
        <div class="modal-head">
            <h3>الفواتير المعلقة</h3>
            <button class="modal-close" type="button" data-modal-close="heldModal">إغلاق</button>
        </div>
        <div class="modal-list" id="heldList"></div>
    </div>
</div>

<div class="modal-backdrop" id="historyModal">
    <div class="pos-modal">
        <div class="modal-head">
            <h3>سجل المبيعات</h3>
            <button class="modal-close" type="button" data-modal-close="historyModal">إغلاق</button>
        </div>
        <div class="modal-list" id="historyList"></div>
    </div>
</div>

<div class="modal-backdrop" id="returnModal">
    <div class="pos-modal">
        <div class="modal-head">
            <h3 id="returnTitle">مرتجع</h3>
            <button class="modal-close" type="button" data-modal-close="returnModal">إغلاق</button>
        </div>

        <input type="hidden" id="returnSaleId">
        <input type="hidden" id="returnMode" value="refund">

        <div class="modal-list" id="returnItems"></div>

        <div class="payment-grid" style="margin-top:12px">
            <div class="field">
                <label>طريقة رد المبلغ</label>
                <select id="refundMethod">
                    <option value="original_method">نفس طريقة الدفع الأصلية</option>
                    <option value="cash">نقدي</option>
                    <option value="card">بطاقة</option>
                    <option value="bank_transfer">تحويل</option>
                    <option value="store_credit">رصيد متجر</option>
                </select>
            </div>
            <div class="field">
                <label>استقطاع</label>
                <input id="returnDeduction" type="number" min="0" step="0.001" value="0">
            </div>
        </div>

        <div class="field">
            <label>السبب</label>
            <input id="returnReason" type="text" maxlength="500">
        </div>

        <button class="pay" type="button" id="submitReturn">
            تنفيذ العملية
        </button>
    </div>
</div>

<div class="toast-pos" id="posToast"></div>

</body>
</html>
