<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>الباقات</title>
<style>
*{box-sizing:border-box}
:root{--bg:#f5f6f8;--card:#fff;--text:#1d2433;--muted:#6b7280;--line:#e2e6ed;--primary:#1f2937}
body{margin:0;font-family:Tahoma,Arial,sans-serif;background:var(--bg);color:var(--text)}
a{text-decoration:none;color:inherit}.wrap{max-width:1450px;margin:auto;padding:24px}
.top{display:flex;justify-content:space-between;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:20px}
.top h1{margin:0}.nav{display:flex;gap:8px;flex-wrap:wrap}
.btn,button{border:0;border-radius:10px;padding:10px 14px;cursor:pointer;font-weight:700;font-family:inherit}
.btn{background:#fff;border:1px solid var(--line)}.primary{background:var(--primary);color:#fff}
.card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:16px;margin-bottom:16px}
.grid{display:grid;gap:14px}.g4{grid-template-columns:repeat(4,minmax(0,1fr))}
.g3{grid-template-columns:repeat(3,minmax(0,1fr))}.g2{grid-template-columns:repeat(2,minmax(0,1fr))}
.stat b{display:block;font-size:22px;margin-top:6px}.muted{color:var(--muted);font-size:13px}
.field{margin-bottom:12px}label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}
input,select,textarea{width:100%;border:1px solid #d8dde6;border-radius:10px;padding:10px;font-family:inherit;background:#fff}
textarea{min-height:90px;resize:vertical}table{width:100%;border-collapse:collapse}
th,td{padding:10px 8px;border-bottom:1px solid #edf0f4;text-align:right;font-size:14px;vertical-align:top}
th{background:#fafbfc}.badge{display:inline-block;padding:5px 9px;border-radius:999px;background:#eef2ff;font-size:12px}
.flash{padding:12px 14px;border-radius:12px;margin-bottom:14px;background:#ecfdf5;color:#166534}
.errors{padding:12px 14px;border-radius:12px;margin-bottom:14px;background:#fef2f2;color:#991b1b}
.section-title{margin:0 0 14px;font-size:18px}.empty{text-align:center;padding:28px;color:var(--muted)}
.number{white-space:nowrap;font-variant-numeric:tabular-nums}
@media(max-width:900px){.g4,.g3,.g2{grid-template-columns:1fr}.wrap{padding:14px}table{display:block;overflow-x:auto;white-space:nowrap}}
</style>
</head><body><div class="wrap">
<div class="top"><div><h1>الباقات الذكية</h1><div class="muted">بيع عدة منتجات كعرض واحد</div></div>
<div class="nav">
<a class="btn" href="{{ route('admin.marketing.index') }}">التسويق</a>
<a class="btn" href="{{ route('admin.marketing.loyalty.index') }}">الولاء</a>
<a class="btn" href="{{ route('admin.marketing.gift-cards.index') }}">بطاقات الهدايا</a>
<a class="btn" href="{{ route('admin.marketing.bundles.index') }}">الباقات</a>
</div>
</div>

@if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
@if($errors->any())
<div class="errors">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
@endif

<div class="card"><h2 class="section-title">إنشاء باقة</h2>
<form method="post" action="{{ route('admin.marketing.bundles.store') }}">@csrf
<div class="grid g3">
<div class="field"><label>اسم الباقة</label><input name="name_ar" required></div>
<div class="field"><label>طريقة التسعير</label><select name="pricing_type"><option value="fixed_price">سعر ثابت</option><option value="percentage_discount">خصم نسبة</option><option value="amount_discount">خصم مبلغ</option></select></div>
<div class="field"><label>القيمة</label><input name="value" type="number" min="0" step="0.001" required></div>
</div>
<div class="grid g2"><div class="field"><label>يبدأ</label><input name="starts_at" type="datetime-local"></div><div class="field"><label>ينتهي</label><input name="ends_at" type="datetime-local"></div></div>
<div class="field"><label>الوصف</label><textarea name="description_ar"></textarea></div>
<div id="bundleItems"></div>
<button type="button" onclick="addBundleItem()">+ إضافة منتج للباقة</button>
<button class="primary" type="submit">حفظ الباقة</button>
</form></div>

<div class="card"><h2 class="section-title">الباقات الحالية</h2><table><thead><tr><th>الباقة</th><th>التسعير</th><th>القيمة</th><th>المنتجات</th><th>الحالة</th></tr></thead><tbody>
@forelse($bundles as $bundle)<tr><td><strong>{{ $bundle->name_ar }}</strong></td><td>{{ $bundle->pricing_type }}</td><td class="number">{{ number_format((float)$bundle->value,3) }}</td><td>@foreach($bundle->items as $item)<div>{{ $item->variant?->product?->name_ar ?: 'منتج' }} × {{ $item->quantity }}</div>@endforeach</td><td><span class="badge">{{ $bundle->is_active ? 'مفعل' : 'متوقف' }}</span></td></tr>
@empty<tr><td colspan="5" class="empty">لا توجد باقات.</td></tr>@endforelse
</tbody></table><div>{{ $bundles->links() }}</div></div>
</div>
<script>
let bundleIndex=0;
const variants=@json($variants->map(fn($v)=>['id'=>$v->id,'name'=>($v->product?->name_ar ?? 'منتج').' · '.($v->sku ?? '')])->values());
function addBundleItem(){
 const box=document.getElementById('bundleItems');
 const row=document.createElement('div'); row.className='grid g2 card';
 let options=variants.map(v=>`<option value="${v.id}">${escapeHtml(v.name)}</option>`).join('');
 row.innerHTML=`<div class="field"><label>المنتج</label><select name="items[${bundleIndex}][product_variant_id]" required>${options}</select></div><div class="field"><label>الكمية</label><input name="items[${bundleIndex}][quantity]" type="number" min="1" value="1" required></div>`;
 box.appendChild(row); bundleIndex++;
}
function escapeHtml(v){return String(v??'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#039;")}
addBundleItem();
</script>
</body></html>