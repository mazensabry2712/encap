<?php

test('admin login page renders accessible authentication controls and language selection', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('noindex,nofollow', false)
        ->assertSee('aria-labelledby="login-title"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false)
        ->assertSee('name="remember"', false)
        ->assertSee('name="_token"', false)
        ->assertSee('login-language-switch', false)
        ->assertSee('--login-forest', false)
        ->assertSee('aria-current="true"', false);
});

test('admin login page renders Arabic copy and right-to-left direction when Arabic is selected', function () {
    $this->withSession(['admin_language' => 'ar'])
        ->get('/admin/login')
        ->assertOk()
        ->assertSee('<html lang="ar" dir="rtl"', false)
        ->assertSee('العمل الجيد يبدأ بمعلومات واضحة.')
        ->assertSee('تسجيل الدخول');
});
