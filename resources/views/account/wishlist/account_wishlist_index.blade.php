<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>المفضلة | لمسة أنوثة</title>
    <link rel="icon" href="{{ asset('images/logo.jpeg') }}">

    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --red:#e21b23;--bg:#f5f6f8;--card:#fff;--text:#171717;
            --muted:#777;--border:#e4e6ea;--shadow:0 8px 28px rgba(0,0,0,.07)
        }
        body.dark{
            --bg:#101010;--card:#191919;--text:#f5f5f5;
            --muted:#aaa;--border:#303030;--shadow:0 12px 35px rgba(0,0,0,.35)
        }
        body{font-family:Arial,"Segoe UI",sans-serif;background:var(--bg);color:var(--text)}
        a{text-decoration:none;color:inherit}
        button{font:inherit}
        .top{
            height:76px;background:var(--card);border-bottom:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between;padding:0 4%
        }
        .brand img{width:145px;height:58px;object-fit:contain}
        .tools{display:flex;gap:8px}
        .tool{
            height:40px;padding:0 12px;border:1px solid var(--border);border-radius:8px;
            background:var(--card);color:var(--text);cursor:pointer
        }
        .content{width:min(1180px,92%);margin:26px auto 60px}
        .back{display:inline-flex;gap:7px;align-items:center;color:var(--muted);font-size:13px;margin-bottom:18px}
        .headline{display:flex;justify-content:space-between;gap:15px;align-items:center;margin-bottom:18px}
        .headline h1{font-size:24px}
        .headline p{color:var(--muted);font-size:13px;margin-top:6px}
        .notice{padding:12px 14px;border-radius:9px;margin-bottom:15px;font-size:13px;background:#d1fae5;color:#087443;border:1px solid #a7f3d0}
        .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:15px}
        .product{
            background:var(--card);border:1px solid var(--border);border-radius:14px;
            overflow:hidden;box-shadow:var(--shadow);display:flex;flex-direction:column
        }
        .image{
            aspect-ratio:1/1;background:var(--bg);display:flex;align-items:center;justify-content:center;
            overflow:hidden
        }
        .image img{width:100%;height:100%;object-fit:cover}
        .placeholder{font-size:46px;color:var(--muted)}
        .body{padding:14px;display:flex;flex-direction:column;gap:8px;flex:1}
        .category{font-size:10px;color:var(--muted)}
        .name{font-size:15px;font-weight:bold;line-height:1.6}
        .price{color:var(--red);font-weight:bold;font-size:16px;margin-top:auto}
        .stock{font-size:11px;color:var(--muted)}
        .actions{display:grid;grid-template-columns:1fr auto;gap:8px;margin-top:10px}
        .btn{
            min-height:40px;border-radius:8px;border:1px solid var(--border);
            background:var(--card);color:var(--text);cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0 12px
        }
        .btn.primary{background:var(--red);color:#fff;border-color:var(--red)}
        .btn.remove{width:42px;color:#b91c1c}
        .empty{
            grid-column:1/-1;text-align:center;background:var(--card);border:1px solid var(--border);
            border-radius:14px;padding:60px 20px;color:var(--muted)
        }
        .empty span{display:block;font-size:50px;margin-bottom:12px}
        .pager{margin-top:20px}
        .pager nav{width:100%}
        .pager svg{width:18px}
        @media(max-width:1000px){.grid{grid-template-columns:repeat(3,1fr)}}
        @media(max-width:760px){.grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:500px){.grid{grid-template-columns:1fr}.headline{flex-direction:column;align-items:flex-start}}
    </style>
</head>
<body>

<header class="top">
    <a class="brand" href="{{ url('/') }}">
        <img src="{{ asset('images/logo.jpeg') }}" alt="لمسة أنوثة">
    </a>

    <div class="tools">
        <a class="tool" href="{{ route('account.index') }}">حسابي</a>
        <button class="tool" id="theme" type="button">☾</button>
    </div>
</header>

<main class="content">

    <a class="back" href="{{ route('account.index') }}">← العودة إلى حسابي</a>

    <section class="headline">
        <div>
            <h1>المفضلة</h1>
            <p>المنتجات التي حفظتها للرجوع إليها لاحقًا.</p>
        </div>
    </section>

    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    <section class="grid">

        @forelse($wishlistItems as $item)

            @php
                $product = $item->product;
                $firstImage = $product?->images?->first();

                $imagePath = $firstImage->path
                    ?? $firstImage->image
                    ?? $firstImage->url
                    ?? null;

                $variant = $product?->variants?->first();

                $price = $variant->sale_price
                    ?? $variant->price
                    ?? $product->sale_price
                    ?? $product->price
                    ?? 0;

                $stock = $product?->variants?->sum('stock')
                    ?? 0;
            @endphp

            @if($product)

                <article class="product">

                    <a class="image" href="{{ route('products.show', $product) }}">

                        @if($imagePath)
                            <img
                                src="{{ \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])
                                    ? $imagePath
                                    : asset('storage/' . ltrim($imagePath, '/')) }}"
                                alt="{{ $product->name }}"
                            >
                        @else
                            <span class="placeholder">👜</span>
                        @endif

                    </a>

                    <div class="body">

                        <div class="category">
                            {{ $product->category->name ?? 'منتج' }}
                        </div>

                        <a class="name" href="{{ route('products.show', $product) }}">
                            {{ $product->name }}
                        </a>

                        <div class="stock">
                            @if($stock > 0)
                                متوفر في المخزون
                            @else
                                غير متوفر حاليًا
                            @endif
                        </div>

                        <div class="price">
                            {{ number_format((float) $price, 3) }} ر.ع
                        </div>

                        <div class="actions">

                            <a class="btn primary" href="{{ route('products.show', $product) }}">
                                عرض المنتج
                            </a>

                            <form method="POST" action="{{ route('account.wishlist.destroy', $item) }}">
                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn remove"
                                    type="submit"
                                    title="إزالة من المفضلة"
                                    onclick="return confirm('هل تريد إزالة هذا المنتج من المفضلة؟')"
                                >
                                    ♥
                                </button>
                            </form>

                        </div>

                    </div>

                </article>

            @endif

        @empty

            <div class="empty">
                <span>♡</span>
                <h3>المفضلة فارغة</h3>
                <p style="margin-top:7px">احفظ المنتجات التي تعجبك لتجدها هنا بسهولة.</p>

                <a
                    class="btn primary"
                    href="{{ route('products.index') }}"
                    style="display:inline-flex;margin-top:16px"
                >
                    تصفح المنتجات
                </a>
            </div>

        @endforelse

    </section>

    @if($wishlistItems->hasPages())
        <div class="pager">
            {{ $wishlistItems->links() }}
        </div>
    @endif

</main>

<script>
    let theme = localStorage.siteTheme || 'light';

    function applyTheme(){
        document.body.classList.toggle('dark', theme === 'dark');
        document.getElementById('theme').textContent = theme === 'dark' ? '☀' : '☾';
    }

    document.getElementById('theme').onclick = () => {
        theme = theme === 'dark' ? 'light' : 'dark';
        localStorage.siteTheme = theme;
        applyTheme();
    };

    applyTheme();
</script>

</body>
</html>
