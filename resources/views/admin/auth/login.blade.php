<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ __('Sign in') }} · ENCAP</title><meta name="robots" content="noindex,nofollow"><link rel="icon" type="image/png" href="{{ \App\Support\EncapImage::url('favicon.png') }}"><link rel="apple-touch-icon" href="{{ \App\Support\EncapImage::url('favicon.png') }}"><link rel="stylesheet" href="{{ asset('css/admin.css') }}"><style>
:root{color-scheme:light;--login-forest:#173c2c;--login-forest-deep:#102e22;--login-gold:#e5bd59;--login-paper:#f4f5ef;--login-ink:#223229;--login-muted:#728073;--login-line:#e2e7de}
html{min-height:100%;background:var(--login-paper)}
body{min-height:100vh;margin:0}
.login-page{min-height:100vh;min-height:100svh;display:grid;grid-template-columns:minmax(0,1.08fr) minmax(390px,.92fr);background:var(--login-paper)}
.login-story{isolation:isolate;position:relative;display:flex;flex-direction:column;justify-content:space-between;min-width:0;overflow:hidden;padding:clamp(28px,5vw,72px);background:radial-gradient(ellipse at 12% 10%,#ffffff12 0,transparent 36%),linear-gradient(145deg,var(--login-forest-deep) 0%,var(--login-forest) 58%,#234d37 100%);color:#fff}
.login-story::before,.login-story::after{content:"";position:absolute;z-index:-1;pointer-events:none;border:1px solid #ffffff18;border-radius:50%}
.login-story::before{width:540px;height:540px;top:-305px;left:-215px;box-shadow:0 0 0 34px #ffffff08,0 0 0 70px #ffffff05}
.login-story::after{width:610px;height:610px;right:-310px;bottom:-310px;box-shadow:0 0 0 34px #ffffff08,0 0 0 72px #ffffff05,0 0 0 112px #ffffff03}
.login-story>*{position:relative;z-index:1}
.login-brand{display:flex;align-items:center;gap:12px;width:max-content;max-width:100%;padding:7px 9px;background:#ffffff08;border:1px solid #ffffff1c;border-radius:7px;color:#fff}
.login-brand:hover{text-decoration:none;background:#ffffff0d}
.login-story-copy{max-width:660px;margin:clamp(48px,8vh,112px) 0}
.login-eyebrow{display:inline-flex;align-items:center;gap:10px;color:var(--login-gold);font-size:10px;font-weight:850;letter-spacing:.16em;text-transform:uppercase}
.login-eyebrow::before{content:"";display:block;width:25px;height:1px;background:currentColor}
.login-story h1{max-width:650px;margin:22px 0 20px;color:#fff;font:500 clamp(42px,5vw,76px)/1.04 Georgia,"Times New Roman",serif;letter-spacing:-.045em;text-wrap:balance}
.login-story p{max-width:475px;margin:0;color:#d1ddd2;font-size:14px;line-height:1.95}
.login-story-foot{display:flex;align-items:center;gap:10px;color:#b7c9b8;font-size:10px;font-weight:750;letter-spacing:.15em}
.login-story-foot::before{content:"";display:block;width:7px;height:7px;border-radius:50%;background:var(--login-gold);box-shadow:0 0 0 4px #e5bd591c}
.login-form-side{display:grid;place-items:center;min-width:0;padding:clamp(24px,4.6vw,68px);background:radial-gradient(ellipse at 85% 12%,#ffffff 0,transparent 52%),var(--login-paper)}
.login-card{position:relative;width:min(100%,450px);min-width:0;padding:clamp(25px,3.4vw,43px);overflow:hidden;background:#fff;border:1px solid var(--login-line);border-radius:13px;box-shadow:0 28px 80px #1b3b2d12,0 3px 12px #1b3b2d06}
.login-card::before{content:"";position:absolute;inset:0 0 auto;height:4px;background:linear-gradient(90deg,var(--login-forest),#3d6c4c 62%,var(--login-gold))}
.login-language-switch{margin:0 0 30px}
.login-language-switch button{min-height:34px;padding:6px 12px;font-size:11px}
.login-language-switch button.active{background:var(--login-forest);border-color:var(--login-forest);color:#fff}
.login-card .admin-kicker{margin:0 0 11px;color:#7b897c}
.login-card h2{margin:0;color:var(--login-ink);font:600 clamp(31px,3vw,38px)/1.15 Georgia,"Times New Roman",serif;letter-spacing:-.035em}
.login-card>p:not(.admin-kicker){margin:12px 0 27px;color:var(--login-muted);font-size:12px;line-height:1.8}
.login-error{display:flex;align-items:flex-start;gap:9px;margin:0 0 19px;padding:12px 13px;background:#fff2ef;border:1px solid #f0d0c9;border-radius:7px;color:#8d3528;font-size:12px;line-height:1.65}
.login-field{display:flex;flex-direction:column;gap:8px;margin:17px 0}
.login-field label{color:#364a3a;font-size:12px;font-weight:800}
.login-field input{width:100%;min-height:48px;padding:12px 14px;border:1px solid #d8dfd5;border-radius:7px;background:#fbfcf8;color:var(--login-ink);font-size:13px;transition:border-color .18s ease,box-shadow .18s ease,background .18s ease}
.login-field input::placeholder{color:#9aa59a}
.login-field input:hover{border-color:#aebdac}
.login-field input:focus{outline:none;border-color:#557b5b;background:#fff;box-shadow:0 0 0 3px #37624918}
.login-field input[aria-invalid="true"]{border-color:#b64d3f;background:#fffaf9}
.login-remember{display:flex;align-items:center;gap:9px;margin:19px 0 23px;color:#5d6c5e;font-size:11px;cursor:pointer}
.login-remember input{width:16px;height:16px;margin:0;accent-color:var(--login-forest);flex:0 0 auto}
.login-submit{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;min-height:49px;padding:12px 16px;background:var(--login-forest);color:#fff;border:1px solid var(--login-forest);border-radius:7px;font-size:12px;font-weight:850;cursor:pointer;box-shadow:0 8px 18px #1b3b2d16;transition:background .18s ease,border-color .18s ease,transform .18s ease,box-shadow .18s ease}
.login-submit:hover{background:#28563c;border-color:#28563c;box-shadow:0 10px 22px #1b3b2d20;transform:translateY(-1px)}
.login-submit span{font-size:16px;line-height:1}
.login-back{display:flex;align-items:center;gap:7px;margin-top:23px;padding-top:17px;border-top:1px solid #edf0e9;color:#49644d;font-size:11px;font-weight:800}
.login-back:hover{color:#1b3b2d}
html[dir="rtl"] .login-story h1{font-family:"Segoe UI",Tahoma,Arial,sans-serif;font-size:clamp(35px,4.25vw,62px);font-weight:750;line-height:1.3;letter-spacing:0}
html[dir="rtl"] .login-eyebrow{letter-spacing:.02em}
html[dir="rtl"] .login-card h2{font-family:"Segoe UI",Tahoma,Arial,sans-serif;font-weight:750;letter-spacing:0}
@media(max-width:1000px){.login-page{grid-template-columns:minmax(0,1fr) minmax(350px,.95fr)}.login-story{padding:clamp(25px,4vw,42px)}.login-form-side{padding:28px}.login-story h1{font-size:clamp(40px,5.2vw,59px)}html[dir="rtl"] .login-story h1{font-size:clamp(34px,4.8vw,50px)}}
@media(max-width:820px){.login-page{grid-template-columns:1fr}.login-story{min-height:320px;padding:26px clamp(22px,7vw,58px)}.login-story-copy{max-width:700px;margin:48px 0 28px}.login-story h1{max-width:700px;font-size:clamp(38px,6vw,54px)}html[dir="rtl"] .login-story h1{font-size:clamp(34px,5.2vw,48px)}.login-story-foot{display:none}.login-form-side{padding:28px 18px 40px}.login-card{width:min(100%,520px);padding:clamp(25px,5vw,42px)}}
@media(max-width:480px){.login-story{min-height:285px;padding:22px 20px}.login-story-copy{margin:39px 0 20px}.login-eyebrow{font-size:9px}.login-story h1{margin:17px 0 12px;font-size:38px}.login-story p{font-size:12px;line-height:1.8}.login-form-side{padding:19px 13px 30px}.login-card{padding:25px 21px;border-radius:10px}.login-language-switch{margin-bottom:25px}.login-card h2{font-size:32px}.login-card>p:not(.admin-kicker){margin-bottom:23px}.login-field{margin:15px 0}.login-back{margin-top:20px}}
@media(prefers-reduced-motion:reduce){.login-field input,.login-submit{transition:none}.login-submit:hover{transform:none}}
</style></head><body><div class="login-page"><section class="login-story"><a class="login-brand" href="{{ route('home', ['locale' => app()->getLocale()]) }}" aria-label="{{ __('ENCAP — home') }}"><span class="login-brand-logo-wrap"><img class="login-brand-logo" src="{{ \App\Support\EncapImage::url('logo.png') }}" alt="ENCAP — Agriculture and Export" width="180" height="52"></span></a><div class="login-story-copy"><div class="login-eyebrow">{{ __('Private administration') }}</div><h1>{{ __('Good work starts with clear information.') }}</h1><p>{{ __('Manage the public product catalogue, review enquiries and maintain the site settings from one secure workspace.') }}</p></div><div class="login-story-foot">{{ __('AUTHORIZED ACCESS ONLY') }}</div></section>
<main class="login-form-side" aria-labelledby="login-title"><div class="login-card"><form class="login-language-switch" method="post" action="{{ route('admin.language') }}" aria-label="{{ __('Dashboard language') }}">@csrf<button type="submit" name="locale" value="en" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}" @if(app()->getLocale() === 'en') aria-current="true" @endif>EN</button><button type="submit" name="locale" value="ar" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}" @if(app()->getLocale() === 'ar') aria-current="true" @endif>العربية</button></form><p class="admin-kicker">{{ __('Welcome back') }}</p><h2 id="login-title">{{ __('Sign in') }}</h2><p>{{ __('Use your authorized administrator account to continue.') }}</p>@if($errors->any())<div class="login-error" id="login-error" role="alert" aria-live="assertive">{{ $errors->first() }}</div>@endif
<form method="post" action="{{ route('admin.login.store') }}">@csrf<div class="login-field"><label for="email">{{ __('Email address') }}</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" autocapitalize="none" inputmode="email" spellcheck="false" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" @if($errors->has('email')) aria-describedby="login-error" @endif required></div><div class="login-field"><label for="password">{{ __('Password') }}</label><input id="password" name="password" type="password" autocomplete="current-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" required></div><label class="login-remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> {{ __('Remember me on this device') }}</label><button class="login-submit" type="submit">{{ __('Sign in to workspace') }} <span aria-hidden="true">↗</span></button></form><a class="login-back" href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('← Back to ENCAP website') }}</a></div></main></div></body></html>
