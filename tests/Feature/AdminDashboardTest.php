<?php

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use App\Models\User;

test('guests are redirected from the admin dashboard to sign in', function () {
    $this->get('/admin')->assertRedirect(route('admin.login'));
});

test('authenticated users see live catalogue and enquiry summaries on the dashboard', function () {
    $category = Category::query()->create([
        'name_en' => 'Dashboard Mango',
        'name_ar' => 'مانجو الداشبورد',
        'slug' => 'dashboard-mango',
        'is_published' => true,
        'sort_order' => 1,
    ]);

    QuoteRequest::query()->create([
        'category_id' => $category->id,
        'contact_name' => 'Ahmed Buyer',
        'company_name' => 'Buyer Company',
        'email' => 'buyer@example.test',
        'country' => 'Germany',
        'message' => 'Please send product information.',
        'status' => 'new',
    ]);

    ContactMessage::query()->create([
        'name' => 'Mona Visitor',
        'email' => 'visitor@example.test',
        'subject' => 'Export question',
        'message' => 'I would like more information.',
        'status' => 'new',
    ]);

    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('كل إدارة ناجحة تبدأ من هنا.')
        ->assertSee('تصنيفات المنتجات')
        ->assertSee('طلبات عروض التصدير')
        ->assertSee('رسائل التواصل')
        ->assertSee('Ahmed Buyer')
        ->assertSee('مانجو الداشبورد')
        ->assertSee('Mona Visitor')
        ->assertSee('Export question')
        ->assertSee('إدارة الكتالوج')
        ->assertSee('إدارة بيانات الموقع ووسائل التواصل');
});


test('admin language preference defaults to Arabic and switches between Arabic and English with correct document direction', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('lang="ar" dir="rtl"', false)
        ->assertSee('تسجيل الدخول')
        ->assertSee('البريد الإلكتروني');

    $this->from('/admin/login')
        ->post('/admin/language', ['locale' => 'ar'])
        ->assertRedirect('/admin/login');

    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('lang="ar" dir="rtl"', false)
        ->assertSee('تسجيل الدخول')
        ->assertSee('البريد الإلكتروني');

    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('lang="ar" dir="rtl"', false)
        ->assertSee('نظرة عامة على لوحة الإدارة')
        ->assertSee('تصنيفات المنتجات');

    $this->get('/admin/seo')
        ->assertOk()
        ->assertSee('إعدادات SEO والموقع')
        ->assertDontSee('Gallery');

    $this->post('/admin/language', ['locale' => 'en'])->assertRedirect();

    $this->get('/admin')
        ->assertOk()
        ->assertSee('lang="en" dir="ltr"', false)
        ->assertSee('Good work starts here.');
});
