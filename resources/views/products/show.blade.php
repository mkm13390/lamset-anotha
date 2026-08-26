<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $product->name_ar ?? 'تفاصيل المنتج' }} | لمسة أنوثة</title>

    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($product->description_ar ?? ''), 155) }}">

    <link rel="icon" href="{{ asset('images/logo.jpeg') }}">

    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{
            --red:#e21b23;
            --red-dark:#b91018;
            --bg:#f4f5f7;
            --card:#ffffff;
            --text:#171717;
            --muted:#777;
            --border:#e2e4e7;
            --green:#14804a;
            --orange:#e88900;
            --shadow:0 10px 32px rgba(0,0,0,.08);
        }

        body.dark{
            --bg:#101010;
            --card:#191919;
            --text:#f5f5f5;
            --muted:#aaa;
            --border:#303030;
            --shadow:0 14px 40px rgba(0,0,0,.35);
        }

        body{
            font-family:Arial,"Segoe UI",sans-serif;
            background:var(--bg);
            color:var(--text);
            min-height:100vh;
        }

        a{color:inherit;text-decoration:none}
        button,select,input{font:inherit}

        .topbar{
            position:sticky;
            top:0;
            z-index:50;
            background:var(--card);
            border-bottom:1px solid var(--border);
        }

        .nav{
            width:min(1180px,94%);
            margin:auto;
            min-height:76px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:18px;
        }

        .brand img{
            width:122px;
            height:58px;
            object-fit:contain;
        }

        .nav-links{
            display:flex;
            align-items:center;
            gap:16px;
            font-size:14px;
        }

        .nav-tools{display:flex;gap:8px}

        .tool{
            height:40px;
            padding:0 12px;
            border:1px solid var(--border);
            border-radius:9px;
            background:var(--card);
            color:var(--text);
            cursor:pointer;
        }

        .container{
            width:min(1180px,94%);
            margin:24px auto 60px;
        }

        .breadcrumb{
            display:flex;
            gap:8px;
            align-items:center;
            color:var(--muted);
            font-size:13px;
            margin-bottom:18px;
            flex-wrap:wrap;
        }

        .product-layout{
            display:grid;
            grid-template-columns:1.05fr .95fr;
            gap:26px;
            align-items:start;
        }

        .gallery,.details{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:18px;
            box-shadow:var(--shadow);
        }

        .gallery{padding:18px}

        .main-image{
            width:100%;
            aspect-ratio:1/1;
            background:var(--bg);
            border-radius:14px;
            overflow:hidden;
            display:grid;
            place-items:center;
        }

        .main-image img{width:100%;height:100%;object-fit:cover}
        .placeholder{color:var(--muted);font-size:64px}

        .thumbs{
            margin-top:12px;
            display:grid;
            grid-template-columns:repeat(5,1fr);
            gap:9px;
        }

        .thumb{
            border:1px solid var(--border);
            background:var(--card);
            border-radius:10px;
            overflow:hidden;
            aspect-ratio:1/1;
            cursor:pointer;
        }

        .thumb.active{border:2px solid var(--red)}
        .thumb img{width:100%;height:100%;object-fit:cover}

        .details{padding:24px}
        .category{color:var(--red);font-size:13px;margin-bottom:8px}
        .title{font-size:30px;line-height:1.35;margin-bottom:10px}
        .sku{color:var(--muted);font-size:12px;margin-bottom:18px}

        .price-row{
            display:flex;
            align-items:end;
            gap:11px;
            flex-wrap:wrap;
            margin-bottom:18px;
        }

        .price{font-size:30px;font-weight:700;color:var(--red)}
        .compare{color:var(--muted);text-decoration:line-through;font-size:16px}
        .badge{padding:6px 10px;border-radius:20px;font-size:11px;background:rgba(226,27,35,.1);color:var(--red)}

        .description{
            line-height:1.9;
            color:var(--muted);
            font-size:14px;
            padding:16px 0;
            border-top:1px solid var(--border);
            border-bottom:1px solid var(--border);
        }

        .option-block{margin-top:18px}

        .option-title{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:10px;
            font-size:13px;
            font-weight:700;
        }

        .choices{display:flex;gap:8px;flex-wrap:wrap}

        .choice{
            min-width:46px;
            height:42px;
            padding:0 12px;
            border:1px solid var(--border);
            border-radius:9px;
            background:var(--card);
            color:var(--text);
            cursor:pointer;
        }

        .choice.active{border-color:var(--red);background:var(--red);color:#fff}
        .choice.disabled{opacity:.35;cursor:not-allowed;text-decoration:line-through}
        .color-choice{display:flex;align-items:center;gap:7px}
        .dot{width:18px;height:18px;border-radius:50%;border:1px solid var(--border);display:inline-block}

        .stock-box{
            margin-top:18px;
            padding:13px 15px;
            border-radius:10px;
            background:var(--bg);
            font-size:13px;
        }

        .stock-ok{color:var(--green)}
        .stock-low{color:var(--orange)}
        .stock-out{color:var(--red)}

        .cart-form{
            display:grid;
            grid-template-columns:92px 1fr auto;
            gap:10px;
            margin-top:18px;
        }

        .qty-input{
            height:48px;
            border:1px solid var(--border);
            border-radius:10px;
            background:var(--card);
            color:var(--text);
            padding:0 10px;
            text-align:center;
        }

        .add-cart{
            height:48px;
            border:0;
            border-radius:10px;
            background:var(--red);
            color:#fff;
            font-weight:700;
            cursor:pointer;
        }

        .add-cart:hover{background:var(--red-dark)}
        .add-cart:disabled{opacity:.45;cursor:not-allowed}

        .wish{
            width:50px;
            height:48px;
            border:1px solid var(--border);
            border-radius:10px;
            background:var(--card);
            color:var(--text);
            font-size:20px;
            cursor:pointer;
        }

        .message{
            margin-top:12px;
            padding:12px;
            border-radius:9px;
            font-size:13px;
        }

        .message.error{background:#fee2e2;color:#b91c1c}
        .message.success{background:#d1fae5;color:#087443}

        .info-grid{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:10px;
            margin-top:18px;
        }

        .info{
            padding:13px;
            background:var(--bg);
            border-radius:10px;
            text-align:center;
            font-size:12px;
            color:var(--muted);
        }

        .info strong{display:block;color:var(--text);margin-bottom:5px}

        @media(max-width:900px){
            .product-layout{grid-template-columns:1fr}
            .nav-links{display:none}
        }

        @media(max-width:560px){
            .container{width:92%}
            .details,.gallery{padding:15px}
            .title{font-size:23px}
            .price{font-size:25px}
            .thumbs{grid-template-columns:repeat(4,1fr)}
            .info-grid{grid-template-columns:1fr}
            .cart-form{grid-template-columns:80px 1fr}
            .wish{grid-column:1/-1;width:100%}
        }
    </style>
</head>

<body>

<header class="topbar">
    <div class="nav">

        <a class="brand" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.jpeg') }}" alt="لمسة أنوثة">
        </a>

        <nav class="nav-links">
            <a href="{{ url('/') }}">الرئيسية</a>
            <a href="{{ route('products.index') }}">المنتجات</a>
            <a href="{{ route('account.index') }}">حسابي</a>
            <a href="{{ route('cart.index') }}">السلة</a>
        </nav>

        <div class="nav-tools">
            <button class="tool" id="theme" type="button">☾</button>
            <button class="tool" id="language" type="button">English</button>
        </div>

    </div>
</header>

<main class="container">

    @if(session('success'))
        <div class="message success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="message error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="breadcrumb">
        <a href="{{ url('/') }}">الرئيسية</a>
        <span>/</span>
        <a href="{{ route('products.index') }}">المنتجات</a>

        @if($product->category)
            <span>/</span>
            <span>{{ $product->category->name_ar ?? '' }}</span>
        @endif

        <span>/</span>
        <span>{{ $product->name_ar ?? 'المنتج' }}</span>
    </div>

    <section class="product-layout">

        <div class="gallery">

            @php
                $mainImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();

                $imagePath = null;

                if ($mainImage && !empty($mainImage->image)) {
                    $imagePath = str_starts_with($mainImage->image, 'http')
                        ? $mainImage->image
                        : asset('storage/' . ltrim($mainImage->image, '/'));
                }
            @endphp

            <div class="main-image">
                @if($imagePath)
                    <img id="mainImage" src="{{ $imagePath }}" alt="{{ $product->name_ar }}">
                @else
                    <div class="placeholder">🛍️</div>
                @endif
            </div>

            @if($product->images->count() > 1)
                <div class="thumbs">
                    @foreach($product->images as $index => $image)
                        @php
                            $thumbPath = str_starts_with($image->image ?? '', 'http')
                                ? $image->image
                                : asset('storage/' . ltrim($image->image ?? '', '/'));
                        @endphp

                        @if(!empty($image->image))
                            <button
                                class="thumb {{ $index === 0 ? 'active' : '' }}"
                                type="button"
                                data-image="{{ $thumbPath }}"
                            >
                                <img src="{{ $thumbPath }}" alt="{{ $product->name_ar }}">
                            </button>
                        @endif
                    @endforeach
                </div>
            @endif

        </div>

        <div class="details">

            @if($product->category)
                <div class="category">{{ $product->category->name_ar ?? '' }}</div>
            @endif

            <h1 class="title">{{ $product->name_ar ?? '' }}</h1>

            <div class="sku">
                SKU: {{ $product->sku ?? '-' }}
            </div>

            <div class="price-row">

                <div class="price">
                    {{ number_format((float)($product->price ?? 0), 3) }} ر.ع
                </div>

                @if(!empty($product->compare_price) && $product->compare_price > $product->price)
                    <div class="compare">
                        {{ number_format((float)$product->compare_price, 3) }} ر.ع
                    </div>

                    <span class="badge">خصم</span>
                @endif

                @if(!empty($product->is_new))
                    <span class="badge">جديد</span>
                @endif

                @if(!empty($product->is_featured))
                    <span class="badge">مميز</span>
                @endif

            </div>

            <div class="description">
                {!! nl2br(e($product->description_ar ?? '')) !!}
            </div>

            @php
                $activeVariants = $product->variants
                    ->where('is_active', true)
                    ->values();

                $colors = $activeVariants
                    ->filter(fn($v) => !empty($v->color_name_ar))
                    ->unique('color_name_ar')
                    ->values();

                $sizes = $activeVariants
                    ->filter(fn($v) => !empty($v->size))
                    ->pluck('size')
                    ->unique()
                    ->values();
            @endphp

            @if($colors->count())
                <div class="option-block">
                    <div class="option-title">
                        <span>اللون</span>
                        <small id="selectedColorLabel"></small>
                    </div>

                    <div class="choices" id="colorChoices">
                        @foreach($colors as $color)
                            <button
                                type="button"
                                class="choice color-choice"
                                data-color="{{ $color->color_name_ar }}"
                            >
                                @if(!empty($color->color_code))
                                    <span class="dot" style="background:{{ $color->color_code }}"></span>
                                @endif

                                <span>{{ $color->color_name_ar }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($sizes->count())
                <div class="option-block">
                    <div class="option-title">
                        <span>المقاس</span>
                        <small id="selectedSizeLabel"></small>
                    </div>

                    <div class="choices" id="sizeChoices">
                        @foreach($sizes as $size)
                            <button
                                type="button"
                                class="choice"
                                data-size="{{ $size }}"
                            >
                                {{ $size }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="stock-box" id="stockBox">
                اختاري اللون والمقاس لمعرفة التوفر.
            </div>

            <form
                class="cart-form"
                method="POST"
                action="{{ route('cart.add') }}"
                id="cartForm"
            >
                @csrf

                <input
                    type="hidden"
                    name="product_variant_id"
                    id="selectedVariantInput"
                    value=""
                >

                <input
                    class="qty-input"
                    type="number"
                    name="quantity"
                    id="quantityInput"
                    value="1"
                    min="1"
                    disabled
                >

                <button
                    class="add-cart"
                    id="addToCart"
                    type="submit"
                    disabled
                >
                    إضافة إلى السلة
                </button>

                <button
                    class="wish"
                    id="wishlistButton"
                    type="button"
                    title="المفضلة"
                >
                    ♡
                </button>

            </form>

            <div class="info-grid">

                <div class="info">
                    <strong>الدفع</strong>
                    خيارات دفع متعددة
                </div>

                <div class="info">
                    <strong>الشحن</strong>
                    حسب المحافظة والمنطقة
                </div>

                <div class="info">
                    <strong>الاستبدال</strong>
                    حسب سياسة المتجر
                </div>

            </div>

        </div>

    </section>

</main>

<script>
    const variants = @json(
        $activeVariants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'color' => $variant->color_name_ar,
                'size' => $variant->size,
                'stock' => (int)($variant->stock_quantity ?? 0),
                'price' => $variant->price,
                'sku' => $variant->sku,
            ];
        })->values()
    );

    let selectedColor = null;
    let selectedSize = null;
    let selectedVariant = null;

    const colorButtons = [...document.querySelectorAll('[data-color]')];
    const sizeButtons = [...document.querySelectorAll('[data-size]')];

    const stockBox = document.getElementById('stockBox');
    const addToCart = document.getElementById('addToCart');
    const selectedVariantInput = document.getElementById('selectedVariantInput');
    const quantityInput = document.getElementById('quantityInput');

    function findVariant() {
        selectedVariant = variants.find(v => {
            const colorMatches = selectedColor === null || v.color === selectedColor;
            const sizeMatches = selectedSize === null || String(v.size) === String(selectedSize);

            return colorMatches && sizeMatches;
        }) || null;

        updateAvailability();
    }

    function updateAvailability() {
        selectedVariantInput.value = '';

        if (!selectedVariant) {
            stockBox.className = 'stock-box';
            stockBox.textContent = 'اختاري الخيارات المتاحة لمعرفة التوفر.';
            addToCart.disabled = true;
            quantityInput.disabled = true;
            quantityInput.value = 1;
            return;
        }

        const stock = Number(selectedVariant.stock || 0);

        selectedVariantInput.value = selectedVariant.id;
        quantityInput.max = Math.max(stock, 1);

        if (stock <= 0) {
            stockBox.className = 'stock-box stock-out';
            stockBox.textContent = 'هذا الخيار نافد من المخزون.';
            addToCart.disabled = true;
            quantityInput.disabled = true;
            quantityInput.value = 1;
        } else if (stock <= 5) {
            stockBox.className = 'stock-box stock-low';
            stockBox.textContent = `متبقي ${stock} فقط.`;
            addToCart.disabled = false;
            quantityInput.disabled = false;
        } else {
            stockBox.className = 'stock-box stock-ok';
            stockBox.textContent = 'متوفر في المخزون.';
            addToCart.disabled = false;
            quantityInput.disabled = false;
        }
    }

    colorButtons.forEach(button => {
        button.addEventListener('click', () => {
            colorButtons.forEach(b => b.classList.remove('active'));
            button.classList.add('active');

            selectedColor = button.dataset.color;
            document.getElementById('selectedColorLabel').textContent = selectedColor;

            refreshSizeAvailability();
            findVariant();
        });
    });

    sizeButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (button.classList.contains('disabled')) return;

            sizeButtons.forEach(b => b.classList.remove('active'));
            button.classList.add('active');

            selectedSize = button.dataset.size;
            document.getElementById('selectedSizeLabel').textContent = selectedSize;

            findVariant();
        });
    });

    function refreshSizeAvailability() {
        sizeButtons.forEach(button => {
            const size = button.dataset.size;

            const matching = variants.some(v => {
                const colorMatches = selectedColor === null || v.color === selectedColor;

                return colorMatches
                    && String(v.size) === String(size)
                    && Number(v.stock || 0) > 0;
            });

            button.classList.toggle('disabled', !matching);

            if (!matching && button.classList.contains('active')) {
                button.classList.remove('active');
                selectedSize = null;

                const label = document.getElementById('selectedSizeLabel');
                if (label) label.textContent = '';
            }
        });
    }

    document.querySelectorAll('.thumb').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.thumb').forEach(b => b.classList.remove('active'));
            button.classList.add('active');

            const image = document.getElementById('mainImage');

            if (image) {
                image.src = button.dataset.image;
            }
        });
    });

    document.getElementById('wishlistButton').addEventListener('click', function () {
        this.textContent = this.textContent === '♡' ? '♥' : '♡';
    });

    let theme = localStorage.siteTheme || 'light';

    function applyTheme() {
        document.body.classList.toggle('dark', theme === 'dark');
        document.getElementById('theme').textContent = theme === 'dark' ? '☀' : '☾';
    }

    document.getElementById('theme').addEventListener('click', () => {
        theme = theme === 'dark' ? 'light' : 'dark';
        localStorage.siteTheme = theme;
        applyTheme();
    });

    document.getElementById('language').addEventListener('click', () => {
        alert('سيتم ربط النصوص الإنجليزية الكاملة في مرحلة توحيد الترجمة.');
    });

    applyTheme();

    if (colorButtons.length === 1) {
        colorButtons[0].click();
    }

    if (sizeButtons.length === 1 && !sizeButtons[0].classList.contains('disabled')) {
        sizeButtons[0].click();
    }

    if (colorButtons.length === 0 && sizeButtons.length === 0 && variants.length === 1) {
        selectedVariant = variants[0];
        updateAvailability();
    }
</script>

</body>
</html>
