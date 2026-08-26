<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>إدارة الكوبونات | لمسة أنوثة</title>
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
        .stats{
            display:grid;
            grid-template-columns:repeat(3,1fr);
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
        .grid{
            display:grid;
            grid-template-columns:360px 1fr;
            gap:16px
        }
        .card{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:13px;
            box-shadow:var(--shadow)
        }
        .card-head{
            padding:16px 18px;
            border-bottom:1px solid var(--border)
        }
        .card-head h3{font-size:17px}
        .form{
            padding:18px;
            display:grid;
            gap:12px
        }
        .field{
            display:grid;
            gap:6px
        }
        .field label{
            font-size:12px;
            color:var(--muted)
        }
        .field input,.field select{
            height:44px;
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--bg);
            color:var(--text);
            padding:0 12px;
            outline:0
        }
        .check{
            display:flex;
            align-items:center;
            gap:8px;
            font-size:13px
        }
        .save{
            height:44px;
            border:0;
            border-radius:8px;
            background:var(--red);
            color:#fff;
            cursor:pointer
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
        .badge{
            display:inline-block;
            padding:5px 9px;
            border-radius:16px;
            font-size:10px
        }
        .on{background:#d1fae5;color:#087443}
        .off{background:#fee2e2;color:#b91c1c}
        .actions{
            display:flex;
            gap:6px
        }
        .icon-btn{
            min-width:34px;
            height:34px;
            padding:0 8px;
            border:1px solid var(--border);
            border-radius:7px;
            background:var(--card);
            color:var(--text);
            cursor:pointer
        }
        .icon-btn:hover{
            border-color:var(--red);
            color:var(--red)
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
        .empty{
            text-align:center;
            padding:45px;
            color:var(--muted)
        }
        @media(max-width:1050px){
            .grid{grid-template-columns:1fr}
        }
        @media(max-width:800px){
            .app{grid-template-columns:1fr}
            .side{display:none}
            .stats{grid-template-columns:1fr}
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
            <a class="active" href="{{ route('admin.coupons.index') }}">٪ الكوبونات</a>
            <a href="{{ url('/admin/pos') }}">▰ نقطة البيع</a>
            <a href="#">♙ العملاء</a>
            <a href="#">▥ التقارير</a>
        </nav>
    </aside>

    <main class="main">

        <header class="top">
            <h1>إدارة الكوبونات</h1>

            <div class="tools">
                <button class="tool" id="theme" type="button">☾</button>
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
                    <h2>الكوبونات والخصومات</h2>
                    <p>إنشاء وتعديل وتفعيل كوبونات الخصم لاستخدامها لاحقًا في السلة والـ Checkout.</p>
                </div>
            </section>

            @php
                $activeCount = ($coupons ?? collect())->where('is_active', true)->count();
                $inactiveCount = ($coupons ?? collect())->where('is_active', false)->count();
            @endphp

            <section class="stats">
                <div class="stat">
                    <span>إجمالي الكوبونات</span>
                    <strong>{{ ($coupons ?? collect())->count() }}</strong>
                </div>

                <div class="stat">
                    <span>مفعلة</span>
                    <strong>{{ $activeCount }}</strong>
                </div>

                <div class="stat">
                    <span>غير مفعلة</span>
                    <strong>{{ $inactiveCount }}</strong>
                </div>
            </section>

            <div class="grid">

                <section class="card">
                    <div class="card-head">
                        <h3>إضافة كوبون جديد</h3>
                    </div>

                    <form
                        class="form"
                        method="POST"
                        action="{{ route('admin.coupons.store') }}"
                    >
                        @csrf

                        <div class="field">
                            <label>رمز الكوبون</label>
                            <input
                                type="text"
                                name="code"
                                value="{{ old('code') }}"
                                placeholder="مثال: WELCOME10"
                                required
                            >
                        </div>

                        <div class="field">
                            <label>اسم الكوبون</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="مثال: خصم العملاء الجدد"
                            >
                        </div>

                        <div class="field">
                            <label>نوع الخصم</label>
                            <select name="discount_type" required>
                                <option value="percentage" @selected(old('discount_type') === 'percentage')>
                                    نسبة مئوية %
                                </option>
                                <option value="fixed" @selected(old('discount_type') === 'fixed')>
                                    مبلغ ثابت
                                </option>
                            </select>
                        </div>

                        <div class="field">
                            <label>قيمة الخصم</label>
                            <input
                                type="number"
                                step="0.001"
                                min="0.001"
                                name="discount_value"
                                value="{{ old('discount_value') }}"
                                required
                            >
                        </div>

                        <div class="field">
                            <label>الحد الأدنى للطلب</label>
                            <input
                                type="number"
                                step="0.001"
                                min="0"
                                name="minimum_order_amount"
                                value="{{ old('minimum_order_amount', 0) }}"
                            >
                        </div>

                        <div class="field">
                            <label>تاريخ ووقت البداية</label>
                            <input
                                type="datetime-local"
                                name="starts_at"
                                value="{{ old('starts_at') }}"
                            >
                        </div>

                        <div class="field">
                            <label>تاريخ ووقت الانتهاء</label>
                            <input
                                type="datetime-local"
                                name="ends_at"
                                value="{{ old('ends_at') }}"
                            >
                        </div>

                        <label class="check">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked(old('is_active', true))
                            >
                            تفعيل الكوبون
                        </label>

                        <button class="save" type="submit">
                            حفظ الكوبون
                        </button>
                    </form>
                </section>

                <section class="card">

                    <div class="card-head">
                        <h3>الكوبونات الحالية</h3>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th>الرمز</th>
                                <th>الاسم</th>
                                <th>الخصم</th>
                                <th>الحد الأدنى</th>
                                <th>الفترة</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                            </thead>

                            <tbody>

                            @forelse(($coupons ?? []) as $coupon)

                                <tr>
                                    <td>
                                        <strong>{{ $coupon->code }}</strong>
                                    </td>

                                    <td>
                                        {{ $coupon->name ?: '—' }}
                                    </td>

                                    <td>
                                        @if($coupon->discount_type === 'percentage')
                                            {{ number_format((float)$coupon->discount_value, 3) }}%
                                        @else
                                            {{ number_format((float)$coupon->discount_value, 3) }} ر.ع
                                        @endif
                                    </td>

                                    <td>
                                        {{ number_format((float)$coupon->minimum_order_amount, 3) }} ر.ع
                                    </td>

                                    <td>
                                        <div>
                                            {{ $coupon->starts_at ? $coupon->starts_at->format('Y-m-d H:i') : 'بدون بداية' }}
                                        </div>
                                        <small>
                                            {{ $coupon->ends_at ? $coupon->ends_at->format('Y-m-d H:i') : 'بدون نهاية' }}
                                        </small>
                                    </td>

                                    <td>
                                        <span class="badge {{ $coupon->is_active ? 'on' : 'off' }}">
                                            {{ $coupon->is_active ? 'مفعل' : 'متوقف' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="actions">

                                            <form
                                                method="POST"
                                                action="{{ route('admin.coupons.toggle', $coupon) }}"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button class="icon-btn" type="submit">
                                                    {{ $coupon->is_active ? 'إيقاف' : 'تفعيل' }}
                                                </button>
                                            </form>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.coupons.destroy', $coupon) }}"
                                                onsubmit="return confirm('هل تريد حذف هذا الكوبون؟')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button class="icon-btn" type="submit">
                                                    حذف
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7">
                                        <div class="empty">
                                            لا توجد كوبونات حتى الآن.
                                        </div>
                                    </td>
                                </tr>

                            @endforelse

                            </tbody>
                        </table>
                    </div>

                </section>

            </div>

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
