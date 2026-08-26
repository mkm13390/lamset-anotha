@extends('layouts.store')

@section('title', 'حسابي | لمسة أنوثة')

@push('styles')
<style>
.account-page{padding:32px 0 70px}.account-hero{padding:24px;border:1px solid var(--sf-border);border-radius:20px;background:var(--sf-surface);display:flex;justify-content:space-between;align-items:center;gap:18px;margin-bottom:18px}
.account-hero h1{margin:0 0 5px}.account-hero p{margin:0;color:var(--sf-muted)}.points{padding:12px 16px;border-radius:14px;background:var(--sf-primary);color:var(--sf-primary-text);text-align:center;min-width:150px}.points b{display:block;font-size:23px}
.account-layout{display:grid;grid-template-columns:230px 1fr;gap:18px}.sidebar{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:16px;padding:8px;height:max-content;position:sticky;top:145px}
.tab-btn{width:100%;border:0;background:transparent;color:var(--sf-text);padding:12px;border-radius:10px;text-align:start;cursor:pointer}.tab-btn.active,.tab-btn:hover{background:var(--sf-primary);color:var(--sf-primary-text)}
.tab-panel{display:none}.tab-panel.active{display:block}.card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:16px;padding:18px;margin-bottom:14px}.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}.stat{padding:18px;border:1px solid var(--sf-border);border-radius:14px;background:var(--sf-surface)}.stat b{display:block;font-size:24px}
.quick{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}.quick a{padding:15px;border:1px solid var(--sf-border);border-radius:12px;text-align:center}.quick a:hover{border-color:var(--sf-text)}
.order{border:1px solid var(--sf-border);border-radius:12px;padding:14px;margin-bottom:10px}.order-top,.order-bottom{display:flex;justify-content:space-between;align-items:center;gap:12px}.order small{color:var(--sf-muted)}.badge{padding:5px 9px;border-radius:999px;background:var(--sf-surface-2);font-size:11px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.field.full{grid-column:1/-1}.field label{display:block;font-size:12px;font-weight:700;margin-bottom:6px}.field input{width:100%;height:44px;border:1px solid var(--sf-border);border-radius:10px;background:var(--sf-bg);color:var(--sf-text);padding:0 12px}
.track-result{margin-top:12px;padding:12px;border:1px solid var(--sf-border);border-radius:10px}.logout{margin-top:8px;width:100%}
@media(max-width:850px){.account-layout{grid-template-columns:1fr}.sidebar{position:static;display:flex;overflow-x:auto}.tab-btn{white-space:nowrap;width:auto}.stats{grid-template-columns:1fr 1fr}.quick{grid-template-columns:1fr 1fr}}
@media(max-width:560px){.account-hero{align-items:flex-start;flex-direction:column}.points{width:100%}.stats,.quick,.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.order-top,.order-bottom{align-items:flex-start;flex-direction:column}}
</style>
@endpush

@section('content')
<section class="account-page">
    <div class="sf-container">
        <div class="account-hero">
            <div>
                <h1>مرحبًا، {{ auth()->user()?->name ?? 'عميلتنا' }}</h1>
                <p>من هنا يمكنك إدارة طلباتك، المفضلة، العناوين والمرتجعات.</p>
            </div>
            <div class="points">
                <span>نقاط لمسة</span>
                <b>{{ (int)($points ?? 0) }}</b>
            </div>
        </div>

        <div class="account-layout">
            <aside class="sidebar">
                <button class="tab-btn active" data-tab="overview">نظرة عامة</button>
                <button class="tab-btn" data-tab="orders">طلباتي</button>
                <button class="tab-btn" data-tab="tracking">تتبع طلب</button>
                <button class="tab-btn" data-tab="profile">بيانات الحساب</button>
                <a class="tab-btn" href="{{ route('account.wishlist.index') }}">المفضلة</a>
                <a class="tab-btn" href="{{ route('account.addresses.index') }}">العناوين</a>
                <a class="tab-btn" href="{{ route('account.returns.index') }}">المرتجعات</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="sf-button sf-button--secondary logout" type="submit">تسجيل الخروج</button>
                </form>
            </aside>

            <div>
                <section class="tab-panel active" id="overview">
                    <div class="stats">
                        <div class="stat"><span class="sf-muted">إجمالي الطلبات</span><b>{{ (int)($ordersCount ?? 0) }}</b></div>
                        <div class="stat"><span class="sf-muted">المفضلة</span><b>{{ (int)($actualWishlistCount ?? 0) }}</b></div>
                        <div class="stat"><span class="sf-muted">نقاط لمسة</span><b>{{ (int)($points ?? 0) }}</b></div>
                    </div>

                    <div class="card" style="margin-top:14px">
                        <h2>وصول سريع</h2>
                        <div class="quick">
                            <a href="{{ route('account.wishlist.index') }}">♡<br>المفضلة</a>
                            <a href="{{ route('account.addresses.index') }}">⌖<br>العناوين</a>
                            <a href="{{ route('account.returns.index') }}">↩<br>المرتجعات</a>
                            <a href="{{ route('products.index') }}">⌕<br>التسوق</a>
                        </div>
                    </div>

                    @if($mainAddress)
                        <div class="card">
                            <h2>العنوان الرئيسي</h2>
                            <p>{{ $mainAddress->address_line1 ?? $mainAddress->address ?? '' }}</p>
                            <p class="sf-muted">
                                {{ $mainAddress->wilayat ?? '' }}
                                @if(!empty($mainAddress->governorate)) · {{ $mainAddress->governorate }} @endif
                            </p>
                            <a class="sf-button sf-button--secondary" href="{{ route('account.addresses.index') }}">إدارة العناوين</a>
                        </div>
                    @endif
                </section>

                <section class="tab-panel" id="orders">
                    <div class="card">
                        <h2>طلباتي</h2>
                        @forelse($orders ?? [] as $order)
                            <div class="order">
                                <div class="order-top">
                                    <div><strong>#{{ $order->order_number }}</strong><br><small>{{ $order->created_at?->format('Y-m-d') }}</small></div>
                                    <span class="badge">{{ $order->status }}</span>
                                </div>
                                <div class="order-bottom" style="margin-top:12px">
                                    <strong>{{ number_format((float)$order->total,3) }} OMR</strong>
                                    <div style="display:flex;gap:7px;flex-wrap:wrap">
                                        <a class="sf-button sf-button--secondary" href="{{ route('orders.invoice',$order) }}">الفاتورة</a>
                                        <a class="sf-button sf-button--secondary" href="{{ route('account.returns.create',$order) }}">طلب إرجاع</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="sf-empty"><strong>لا توجد طلبات حتى الآن.</strong></div>
                        @endforelse
                    </div>
                </section>

                <section class="tab-panel" id="tracking">
                    <form class="card" method="POST" action="{{ route('account.track') }}">
                        @csrf
                        <h2>تتبع طلب</h2>
                        <div class="field">
                            <label>رقم الطلب</label>
                            <input name="order_number" value="{{ old('order_number') }}" placeholder="LA-..." required>
                        </div>
                        <button class="sf-button sf-button--primary" type="submit">تتبع</button>

                        @if($tracked)
                            <div class="track-result">
                                <strong>#{{ $tracked['order_number'] ?? '' }}</strong><br>
                                <span class="sf-muted">الحالة: {{ $tracked['status'] ?? '' }}</span><br>
                                <span>الإجمالي: {{ number_format((float)($tracked['total'] ?? 0),3) }} OMR</span>
                            </div>
                        @endif
                    </form>
                </section>

                <section class="tab-panel" id="profile">
                    <form class="card" method="POST" action="{{ route('account.profile.update') }}">
                        @csrf
                        @method('PATCH')
                        <h2>البيانات الشخصية</h2>
                        <div class="form-grid">
                            <div class="field">
                                <label>الاسم</label>
                                <input name="name" value="{{ old('name',auth()->user()?->name) }}" required>
                            </div>
                            <div class="field">
                                <label>رقم الهاتف</label>
                                <input name="phone" value="{{ old('phone',auth()->user()?->phone) }}" required>
                            </div>
                            <div class="field full">
                                <label>البريد الإلكتروني</label>
                                <input type="email" name="email" value="{{ old('email',auth()->user()?->email) }}" required>
                            </div>
                        </div>
                        <button class="sf-button sf-button--primary" type="submit">حفظ البيانات</button>
                    </form>

                    <form class="card" method="POST" action="{{ route('account.password.update') }}">
                        @csrf
                        @method('PATCH')
                        <h2>تغيير كلمة المرور</h2>
                        <div class="form-grid">
                            <div class="field full">
                                <label>كلمة المرور الحالية</label>
                                <input name="current_password" type="password" required>
                            </div>
                            <div class="field">
                                <label>كلمة المرور الجديدة</label>
                                <input name="password" type="password" required>
                            </div>
                            <div class="field">
                                <label>تأكيد كلمة المرور</label>
                                <input name="password_confirmation" type="password" required>
                            </div>
                        </div>
                        <button class="sf-button sf-button--primary" type="submit">تحديث كلمة المرور</button>
                    </form>
                </section>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-tab]').forEach(button => {
    button.addEventListener('click', () => {
        document.querySelectorAll('[data-tab]').forEach(x => x.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(x => x.classList.remove('active'));
        button.classList.add('active');
        document.getElementById(button.dataset.tab)?.classList.add('active');
    });
});
</script>
@endpush
