<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>الموردون | لمسة أنوثة</title>
    <link rel="icon" href="{{ asset('images/logo.jpeg') }}">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{--red:#e21b23;--bg:#f4f5f7;--card:#fff;--text:#171717;--muted:#777;--border:#e2e4e7;--shadow:0 8px 28px rgba(0,0,0,.07)}
        body.dark{--bg:#101010;--card:#191919;--text:#f5f5f5;--muted:#aaa;--border:#303030;--shadow:0 12px 35px rgba(0,0,0,.35)}
        body{font-family:Arial,"Segoe UI",sans-serif;background:var(--bg);color:var(--text)}
        a{color:inherit;text-decoration:none}
        button,input,textarea,select{font:inherit}
        .app{min-height:100vh;display:grid;grid-template-columns:245px 1fr}
        .side{background:#090909;color:#eee;padding:18px 13px}
        .logo{height:98px;display:flex;align-items:center;justify-content:center;border-bottom:1px solid #292929;margin-bottom:15px}
        .logo img{width:205px;height:86px;object-fit:contain}
        .nav a{display:flex;gap:10px;padding:12px;border-radius:9px;margin:3px 0;font-size:14px}
        .nav a:hover,.nav a.active{background:var(--red)}
        .main{min-width:0}
        .top{height:74px;padding:0 3%;display:flex;align-items:center;justify-content:space-between;background:var(--card);border-bottom:1px solid var(--border)}
        .top h1{font-size:21px}
        .tool{height:40px;padding:0 12px;border:1px solid var(--border);border-radius:8px;background:var(--card);color:var(--text);cursor:pointer}
        .content{padding:25px 3% 55px}
        .headline{display:flex;justify-content:space-between;gap:15px;align-items:end;margin-bottom:18px}
        .headline p{color:var(--muted);font-size:13px;margin-top:5px}
        .btn-primary{height:42px;padding:0 16px;border:0;border-radius:8px;background:var(--red);color:#fff;cursor:pointer}
        .notice{padding:12px 14px;border-radius:9px;margin-bottom:14px;background:#d1fae5;color:#087443;border:1px solid #a7f3d0;font-size:13px}
        .error{background:#fee2e2;color:#b91c1c;border-color:#fecaca}
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:17px}
        .stat,.card{background:var(--card);border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow)}
        .stat{padding:17px}
        .stat span{color:var(--muted);font-size:12px}
        .stat strong{display:block;font-size:24px;margin-top:8px}
        .search{display:flex;gap:8px;margin-bottom:14px}
        .search input{height:42px;flex:1;padding:0 12px;border:1px solid var(--border);border-radius:8px;background:var(--card);color:var(--text)}
        .table-wrap{overflow:auto}
        table{width:100%;border-collapse:collapse;white-space:nowrap}
        th,td{text-align:start;padding:13px 14px;border-bottom:1px solid var(--border);font-size:12px;vertical-align:top}
        th{background:var(--bg);color:var(--muted);font-weight:normal}
        .badge{display:inline-block;padding:5px 8px;border-radius:20px;font-size:10px;background:#eee;color:#666}
        .badge.ok{background:#d1fae5;color:#087443}.badge.off{background:#fee2e2;color:#b91c1c}
        .actions{display:flex;gap:6px;flex-wrap:wrap}
        .btn{min-height:34px;padding:0 10px;border:1px solid var(--border);border-radius:7px;background:var(--card);color:var(--text);cursor:pointer}
        .empty{text-align:center;padding:48px;color:var(--muted)}
        .form-card{margin-top:18px;padding:18px}
        .form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
        .field.full{grid-column:1/-1}.field label{display:block;font-size:12px;margin-bottom:6px;color:var(--muted)}
        .field input,.field textarea,.field select{width:100%;border:1px solid var(--border);border-radius:8px;background:var(--bg);color:var(--text);padding:10px}
        .field textarea{min-height:85px;resize:vertical}
        .check{display:flex;gap:8px;align-items:center}
        .pager{padding:14px}
        @media(max-width:1000px){.stats{grid-template-columns:1fr 1fr}}
        @media(max-width:800px){.app{grid-template-columns:1fr}.side{display:none}}
        @media(max-width:600px){.stats,.form-grid{grid-template-columns:1fr}.headline{flex-direction:column;align-items:stretch}}
    </style>
</head>
<body>
<div class="app">
    <aside class="side">
        <a class="logo" href="{{ route('admin.dashboard') }}"><img src="{{ asset('images/logo.jpeg') }}" alt="لمسة أنوثة"></a>
        <nav class="nav">
            <a href="{{ route('admin.dashboard') }}">⌂ لوحة التحكم</a>
            <a href="{{ route('admin.orders.index') }}">▤ الطلبات</a>
            <a href="{{ route('admin.customers.index') }}">♙ العملاء</a>
            <a href="{{ route('admin.inventory.index') }}">▦ المخزون</a>
            <a class="active" href="{{ route('admin.suppliers.index') }}">♧ الموردون</a>
            <a href="{{ route('admin.purchases.index') }}">🧾 المشتريات</a>
            <a href="{{ route('admin.pos.index') }}">▰ نقطة البيع</a>
        </nav>
    </aside>

    <main class="main">
        <header class="top">
            <h1>إدارة الموردين</h1>
            <button class="tool" id="theme" type="button">☾</button>
        </header>

        <div class="content">
            @if(session('success'))<div class="notice">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="notice error">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

            <div class="headline">
                <div><h2>قائمة الموردين</h2><p>إدارة بيانات الموردين، الأرصدة وشروط الدفع.</p></div>
            </div>

            <section class="stats">
                <div class="stat"><span>إجمالي الموردين</span><strong>{{ $stats['total'] ?? 0 }}</strong></div>
                <div class="stat"><span>نشط</span><strong>{{ $stats['active'] ?? 0 }}</strong></div>
                <div class="stat"><span>غير نشط</span><strong>{{ $stats['inactive'] ?? 0 }}</strong></div>
                <div class="stat"><span>إجمالي الأرصدة</span><strong>{{ number_format((float)($stats['balance'] ?? 0),3) }} ر.ع</strong></div>
            </section>

            <form class="search" method="GET" action="{{ route('admin.suppliers.index') }}">
                <input name="search" value="{{ $search }}" placeholder="بحث بالاسم أو الهاتف أو البريد أو السجل التجاري">
                <button class="btn-primary" type="submit">بحث</button>
            </form>

            <section class="card">
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>المورد</th><th>التواصل</th><th>المشتريات</th><th>الرصيد</th><th>الحالة</th><th>الإجراءات</th></tr></thead>
                        <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td><b>{{ $supplier->name }}</b><br><small>{{ $supplier->company_name ?: '—' }}</small></td>
                                <td>{{ $supplier->phone ?: '—' }}<br><small>{{ $supplier->email ?: '—' }}</small></td>
                                <td>{{ $supplier->purchase_orders_count ?? 0 }} طلب<br><small>{{ number_format((float)($supplier->purchases_total ?? 0),3) }} ر.ع</small></td>
                                <td>{{ number_format((float)$supplier->current_balance,3) }} ر.ع</td>
                                <td><span class="badge {{ $supplier->is_active ? 'ok' : 'off' }}">{{ $supplier->is_active ? 'نشط' : 'موقوف' }}</span></td>
                                <td>
                                    <div class="actions">
                                        <form method="POST" action="{{ route('admin.suppliers.toggle', $supplier) }}">@csrf @method('PATCH')
                                            <button class="btn" type="submit">{{ $supplier->is_active ? 'إيقاف' : 'تفعيل' }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="empty">لا يوجد موردون حتى الآن.</div></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($suppliers->hasPages())<div class="pager">{{ $suppliers->links() }}</div>@endif
            </section>

            <section class="card form-card">
                <h3 style="margin-bottom:14px">إضافة مورد جديد</h3>

                <form method="POST" action="{{ route('admin.suppliers.store') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="field"><label>اسم المورد</label><input name="name" required></div>
                        <div class="field"><label>اسم الشركة</label><input name="company_name"></div>
                        <div class="field"><label>الهاتف</label><input name="phone"></div>
                        <div class="field"><label>واتساب</label><input name="whatsapp"></div>
                        <div class="field"><label>البريد الإلكتروني</label><input name="email" type="email"></div>
                        <div class="field"><label>السجل التجاري</label><input name="commercial_registration"></div>
                        <div class="field"><label>الرقم الضريبي</label><input name="tax_number"></div>
                        <div class="field"><label>الدولة</label><input name="country" value="Oman" required></div>
                        <div class="field"><label>المحافظة</label><input name="governorate"></div>
                        <div class="field"><label>الولاية</label><input name="wilayat"></div>
                        <div class="field"><label>الرصيد الافتتاحي</label><input name="opening_balance" type="number" step="0.001" min="0" value="0"></div>
                        <div class="field"><label>مدة السداد بالأيام</label><input name="payment_terms_days" type="number" min="0" value="0"></div>
                        <div class="field full"><label>العنوان</label><textarea name="address"></textarea></div>
                        <div class="field full"><label>ملاحظات</label><textarea name="notes"></textarea></div>
                        <div class="field full"><label class="check"><input name="is_active" type="checkbox" value="1" checked> مورد نشط</label></div>
                    </div>

                    <br>
                    <button class="btn-primary" type="submit">حفظ المورد</button>
                </form>
            </section>
        </div>
    </main>
</div>
<script>
let theme=localStorage.adminTheme||'light';
function applyTheme(){document.body.classList.toggle('dark',theme==='dark');document.getElementById('theme').textContent=theme==='dark'?'☀':'☾'}
document.getElementById('theme').onclick=()=>{theme=theme==='dark'?'light':'dark';localStorage.adminTheme=theme;applyTheme()};
applyTheme();
</script>
</body>
</html>