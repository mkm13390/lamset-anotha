<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>المنتجات | لمسة أنوثة</title>
    <link rel="icon" href="{{ asset('images/logo.jpeg') }}">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}html{scroll-behavior:smooth}
        :root{--red:#e21b23;--red2:#b91017;--bg:#f7f7f7;--card:#fff;--text:#171717;--muted:#727272;--border:#e5e5e5;--shadow:0 12px 35px rgba(0,0,0,.08)}
        body.dark{--bg:#101010;--card:#1a1a1a;--text:#f7f7f7;--muted:#aaa;--border:#303030;--shadow:0 14px 40px rgba(0,0,0,.35)}
        body{font-family:Arial,"Segoe UI",sans-serif;background:var(--bg);color:var(--text);transition:.25s}.container{width:min(1180px,92%);margin:auto}a{color:inherit;text-decoration:none}button,input,select{font:inherit}
        header{position:sticky;top:0;z-index:100;background:var(--card);border-bottom:1px solid var(--border)}.header-row{min-height:110px;display:flex;align-items:center;justify-content:space-between;gap:20px}.logo{width:235px;height:95px;display:flex;align-items:center}.logo img{width:100%;height:100%;object-fit:contain}.nav{display:flex;gap:22px;font-size:14px}.nav a:hover,.nav a.active{color:var(--red)}.tools{display:flex;gap:8px;align-items:center}.tool{height:40px;padding:0 12px;border:1px solid var(--border);border-radius:9px;background:var(--card);color:var(--text);cursor:pointer}.cart{position:relative}.count{position:absolute;top:-8px;left:-7px;min-width:19px;height:19px;padding:0 4px;display:grid;place-items:center;border-radius:20px;background:var(--red);color:#fff;font-size:11px}
        .page-head{padding:48px 0 28px;background:linear-gradient(135deg,#080808,#241012);color:#fff}.page-head small{color:#ff8589}.page-head h1{font-size:clamp(34px,5vw,58px);margin:9px 0}.page-head p{color:#d0d0d0;max-width:650px;line-height:1.8}
        .shop{padding:35px 0 70px}.search-row{display:grid;grid-template-columns:1fr auto;gap:12px;margin-bottom:22px}.search-box{position:relative}.search-box input{width:100%;height:50px;padding:0 18px;border:1px solid var(--border);border-radius:12px;background:var(--card);color:var(--text);outline:0}.search-box input:focus{border-color:var(--red)}.sort{height:50px;padding:0 14px;border:1px solid var(--border);border-radius:12px;background:var(--card);color:var(--text)}
        .filters{display:flex;gap:9px;overflow:auto;padding:2px 0 20px}.filter{white-space:nowrap;padding:10px 18px;border:1px solid var(--border);border-radius:30px;background:var(--card);color:var(--text);cursor:pointer}.filter.active,.filter:hover{background:var(--red);border-color:var(--red);color:#fff}.results{display:flex;justify-content:space-between;align-items:center;margin:4px 0 18px;color:var(--muted);font-size:14px}
        .products{display:grid;grid-template-columns:repeat(4,1fr);gap:18px}.product{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;box-shadow:var(--shadow);transition:.25s}.product:hover{transform:translateY(-5px)}.photo{height:285px;position:relative;background:#ececec;overflow:hidden}.photo img{width:100%;height:100%;object-fit:cover;transition:.4s}.product:hover .photo img{transform:scale(1.04)}.placeholder{width:100%;height:100%;display:grid;place-items:center;background:linear-gradient(135deg,#efefef,#ddd);color:#888;font-size:52px}.badge{position:absolute;top:12px;right:12px;background:var(--red);color:#fff;padding:6px 10px;border-radius:20px;font-size:11px}.heart{position:absolute;top:11px;left:11px;width:38px;height:38px;border:0;border-radius:50%;background:rgba(255,255,255,.92);font-size:20px;cursor:pointer}.heart.active{background:var(--red);color:#fff}.info{padding:16px}.category{font-size:12px;color:var(--red);margin-bottom:7px}.name{font-size:17px;min-height:42px;line-height:1.5}.price-row{display:flex;align-items:center;gap:8px;margin:12px 0}.price{font-size:18px;font-weight:bold}.compare{text-decoration:line-through;color:var(--muted);font-size:13px}.add{width:100%;height:42px;border:0;border-radius:9px;background:#171717;color:#fff;cursor:pointer}.dark .add{background:#f4f4f4;color:#111}.add:hover{background:var(--red);color:#fff}
        .empty{grid-column:1/-1;text-align:center;background:var(--card);border:1px dashed var(--border);border-radius:16px;padding:55px 20px}.empty span{display:block;font-size:50px;margin-bottom:14px}.empty h2{margin-bottom:10px}.empty p{color:var(--muted);line-height:1.7}
        footer{background:#080808;color:#eee;padding:26px 0}.footer-row{display:flex;justify-content:space-between;align-items:center;gap:20px}.social{display:flex;gap:10px}.social a{border:1px solid #444;padding:9px 13px;border-radius:8px}.social a:hover{border-color:var(--red);color:var(--red)}.toast{position:fixed;bottom:25px;left:50%;z-index:300;transform:translate(-50%,120px);background:#151515;color:#fff;padding:13px 22px;border-radius:10px;opacity:0;transition:.3s}.toast.show{transform:translate(-50%,0);opacity:1}
        @media(max-width:980px){.nav{display:none}.products{grid-template-columns:repeat(3,1fr)}}
        @media(max-width:720px){.header-row{flex-wrap:wrap;padding:8px 0}.logo{margin:auto;width:215px;height:86px}.tools{width:100%;justify-content:center;padding-bottom:10px}.products{grid-template-columns:repeat(2,1fr);gap:11px}.photo{height:230px}.search-row{grid-template-columns:1fr}.footer-row{flex-direction:column;text-align:center}}
        @media(max-width:420px){.products{grid-template-columns:1fr}.photo{height:340px}.page-head{text-align:center}}
    </style>
</head>
<body>
<header><div class="container header-row">
    <a class="logo" href="{{ url('/') }}"><img src="{{ asset('images/logo.jpeg') }}" alt="لمسة أنوثة"></a>
    <nav class="nav"><a href="{{ url('/') }}" data-i18n="home">الرئيسية</a><a class="active" href="{{ url('/products') }}" data-i18n="products">المنتجات</a><a href="{{ url('/#categories') }}" data-i18n="categories">الأقسام</a><a href="{{ url('/#footer') }}" data-i18n="contact">تواصل معنا</a></nav>
    <div class="tools"><button class="tool" id="theme">☾</button><button class="tool" id="language">English</button><button class="tool cart">🛒 <span class="count" id="cartCount">0</span></button></div>
</div></header>

<section class="page-head"><div class="container"><small data-i18n="eyebrow">أناقة مختارة لكِ</small><h1 data-i18n="title">جميع المنتجات</h1><p data-i18n="subtitle">اكتشفي تشكيلتنا من الحقائب والأحذية والإكسسوارات المختارة بعناية لتكمّل إطلالتك.</p></div></section>

<main class="container shop">
    <div class="search-row"><div class="search-box"><input id="search" type="search" data-placeholder-ar="ابحثي باسم المنتج..." data-placeholder-en="Search products..." placeholder="ابحثي باسم المنتج..."></div><select class="sort" id="sort"><option value="default" data-i18n="sortDefault">الترتيب الافتراضي</option><option value="low" data-i18n="sortLow">السعر: الأقل أولاً</option><option value="high" data-i18n="sortHigh">السعر: الأعلى أولاً</option></select></div>
    <div class="filters"><button class="filter active" data-category="all" data-i18n="all">الكل</button><button class="filter" data-category="bags" data-i18n="bags">الحقائب</button><button class="filter" data-category="shoes" data-i18n="shoes">الأحذية</button><button class="filter" data-category="accessories" data-i18n="accessories">الإكسسوارات</button></div>
    <div class="results"><span id="resultText"></span><span data-i18n="currencyNote">الأسعار بالريال العُماني</span></div>
    <section class="products" id="productGrid">
        @forelse(($products ?? []) as $product)
            @php
                $image = optional($product->images->first())->image ?? null;
                $categorySlug = strtolower(optional($product->category)->slug ?? 'other');
                $categoryName = optional($product->category)->name_ar ?? 'منتجات';
            @endphp
            <article class="product" data-name="{{ mb_strtolower($product->name_ar.' '.$product->name_en) }}" data-category="{{ $categorySlug }}" data-price="{{ (float)$product->price }}">
                <div class="photo">
                    @if($image)<img src="{{ asset('storage/'.$image) }}" alt="{{ $product->name_ar }}">@else<div class="placeholder">🛍️</div>@endif
                    @if($product->is_new)<span class="badge" data-i18n="new">جديد</span>@endif
                    <button class="heart" type="button" aria-label="المفضلة">♡</button>
                </div>
                <div class="info"><div class="category">{{ $categoryName }}</div><h2 class="name">{{ $product->name_ar }}</h2><div class="price-row"><span class="price">{{ number_format($product->price,3) }} ر.ع</span>@if($product->compare_price)<span class="compare">{{ number_format($product->compare_price,3) }} ر.ع</span>@endif</div><button class="add" type="button" data-i18n="add">إضافة إلى السلة</button></div>
            </article>
        @empty
            <div class="empty"><span>🛍️</span><h2 data-i18n="emptyTitle">لا توجد منتجات حاليًا</h2><p data-i18n="emptyText">عند إضافة المنتجات من لوحة التحكم أو ملف Excel ستظهر هنا تلقائيًا.</p></div>
        @endforelse
    </section>
</main>

<footer><div class="container footer-row"><p data-i18n="rights">جميع الحقوق محفوظة © 2026 لمسة أنوثة</p><div class="social"><a href="https://wa.me/96895426555" target="_blank">WhatsApp</a><a href="https://www.instagram.com/mkm13390" target="_blank">Instagram</a></div></div></footer>
<div class="toast" id="toast"></div>
<script>
const q=s=>document.querySelector(s),qa=s=>[...document.querySelectorAll(s)];let lang=localStorage.siteLanguage||'ar',theme=localStorage.siteTheme||'light',active='all';
const tr={ar:{home:'الرئيسية',products:'المنتجات',categories:'الأقسام',contact:'تواصل معنا',eyebrow:'أناقة مختارة لكِ',title:'جميع المنتجات',subtitle:'اكتشفي تشكيلتنا من الحقائب والأحذية والإكسسوارات المختارة بعناية لتكمّل إطلالتك.',sortDefault:'الترتيب الافتراضي',sortLow:'السعر: الأقل أولاً',sortHigh:'السعر: الأعلى أولاً',all:'الكل',bags:'الحقائب',shoes:'الأحذية',accessories:'الإكسسوارات',currencyNote:'الأسعار بالريال العُماني',new:'جديد',add:'إضافة إلى السلة',emptyTitle:'لا توجد منتجات حاليًا',emptyText:'عند إضافة المنتجات من لوحة التحكم أو ملف Excel ستظهر هنا تلقائيًا.',rights:'جميع الحقوق محفوظة © 2026 لمسة أنوثة',added:'تمت إضافة المنتج إلى السلة',result:n=>'عدد المنتجات: '+n},en:{home:'Home',products:'Products',categories:'Categories',contact:'Contact',eyebrow:'Elegance selected for you',title:'All Products',subtitle:'Discover our carefully selected collection of bags, shoes and accessories.',sortDefault:'Default sorting',sortLow:'Price: low to high',sortHigh:'Price: high to low',all:'All',bags:'Bags',shoes:'Shoes',accessories:'Accessories',currencyNote:'Prices in Omani Rial',new:'New',add:'Add to cart',emptyTitle:'No products yet',emptyText:'Products added from the dashboard or Excel file will appear here automatically.',rights:'All rights reserved © 2026 Lamset Anotha',added:'Product added to cart',result:n=>'Products: '+n}};
function applyTheme(){document.body.classList.toggle('dark',theme==='dark');q('#theme').textContent=theme==='dark'?'☀':'☾'}
function applyLang(){document.documentElement.lang=lang;document.documentElement.dir=lang==='ar'?'rtl':'ltr';q('#language').textContent=lang==='ar'?'English':'العربية';qa('[data-i18n]').forEach(e=>{let v=tr[lang][e.dataset.i18n];if(v)e.textContent=v});q('#search').placeholder=q('#search').dataset['placeholder'+(lang==='ar'?'Ar':'En')];filterProducts()}
function filterProducts(){let term=q('#search').value.trim().toLowerCase(),cards=qa('.product'),shown=0;cards.forEach(c=>{let categoryOk=active==='all'||c.dataset.category.includes(active),searchOk=c.dataset.name.includes(term),show=categoryOk&&searchOk;c.style.display=show?'':'none';if(show)shown++});q('#resultText').textContent=tr[lang].result(shown)}
q('#theme').onclick=()=>{theme=theme==='dark'?'light':'dark';localStorage.siteTheme=theme;applyTheme()};q('#language').onclick=()=>{lang=lang==='ar'?'en':'ar';localStorage.siteLanguage=lang;applyLang()};q('#search').oninput=filterProducts;qa('.filter').forEach(b=>b.onclick=()=>{qa('.filter').forEach(x=>x.classList.remove('active'));b.classList.add('active');active=b.dataset.category;filterProducts()});
q('#sort').onchange=()=>{let grid=q('#productGrid'),cards=qa('.product');if(q('#sort').value!=='default')cards.sort((a,b)=>q('#sort').value==='low'?a.dataset.price-b.dataset.price:b.dataset.price-a.dataset.price).forEach(c=>grid.appendChild(c))};
let cart=Number(localStorage.cartCount||0);q('#cartCount').textContent=cart;qa('.add').forEach(b=>b.onclick=()=>{cart++;localStorage.cartCount=cart;q('#cartCount').textContent=cart;let t=q('#toast');t.textContent=tr[lang].added;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),1800)});qa('.heart').forEach(b=>b.onclick=()=>{b.classList.toggle('active');b.textContent=b.classList.contains('active')?'♥':'♡'});applyTheme();applyLang();
</script>
</body>
</html>
