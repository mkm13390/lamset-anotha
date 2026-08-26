@extends('layouts.store')

@section('title', 'إتمام الطلب | لمسة أنوثة')

@push('styles')
<style>
.checkout-page{padding:34px 0 70px}.checkout-head{margin-bottom:18px}.checkout-head h1{margin:0;font-size:34px}.checkout-head p{color:var(--sf-muted);margin:5px 0 0}
.checkout-layout{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(320px,.7fr);gap:22px}.panel{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:18px;padding:20px}
.section-title{font-size:18px;margin:0 0 17px;padding-bottom:12px;border-bottom:1px solid var(--sf-border)}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:13px}
.field.full{grid-column:1/-1}.field label{display:block;font-size:12px;font-weight:700;margin-bottom:6px}.field input,.field select,.field textarea{width:100%;border:1px solid var(--sf-border);border-radius:10px;background:var(--sf-bg);color:var(--sf-text);padding:0 12px}
.field input,.field select{height:45px}.field textarea{min-height:92px;padding-top:11px;resize:vertical}.required{color:#a23737}
.choice-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px}.choice{display:flex;gap:9px;border:1px solid var(--sf-border);border-radius:12px;padding:12px;cursor:pointer;background:var(--sf-bg)}
.choice input{margin-top:4px}.choice strong{display:block;font-size:13px}.choice span{display:block;font-size:11px;color:var(--sf-muted);margin-top:3px}
.order-summary{position:sticky;top:145px;height:max-content}.checkout-item{display:grid;grid-template-columns:58px 1fr auto;gap:10px;align-items:center;padding:10px 0;border-bottom:1px solid var(--sf-border)}
.checkout-item:last-child{border-bottom:0}.checkout-item .img{width:58px;height:64px;border-radius:9px;background:var(--sf-surface-2);overflow:hidden;display:grid;place-items:center}.checkout-item img{width:100%;height:100%;object-fit:cover}
.checkout-item h3{font-size:12px;margin:0}.checkout-item .meta{font-size:10px;color:var(--sf-muted)}.sum-line{display:flex;justify-content:space-between;gap:10px;padding:9px 0;color:var(--sf-muted)}.sum-line strong{color:var(--sf-text)}.sum-total{border-top:1px solid var(--sf-border);font-size:17px;padding-top:14px;margin-top:7px}
.place-order{width:100%;margin-top:15px}.secure{text-align:center;font-size:11px;color:var(--sf-muted);margin-top:10px}
@media(max-width:850px){.checkout-layout{grid-template-columns:1fr}.order-summary{position:static}}
@media(max-width:620px){.form-grid,.choice-grid{grid-template-columns:1fr}.field.full{grid-column:auto}}
</style>
@endpush

@section('content')
@php
    $items = $cartItems ?? [];
    $cartSubtotal = (float)($subtotal ?? 0);
@endphp

<section class="checkout-page">
    <div class="sf-container">
        <div class="checkout-head">
            <h1>إتمام الطلب</h1>
            <p>أدخلي بيانات التوصيل واختاري طريقة الدفع المناسبة.</p>
        </div>

        @if(count($items))
            <form method="POST" action="{{ route('checkout.store') }}">
                @csrf
                <div class="checkout-layout">
                    <div>
                        <section class="panel">
                            <h2 class="section-title">بيانات الشحن</h2>

                            <div class="form-grid">
                                <div class="field">
                                    <label>الاسم الأول <span class="required">*</span></label>
                                    <input name="first_name" value="{{ old('first_name', auth()->user()?->name) }}" required>
                                </div>

                                <div class="field">
                                    <label>اسم العائلة <span class="required">*</span></label>
                                    <input name="last_name" value="{{ old('last_name') }}" required>
                                </div>

                                <div class="field">
                                    <label>رقم الهاتف <span class="required">*</span></label>
                                    <input name="phone" type="tel" inputmode="numeric" value="{{ old('phone', auth()->user()?->phone) }}" required>
                                </div>

                                <div class="field">
                                    <label>البريد الإلكتروني</label>
                                    <input name="email" type="email" value="{{ old('email', auth()->user()?->email) }}">
                                </div>

                                <div class="field">
                                    <label>المحافظة <span class="required">*</span></label>
                                    <select name="governorate" required>
                                        <option value="">اختاري المحافظة</option>
                                        @foreach(['مسقط','ظفار','مسندم','البريمي','الداخلية','شمال الباطنة','جنوب الباطنة','شمال الشرقية','جنوب الشرقية','الظاهرة','الوسطى'] as $gov)
                                            <option value="{{ $gov }}" @selected(old('governorate') === $gov)>{{ $gov }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="field">
                                    <label>الولاية <span class="required">*</span></label>
                                    <input name="wilayat" value="{{ old('wilayat') }}" required>
                                </div>

                                <div class="field full">
                                    <label>العنوان التفصيلي <span class="required">*</span></label>
                                    <textarea name="address" required placeholder="المنطقة، رقم المنزل، أقرب معلم...">{{ old('address') }}</textarea>
                                </div>

                                <div class="field full">
                                    <label>ملاحظات الطلب</label>
                                    <textarea name="notes" placeholder="أي ملاحظات خاصة بالتوصيل أو الطلب">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </section>

                        <section class="panel" style="margin-top:14px">
                            <h2 class="section-title">طريقة الاستلام</h2>
                            <div class="choice-grid">
                                <label class="choice">
                                    <input type="radio" name="delivery" value="standard" @checked(old('delivery','standard') === 'standard')>
                                    <div><strong>توصيل</strong><span>يتم تحديد رسوم وموعد التوصيل حسب العنوان.</span></div>
                                </label>

                                <label class="choice">
                                    <input type="radio" name="delivery" value="pickup" @checked(old('delivery') === 'pickup')>
                                    <div><strong>استلام</strong><span>استلام الطلب حسب ترتيبات المتجر.</span></div>
                                </label>
                            </div>
                        </section>

                        <section class="panel" style="margin-top:14px">
                            <h2 class="section-title">طريقة الدفع</h2>
                            <div class="choice-grid">
                                <label class="choice">
                                    <input type="radio" name="payment" value="cod" @checked(old('payment','cod') === 'cod')>
                                    <div><strong>الدفع عند الاستلام</strong><span>ادفعي عند استلام الطلب.</span></div>
                                </label>

                                <label class="choice">
                                    <input type="radio" name="payment" value="transfer" @checked(old('payment') === 'transfer')>
                                    <div><strong>تحويل بنكي</strong><span>سيتم تأكيد الطلب بعد مراجعة التحويل.</span></div>
                                </label>

                                <label class="choice" style="opacity:.55">
                                    <input type="radio" name="payment" value="online" disabled>
                                    <div><strong>دفع إلكتروني</strong><span>يظهر بعد ربط بوابة الدفع الفعلية.</span></div>
                                </label>
                            </div>
                        </section>
                    </div>

                    <aside class="panel order-summary">
                        <h2 class="section-title">ملخص طلبك</h2>

                        @foreach($items as $item)
                            @php
                                $qty = (int)($item['quantity'] ?? 1);
                                $price = (float)($item['price'] ?? 0);
                                $lineTotal = $qty * $price;
                                $imagePath = null;
                                if (!empty($item['image'])) {
                                    $imagePath = str_starts_with($item['image'],'http')
                                        ? $item['image']
                                        : asset('storage/' . ltrim($item['image'],'/'));
                                }
                            @endphp

                            <div class="checkout-item">
                                <div class="img">
                                    @if($imagePath)<img src="{{ $imagePath }}" alt="{{ $item['name_ar'] ?? 'منتج' }}">@else 👜 @endif
                                </div>
                                <div>
                                    <h3>{{ $item['name_ar'] ?? 'منتج' }}</h3>
                                    <div class="meta">الكمية: {{ $qty }} @if(!empty($item['size'])) · مقاس {{ $item['size'] }} @endif</div>
                                </div>
                                <strong>{{ number_format($lineTotal,3) }}</strong>
                            </div>
                        @endforeach

                        <div class="sum-line" style="margin-top:9px"><span>المجموع</span><strong>{{ number_format($cartSubtotal,3) }} OMR</strong></div>
                        <div class="sum-line"><span>الشحن</span><strong>يحدد حسب العنوان</strong></div>
                        <div class="sum-line sum-total"><span>الإجمالي الحالي</span><strong>{{ number_format($cartSubtotal,3) }} OMR</strong></div>

                        <button class="sf-button sf-button--primary place-order" type="submit">تأكيد الطلب</button>
                        <div class="secure">🔒 بياناتك لا تُعرض للعموم</div>
                    </aside>
                </div>
            </form>
        @else
            <div class="panel sf-empty">
                <div class="sf-empty__icon">🛍</div>
                <strong>لا توجد منتجات لإتمام الطلب.</strong><br><br>
                <a class="sf-button sf-button--primary" href="{{ route('products.index') }}">العودة للمنتجات</a>
            </div>
        @endif
    </div>
</section>
@endsection
