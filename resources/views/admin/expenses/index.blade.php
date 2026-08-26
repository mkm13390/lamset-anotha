<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>إدارة المصروفات | لمسة أنوثة</title>
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
        .headline{margin-bottom:18px}
        .headline p{
            margin-top:5px;
            color:var(--muted);
            font-size:13px
        }
        .stats{
            display:grid;
            grid-template-columns:repeat(4,1fr);
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
        .two{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:10px
        }
        .btn{
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
            padding:13px 14px;
            border-bottom:1px solid var(--border);
            font-size:12px
        }
        th{
            background:var(--bg);
            color:var(--muted);
            font-weight:normal
        }
        .amount{
            color:var(--red);
            font-weight:bold
        }
        .delete{
            border:1px solid var(--border);
            background:var(--card);
            color:var(--red);
            border-radius:7px;
            padding:7px 10px;
            cursor:pointer
        }
        .empty{
            text-align:center;
            padding:40px;
            color:var(--muted)
        }
        @media(max-width:1050px){
            .stats{grid-template-columns:repeat(2,1fr)}
            .grid{grid-template-columns:1fr}
        }
        @media(max-width:800px){
            .app{grid-template-columns:1fr}
            .side{display:none}
        }
        @media(max-width:560px){
            .stats{grid-template-columns:1fr}
            .two{grid-template-columns:1fr}
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
            <a href="{{ url('/admin/cash-registers') }}">▣ الخزائن</a>
            <a class="active" href="{{ route('admin.expenses.index') }}">◈ المصروفات</a>
            <a href="{{ url('/admin/reports') }}">▥ التقارير</a>
        </nav>
    </aside>

    <main class="main">

        <header class="top">
            <h1>إدارة المصروفات</h1>
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
                <h2>المصروفات التشغيلية والإدارية</h2>
                <p>تسجيل المصروفات وربطها بالخزينة ومتابعة إجمالياتها.</p>
            </section>

            <section class="stats">

                <div class="stat">
                    <span>عدد المصروفات</span>
                    <strong>{{ $summary['count'] ?? 0 }}</strong>
                </div>

                <div class="stat">
                    <span>إجمالي المصروفات</span>
                    <strong>{{ number_format((float)($summary['total'] ?? 0),3) }} ر.ع</strong>
                </div>

                <div class="stat">
                    <span>مصروفات اليوم</span>
                    <strong>{{ number_format((float)($summary['today'] ?? 0),3) }} ر.ع</strong>
                </div>

                <div class="stat">
                    <span>مصروفات الشهر</span>
                    <strong>{{ number_format((float)($summary['month'] ?? 0),3) }} ر.ع</strong>
                </div>

            </section>

            <div class="grid">

                <section class="card">
                    <div class="card-head">
                        <h3>إضافة مصروف جديد</h3>
                    </div>

                    <form
                        class="form"
                        method="POST"
                        action="{{ route('admin.expenses.store') }}"
                    >
                        @csrf

                        <div class="field">
                            <label>الخزينة</label>

                            <select name="cash_register_id">
                                <option value="">بدون خزينة</option>

                                @foreach($registers as $register)
                                    <option
                                        value="{{ $register->id }}"
                                        @selected(old('cash_register_id') == $register->id)
                                    >
                                        {{ $register->name }}
                                        - {{ number_format((float)$register->current_balance,3) }} ر.ع
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="two">

                            <div class="field">
                                <label>التصنيف</label>
                                <input
                                    type="text"
                                    name="category"
                                    value="{{ old('category') }}"
                                    placeholder="مثال: توصيل / تسويق / إيجار"
                                    required
                                >
                            </div>

                            <div class="field">
                                <label>اسم المصروف</label>
                                <input
                                    type="text"
                                    name="title"
                                    value="{{ old('title') }}"
                                    placeholder="وصف مختصر"
                                    required
                                >
                            </div>

                        </div>

                        <div class="two">

                            <div class="field">
                                <label>المبلغ</label>
                                <input
                                    type="number"
                                    name="amount"
                                    value="{{ old('amount') }}"
                                    step="0.001"
                                    min="0.001"
                                    required
                                >
                            </div>

                            <div class="field">
                                <label>تاريخ المصروف</label>
                                <input
                                    type="date"
                                    name="expense_date"
                                    value="{{ old('expense_date', now()->toDateString()) }}"
                                    required
                                >
                            </div>

                        </div>

                        <div class="two">

                            <div class="field">
                                <label>طريقة الدفع</label>

                                <select name="payment_method" required>
                                    <option value="cash" @selected(old('payment_method') === 'cash')>نقدي</option>
                                    <option value="card" @selected(old('payment_method') === 'card')>بطاقة</option>
                                    <option value="transfer" @selected(old('payment_method') === 'transfer')>تحويل</option>
                                    <option value="other" @selected(old('payment_method') === 'other')>أخرى</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>المرجع</label>
                                <input
                                    type="text"
                                    name="reference"
                                    value="{{ old('reference') }}"
                                    placeholder="اختياري"
                                >
                            </div>

                        </div>

                        <div class="field">
                            <label>ملاحظات</label>
                            <textarea
                                name="notes"
                                placeholder="اختياري"
                            >{{ old('notes') }}</textarea>
                        </div>

                        <button class="btn" type="submit">
                            حفظ المصروف
                        </button>
                    </form>
                </section>

                <section class="card">

                    <div class="card-head">
                        <h3>سجل المصروفات</h3>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>التصنيف</th>
                                <th>المصروف</th>
                                <th>الخزينة</th>
                                <th>طريقة الدفع</th>
                                <th>المرجع</th>
                                <th>المبلغ</th>
                                <th>الإجراء</th>
                            </tr>
                            </thead>

                            <tbody>

                            @forelse($expenses as $expense)

                                <tr>
                                    <td>
                                        {{ optional($expense->expense_date)->format('Y-m-d') }}
                                    </td>

                                    <td>{{ $expense->category }}</td>

                                    <td>
                                        <strong>{{ $expense->title }}</strong>
                                    </td>

                                    <td>
                                        {{ optional($expense->cashRegister)->name ?? '—' }}
                                    </td>

                                    <td>{{ $expense->payment_method }}</td>

                                    <td>{{ $expense->reference ?? '—' }}</td>

                                    <td class="amount">
                                        {{ number_format((float)$expense->amount,3) }} ر.ع
                                    </td>

                                    <td>
                                        <form
                                            method="POST"
                                            action="{{ route('admin.expenses.destroy', $expense) }}"
                                            onsubmit="return confirm('هل تريد حذف هذا المصروف؟ سيتم تصحيح رصيد الخزينة تلقائيًا إذا كان مرتبطًا بها.')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button class="delete" type="submit">
                                                حذف
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8">
                                        <div class="empty">
                                            لا توجد مصروفات حتى الآن.
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

applyTheme();
</script>

</body>
</html>
