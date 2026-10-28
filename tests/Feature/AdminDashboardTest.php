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
        ->assertSee('إدارة بيانات الموقع ووسائل التواصل')
        ->assertSee(route('admin.images.prepare'), false);
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


test('authenticated administrators can prepare image storage from the dashboard without shell access', function () {
    \Illuminate\Support\Facades\Storage::fake('public');
    \Illuminate\Support\Facades\Storage::fake('encap_images');
    \Illuminate\Support\Facades\Storage::disk('public')->put('categories/legacy-photo.jpg', 'legacy upload');

    $jpeg = "\xFF\xD8\xFF" . str_repeat('A', 6000);
    \Illuminate\Support\Facades\Http::preventStrayRequests();
    \Illuminate\Support\Facades\Http::fake([
        'https://images.unsplash.com/*' => \Illuminate\Support\Facades\Http::response($jpeg, 200, ['Content-Type' => 'image/jpeg']),
        'https://images.pexels.com/*' => \Illuminate\Support\Facades\Http::response($jpeg, 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $this->actingAs(User::factory()->create());

    $offset = 0;
    $result = null;
    do {
        $response = $this->post(route('admin.images.prepare'), ['offset' => $offset]);
        $response->assertRedirect(route('admin.dashboard'));
        $result = session('imageLibraryResult');
        expect($result)->toBeArray();
        $offset = $result['next_offset'];
    } while (! $result['finished']);

    expect($result['complete'])->toBeTrue()
        ->and($result['downloaded'])->toBe(20)
        ->and($result['copied'])->toBe(1);

    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('citrus-orchard-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('frozen-berries-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('mango-replacement-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('product-onions-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('product-potatoes-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('farm-cultivation-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('product-grapes-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('produce-mix-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('product-garlic-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('product-pomegranates-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('product-peppers-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('product-strawberries-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('orange-orchard-hero-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('orchard-harvest-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('product-citrus-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('potato-sorting-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('produce-packing-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('crate-labels-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('export-crates-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('cargo-port-2026.jpg');
    \Illuminate\Support\Facades\Storage::disk('encap_images')->assertExists('categories/legacy-photo.jpg');
    \Illuminate\Support\Facades\Storage::disk('public')->assertExists('categories/legacy-photo.jpg');
});
