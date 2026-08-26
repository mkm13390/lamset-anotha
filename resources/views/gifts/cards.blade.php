@extends('layouts.store')
@section('title','بطاقات الهدايا | لمسة أنوثة')
@push('styles')
<style>
.page{padding:42px 0 80px}.layout{display:grid;grid-template-columns:1fr 1fr;gap:22px;align-items:center}.visual{min-height:390px;border-radius:26px;background:linear-gradient(135deg,#181818,#856d5b);color:#fff;padding:34px;display:flex;flex-direction:column;justify-content:space-between;box-shadow:var(--sf-shadow)}.visual small{opacity:.72}.visual h1{font-size:42px;margin:0}.amount{font-size:34px;font-weight:900}.card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:20px;padding:24px}.quick{display:grid;grid-template-columns:repeat(5,1fr);gap:7px;margin:10px 0 16px}.quick button{height:42px;border:1px solid var(--sf-border);border-radius:10px;background:var(--sf-bg);color:var(--sf-text);cursor:pointer}.quick button.active{background:var(--sf-primary);color:var(--sf-primary-text)}.field{margin-bottom:12px}.field label{display:block;font-size:12px;font-weight:700;margin-bottom:6px}.field input,.field textarea{width:100%;border:1px solid var(--sf-border);border-radius:10px;background:var(--sf-bg);color:var(--sf-text);padding:0 12px}.field input{height:45px}.field textarea{min-height:90px;padding-top:10px}.note{font-size:11px;color:var(--sf-muted);line-height:1.8;margin-top:12px}
@media(max-width:800px){.layout{grid-template-columns:1fr}.quick{grid-template-columns:repeat(3,1fr)}}
</style>
@endpush
@section('content')
<section class="page"><div class="sf-container"><div class="layout">
<div class="visual"><div><small>لمسة أنوثة</small><h1>بطاقة هدية</h1></div><div><small>قيمة البطاقة</small><div class="amount" id="giftPreview">5.000 OMR</div></div></div>
<div class="card">
<h2>اختاري قيمة الهدية 🎁</h2>
<p class="sf-muted">تبدأ البطاقة من {{ number_format((float)config('storefront.gift_cards.minimum_amount',5),3) }} OMR.</p>
<div class="quick">
@foreach(config('storefront.gift_cards.quick_amounts',[5,10,20,30,50]) as $amount)
<button type="button" data-gift-amount="{{ (float)$amount }}">{{ number_format((float)$amount,0) }}</button>
@endforeach
</div>
<div class="field"><label>مبلغ مخصص</label><input id="customGiftAmount" type="number" step="0.001" min="{{ config('storefront.gift_cards.minimum_amount',5) }}" max="{{ config('storefront.gift_cards.maximum_amount',200) }}" value="{{ config('storefront.gift_cards.minimum_amount',5) }}"></div>
<div class="field"><label>اسم المستلم</label><input id="giftName"></div>
<div class="field"><label>رقم هاتف المستلم</label><input id="giftPhone"></div>
<div class="field"><label>رسالة الإهداء</label><textarea id="giftMessage"></textarea></div>
<a id="giftWhatsapp" class="sf-button sf-button--primary" target="_blank" rel="noopener" href="#">طلب البطاقة عبر واتساب</a>
<div class="note">إصدار البطاقة وتفعيل الرصيد يتم بعد تأكيد الدفع. لن نفعّل بطاقة مدفوعة إلكترونيًا بشكل وهمي قبل ربط بوابة الدفع الفعلية.</div>
</div>
</div></div></section>
@endsection
@push('scripts')
<script>
const minGift=Number(@json(config('storefront.gift_cards.minimum_amount',5)));
const maxGift=Number(@json(config('storefront.gift_cards.maximum_amount',200)));
const custom=document.getElementById('customGiftAmount');
const preview=document.getElementById('giftPreview');
const link=document.getElementById('giftWhatsapp');
const whatsapp=@json(config('storefront.brand.whatsapp','96895426555'));
function giftValue(){return Math.min(maxGift,Math.max(minGift,Number(custom.value||minGift)))}
function updateGift(){
 const amount=giftValue(); custom.value=amount.toFixed(3); preview.textContent=amount.toFixed(3)+' OMR';
 document.querySelectorAll('[data-gift-amount]').forEach(b=>b.classList.toggle('active',Number(b.dataset.giftAmount)===amount));
 const name=document.getElementById('giftName').value||'-',phone=document.getElementById('giftPhone').value||'-',message=document.getElementById('giftMessage').value||'-';
 const text=`مرحبًا، أريد بطاقة هدية من لمسة أنوثة\nالقيمة: ${amount.toFixed(3)} OMR\nالمستلم: ${name}\nالهاتف: ${phone}\nالرسالة: ${message}`;
 link.href='https://wa.me/'+whatsapp+'?text='+encodeURIComponent(text);
}
document.querySelectorAll('[data-gift-amount]').forEach(b=>b.onclick=()=>{custom.value=Number(b.dataset.giftAmount).toFixed(3);updateGift()});
['input','change'].forEach(ev=>document.querySelectorAll('#customGiftAmount,#giftName,#giftPhone,#giftMessage').forEach(el=>el.addEventListener(ev,updateGift)));
updateGift();
</script>
@endpush
