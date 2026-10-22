<?php

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use App\Models\SeoSetting;


test('homepage includes the ENCAP signature design and accessible navigation', function () {
    Category::query()->create([
        'name_en' => 'Homepage Citrus',
        'name_ar' => 'موالح الرئيسية',
        'slug' => 'homepage-citrus',
        'is_published' => true,
    ]);

    $this->get('/en')
        ->assertOk()
        ->assertSee('class="skip-link"', false)
        ->assertSee('assets/logo.png', false)
        ->assertSee('assets/favicone.png', false)
        ->assertSee('encap-hero', false)
        ->assertSee('encap-hero-experience', false)
        ->assertSee('data-hero-tilt', false)
        ->assertSee('data-hero-layer', false)
        ->assertSee('encap-hero-stage-ring--outer', false)
        ->assertSee('encap-hero-motion-stamp', false)
        ->assertSee('css/hero-experience.css', false)
        ->assertSee('js/hero-experience.js', false)
        ->assertSee('encap-hero-image', false)
        ->assertSee('encap-image-label-logo', false)
        ->assertSee('encap-story-brand', false)
        ->assertSee('encap-orbit-brand', false)
        ->assertSee('encap-hero-index', false)
        ->assertSee('footer-copyright-brand', false)
        ->assertSee('encap-floating-note', false)
        ->assertSee('encap-product-grid', false)
        ->assertSee('encap-process-row', false)
        ->assertSee('encap-quality-band', false)
        ->assertSee('encap-final-cta', false)
        ->assertSee('encap-service-hub', false)
        ->assertSee('/en/packing-stations', false)
        ->assertSee('encap-signature-logo', false)
        ->assertSee('encap-process-decoration-logo', false)
        ->assertSee('encap-product-placeholder-logo', false)
        ->assertSee('id="icon-sprout"', false)
        ->assertSee('js/encap-interactions.js', false)
        ->assertSee('css/site-motion.css', false)
        ->assertSee('js/site-motion.js', false);
});


test('site-wide motion assets are loaded on public English and Arabic page templates', function () {
    Category::query()->create([
        'name_en' => 'Motion Mango',
        'name_ar' => 'مانجو الحركة',
        'slug' => 'motion-mango',
        'short_description_en' => 'Seasonal Egyptian mango sourcing.',
        'short_description_ar' => 'توريد المانجو المصري حسب الموسم.',
        'description_en' => 'Contact ENCAP to confirm availability.',
        'description_ar' => 'تواصل مع ENCAP لتأكيد التوافر.',
        'image' => 'categories/motion-mango.webp',
        'is_published' => true,
    ]);

    $paths = [
        '/en', '/ar', '/en/about', '/ar/farms', '/en/packing-stations',
        '/ar/quality', '/en/export', '/ar/gallery', '/en/products', '/ar/products',
        '/en/products/motion-mango', '/ar/products/motion-mango',
        '/en/contact', '/ar/request-a-quote',
    ];

    foreach ($paths as $path) {
        $this->get($path)
            ->assertOk()
            ->assertSee('css/site-motion.css', false)
            ->assertSee('js/site-motion.js', false);
    }
});

test('admin sign-in uses the ENCAP logo and favicon assets', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('assets/logo.png', false)
        ->assertSee('assets/favicone.png', false);
});

test('product detail gallery renders accessible thumbnail controls', function () {
    \Illuminate\Support\Facades\Storage::fake('public');

    $category = Category::query()->create([
        'name_en' => 'Gallery Citrus',
        'name_ar' => 'موالح',
        'slug' => 'gallery-citrus',
        'image' => 'categories/main.webp',
        'gallery' => ['categories/gallery/one.webp', 'categories/gallery/two.webp'],
        'available_forms' => ['fresh', 'frozen'],
        'is_published' => true,
    ]);

    $this->get(route('products.show', ['locale' => 'en', 'category' => $category->slug]))
        ->assertOk()
        ->assertSee('data-gallery-main', false)
        ->assertSee('data-gallery-thumb', false)
        ->assertSee('aria-pressed="false"', false)
        ->assertSee('data-image-src', false);

    $this->get(route('products.show', ['locale' => 'ar', 'category' => $category->slug]))
        ->assertOk()
        ->assertSee('القائمة')
        ->assertSee('طازج')
        ->assertSee('مجمّد');
});

test('primary navigation is centered, bilingual, and responsive', function () {
    $englishResponse = $this->get('/en');

    $englishResponse
        ->assertOk()
        ->assertSee('primary-nav', false)
        ->assertSee('primary-links', false)
        ->assertSee('primary-dropdown-panel', false)
        ->assertSee('data-language-switcher', false)
        ->assertSee('data-language-trigger', false)
        ->assertSee('data-language-menu', false)
        ->assertSee('data-primary-mobile-nav', false)
        ->assertSee('/en/packing-stations', false)
        ->assertSee('/en/export', false)
        ->assertSee('/en/quality', false)
        ->assertSee('/en/gallery', false)
        ->assertSee('aria-expanded="false"', false)
        ->assertDontSee('data-primary-language', false)
        ->assertDontSee('topbar-note', false);

    $html = $englishResponse->getContent();
    $navStart = strpos($html, '<nav class="primary-links"');
    expect($navStart)->not->toBeFalse();

    $navEnd = strpos($html, '</nav>', $navStart);
    $desktopNavigation = substr($html, $navStart, $navEnd - $navStart);

    expect($desktopNavigation)->toMatch(
        '/<a href="' . preg_quote(route('home', ['locale' => 'en']), '/') . '"\s+aria-current="page"\s*>Home<\/a>/'
    );

    $this->get('/ar')
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee('اختيار اللغة')
        ->assertSee('aria-current="page"', false)
        ->assertSee('عملياتنا')
        ->assertSee('الرئيسية');
});


test('public typography uses legible Latin and Arabic fonts with comfortable reading sizes', function () {
    $this->get('/en')
        ->assertOk()
        ->assertSee('family=Cairo', false)
        ->assertSee('css/typography.css', false);

    $this->get('/ar')
        ->assertOk()
        ->assertSee('lang="ar"', false)
        ->assertSee('css/typography.css', false);

    expect(file_exists(public_path('css/typography.css')))->toBeTrue();

    $stylesheet = file_get_contents(public_path('css/typography.css'));

    expect($stylesheet)
        ->toContain('font-size: 16px !important')
        ->toContain('Cairo')
        ->toContain('font-size: clamp(2rem, 4.6vw, 3.5rem) !important;')
        ->toContain('font-size: clamp(1.9rem, 7.8vw, 2.45rem) !important;')
        ->toContain('text-size-adjust: 100%');
});

test('homepage hero motion uses layered parallax and respects reduced motion', function () {
    expect(file_exists(public_path('css/hero-experience.css')))->toBeTrue()
        ->and(file_exists(public_path('js/hero-experience.js')))->toBeTrue();

    $stylesheet = file_get_contents(public_path('css/hero-experience.css'));
    $script = file_get_contents(public_path('js/hero-experience.js'));

    expect($stylesheet)
        ->toContain('perspective: 1100px')
        ->toContain('@media (prefers-reduced-motion: reduce)')
        ->toContain('encap-hero-photo-drift');

    expect($script)
        ->toContain('prefers-reduced-motion')
        ->toContain('pointermove')
        ->toContain('requestAnimationFrame')
        ->toContain('data-hero-layer');
});

test('the home page uses central defaults without writing settings during a public read', function () {
    $this->get('/en')->assertOk()->assertSee('ENCAP');

    expect(SeoSetting::query()->count())->toBe(0);
});

test('bilingual company pages render in the requested language', function () {
    $this->get('/en/about')->assertOk()->assertSee('Agriculture with a clear route to market')->assertSee('content-story-brand', false)->assertSee('assets/favicone.png', false)->assertSee('encap-principles-grid', false)->assertSee('Our mission, vision & values');
    $this->get('/ar/about')->assertOk()->assertSee('dir="rtl"', false)->assertSee('عن ENCAP')->assertSee('encap-principles-grid', false)->assertSee('رسالتنا ورؤيتنا وقيمنا');
});

test('product catalogue uses numbered cards and links to category details', function () {
    Category::query()->create([
        'name_en' => 'Catalogue Mango',
        'name_ar' => 'مانجو الكتالوج',
        'slug' => 'catalogue-mango',
        'short_description_en' => 'Fresh and prepared produce enquiries.',
        'is_published' => true,
    ]);

    $this->get('/en/products')
        ->assertOk()
        ->assertSee('product-catalog-grid', false)
        ->assertSee('category-index', false)
        ->assertSee('category-image-open', false)
        ->assertSee('data-tilt', false)
        ->assertSee('/en/products/catalogue-mango', false);
});

test('product gallery cards link to interactive category detail pages', function () {
    \Illuminate\Support\Facades\Storage::fake('public');

    Category::query()->create([
        'name_en' => 'Gallery Mango',
        'name_ar' => 'مانجو المعرض',
        'slug' => 'gallery-mango',
        'image' => 'categories/gallery-mango.webp',
        'is_published' => true,
    ]);

    $this->get('/en/gallery')
        ->assertOk()
        ->assertSee('class="gallery-card" data-tilt', false)
        ->assertSee('/en/products/gallery-mango', false);
});

test('unpublished categories are not publicly accessible', function () {
    $category = Category::query()->create([
        'name_en' => 'Draft Mango',
        'name_ar' => 'مانجو مسودة',
        'slug' => 'draft-mango',
        'is_published' => false,
    ]);

    $this->get(route('products.show', ['locale' => 'en', 'category' => $category->slug]))
        ->assertNotFound();
});

test('a published category has a stable detail page and category title', function () {
    $category = Category::query()->create([
        'name_en' => 'Mango',
        'name_ar' => 'مانجو',
        'slug' => 'mango',
        'short_description_en' => 'Egyptian mango sourcing enquiries.',
        'short_description_ar' => 'طلبات توريد المانجو المصري.',
        'description_en' => 'Confirm current availability with ENCAP.',
        'description_ar' => 'تأكد من التوافر الحالي مع ENCAP.',
        'varieties_en' => 'Valencia and Navel',
        'varieties_ar' => 'فالنسيا ونافيل',
        'harvest_window_en' => 'Confirmed seasonally',
        'harvest_window_ar' => 'يتم التأكيد حسب الموسم',
        'specifications_en' => 'Grade and size confirmed per enquiry.',
        'specifications_ar' => 'يتم تأكيد الدرجة والحجم لكل طلب.',
        'is_published' => true,
    ]);

    $this->get(route('products.show', ['locale' => 'en', 'category' => 'mango']))
        ->assertOk()
        ->assertSee('<title>Mango | ENCAP</title>', false)
        ->assertSee('Confirm current availability with ENCAP.')
        ->assertSee('Product specifications')
        ->assertSee('Valencia and Navel')
        ->assertSee('Confirmed seasonally');

    $this->get(route('products.show', ['locale' => 'ar', 'category' => 'mango']))
        ->assertOk()
        ->assertSee('مواصفات المنتج')
        ->assertSee('فالنسيا ونافيل')
        ->assertSee('يتم التأكيد حسب الموسم');
});

test('robots.txt is served by Laravel and points to the sitemap', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('content-type', 'text/plain; charset=UTF-8')
        ->assertSee('Disallow: /admin')
        ->assertSee('Sitemap: ' . rtrim(config('app.url'), '/') . '/sitemap.xml');
});

test('quote form is not indexable while public product pages use the configured default', function () {
    $this->get('/en/request-a-quote')
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,follow">', false);

    $this->get('/en')
        ->assertOk()
        ->assertSee('<meta name="robots" content="index,follow">', false);
});

test('sitemap only includes published categories and is xml', function () {
    Category::query()->create(['name_en' => 'Visible Mango', 'slug' => 'visible-mango', 'is_published' => true]);
    Category::query()->create(['name_en' => 'Hidden Mango', 'slug' => 'hidden-mango', 'is_published' => false]);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('content-type', 'application/xml; charset=UTF-8')
        ->assertSee('/en/packing-stations')
        ->assertSee('/ar/packing-stations')
        ->assertSee('/en/products/visible-mango')
        ->assertDontSee('/en/products/hidden-mango');
});

test('quote enquiry is validated and saved for the admin inbox', function () {
    $category = Category::query()->create([
        'name_en' => 'Citrus',
        'slug' => 'citrus',
        'is_published' => true,
    ]);

    $response = $this->post('/en/request-a-quote', [
        'category_id' => $category->id,
        'contact_name' => 'Test Buyer',
        'company_name' => 'Example Import GmbH',
        'email' => 'buyer@example.test',
        'country' => 'Germany',
        'quantity' => '20',
        'quantity_unit' => 'metric tons',
        'product_form' => 'fresh',
        'message' => 'Please share current availability and export terms.',
    ]);

    $response->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('quote_requests', [
        'email' => 'buyer@example.test',
        'category_id' => $category->id,
        'status' => 'new',
    ]);
    expect(QuoteRequest::query()->count())->toBe(1);
});

test('contact message is saved and the honeypot blocks submissions', function () {
    $this->post('/en/contact', [
        'name' => 'Test Contact',
        'email' => 'contact@example.test',
        'subject' => 'Supply enquiry',
        'message' => 'Please contact me about your product range.',
        'website' => '',
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseHas('contact_messages', [
        'email' => 'contact@example.test',
        'status' => 'new',
    ]);
    expect(ContactMessage::query()->count())->toBe(1);

    $this->post('/en/contact', [
        'name' => 'Spam Bot',
        'email' => 'spam@example.test',
        'subject' => 'Spam',
        'message' => 'Promotional text.',
        'website' => 'filled-by-bot',
    ])->assertSessionHasErrors('website');

    expect(ContactMessage::query()->count())->toBe(1);
});


test('farm, packing station and export service pages render in English and Arabic', function () {
    $this->get('/en/farms')
        ->assertOk()
        ->assertSee('Crop production & farm operations')
        ->assertSee('encap-service-grid', false)
        ->assertSee('encap-service-steps', false)
        ->assertSee('encap-service-visual-figure', false)
        ->assertSee('encap-service-card-media', false)
        ->assertSee('images.unsplash.com/photo-1761839259494', false);

    $this->get('/ar/farms')
        ->assertOk()
        ->assertSee('الإنتاج الزراعي وإدارة المزرعة')
        ->assertSee('encap-service-grid', false)
        ->assertSee('encap-service-visual-copy', false)
        ->assertSee('images.unsplash.com/photo-1761839259494', false);

    $this->get('/en/packing-stations')
        ->assertOk()
        ->assertSee('Packing Stations')
        ->assertSee('Sorting & grading')
        ->assertSee('Pre-cooling & cold-chain coordination')
        ->assertSee('encap-service-visual-figure', false)
        ->assertSee('images.unsplash.com/photo-1767982619694', false)
        ->assertSee('assets/favicone.png', false);

    $this->get('/ar/packing-stations')
        ->assertOk()
        ->assertSee('محطات التعبئة والتجهيز')
        ->assertSee('الفرز والتصنيف')
        ->assertSee('التبريد المسبق وتنسيق سلسلة التبريد');

    $this->get('/en/export')
        ->assertOk()
        ->assertSee('Export & Logistics')
        ->assertSee('Documentation & compliance coordination')
        ->assertSee('encap-service-visual-figure', false)
        ->assertSee('images.unsplash.com/photo-1774929104515', false);

    $this->get('/ar/export')
        ->assertOk()
        ->assertSee('التصدير والخدمات اللوجستية')
        ->assertSee('تنسيق المستندات والامتثال');
});
