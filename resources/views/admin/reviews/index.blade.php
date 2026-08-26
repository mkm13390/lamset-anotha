<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>إدارة التقييمات | لمسة أنوثة</title>
    <link rel="icon" href="{{ asset('images/logo.jpeg') }}">

    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --red:#e21b23;--bg:#f4f5f7;--card:#fff;--text:#171717;
            --muted:#777;--border:#e2e4e7;--shadow:0 8px 28px rgba(0,0,0,.07)
        }
        body.dark{
            --bg:#101010;--card:#191919;--text:#f5f5f5;
            --muted:#aaa;--border:#303030;--shadow:0 12px 35px rgba(0,0,0,.35)
        }
        body{font-family:Arial,"Segoe UI",sans-serif;background:var(--bg);color:var(--text)}
        a{color:inherit;text-decoration:none}
        button,input,textarea{font:inherit}
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
        .headline{margin-bottom:18px}
        .headline p{color:var(--muted);font-size:13px;margin-top:5px}
        .notice{padding:12px 14px;border-radius:9px;margin-bottom:14px;background:#d1fae5;color:#087443;border:1px solid #a7f3d0;font-size:13px}
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:17px}
        .stat{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:17px;box-shadow:var(--shadow)}
        .stat span{color:var(--muted);font-size:12px}
        .stat strong{display:block;font-size:24px;margin-top:8px}
        .tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}
        .tab{padding:9px 13px;border:1px solid var(--border);border-radius:8px;background:var(--card)}
        .tab.active{background:var(--red);color:#fff;border-color:var(--red)}
        .card{background:var(--card);border:1px solid var(--border);border-radius:13px;box-shadow:var(--shadow);overflow:hidden}
        .table-wrap{overflow:auto}
        table{width:100%;border-collapse:collapse;white-space:nowrap}
        th,td{text-align:start;padding:13px 14px;border-bottom:1px solid var(--border);font-size:12px;vertical-align:top}
        th{background:var(--bg);color:var(--muted);font-weight:normal}
        .stars{color:#c28200;font-size:15px}
        .review-text{white-space:normal;min-width:260px;max-width:420px;line-height:1.7}
        .review-text small{display:block;color:var(--muted);margin-top:5px}
        .badge{display:inline-block;padding:5px 8px;border-radius:20px;font-size:10px;background:#eee;color:#666}
        .badge.ok{background:#d1fae5;color:#087443}
        .badge.warn{background:#fff3cd;color:#856404}
        .badge.hidden{background:#fee2e2;color:#b91c1c}
        .actions{display:flex;gap:6px;flex-wrap:wrap}
        .btn{min-height:34px;padding:0 10px;border:1px solid var(--border);border-radius:7px;background:var(--card);color:var(--text);cursor:pointer}
        .btn.green{color:#087443}
        .btn.red{color:#b91c1c}
        .btn.orange{color:#9a6700}
        .reject-form{display:flex;gap:5px}
        .reject-form input{height:34px;width:150px;border:1px solid var(--border);border-radius:7px;background:var(--bg);color:var(--text);padding:0 8px}
        .empty{text-align:center;padding:50px;color:var(--muted)}
        .pager{padding:14px}
        @media(max-width:1000px){.stats{grid-template-columns:1fr 1fr}}
        @media(max-width:800px){.app{grid-template-columns:1fr}.side{display:none}}
        @media(max-width:560px){.stats{grid-template-columns:1fr}}
    </style>
</head>

<body>
<div class="app">

    <aside class="side">
        <a class="logo" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('images/logo.jpeg') }}" alt="لمسة أنوثة">
        </a>

        <nav class="nav">
            <a href="{{ route('admin.dashboard') }}">⌂ لوحة التحكم</a>
            <a href="{{ route('admin.orders.index') }}">▤ الطلبات</a>
            <a href="{{ route('admin.customers.index') }}">♙ العملاء</a>
            <a class="active" href="{{ route('admin.reviews.index') }}">★ التقييمات</a>
            <a href="{{ route('admin.inventory.index') }}">▦ المخزون</a>
            <a href="{{ route('admin.pos.index') }}">▰ نقطة البيع</a>
        </nav>
    </aside>

    <main class="main">

        <header class="top">
            <h1>إدارة التقييمات</h1>
            <button class="tool" id="theme" type="button">☾</button>
        </header>

        <div class="content">

            @if(session('success'))
                <div class="notice">{{ session('success') }}</div>
            @endif

            <section class="headline">
                <h2>تقييمات المنتجات</h2>
                <p>مراجعة واعتماد أو إخفاء تقييمات العملاء قبل ظهورها في المتجر.</p>
            </section>

            <section class="stats">
                <div class="stat">
                    <span>إجمالي التقييمات</span>
                    <strong>{{ number_format($stats['total'] ?? 0) }}</strong>
                </div>

                <div class="stat">
                    <span>بانتظار المراجعة</span>
                    <strong>{{ number_format($stats['pending'] ?? 0) }}</strong>
                </div>

                <div class="stat">
                    <span>معتمدة</span>
                    <strong>{{ number_format($stats['approved'] ?? 0) }}</strong>
                </div>

                <div class="stat">
                    <span>مخفية</span>
                    <strong>{{ number_format($stats['hidden'] ?? 0) }}</strong>
                </div>
            </section>

            <div class="tabs">
                <a class="tab {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">الكل</a>
                <a class="tab {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.reviews.index', ['status' => 'pending']) }}">بانتظار المراجعة</a>
                <a class="tab {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('admin.reviews.index', ['status' => 'approved']) }}">المعتمدة</a>
                <a class="tab {{ $status === 'hidden' ? 'active' : '' }}" href="{{ route('admin.reviews.index', ['status' => 'hidden']) }}">المخفية</a>
            </div>

            <section class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>العميل</th>
                            <th>المنتج</th>
                            <th>التقييم</th>
                            <th>المراجعة</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>
                                    <b>{{ $review->user->name ?? 'عميل' }}</b><br>
                                    <small style="color:var(--muted)">
                                        {{ $review->user->email ?? '—' }}
                                    </small>
                                </td>

                                <td>
                                    <b>{{ $review->product->name_ar ?? $review->product->name ?? 'منتج' }}</b><br>

                                    @if($review->is_verified_purchase)
                                        <span class="badge ok">✓ شراء موثّق</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="stars">
                                        {{ str_repeat('★', (int) $review->rating) }}
                                        {{ str_repeat('☆', 5 - (int) $review->rating) }}
                                    </div>
                                </td>

                                <td class="review-text">
                                    @if($review->title)
                                        <b>{{ $review->title }}</b>
                                    @endif

                                    @if($review->review)
                                        <div>{{ $review->review }}</div>
                                    @endif

                                    <small>
                                        {{ optional($review->created_at)->format('Y-m-d H:i') }}
                                    </small>

                                    @if($review->admin_note)
                                        <small style="color:#b91c1c">
                                            ملاحظة الإدارة: {{ $review->admin_note }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    @if(!$review->is_visible)
                                        <span class="badge hidden">مخفي</span>
                                    @elseif($review->is_approved)
                                        <span class="badge ok">معتمد</span>
                                    @else
                                        <span class="badge warn">قيد المراجعة</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="actions">

                                        @unless($review->is_approved && $review->is_visible)
                                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn green" type="submit">✓ اعتماد</button>
                                            </form>
                                        @endunless

                                        @if($review->is_visible)
                                            <form method="POST" action="{{ route('admin.reviews.hide', $review) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn orange" type="submit">إخفاء</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.reviews.show', $review) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn green" type="submit">إظهار</button>
                                            </form>
                                        @endif

                                        <form class="reject-form" method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                name="admin_note"
                                                placeholder="سبب الرفض"
                                                maxlength="1000"
                                            >

                                            <button class="btn red" type="submit">
                                                رفض
                                            </button>
                                        </form>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.reviews.destroy', $review) }}"
                                            onsubmit="return confirm('حذف التقييم نهائيًا؟')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn red" type="submit">
                                                🗑 حذف
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty">
                                        لا توجد تقييمات في هذا القسم.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reviews->hasPages())
                    <div class="pager">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </section>

        </div>
    </main>
</div>

<script>
    let theme = localStorage.adminTheme || 'light';

    function applyTheme(){
        document.body.classList.toggle('dark', theme === 'dark');
        document.getElementById('theme').textContent =
            theme === 'dark' ? '☀' : '☾';
    }

    document.getElementById('theme').onclick = () => {
        theme = theme === 'dark' ? 'light' : 'dark';
        localStorage.adminTheme = theme;
        applyTheme();
    };

    applyTheme();
</script>
</body>
</html>
