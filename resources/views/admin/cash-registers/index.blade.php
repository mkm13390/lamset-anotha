<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>إدارة الخزائن | لمسة أنوثة</title>
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
        .notice{
            margin-bottom:15px;
            padding:11px 13px;
            border-radius:8px;
            font-size:12px
        }
        .success{background:#d1fae5;color:#087443}
        .error{background:#fee2e2;color:#b91c1c}
        .headline{
            margin-bottom:18px
        }
        .headline p{
            margin-top:5px;
            color:var(--muted);
            font-size:13px
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
            color:var(--muted);
            font-size:12px
        }
        .field input,.field select,.field textarea{
            border:1px solid var(--border);
            border-radius:8px;
            background:var(--bg);
            color:var(--text);
            padding:0 12px;
            outline:0
        }
        .field input,.field select{height:44px}
        .field textarea{
            min-height:80px;
            padding-top:10px;
            resize:vertical
        }
        .check{
            display:flex;
            gap:8px;
            align-items:center;
            font-size:13px
        }
        .btn{
            height:44px;
            border:0;
            border-radius:8px;
            background:var(--red);
            color:#fff;
            cursor:pointer
        }
        .registers{
            padding:16px;
            display:grid;
            gap:12px
        }
        .register{
            border:1px solid var(--border);
            border-radius:11px;
            padding:14px
        }
        .register-top{
            display:flex;
            justify-content:space-between;
            gap:10px;
            align-items:start
        }
        .register h3{
            font-size:16px;
            margin-bottom:5px
        }
        .register small{color:var(--muted)}
        .balance{
            margin-top:12px;
            font-size:13px
        }
        .balance strong{
            color:var(--red);
            font-size:20px
        }
        .badge{
            display:inline-block;
            padding:5px 9px;
            border-radius:16px;
            font-size:10px
        }
        .on{background:#d1fae5;color:#087443}
        .off{background:#fee2e2;color:#b91c1c}
        .register-actions{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:8px;
            margin-top:12px
        }
        .small-btn{
            height:38px;
            border:1px solid var(--border);
            border-radius:7px;
            background:var(--card);
            color:var(--text);
            cursor:pointer
        }
        .movement-box{
            margin-top:12px;
            padding-top:12px;
            border-top:1px solid var(--border)
        }
        .movement-box form{
            display:grid;
            grid-template-columns:130px 1fr 1fr auto;
            gap:8px
        }
        .movement-box input,.movement-box select{
            height:38px;
            border:1px solid var(--border);
            border-radius:7px;
            background:var(--bg);
            color:var(--text);
            padding:0 9px
        }
        .movement-box button{
            border:0;
            border-radius:7px;
            background:#222;
            color:#fff;
            padding:0 13px;
            cursor:pointer
        }
        .table-card{
            margin-top:16px;
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
            padding:13px 14px;
            border-bottom:1px solid var(--border);
            font-size:12px
        }
        th{
            background:var(--bg);
            color:var(--muted);
            font-weight:normal
        }
        .empty{
            text-align:center;
            padding:35px;
            color:var(--muted)
        }
        @media(max-width:1000px){
            .grid{grid-template-columns:1fr}
        }
        @media(max-width:800px){
            .app{grid-template-columns:1fr}
            .side{display:none}
        }
        @media(max-width:650px){
            .movement-box form{grid-template-columns:1fr}
            .register-actions{grid-template-columns:1fr}
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
            <a class="active" href="{{ route('admin.cash-registers.index') }}">▣ الخزائن</a>
            <a href="{{ url('/admin/reports') }}">▥ التقارير</a>
        </nav>
    </aside>

    <main class="main">

        <header class="top">
            <h1>إدارة الخزائن</h1>
            <button class="tool" id="theme" type="button">☾</button>
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
                <h2>الخزائن والحركات النقدية</h2>
                <p>إنشاء خزائن، متابعة الرصيد، وإضافة الإيداعات والسحوبات والتعديلات.</p>
            </section>

            <div class="grid">

                <section class="card">
                    <div class="card-head">
                        <h3>إضافة خزينة جديدة</h3>
                    </div>

                    <form class="form" method="POST" action="{{ route('admin.cash-registers.store') }}">
                        @csrf

                        <div class="field">
                            <label>اسم الخزينة</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="مثال: الخزينة الرئيسية"
                                required
                            >
                        </div>

                        <div class="field">
                            <label>الرصيد الافتتاحي</label>
                            <input
                                type="number"
                                name="opening_balance"
                                step="0.001"
                                min="0"
                                value="{{ old('opening_balance', 0) }}"
                            >
                        </div>

                        <label class="check">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked(old('is_active', true))
                            >
                            تفعيل الخزينة
                        </label>

                        <button class="btn" type="submit">
                            حفظ الخزينة
                        </button>
                    </form>
                </section>

                <section class="card">
                    <div class="card-head">
                        <h3>الخزائن الحالية</h3>
                    </div>

                    <div class="registers">

                        @forelse($registers as $register)

                            <article class="register">

                                <div class="register-top">
                                    <div>
                                        <h3>{{ $register->name }}</h3>
                                        <small>
                                            الرصيد الافتتاحي:
                                            {{ number_format((float)$register->opening_balance,3) }} ر.ع
                                        </small>
                                    </div>

                                    <span class="badge {{ $register->is_active ? 'on' : 'off' }}">
                                        {{ $register->is_active ? 'مفعلة' : 'متوقفة' }}
                                    </span>
                                </div>

                                <div class="balance">
                                    الرصيد الحالي:
                                    <strong>
                                        {{ number_format((float)$register->current_balance,3) }} ر.ع
                                    </strong>
                                </div>

                                <div class="register-actions">

                                    <form
                                        method="POST"
                                        action="{{ route('admin.cash-registers.toggle', $register) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button class="small-btn" type="submit" style="width:100%">
                                            {{ $register->is_active ? 'تعطيل الخزينة' : 'تفعيل الخزينة' }}
                                        </button>
                                    </form>

                                    <button
                                        class="small-btn"
                                        type="button"
                                        onclick="toggleMovement({{ $register->id }})"
                                    >
                                        إضافة حركة
                                    </button>

                                </div>

                                <div
                                    class="movement-box"
                                    id="movement-{{ $register->id }}"
                                    style="display:none"
                                >
                                    <form
                                        method="POST"
                                        action="{{ route('admin.cash-registers.movement', $register) }}"
                                    >
                                        @csrf

                                        <select name="type" required>
                                            <option value="deposit">إيداع</option>
                                            <option value="withdrawal">سحب</option>
                                            <option value="adjustment">تعديل</option>
                                        </select>

                                        <input
                                            type="number"
                                            name="amount"
                                            min="0.001"
                                            step="0.001"
                                            placeholder="المبلغ"
                                            required
                                        >

                                        <input
                                            type="text"
                                            name="description"
                                            placeholder="الوصف"
                                        >

                                        <button type="submit">
                                            حفظ
                                        </button>
                                    </form>
                                </div>

                            </article>

                        @empty

                            <div class="empty">
                                لا توجد خزائن حتى الآن.
                            </div>

                        @endforelse

                    </div>
                </section>

            </div>

            <section class="card table-card">
                <div class="card-head">
                    <h3>آخر حركات الخزائن</h3>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>الخزينة</th>
                            <th>نوع الحركة</th>
                            <th>المبلغ</th>
                            <th>الرصيد بعد الحركة</th>
                            <th>المرجع</th>
                            <th>الوصف</th>
                            <th>التاريخ</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($movements as $movement)

                            <tr>
                                <td>{{ optional($movement->cashRegister)->name ?? '—' }}</td>
                                <td>{{ $movement->type }}</td>
                                <td>{{ number_format((float)$movement->amount,3) }} ر.ع</td>
                                <td>{{ number_format((float)$movement->balance_after,3) }} ر.ع</td>
                                <td>{{ $movement->reference ?? '—' }}</td>
                                <td>{{ $movement->description ?? '—' }}</td>
                                <td>{{ optional($movement->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="7">
                                    <div class="empty">
                                        لا توجد حركات خزينة حتى الآن.
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
let theme = localStorage.adminTheme || 'light';
const themeButton = document.getElementById('theme');

function applyTheme(){
    document.body.classList.toggle('dark', theme === 'dark');
    themeButton.textContent = theme === 'dark' ? '☀' : '☾';
}

themeButton.onclick = () => {
    theme = theme === 'dark' ? 'light' : 'dark';
    localStorage.adminTheme = theme;
    applyTheme();
};

function toggleMovement(id){
    const box = document.getElementById('movement-' + id);

    if (!box) {
        return;
    }

    box.style.display =
        box.style.display === 'none'
            ? 'block'
            : 'none';
}

applyTheme();
</script>

</body>
</html>
