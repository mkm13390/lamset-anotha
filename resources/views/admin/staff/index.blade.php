<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>إدارة الموظفين | لمسة أنوثة</title>
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
        .headline{margin-bottom:18px}
        .headline p{
            margin-top:5px;
            color:var(--muted);
            font-size:13px
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
        .stat span{
            color:var(--muted);
            font-size:11px
        }
        .stat strong{
            display:block;
            margin-top:8px;
            font-size:22px
        }
        .grid{
            display:grid;
            grid-template-columns:370px 1fr;
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
        .btn{
            height:44px;
            border:0;
            border-radius:8px;
            background:var(--red);
            color:#fff;
            cursor:pointer
        }
        .staff-list{
            padding:16px;
            display:grid;
            gap:12px
        }
        .staff-card{
            border:1px solid var(--border);
            border-radius:11px;
            padding:14px
        }
        .staff-head{
            display:flex;
            justify-content:space-between;
            gap:12px;
            align-items:start;
            margin-bottom:12px
        }
        .staff-head h3{
            font-size:16px;
            margin-bottom:4px
        }
        .staff-head small{
            color:var(--muted);
            display:block;
            margin-top:3px
        }
        .badge{
            display:inline-block;
            padding:5px 9px;
            border-radius:16px;
            font-size:10px
        }
        .admin{background:#ede9fe;color:#6d28d9}
        .staff{background:#dbeafe;color:#1d4ed8}
        .on{background:#d1fae5;color:#087443}
        .off{background:#fee2e2;color:#b91c1c}
        .edit-form{
            display:grid;
            grid-template-columns:repeat(2,1fr);
            gap:9px
        }
        .edit-form input,.edit-form select{
            height:40px;
            border:1px solid var(--border);
            border-radius:7px;
            background:var(--bg);
            color:var(--text);
            padding:0 10px
        }
        .full{grid-column:1/-1}
        .actions{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:8px;
            margin-top:10px
        }
        .small-btn{
            height:38px;
            border:1px solid var(--border);
            border-radius:7px;
            background:var(--card);
            color:var(--text);
            cursor:pointer
        }
        .danger{color:var(--red)}
        .password-box{
            margin-top:12px;
            padding-top:12px;
            border-top:1px solid var(--border)
        }
        .password-box form{
            display:grid;
            grid-template-columns:1fr 1fr auto;
            gap:8px
        }
        .password-box input{
            height:38px;
            border:1px solid var(--border);
            border-radius:7px;
            background:var(--bg);
            color:var(--text);
            padding:0 10px
        }
        .password-box button{
            border:0;
            border-radius:7px;
            background:#222;
            color:#fff;
            padding:0 13px;
            cursor:pointer
        }
        .empty{
            text-align:center;
            padding:35px;
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
        @media(max-width:620px){
            .edit-form{grid-template-columns:1fr}
            .full{grid-column:auto}
            .actions{grid-template-columns:1fr}
            .password-box form{grid-template-columns:1fr}
        }
    </style>
</head>

<body>

@php
    $staffCount = ($staff ?? collect())->where('role', 'staff')->count();
    $adminCount = ($staff ?? collect())->where('role', 'admin')->count();
    $activeCount = ($staff ?? collect())->where('is_active', true)->count();
@endphp

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
            <a href="{{ url('/admin/cash-registers') }}">▣ الخزائن</a>
            <a href="{{ url('/admin/expenses') }}">◈ المصروفات</a>
            <a class="active" href="{{ route('admin.staff.index') }}">♙ الموظفون</a>
            <a href="{{ url('/admin/reports') }}">▥ التقارير</a>
        </nav>
    </aside>

    <main class="main">

        <header class="top">
            <h1>إدارة الموظفين والصلاحيات</h1>
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
                <h2>حسابات الإدارة والموظفين</h2>
                <p>إنشاء الحسابات، تغيير الدور، تفعيل أو تعطيل الوصول، وتغيير كلمات المرور.</p>
            </section>

            <section class="stats">
                <div class="stat">
                    <span>الموظفون</span>
                    <strong>{{ $staffCount }}</strong>
                </div>

                <div class="stat">
                    <span>المديرون</span>
                    <strong>{{ $adminCount }}</strong>
                </div>

                <div class="stat">
                    <span>الحسابات المفعلة</span>
                    <strong>{{ $activeCount }}</strong>
                </div>
            </section>

            <div class="grid">

                <section class="card">
                    <div class="card-head">
                        <h3>إضافة حساب جديد</h3>
                    </div>

                    <form
                        class="form"
                        method="POST"
                        action="{{ route('admin.staff.store') }}"
                    >
                        @csrf

                        <div class="field">
                            <label>الاسم</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                            >
                        </div>

                        <div class="field">
                            <label>البريد الإلكتروني</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                            >
                        </div>

                        <div class="field">
                            <label>رقم الهاتف</label>
                            <input
                                type="text"
                                name="phone"
                                value="{{ old('phone') }}"
                                required
                            >
                        </div>

                        <div class="field">
                            <label>الدور</label>
                            <select name="role" required>
                                <option value="staff" @selected(old('role') === 'staff')>
                                    موظف
                                </option>
                                <option value="admin" @selected(old('role') === 'admin')>
                                    مدير
                                </option>
                            </select>
                        </div>

                        <div class="field">
                            <label>كلمة المرور</label>
                            <input
                                type="password"
                                name="password"
                                required
                            >
                        </div>

                        <div class="field">
                            <label>تأكيد كلمة المرور</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                required
                            >
                        </div>

                        <label class="check">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked(old('is_active', true))
                            >
                            تفعيل الحساب
                        </label>

                        <button class="btn" type="submit">
                            إنشاء الحساب
                        </button>
                    </form>
                </section>

                <section class="card">
                    <div class="card-head">
                        <h3>الحسابات الحالية</h3>
                    </div>

                    <div class="staff-list">

                        @forelse($staff as $member)

                            <article class="staff-card">

                                <div class="staff-head">
                                    <div>
                                        <h3>{{ $member->name }}</h3>
                                        <small>{{ $member->email }}</small>
                                        <small>{{ $member->phone }}</small>
                                    </div>

                                    <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:end">
                                        <span class="badge {{ $member->role === 'admin' ? 'admin' : 'staff' }}">
                                            {{ $member->role === 'admin' ? 'مدير' : 'موظف' }}
                                        </span>

                                        <span class="badge {{ $member->is_active ? 'on' : 'off' }}">
                                            {{ $member->is_active ? 'مفعّل' : 'متوقف' }}
                                        </span>
                                    </div>
                                </div>

                                <form
                                    class="edit-form"
                                    method="POST"
                                    action="{{ route('admin.staff.update', $member) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ $member->name }}"
                                        required
                                    >

                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ $member->email }}"
                                        required
                                    >

                                    <input
                                        type="text"
                                        name="phone"
                                        value="{{ $member->phone }}"
                                        required
                                    >

                                    <select name="role" required>
                                        <option value="staff" @selected($member->role === 'staff')>
                                            موظف
                                        </option>
                                        <option value="admin" @selected($member->role === 'admin')>
                                            مدير
                                        </option>
                                    </select>

                                    <label class="check full">
                                        <input
                                            type="checkbox"
                                            name="is_active"
                                            value="1"
                                            @checked($member->is_active)
                                        >
                                        الحساب مفعّل
                                    </label>

                                    <button class="btn full" type="submit">
                                        حفظ التعديلات
                                    </button>
                                </form>

                                <div class="actions">

                                    <form
                                        method="POST"
                                        action="{{ route('admin.staff.toggle', $member) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button class="small-btn" type="submit" style="width:100%">
                                            {{ $member->is_active ? 'تعطيل الحساب' : 'تفعيل الحساب' }}
                                        </button>
                                    </form>

                                    <button
                                        class="small-btn"
                                        type="button"
                                        onclick="togglePassword({{ $member->id }})"
                                    >
                                        تغيير كلمة المرور
                                    </button>

                                </div>

                                <div
                                    class="password-box"
                                    id="password-{{ $member->id }}"
                                    style="display:none"
                                >
                                    <form
                                        method="POST"
                                        action="{{ route('admin.staff.password', $member) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="password"
                                            name="password"
                                            placeholder="كلمة المرور الجديدة"
                                            required
                                        >

                                        <input
                                            type="password"
                                            name="password_confirmation"
                                            placeholder="تأكيد كلمة المرور"
                                            required
                                        >

                                        <button type="submit">
                                            حفظ
                                        </button>
                                    </form>
                                </div>

                            </article>

                        @empty

                            <div class="empty">
                                لا توجد حسابات موظفين أو مديرين حتى الآن.
                            </div>

                        @endforelse

                    </div>
                </section>

            </div>

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

function togglePassword(id){
    const box = document.getElementById('password-' + id);

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
