@extends('layouts.store')
@section('title','تسجيل الدخول | لمسة أنوثة')
@push('styles')
<style>
.auth-page{padding:42px 0 80px}.auth-wrap{max-width:980px;margin:auto;display:grid;grid-template-columns:1fr 1fr;gap:18px}
.auth-card{background:var(--sf-surface);border:1px solid var(--sf-border);border-radius:20px;padding:24px}.auth-card h1,.auth-card h2{margin-top:0}
.field{margin-bottom:12px}.field label{display:block;font-size:12px;font-weight:700;margin-bottom:6px}.field input{width:100%;height:45px;border:1px solid var(--sf-border);border-radius:10px;background:var(--sf-bg);color:var(--sf-text);padding:0 12px}
.row{display:flex;justify-content:space-between;align-items:center;gap:10px}.row label{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--sf-muted)}
.auth-card .sf-button{width:100%;margin-top:5px}.hint{color:var(--sf-muted);font-size:12px;line-height:1.8}
@media(max-width:760px){.auth-wrap{grid-template-columns:1fr}}
</style>
@endpush
@section('content')
<section class="auth-page"><div class="sf-container">
<div class="auth-wrap">
<form class="auth-card" method="POST" action="{{ route('login.store') }}">
@csrf
<h1>تسجيل الدخول</h1>
<p class="hint">يمكنك الدخول باستخدام البريد الإلكتروني أو رقم الهاتف.</p>
<div class="field"><label>البريد الإلكتروني أو رقم الهاتف</label><input name="login" value="{{ old('login') }}" required autocomplete="username"></div>
<div class="field"><label>كلمة المرور</label><input name="password" type="password" required autocomplete="current-password"></div>
<div class="row"><label><input type="checkbox" name="remember" value="1"> تذكرني</label></div>
<button class="sf-button sf-button--primary" type="submit">دخول</button>
</form>

<form class="auth-card" method="POST" action="{{ route('register.store') }}">
@csrf
<h2>إنشاء حساب جديد</h2>
<p class="hint">الحساب يسهل تتبع الطلبات وحفظ العناوين والمفضلة والمرتجعات.</p>
<div class="field"><label>الاسم الكامل</label><input name="name" value="{{ old('name') }}" required></div>
<div class="field"><label>رقم الهاتف</label><input name="phone" value="{{ old('phone') }}" required></div>
<div class="field"><label>البريد الإلكتروني</label><input name="email" type="email" value="{{ old('email') }}" required></div>
<div class="field"><label>كلمة المرور</label><input name="password" type="password" required></div>
<div class="field"><label>تأكيد كلمة المرور</label><input name="password_confirmation" type="password" required></div>
<button class="sf-button sf-button--primary" type="submit">إنشاء الحساب</button>
</form>
</div></div></section>
@endsection
