<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>الشحن</title>
<style>
*{box-sizing:border-box}
:root{--bg:#f5f6f8;--card:#fff;--text:#1d2433;--muted:#6b7280;--line:#e2e6ed;--primary:#1f2937}
body{margin:0;font-family:Tahoma,Arial,sans-serif;background:var(--bg);color:var(--text)}
a{text-decoration:none;color:inherit}
.wrap{max-width:1450px;margin:auto;padding:24px}
.top{display:flex;justify-content:space-between;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:20px}
.top h1{margin:0}
.nav{display:flex;gap:8px;flex-wrap:wrap}
.btn,button{border:0;border-radius:10px;padding:10px 14px;cursor:pointer;font-weight:700;font-family:inherit}
.btn{background:#fff;border:1px solid var(--line)}
.primary{background:var(--primary);color:#fff}
.success-btn{background:#166534;color:#fff}
.warning-btn{background:#92400e;color:#fff}
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
.number{white-space:nowrap;font-variant-numeric:tabular-nums}.inline{display:flex;gap:8px;flex-wrap:wrap;align-items:end}
@media(max-width:900px){.g4,.g3,.g2{grid-template-columns:1fr}.wrap{padding:14px}table{display:block;overflow-x:auto;white-space:nowrap}}
</style>
</head><body><div class="wrap">
<div class="top"><div><h1>إدارة الشحن</h1><div class="muted">الشركات، الخدمات، المناطق، الأسعار والتتبع</div></div>
<div class="nav">
<a class="btn" href="{{ route('admin.shipping.index') }}">الشحن</a>
<a class="btn" href="{{ route('admin.payments.index') }}">المدفوعات</a>
<a class="btn" href="{{ route('admin.returns.index') }}">المرتجعات</a>
</div>
</div>

@if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
@if($errors->any())<div class="errors">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif

<div class="grid g3">
<div class="card"><h2 class="section-title">شركة شحن</h2><form method="post" action="{{ route('admin.shipping.carriers.store') }}">@csrf
<div class="field"><label>الاسم</label><input name="name_ar" required></div><div class="field"><label>الكود</label><input name="code" required></div>
<div class="field"><label>نوع الربط</label><select name="integration_type"><option value="manual">يدوي</option><option value="api">API</option><option value="webhook">Webhook</option><option value="aggregator">Aggregator</option></select></div>
<div class="field"><label>رابط التتبع</label><input name="tracking_url_template" placeholder="...{tracking_number}..."></div>
<label><input style="width:auto" type="checkbox" name="supports_cod" value="1"> يدعم الدفع عند الاستلام</label><br>
<label><input style="width:auto" type="checkbox" name="supports_return_pickup" value="1"> يدعم استلام المرتجعات</label><br><br>
<button class="primary">حفظ الشركة</button></form></div>

<div class="card"><h2 class="section-title">خدمة شحن</h2><form method="post" action="{{ route('admin.shipping.services.store') }}">@csrf
<div class="field"><label>الشركة</label><select name="shipping_carrier_id"><option value="">بدون شركة</option>@foreach($carriers as $carrier)<option value="{{ $carrier->id }}">{{ $carrier->name_ar }}</option>@endforeach</select></div>
<div class="field"><label>اسم الخدمة</label><input name="name_ar" required></div><div class="field"><label>الكود</label><input name="code" required></div>
<div class="grid g2"><div class="field"><label>أقل أيام</label><input name="estimated_days_min" type="number" min="0"></div><div class="field"><label>أعلى أيام</label><input name="estimated_days_max" type="number" min="0"></div></div>
<label><input style="width:auto" type="checkbox" name="supports_cod" value="1"> COD</label><br><br><button class="primary">حفظ الخدمة</button></form></div>

<div class="card"><h2 class="section-title">منطقة شحن</h2><form method="post" action="{{ route('admin.shipping.zones.store') }}">@csrf
<div class="field"><label>اسم المنطقة</label><input name="name_ar" required></div><div class="field"><label>الكود</label><input name="code" required></div>
<div class="field"><label>المحافظة</label><input name="governorate"></div><div class="field"><label>الولاية</label><input name="wilayat"></div>
<input type="hidden" name="country_code" value="OM"><button class="primary">حفظ المنطقة</button></form></div>
</div>

<div class="card"><h2 class="section-title">سعر شحن</h2><form method="post" action="{{ route('admin.shipping.rates.store') }}">@csrf
<div class="grid g4">
<div class="field"><label>المنطقة</label><select name="shipping_zone_id" required>@foreach($zones as $zone)<option value="{{ $zone->id }}">{{ $zone->name_ar }}</option>@endforeach</select></div>
<div class="field"><label>الخدمة</label><select name="shipping_service_id" required>@foreach($carriers as $carrier)@foreach($carrier->services as $service)<option value="{{ $service->id }}">{{ $carrier->name_ar }} · {{ $service->name_ar }}</option>@endforeach@endforeach</select></div>
<div class="field"><label>السعر</label><input name="base_price" type="number" step="0.001" min="0" required></div>
<div class="field"><label>الشحن المجاني من</label><input name="free_shipping_threshold" type="number" step="0.001" min="0"></div>
<div class="field"><label>أقل طلب</label><input name="min_order_amount" type="number" step="0.001" min="0" value="0"></div>
<div class="field"><label>أقصى طلب</label><input name="max_order_amount" type="number" step="0.001" min="0"></div>
<div class="field"><label>أقصى وزن</label><input name="max_weight_kg" type="number" step="0.001" min="0"></div>
<div class="field"><label>سعر الكيلو الإضافي</label><input name="extra_kg_price" type="number" step="0.001" min="0" value="0"></div>
<div class="field"><label>رسوم COD</label><input name="cod_fee" type="number" step="0.001" min="0" value="0"></div>
</div><label><input style="width:auto" type="checkbox" name="cod_available" value="1"> الدفع عند الاستلام متاح</label><br><br><button class="primary">حفظ السعر</button></form></div>

<div class="card"><h2 class="section-title">الشحنات</h2><table><thead><tr><th>الشحنة</th><th>الطلب</th><th>الشركة</th><th>الخدمة</th><th>التتبع</th><th>الحالة</th><th>تحديث</th></tr></thead><tbody>
@forelse($shipments as $shipment)<tr><td>{{ $shipment->shipment_number }}</td><td>{{ $shipment->order?->order_number }}</td><td>{{ $shipment->carrier?->name_ar ?: '-' }}</td><td>{{ $shipment->service?->name_ar ?: '-' }}</td><td>{{ $shipment->tracking_number ?: '-' }}</td><td><span class="badge">{{ $shipment->status }}</span></td><td><form class="inline" method="post" action="{{ route('admin.shipping.shipments.status',$shipment) }}">@csrf<select name="status"><option value="pending">pending</option><option value="ready">ready</option><option value="picked_up">picked_up</option><option value="in_transit">in_transit</option><option value="out_for_delivery">out_for_delivery</option><option value="delivered">delivered</option><option value="failed_delivery">failed_delivery</option><option value="returned_to_sender">returned_to_sender</option><option value="cancelled">cancelled</option></select><button>حفظ</button></form></td></tr>
@empty<tr><td colspan="7" class="empty">لا توجد شحنات.</td></tr>@endforelse
</tbody></table><div>{{ $shipments->links() }}</div></div>
</div></body></html>