<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>ENCAP Admin Login</title>
<style>body{margin:0;min-height:100vh;display:grid;place-items:center;background:#f4f6f0;font:16px/1.5 system-ui;color:#17241b}.card{width:min(420px,calc(100% - 32px));background:white;padding:32px;border:1px solid #e1e7dc;border-radius:8px}h1{color:#153d2b}.field{display:grid;gap:6px;margin:16px 0}input{padding:12px;border:1px solid #cbd4ca;border-radius:4px;font:inherit}.button{background:#153d2b;color:white;border:0;border-radius:4px;padding:12px 16px;font:inherit;font-weight:700;width:100%;cursor:pointer}.error{color:#9c2d1b;font-size:14px}</style></head>
<body><main class="card"><a href="{{ route('home',['locale'=>'en']) }}" style="color:#153d2b;font-weight:900;letter-spacing:.15em">ENCAP.</a><h1>Admin sign in</h1><p>Sign in with an authorized administrator account.</p>
@if($errors->any())<p class="error" role="alert">{{ $errors->first() }}</p>@endif
<form method="post" action="{{ route('admin.login.store') }}">@csrf
<div class="field"><label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required></div>
<div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
<label><input type="checkbox" name="remember" value="1"> Remember me</label><p><button class="button" type="submit">Sign in</button></p></form></main></body></html>
