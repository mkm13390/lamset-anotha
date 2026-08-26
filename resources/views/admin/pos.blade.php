<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>نقطة البيع | لمسة أنوثة</title>
<link rel="icon" href="{{ asset('images/logo.jpeg') }}">
<style>
*{box-sizing:border-box;margin:0;padding:0}:root{--red:#e21b23;--bg:#f3f4f6;--card:#fff;--text:#171717;--muted:#777;--border:#e1e3e6;--shadow:0 8px 28px rgba(0,0,0,.07)}body.dark{--bg:#101010;--card:#191919;--text:#f5f5f5;--muted:#aaa;--border:#303030;--shadow:0 12px 35px rgba(0,0,0,.35)}body{font-family:Arial,"Segoe UI",sans-serif;background:var(--bg);color:var(--text);overflow:hidden}a{color:inherit;text-decoration:none}button,input,select{font:inherit}.top{height:78px;display:flex;align-items:center;justify-content:space-between;gap:18px;padding:0 2%;background:#090909;color:#fff}.brand{display:flex;align-items:center;gap:15px}.brand img{width:145px;height:65px;object-fit:contain}.brand h1{font-size:18px;border-inline-start:1px solid #444;padding-inline-start:15px}.top-actions{display:flex;align-items:center;gap:8px}.tool{height:40px;padding:0 12px;border:1px solid #444;border-radius:8px;background:#171717;color:#fff;cursor:pointer}.cashier{color:#bbb;font-size:12px}.app{height:calc(100vh - 78px);display:grid;grid-template-columns:1.45fr .75fr;gap:1px;background:var(--border)}.catalog,.bill{background:var(--bg);min-width:0;overflow:hidden}.catalog{padding:18px;display:flex;flex-direction:column}.search-row{display:grid;grid-template-columns:1fr auto;gap:10px}.search{height:48px;border:1px solid var(--border);border-radius:10px;background:var(--card);color:var(--text);padding:0 14px;outline:0}.search:focus{border-color:var(--red)}.barcode{height:48px;padding:0 16px;border:0;border-radius:10px;background:#222;color:#fff;cursor:pointer}.categories{display:flex;gap:8px;overflow:auto;padding:14px 0}.category{white-space:nowrap;padding:9px 16px;border:1px solid var(--border);border-radius:25px;background:var(--card);color:var(--text);cursor:pointer}.category.active{background:var(--red);border-color:var(--red);color:#fff}.products{display:grid;grid-template-columns:repeat(4,1fr);gap:11px;overflow:auto;padding:2px 3px 20px}.product{background:var(--card);border:1px solid var(--border);border-radius:12px;overflow:hidden;cursor:pointer;transition:.2s}.product:hover{border-color:var(--red);transform:translateY(-2px)}.product-img{height:125px;background:linear-gradient(145deg,#eee,#ddd);display:grid;place-items:center;font-size:36px;overflow:hidden}.product-img img{width:100%;height:100%;object-fit:cover}.product-info{padding:11px}.product-info h3{font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.product-info small{display:block;color:var(--muted);margin:6px 0}.product-info strong{color:var(--red)}.empty-products{grid-column:1/-1;text-align:center;padding:60px;color:var(--muted);background:var(--card);border-radius:12px}
.bill{background:var(--card);display:flex;flex-direction:column}.bill-head{height:68px;padding:0 18px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border)}.bill-head h2{font-size:18px}.bill-head button{border:0;background:none;color:var(--red);cursor:pointer}.customer{padding:12px 16px;border-bottom:1px solid var(--border)}.customer select{width:100%;height:42px;border:1px solid var(--border);border-radius:8px;background:var(--bg);color:var(--text);padding:0 10px}.cart-items{flex:1;overflow:auto;padding:5px 16px}.cart-empty{text-align:center;padding:65px 15px;color:var(--muted)}.cart-empty span{display:block;font-size:50px;margin-bottom:12px}.cart-item{display:grid;grid-template-columns:1fr auto;gap:10px;padding:13px 0;border-bottom:1px solid var(--border)}.cart-item h3{font-size:13px}.cart-item small{color:var(--muted)}.item-bottom{display:flex;align-items:center;gap:9px;margin-top:8px}.qty{display:flex;border:1px solid var(--border);border-radius:7px;overflow:hidden}.qty button,.qty span{width:29px;height:29px;border:0;display:grid;place-items:center;background:var(--bg);color:var(--text)}.qty button{cursor:pointer}.delete{border:0;background:none;color:var(--red);cursor:pointer}.item-total{font-weight:bold;color:var(--red);white-space:nowrap}.totals{padding:14px 17px;border-top:1px solid var(--border);background:var(--bg)}.line{display:flex;justify-content:space-between;padding:7px 0;color:var(--muted);font-size:13px}.line strong{color:var(--text)}.grand{font-size:19px;border-top:1px solid var(--border);padding-top:12px;margin-top:5px}.grand strong{font-size:24px;color:var(--red)}.discount{display:flex;margin-top:10px}.discount input{min-width:0;flex:1;height:38px;border:1px solid var(--border);border-radius:7px 0 0 7px;background:var(--card);color:var(--text);padding:0 9px}.discount button{border:0;padding:0 11px;background:#222;color:#fff;border-radius:0 7px 7px 0}.payments{display:grid;grid-template-columns:repeat(3,1fr);gap:7px;margin-top:11px}.pay-type{height:43px;border:1px solid var(--border);border-radius:8px;background:var(--card);color:var(--text);cursor:pointer}.pay-type.active{border-color:var(--red);color:var(--red);background:rgba(226,27,35,.05)}.complete{width:100%;height:51px;margin-top:10px;border:0;border-radius:9px;background:var(--red);color:#fff;font-weight:bold;cursor:pointer}.complete:disabled{opacity:.5;cursor:not-allowed}.notice{display:none;padding:9px;margin-top:8px;border-radius:7px;background:rgba(226,27,35,.1);color:var(--red);font-size:12px;text-align:center}.overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:80}.mobile-bill{display:none;position:fixed;bottom:15px;left:50%;transform:translateX(-50%);z-index:60;width:90%;height:52px;border:0;border-radius:12px;background:var(--red);color:#fff;font-weight:bold}
@media(max-width:1100px){.products{grid-template-columns:repeat(3,1fr)}}@media(max-width:800px){body{overflow:auto}.app{height:auto;min-height:calc(100vh - 78px);grid-template-columns:1fr}.catalog{min-height:calc(100vh - 78px)}.bill{position:fixed;z-index:90;inset-block:0;inset-inline-end:0;width:min(420px,94%);transform:translateX(-110%);transition:.25s}[dir=rtl] .bill{transform:translateX(-110%)}[dir=ltr] .bill{transform:translateX(110%)}.bill.open{transform:translateX(0)}.overlay.open{display:block}.mobile-bill{display:block}.products{padding-bottom:85px}}@media(max-width:520px){.brand h1,.cashier{display:none}.products{grid-template-columns:repeat(2,1fr)}.product-img{height:135px}.top{padding:0 3%}}
</style>
</head>
<body>
<div class="overlay" id="overlay">
</div>
<header class="top">
<div class="brand">
<a href="{{ url('/admin') }}">
<img src="{{ asset('images/logo.jpeg') }}" alt="لمسة أنوثة">
</a>
<h1 data-t="pos">نقطة البيع</h1>
</div>
<div class="top-actions">
<span class="cashier">
<span data-t="cashier">الكاشير</span>: Admin</span>
<button class="tool" id="theme">☾</button>
<button class="tool" id="language">English</button>
<a class="tool" href="{{ url('/admin') }}" style="display:grid;place-items:center" data-t="exit">خروج</a>
</div>
</header>
<main class="app">
<section class="catalog">
<div class="search-row">
<input class="search" id="search" data-placeholder-ar="ابحث عن منتج أو SKU..." data-placeholder-en="Search product or SKU..." placeholder="ابحث عن منتج أو SKU...">
<button class="barcode" id="barcode" data-t="barcode">▥ الباركود</button>
</div>
<div class="categories">
<button class="category active" data-category="all" data-t="all">الكل</button>
<button class="category" data-category="bags" data-t="bags">الحقائب</button>
<button class="category" data-category="shoes" data-t="shoes">الأحذية</button>
<button class="category" data-category="accessories" data-t="accessories">الإكسسوارات</button>
</div>
<div class="products" id="products">@forelse(($products ?? []) as $product)@php $img=optional($product->images->first())->image; $cat=strtolower(optional($product->category)->slug ?? 'other'); @endphp<div class="product" data-id="{{ $product->id }}" data-name="{{ $product->name_ar }}" data-price="{{ (float)$product->price }}" data-category="{{ $cat }}" data-search="{{ mb_strtolower($product->name_ar.' '.$product->name_en.' '.$product->sku) }}">
<div class="product-img">@if($img)<img src="{{ asset('storage/'.$img) }}" alt="{{ $product->name_ar }}">@else🛍️@endif</div>
<div class="product-info">
<h3>{{ $product->name_ar }}</h3>
<small>{{ $product->sku }}</small>
<strong>{{ number_format($product->price,3) }} ر.ع</strong>
</div>
</div>@empty<div class="empty-products">
<span style="font-size:45px">▣</span>
<p data-t="noProducts">ستظهر المنتجات هنا بعد إضافتها إلى المتجر</p>
</div>@endforelse</div>
</section>
<aside class="bill" id="bill">
<div class="bill-head">
<h2 data-t="currentSale">الفاتورة الحالية</h2>
<button id="clear" data-t="clear">مسح الكل</button>
</div>
<div class="customer">
<select>
<option data-t="walkIn">عميل نقدي</option>
<option data-t="addCustomer">+ إضافة عميل</option>
</select>
</div>
<div class="cart-items" id="cart">
<div class="cart-empty" id="empty">
<span>🛒</span>
<p data-t="emptyCart">اضغط على منتج لإضافته إلى الفاتورة</p>
</div>
</div>
<div class="totals">
<div class="line">
<span data-t="subtotal">المجموع الفرعي</span>
<strong>
<span id="subtotal">0.000</span> ر.ع</strong>
</div>
<div class="line">
<span data-t="discount">الخصم</span>
<strong>
<span id="discountValue">0.000</span> ر.ع</strong>
</div>
<div class="line grand">
<span data-t="total">الإجمالي</span>
<strong>
<span id="total">0.000</span> ر.ع</strong>
</div>
<div class="discount">
<input id="discount" type="number" min="0" step="0.001" data-placeholder-ar="قيمة الخصم" data-placeholder-en="Discount amount" placeholder="قيمة الخصم">
<button id="apply" data-t="apply">تطبيق</button>
</div>
<div class="payments">
<style>.payments{gap:9px}.pay-type{height:66px!important;display:flex;align-items:center;justify-content:center;gap:8px;font-weight:bold;transition:.2s}.pay-icon{width:32px;height:32px;display:grid;place-items:center;border-radius:50%;color:#fff;font-size:17px;box-shadow:0 4px 12px rgba(0,0,0,.18)}.cash-icon{background:linear-gradient(135deg,#16a05d,#087a42)}.card-icon{background:linear-gradient(135deg,#3478f6,#1748a8)}.transfer-icon{background:linear-gradient(135deg,#f39c12,#c46d00)}.pay-type.active{border-width:2px!important;box-shadow:0 5px 16px rgba(226,27,35,.16);transform:translateY(-2px)}.pay-type:hover{transform:translateY(-2px);border-color:var(--red)}</style>
<button class="pay-type active" data-pay="cash"><span class="pay-icon cash-icon">💵</span><span data-t="cash">نقدي</span></button>
<button class="pay-type" data-pay="card"><span class="pay-icon card-icon">💳</span><span data-t="card">فيزا</span></button>
<button class="pay-type" data-pay="transfer"><span class="pay-icon transfer-icon">📱</span><span data-t="transfer">تحويل</span></button>
</div>
<button class="complete" id="complete" disabled data-t="complete">إتمام وطباعة الفاتورة</button>
<div class="notice" id="notice">
</div>
</div>
</aside>
</main>
<button class="mobile-bill" id="mobileBill">
<span data-t="viewBill">عرض الفاتورة</span> — <span id="mobileTotal">0.000</span> ر.ع</button>
<script>const q=s=>document.querySelector(s),qa=s=>[...document.querySelectorAll(s)];let lang=localStorage.adminLanguage||'ar',theme=localStorage.adminTheme||'light',cart={},discount=0,active='all';const tr={ar:{pos:'نقطة البيع',cashier:'الكاشير',exit:'خروج',barcode:'▥ الباركود',all:'الكل',bags:'الحقائب',shoes:'الأحذية',accessories:'الإكسسوارات',noProducts:'ستظهر المنتجات هنا بعد إضافتها إلى المتجر',currentSale:'الفاتورة الحالية',clear:'مسح الكل',walkIn:'عميل نقدي',addCustomer:'+ إضافة عميل',emptyCart:'اضغط على منتج لإضافته إلى الفاتورة',subtotal:'المجموع الفرعي',discount:'الخصم',total:'الإجمالي',apply:'تطبيق',cash:'نقدي',card:'فيزا',transfer:'تحويل',complete:'إتمام وطباعة الفاتورة',viewBill:'عرض الفاتورة',done:'واجهة نقطة البيع جاهزة. سيتم حفظ الفاتورة وتحديث المخزون بعد ربط النظام.',scan:'سيتم ربط قارئ الباركود بعد إضافة المنتجات.'},en:{pos:'Point of Sale',cashier:'Cashier',exit:'Exit',barcode:'▥ Barcode',all:'All',bags:'Bags',shoes:'Shoes',accessories:'Accessories',noProducts:'Products will appear here after being added',currentSale:'Current Sale',clear:'Clear all',walkIn:'Walk-in customer',addCustomer:'+ Add customer',emptyCart:'Tap a product to add it to the bill',subtotal:'Subtotal',discount:'Discount',total:'Total',apply:'Apply',cash:'Cash',card:'Card',transfer:'Transfer',complete:'Complete & Print',viewBill:'View Bill',done:'The POS interface is ready. Invoice saving and inventory updates will be connected later.',scan:'Barcode scanning will be connected after products are added.'}};function applyTheme(){document.body.classList.toggle('dark',theme==='dark');q('#theme').textContent=theme==='dark'?'☀':'☾'}function applyLang(){document.documentElement.lang=lang;document.documentElement.dir=lang==='ar'?'rtl':'ltr';q('#language').textContent=lang==='ar'?'English':'العربية';qa('[data-t]').forEach(e=>{let v=tr[lang][e.dataset.t];if(v)e.textContent=v});qa('[data-placeholder-ar]').forEach(e=>e.placeholder=e.dataset[lang==='ar'?'placeholderAr':'placeholderEn'])}function draw(){q('#cart').innerHTML='';let sum=0,ObjectValues=Object.values(cart);if(!ObjectValues.length)q('#cart').innerHTML='<div class="cart-empty" id="empty">
<span>🛒</span>
<p>'+tr[lang].emptyCart+'</p>
</div>';ObjectValues.forEach(i=>{sum+=i.price*i.qty;let el=document.createElement('div');el.className='cart-item';el.innerHTML='<div>
<h3>'+i.name+'</h3>
<small>'+i.price.toFixed(3)+' ر.ع</small>
<div class="item-bottom">
<div class="qty">
<button data-do="plus">+</button>
<span>'+i.qty+'</span>
<button data-do="minus">−</button>
</div>
<button class="delete">×</button>
</div>
</div>
<span class="item-total">'+(i.price*i.qty).toFixed(3)+' ر.ع</span>';el.querySelector('[data-do=plus]').onclick=()=>{i.qty++;draw()};el.querySelector('[data-do=minus]').onclick=()=>{if(i.qty>1)i.qty--;else delete cart[i.id];draw()};el.querySelector('.delete').onclick=()=>{delete cart[i.id];draw()};q('#cart').appendChild(el)});discount=Math.min(discount,sum);let total=sum-discount;q('#subtotal').textContent=sum.toFixed(3);q('#discountValue').textContent=discount.toFixed(3);q('#total').textContent=q('#mobileTotal').textContent=total.toFixed(3);q('#complete').disabled=!ObjectValues.length}qa('.product').forEach(p=>p.onclick=()=>{let id=p.dataset.id;if(cart[id])cart[id].qty++;else cart[id]={id,name:p.dataset.name,price:Number(p.dataset.price),qty:1};draw()});q('#search').oninput=()=>{let s=q('#search').value.toLowerCase();qa('.product').forEach(p=>p.style.display=(p.dataset.search.includes(s)&&(active==='all'||p.dataset.category.includes(active)))?'':'none')};qa('.category').forEach(b=>b.onclick=()=>{qa('.category').forEach(x=>x.classList.remove('active'));b.classList.add('active');active=b.dataset.category;q('#search').dispatchEvent(new Event('input'))});q('#clear').onclick=()=>{cart={};discount=0;draw()};q('#apply').onclick=()=>{discount=Math.max(0,Number(q('#discount').value)||0);draw()};qa('.pay-type').forEach(b=>b.onclick=()=>{qa('.pay-type').forEach(x=>x.classList.remove('active'));b.classList.add('active')});q('#complete').onclick=()=>{q('#notice').textContent=tr[lang].done;q('#notice').style.display='block'};q('#barcode').onclick=()=>alert(tr[lang].scan);function closeBill(){q('#bill').classList.remove('open');q('#overlay').classList.remove('open')}q('#mobileBill').onclick=()=>{q('#bill').classList.add('open');q('#overlay').classList.add('open')};q('#overlay').onclick=closeBill;q('#theme').onclick=()=>{theme=theme==='dark'?'light':'dark';localStorage.adminTheme=theme;applyTheme()};q('#language').onclick=()=>{lang=lang==='ar'?'en':'ar';localStorage.adminLanguage=lang;applyLang();draw()};applyTheme();applyLang();draw();</script>
</body>
</html>
