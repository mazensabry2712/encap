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
        'available_forms' => ['Fresh', 'Frozen'],
        'is_published' => true,
    ]);

    $this->get('/en')
        ->assertOk()
        ->assertSee('class="skip-link"', false)
        ->assertSee('encap-img/logo.png', false)
        ->assertSee('encap-img/favicon.png', false)
        ->assertSee('encap-hero', false)
        ->assertSee('encap-hero-experience', false)
        ->assertSee('data-hero-tilt', false)
        ->assertSee('data-hero-layer', false)
        ->assertSee('encap-hero-stage-ring--outer', false)
        ->assertSee('encap-hero-motion-stamp', false)
        ->assertSee('css/hero-experience.css', false)
        ->assertSee('js/hero-experience.js', false)
        ->assertSee('encap-hero-image', false)
        ->assertSee(parse_url(\App\Support\EncapImage::url('orange-orchard-hero-2026.jpg', 'https://images.pexels.com/photos/10970006/pexels-photo-10970006.jpeg?auto=compress&cs=tinysrgb&w=2000'), PHP_URL_PATH), false)
        ->assertSee('encap-image-label-logo', false)
        ->assertSee('encap-story-brand', false)
        ->assertSee('encap-orbit-brand', false)
        ->assertSee('encap-hero-index', false)
        ->assertSee('encap-footer-copyright', false)
        ->assertSee('encap-floating-note', false)
        ->assertSee('encap-product-grid', false)
        ->assertSee('encap-process-row', false)
        ->assertSee('03 / How we work')
        ->assertSee('04 / Quality in every detail')
        ->assertSee('class="encap-final-number">05<span', false)
        ->assertSee('LET’S TALK')
        ->assertSee('encap-quality-band', false)
        ->assertSee('encap-final-cta', false)
        ->assertSee('encap-frozen-feature', false)
        ->assertSee('Frozen produce, specified for your market.')
        ->assertSee(parse_url(\App\Support\EncapImage::url('frozen-berries-2026.jpg', 'https://images.pexels.com/photos/9814690/pexels-photo-9814690.jpeg?auto=compress&cs=tinysrgb&w=1260'), PHP_URL_PATH), false)
        ->assertSee('Frozen berries prepared for temperature-controlled export supply.')
        ->assertSee('Browse frozen products')
        ->assertSee('href="/en/frozen-products"', false)
        ->assertDontSee('href="/en/request-a-quote?form=frozen"', false)
        ->assertDontSee('We welcome enquiries for frozen fruit and vegetables.')
        ->assertDontSee('encap-frozen-product', false)
        ->assertDontSee('Profile photos can be added to these contact cards later.')
        ->assertSee('encap-footer', false)
        ->assertSee('encap-footer-business-grid', false)
        ->assertSee('href="https://bahnes.com"', false)
        ->assertSee('BAHNES IT SOLUTIONS')
        ->assertSee('footer-agency-credit', false)
        ->assertSee('encap-footer-bottom-separator', false)
        ->assertSee('+966 56 830 9656', false)
        ->assertSee('+20 106 011 3074', false)
        ->assertSee('شركة حدائق الشمال', false)
        ->assertSee('encap-service-hub', false)
        ->assertSee('/en/packing-stations', false)
        ->assertSee('encap-signature-logo', false)
        ->assertSee('encap-process-decoration-logo', false)
        ->assertDontSee('encap-product-placeholder-logo', false)
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
        '/en', '/ar', '/en/about', '/ar/farms', '/en/companies', '/ar/companies', '/en/packing-stations',
        '/ar/quality', '/en/export', '/en/products', '/ar/products',
        '/en/products/motion-mango', '/ar/products/motion-mango',
        '/en/companies/hadaeq-al-shamal', '/ar/companies/hadaeq-al-shamal',
        '/en/contact', '/ar/request-a-quote',
    ];

    foreach ($paths as $path) {
        $this->get($path)
            ->assertOk()
            ->assertSee('css/site-motion.css', false)
            ->assertSee('js/site-motion.js', false);
    }
});

test('quality pages explain reference frameworks without claiming ENCAP certification', function () {
    foreach (['/en/quality', '/ar/quality'] as $path) {
        $this->get($path)
            ->assertOk()
            ->assertSee('encap-quality-frameworks', false)
            ->assertSee('BRCGS Food Safety (BRC)')
            ->assertSee('GLOBALG.A.P.')
            ->assertSee('ISO 9001')
            ->assertSee('GRASP')
            ->assertSee('SEDEX / SMETA')
            ->assertDontSee('decoding="async"> / 04', false);
    }
});


test('admin sign-in uses the ENCAP logo and favicon assets', function () {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('encap-img/logo.png', false)
        ->assertSee('encap-img/favicon.png', false);
});


test('mango uses one curated stock photo instead of dashboard-uploaded photos', function () {
    \Illuminate\Support\Facades\Storage::fake('public');
    \Illuminate\Support\Facades\Storage::fake('encap_images');

    \Illuminate\Support\Facades\Storage::disk('encap_images')->put(
        'categories/mango-dashboard-upload.jpg',
        'old mango image'
    );
    \Illuminate\Support\Facades\Storage::disk('encap_images')->put(
        'categories/gallery/mango-dashboard-gallery.jpg',
        'old mango gallery image'
    );

    $category = Category::query()->create([
        'name_en' => 'Mango',
        'name_ar' => 'مانجو',
        'slug' => 'mango',
        'image' => 'categories/mango-dashboard-upload.jpg',
        'gallery' => ['categories/gallery/mango-dashboard-gallery.jpg'],
        'is_published' => true,
    ]);

    $images = \App\Support\EncapProductImage::galleryUrls($category);

    expect($images)
        ->toHaveCount(1)
        ->and($images[0])->toContain('images.pexels.com/photos/29782357');

    $this->get('/ar/products/mango')
        ->assertOk()
        ->assertSee('photos/29782357', false)
        ->assertDontSee('categories/mango-dashboard-upload.jpg')
        ->assertDontSee('categories/gallery/mango-dashboard-gallery.jpg')
        ->assertDontSee('data-total-images=', false);
});


test('product detail gallery renders accessible thumbnail controls', function () {
    \Illuminate\Support\Facades\Storage::fake('public');
    \Illuminate\Support\Facades\Storage::fake('encap_images');

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
        ->assertSee('data-gallery-counter', false)
        ->assertSee('data-total-images="3"', false)
        ->assertSee('data-gallery-index="1"', false)
        ->assertSee('data-gallery-index="3"', false)
        ->assertSee('aria-pressed="true"', false)
        ->assertSee('aria-pressed="false"', false)
        ->assertSee('data-image-src', false);

    $this->get(route('products.show', ['locale' => 'ar', 'category' => $category->slug]))
        ->assertOk()
        ->assertSee('القائمة')
        ->assertSee('طازج')
        ->assertSee('مجمّد');
});

test('group companies have a separate localized page from operations', function () {
    $labels = [
        'en' => 'Our Companies',
        'ar' => 'شركاتنا',
        'fr' => 'Nos sociétés',
        'ru' => 'Наши компании',
        'tr' => 'Şirketlerimiz',
    ];

    foreach ($labels as $locale => $label) {
        $this->get('/'.$locale.'/companies')
            ->assertOk()
            ->assertSee('lang="'.$locale.'"', false)
            ->assertSee($label)
            ->assertSee('encap-catalog-hero', false)
            ->assertSee('encap-footer-group-company-card', false);
    }

    $this->get('/en/companies')
        ->assertOk()
        ->assertSee('Group Companies')
        ->assertSee('Hadaeq Al-Shamal Company')
        ->assertSee('ENCAP subsidiary')
        ->assertSee('Aljouf - Saudia');

    $this->get('/ar/companies')
        ->assertOk()
        ->assertSee('شركة حدائق الشمال')
        ->assertSee('عملياتنا')
        ->assertSee('شركاتنا');

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertSee('/en/companies', false)
        ->assertSee('/ar/companies', false);
});


test('group company catalogue and detail pages use the public product catalogue experience', function () {
    $this->get('/ar/companies')
        ->assertOk()
        ->assertSee('encap-catalog-hero', false)
        ->assertSee('encap-catalog-summary-band', false)
        ->assertSee('encap-product-grid', false)
        ->assertSee('شركة حدائق الشمال')
        ->assertSee('/ar/companies/hadaeq-al-shamal', false)
        ->assertSee('الشركات المنشورة');

    $this->get('/ar/companies/hadaeq-al-shamal')
        ->assertOk()
        ->assertSee('product-detail', false)
        ->assertSee('شركة حدائق الشمال')
        ->assertSee('نبذة عن الشركة')
        ->assertSee('الجوف، المملكة العربية السعودية')
        ->assertSee('encap-img/favicon.png', false);

    $hidden = \App\Models\Company::query()->create([
        'name_en' => 'Hidden Company',
        'name_ar' => 'شركة مخفية',
        'slug' => 'hidden-company',
        'is_published' => false,
        'sort_order' => 99,
    ]);

    $this->get('/ar/companies')
        ->assertOk()
        ->assertDontSee('شركة مخفية');

    $this->get('/ar/companies/hidden-company')->assertNotFound();
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
        ->assertSee('Our Operations')
        ->assertSee('Our Companies')
        ->assertSee('primary-mobile-operations-group', false)
        ->assertSee('/en/companies', false)
        ->assertSee('/en/packing-stations', false)
        ->assertSee('/en/export', false)
        ->assertSee('/en/quality', false)
        ->assertDontSee('/en/gallery', false)
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
        ->assertSee('شركاتنا')
        ->assertSee('/ar/companies', false)
        ->assertSee('/ar/about', false)
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

test('the home page uses central defaults and social preview metadata without writing settings during a public read', function () {
    $this->get('/en')
        ->assertOk()
        ->assertSee('ENCAP')
        ->assertSee('property="og:image"', false)
        ->assertSee('name="twitter:card" content="summary_large_image"', false);

    expect(SeoSetting::query()->count())->toBe(0);
});

test('the full Arabic company name is prominent across supported locales and structured data', function () {
    $companyName = 'الشركة المصرية الوطنية للإنتاج الزراعي';

    foreach (['en', 'ar', 'fr', 'ru', 'tr'] as $locale) {
        $this->get('/'.$locale)
            ->assertOk()
            ->assertSee('encap-company-name-strip', false)
            ->assertSee('footer-legal-name', false)
            ->assertSee('alt="ENCAP"', false)
            ->assertDontSee('primary-brand-lockup', false)
            ->assertDontSee('encap-company-name-band', false)
            ->assertSee($companyName)
            ->assertSee('legalName', false)
            ->assertSee('alternateName', false);
    }

    $this->get('/ar')
        ->assertOk()
        ->assertSee('encap-company-name-strip', false)
        ->assertSee('footer-legal-name', false)
        ->assertDontSee('primary-brand-lockup', false)
        ->assertDontSee('encap-company-name-band', false);
});


test('footer closing signature groups ENCAP legal text with the Bahnes developer link', function () {
    $stylesheet = file_get_contents(public_path('css/encap-redesign.css'));

    expect($stylesheet)
        ->toContain('.footer.encap-footer .encap-footer-bottom{')
        ->toContain('.footer.encap-footer .encap-footer-bottom-legal{')
        ->toContain('.footer.encap-footer .footer-agency-credit:before')
        ->toContain('@media(max-width:600px)');

    $this->get('/ar')
        ->assertOk()
        ->assertSee('encap-footer-bottom', false)
        ->assertSee('encap-footer-bottom-separator', false)
        ->assertSee('جميع الحقوق محفوظة.')
        ->assertSee('الشركة المصرية الوطنية للإنتاج الزراعي')
        ->assertSee('تصميم وتطوير بواسطة')
        ->assertSee('BAHNES IT SOLUTIONS')
        ->assertSee('href="https://bahnes.com"', false);
});

test('the company legal name appears in a static strip above the sticky navigation', function () {
    $stylesheet = file_get_contents(public_path('css/navigation.css'));

    expect($stylesheet)
        ->toContain('height:118px')
        ->toContain('min-height:100px')
        ->toContain('flex:0 0 32px')
        ->toContain('.encap-company-name-strip__text')
        ->not->toContain('encap-company-marquee');

    expect($stylesheet)->not->toContain('primary-brand-lockup');

    $html = $this->get('/ar')
        ->assertOk()
        ->assertSee('encap-company-name-strip', false)
        ->assertDontSee('company-marquee.js', false)
        ->getContent();

    $stripStart = strpos($html, 'class="encap-company-name-strip"');
    $stripEnd = strpos($html, 'class="container primary-nav"', $stripStart);
    $stripMarkup = substr($html, $stripStart, $stripEnd - $stripStart);

    expect(substr_count($stripMarkup, 'الشركة المصرية الوطنية للإنتاج الزراعي'))->toBe(1)
        ->and($stripMarkup)->toContain('encap-company-name-strip__text')
        ->and($stripMarkup)->not->toContain('data-company-marquee-track')
        ->and($stripMarkup)->not->toContain('data-company-marquee-source');

    $headerStart = strpos($html, '<header class="site-header primary-header">');
    $headerEnd = strpos($html, '</header>', $headerStart);
    $stripStart = strpos($html, 'class="encap-company-name-strip"', $headerStart);
    $navStart = strpos($html, 'class="container primary-nav"', $headerStart);

    expect($headerStart)->not->toBeFalse()
        ->and($headerEnd)->not->toBeFalse()
        ->and($stripStart)->toBeGreaterThan($headerStart)
        ->and($navStart)->toBeGreaterThan($stripStart)
        ->and($headerEnd)->toBeGreaterThan($navStart);
});

test('bilingual company pages render the About supply story and principles', function () {
    $this->get('/en/about')
        ->assertOk()
        ->assertSee('Agriculture with a clear route to market')
        ->assertSee('encap-about-hero', false)
        ->assertSee('encap-about-journey', false)
        ->assertSee('One connected journey, from origin to market')
        ->assertSee('encap-about-practice', false)
        ->assertSee('encap-about-principles', false)
        ->assertSee('encap-principles-grid', false)
        ->assertSee('Our mission, vision & values')
        ->assertSee('encap-about-final-cta', false)
        ->assertSee('encap-shared-brand-core', false)
        ->assertSee('encap-img/favicon.png')
        ->assertSee('Illustrative stock photography.');

    $this->get('/ar/about')
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee('عن ENCAP')
        ->assertSee('encap-about-hero', false)
        ->assertSee('encap-about-journey', false)
        ->assertSee('رحلة واحدة مترابطة من المصدر إلى السوق')
        ->assertSee('encap-principles-grid', false)
        ->assertSee('رسالتنا ورؤيتنا وقيمنا')
        ->assertSee('صور توضيحية من مصادر خارجية');
});

test('About previews only published product categories with main images', function () {
    foreach (range(1, 5) as $number) {
        Category::query()->create([
            'name_en' => 'About Fruit ' . $number,
            'name_ar' => 'فاكهة ' . $number,
            'slug' => 'about-fruit-' . $number,
            'image' => 'categories/about-fruit-' . $number . '.webp',
            'is_published' => true,
        ]);
    }

    Category::query()->create([
        'name_en' => 'Hidden About Fruit',
        'name_ar' => 'فاكهة مخفية',
        'slug' => 'hidden-about-fruit',
        'image' => 'categories/hidden-about-fruit.webp',
        'is_published' => false,
    ]);

    $this->get('/en/about')
        ->assertOk()
        ->assertSee('encap-about-catalog-grid', false)
        ->assertSee('about-fruit-1.webp')
        ->assertSee('about-fruit-4.webp')
        ->assertDontSee('hidden-about-fruit.webp')
        ->assertDontSee('about-fruit-5.webp');
});

test('product catalogue uses the redesigned hero and data-backed category cards', function () {
    Category::query()->create([
        'name_en' => 'Catalogue Mango',
        'name_ar' => 'مانجو الكتالوج',
        'slug' => 'catalogue-mango',
        'short_description_en' => 'Fresh and prepared produce enquiries.',
        'short_description_ar' => 'استفسارات عن المنتجات الطازجة والمجهزة.',
        'is_published' => true,
    ]);

    $this->get('/en/products')
        ->assertOk()
        ->assertSee('encap-catalog-hero', false)
        ->assertSee(\App\Support\EncapImage::url('citrus-orchard-2026.jpg', 'https://images.unsplash.com/photo-1705573382809-bbf1a337746f?fit=crop&fm=jpg&w=1000&q=85'))
        ->assertDontSee('field-origin-2026.jpg', false)
        ->assertDontSee('photo-1775931438023', false)
        ->assertSee(\App\Support\EncapImage::url('produce-mix-2026.jpg', 'https://images.unsplash.com/photo-1764390745408-74f0dbbb5bca?fit=crop&fm=jpg&w=800&q=85'))
        ->assertSee('encap-catalog-cta-image', false)
        ->assertSee('encap-shared-brand-core', false)
        ->assertSee('encap-img/favicon.png')
        ->assertSee('encap-catalog-grid', false)
        ->assertDontSee('encap-catalog-heading-number', false)
        ->assertDontSee('encap-catalog-card-index', false)
        ->assertDontSee('encap-catalog-cta-index', false)
        ->assertSee('data-tilt', false)
        ->assertSee('/en/products/catalogue-mango', false)
        ->assertSee('Published categories');

    $this->get('/ar/products')
        ->assertOk()
        ->assertSee('منتجات للأسواق العالمية')
        ->assertSee('مانجو الكتالوج')
        ->assertSee('encap-catalog-hero', false);
});

test('standalone gallery page is removed in favour of the product catalogue', function () {
    $this->get('/en/gallery')->assertNotFound();
    $this->get('/ar/gallery')->assertNotFound();
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

test('product detail pages use category-specific copy and visibility settings', function () {
    $category = Category::query()->create([
        'name_en' => 'Configured Mango',
        'name_ar' => 'مانجو مخصصة',
        'slug' => 'configured-mango',
        'short_description_ar' => 'وصف مخصص للمانجو.',
        'overview_heading_ar' => 'تفاصيل توريد مخصصة',
        'enquiry_note_heading_ar' => 'ملاحظة للمستورد',
        'enquiry_note_text_ar' => 'اكتب الكمية والوجهة المطلوبة.',
        'bottom_cta_heading_ar' => 'ابدأ طلب التوريد',
        'bottom_cta_text_ar' => 'حدد احتياجات السوق.',
        'show_product_images' => false,
        'show_enquiry_note' => true,
        'show_description' => true,
        'show_available_forms' => false,
        'show_packaging' => false,
        'show_specifications' => false,
        'show_bottom_cta' => true,
        'is_published' => true,
    ]);

    $this->get(route('products.show', ['locale' => 'ar', 'category' => $category->slug]))
        ->assertOk()
        ->assertSee('وصف مخصص للمانجو.')
        ->assertSee('تفاصيل توريد مخصصة')
        ->assertSee('ملاحظة للمستورد')
        ->assertSee('اكتب الكمية والوجهة المطلوبة.')
        ->assertSee('ابدأ طلب التوريد')
        ->assertSee('حدد احتياجات السوق.')
        ->assertDontSee('data-gallery-main', false)
        ->assertDontSee('Available formats')
        ->assertDontSee('product-specifications', false);
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
        ->assertDontSee('Disallow: /en/request-a-quote')
        ->assertDontSee('Disallow: /ar/request-a-quote')
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

test('all supported locales expose canonical and hreflang metadata', function () {
    foreach (['en', 'ar', 'fr', 'ru', 'tr'] as $locale) {
        $response = $this->get('/'.$locale);
        $response->assertOk()
            ->assertSee('<link rel="canonical" href="'.rtrim(config('app.url'), '/').'/'.$locale.'">', false)
            ->assertSee('hreflang="en"', false)
            ->assertSee('hreflang="ar"', false)
            ->assertSee('hreflang="fr"', false)
            ->assertSee('hreflang="ru"', false)
            ->assertSee('hreflang="tr"', false)
            ->assertSee('hreflang="x-default"', false);
    }
});

test('French Russian and Turkish homepage and catalogue headlines are translated', function () {
    $expected = [
        'fr' => ['De la terre égyptienne à la table du monde.', 'Trouvez la bonne catégorie de produits.'],
        'ru' => ['С египетской земли — к столам всего мира.', 'Выберите подходящую категорию продукции.'],
        'tr' => ['Mısır toprağından dünyanın sofralarına.', 'Doğru ürün kategorisini bulun.'],
    ];

    foreach ($expected as $locale => [$homeHeadline, $catalogueHeadline]) {
        $this->get('/'.$locale)
            ->assertOk()
            ->assertSee($homeHeadline)
            ->assertDontSee('From Egyptian soil to the world’s table.');

        $this->get('/'.$locale.'/products')
            ->assertOk()
            ->assertSee($catalogueHeadline)
            ->assertDontSee('Find the right product category.');
    }
});

test('XML sitemap includes all supported public locales', function () {
    Category::query()->create([
        'name_en' => 'Sitemap Mango',
        'name_ar' => 'مانجو خريطة الموقع',
        'slug' => 'sitemap-mango',
        'description_ar' => 'وصف عربي للمانجو.',
        'is_published' => true,
    ]);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('content-type', 'application/xml; charset=UTF-8')
        ->assertSee('/en')
        ->assertSee('/ar')
        ->assertSee('/fr')
        ->assertSee('/ru')
        ->assertSee('/tr')
        ->assertSee('/fr/products/sitemap-mango')
        ->assertSee('hreflang="fr"', false)
        ->assertSee('hreflang="ru"', false)
        ->assertSee('hreflang="tr"', false);
});

test('frozen supply section uses direct buyer-focused copy in both locales', function () {
    $this->get('/en')
        ->assertOk()
        ->assertSee('Frozen produce, specified for your market.')
        ->assertSee('Tell us the product, format, volume and destination.')
        ->assertSee('Supply built around your specification.');

    $this->get('/ar')
        ->assertOk()
        ->assertSee('توريد المجمدات وفق مواصفات سوقك.')
        ->assertSee('حدد الصنف وشكل التجهيز والكمية والوجهة، وسننسق معك متطلبات التوريد والتعبئة والتصدير من البداية.')
        ->assertSee('توريد يبدأ من مواصفاتك.')
        ->assertSee('تصفح المجمدات')
        ->assertSee('href="/ar/frozen-products"', false)
        ->assertDontSee('ENCAP / FROZEN PRODUCE');
});

test('quote form preselects frozen when explicitly requested in both locales', function () {
    foreach (['/en/request-a-quote?form=frozen', '/ar/request-a-quote?form=frozen'] as $path) {
        $this->get($path)
            ->assertOk()
            ->assertSee('value="frozen" selected', false);
    }

    $this->get('/en/request-a-quote?form=unsupported')
        ->assertOk()
        ->assertDontSee('value="frozen" selected', false);
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

test('contact page shows the full bilingual enquiry experience', function () {
    $this->get('/en/contact')
        ->assertOk()
        ->assertSee('encap-contact-hero', false)
        ->assertSee('Let’s make the next step clear.')
        ->assertSee('encap-contact-form', false)
        ->assertSee('encap-contact-details', false)
        ->assertSee('encap-contact-next', false)
        ->assertSee('name="name"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="subject"', false)
        ->assertSee('name="message"', false);

    $this->get('/ar/contact')
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee('encap-contact-hero', false)
        ->assertSee('دعنا نوضح الخطوة التالية.')
        ->assertSee('encap-contact-next', false)
        ->assertSee('استفسارات عامة')
        ->assertSee('سواء كنت تستكشف منتجاتنا أو تخطط لطلب توريد، أخبرنا باحتياجاتك والوجهة المطلوبة.');
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
        ->assertDontSee('encap-service-number', false)
        ->assertDontSee('encap-service-step-number', false)
        ->assertSee(\App\Support\EncapImage::url('orchard-harvest-2026.jpg', 'https://images.unsplash.com/photo-1643138411076-238fda8c1e1d?fit=crop&fm=jpg&w=1400&q=85'));

    $this->get('/ar/farms')
        ->assertOk()
        ->assertSee('الإنتاج الزراعي وإدارة المزرعة')
        ->assertSee('encap-service-grid', false)
        ->assertSee('encap-service-visual-copy', false)
        ->assertDontSee('encap-service-number', false)
        ->assertDontSee('encap-service-step-number', false)
        ->assertSee(\App\Support\EncapImage::url('orchard-harvest-2026.jpg', 'https://images.unsplash.com/photo-1643138411076-238fda8c1e1d?fit=crop&fm=jpg&w=1400&q=85'));

    $this->get('/en/packing-stations')
        ->assertOk()
        ->assertSee('Packing Stations')
        ->assertSee('Sorting & grading')
        ->assertSee('Pre-cooling & cold-chain coordination')
        ->assertSee('encap-service-visual-figure', false)
        ->assertSee(\App\Support\EncapImage::url('potato-sorting-2026.jpg', 'https://images.pexels.com/photos/2889093/pexels-photo-2889093.jpeg?auto=compress&cs=tinysrgb&w=1400'))
        ->assertSee('encap-img/favicon.png', false)
        ->assertDontSee('encap-service-number', false)
        ->assertDontSee('encap-service-step-number', false);

    $this->get('/ar/packing-stations')
        ->assertOk()
        ->assertSee('محطات التعبئة والتجهيز')
        ->assertSee('الفرز والتصنيف')
        ->assertSee('التبريد المسبق وتنسيق سلسلة التبريد')
        ->assertDontSee('encap-service-number', false)
        ->assertDontSee('encap-service-step-number', false);

    $this->get('/en/export')
        ->assertOk()
        ->assertSee('Export & Logistics')
        ->assertSee('Documentation & compliance coordination')
        ->assertSee('encap-service-visual-figure', false)
        ->assertSee(\App\Support\EncapImage::url('cargo-port-2026.jpg', 'https://images.unsplash.com/photo-1759272840538-ae4b07214c71?fit=crop&fm=jpg&w=1400&q=85'))
        ->assertDontSee('encap-service-number', false)
        ->assertDontSee('encap-service-step-number', false);

    $this->get('/ar/export')
        ->assertOk()
        ->assertSee('التصدير والخدمات اللوجستية')
        ->assertSee('تنسيق المستندات والامتثال')
        ->assertDontSee('encap-service-number', false)
        ->assertDontSee('encap-service-step-number', false);
});

test('homepage capability cards use photographic backgrounds with blur and contrast overlays', function () {
    $response = $this->get('/en')
        ->assertOk()
        ->assertSee('--encap-service-card-image: url(', false);

    expect(substr_count($response->getContent(), '--encap-service-card-image: url('))->toBe(3);

    $stylesheet = file_get_contents(public_path('css/encap-redesign.css'));

    expect($stylesheet)
        ->toContain('background-image:var(--encap-service-card-image)')
        ->toContain('filter:blur(3px)')
        ->toContain('.encap-service-hub-card:after')
        ->toContain('rgba(4,22,15,.92)');
});

test('mobile navigation keeps the export quote action pinned outside the scrollable links', function () {
    $response = $this->get('/ar')->assertOk();
    $html = $response->getContent();

    expect($html)
        ->toContain('class="primary-mobile-panel-links"')
        ->toContain('class="primary-mobile-quote"');

    $stylesheet = file_get_contents(public_path('css/navigation.css'));

    expect($stylesheet)
        ->toContain('grid-template-rows:minmax(0,1fr) auto')
        ->toContain('.primary-mobile-panel-links{display:grid;align-content:start;')
        ->toContain('max-height:min(72vh,620px);overflow:hidden');
});

test('Arabic contact and quote pages show usable regional phone links and translated helpers', function () {
    $this->get('/ar/contact')
        ->assertOk()
        ->assertSee('tel:+966568309656', false)
        ->assertSee('tel:+201060113074', false)
        ->assertSee('تواصل مباشرة مع فرقنا الإقليمية أو استخدم النموذج أدناه.')
        ->assertSee('يساعدنا تحديد المنتج والكمية وشكل المنتج والتعبئة والوجهة على فهم متطلباتك.')
        ->assertDontSee('ستظهر بيانات التواصل هنا بعد إدخالها من لوحة الإدارة.');

    $this->get('/ar/request-a-quote')
        ->assertOk()
        ->assertSee('placeholder="مثال: طن متري"', false)
        ->assertDontSee('e.g. metric tons');
});

test('site-wide heading treatments preserve contrast on dark photographic sections', function () {
    $stylesheet = file_get_contents(public_path('css/encap-redesign.css'));

    expect($stylesheet)
        ->toContain('section:not(.encap-hero):not(.encap-catalog-hero):not(.encap-catalog-cta-section):not(.encap-contact-hero)')
        ->toContain('not(.encap-quality-band):not(.encap-frozen-feature)')
        ->toContain('not(.encap-final-cta):not(.encap-about-final-cta)')
        ->toContain('not(.encap-about-principles):not(.dark-feature)')
        ->toContain('.encap-contact-hero h1{max-width:720px')
        ->toContain('.encap-catalog-cta h2{max-width:650px;margin:14px 0 14px;color:#fff')
        ->toContain('.encap-catalog-cta-orbit>.encap-shared-brand-core{position:relative;inset:auto;display:flex;align-items:center;justify-content:center;flex-direction:column;margin:auto;width:104px;aspect-ratio:1;}')
        ->toContain('.encap-frozen-feature .encap-frozen-copy h2')
        ->not->toContain('body.site-body main section :is(h1,h2){font-weight:800!important;background:');
});

test('catalogue and product details resolve crop-specific photos with or without local image assets', function () {
    $slugs = [
        'mango', 'citrus', 'grapes', 'potatoes', 'onions',
        'garlic', 'pomegranates', 'peppers', 'strawberries',
        'other-fruits-vegetables',
    ];
    $categories = [];

    foreach ($slugs as $index => $slug) {
        $categories[$slug] = Category::query()->create([
            'name_en' => ucwords(str_replace('-', ' ', $slug)),
            'name_ar' => 'منتج ' . ($index + 1),
            'slug' => $slug,
            'is_published' => true,
            'show_product_images' => true,
            'sort_order' => $index + 1,
        ]);
    }

    $catalogue = $this->get('/en/products')->assertOk();

    foreach ($categories as $category) {
        $catalogue->assertSee(parse_url(\App\Support\EncapProductImage::url($category), PHP_URL_PATH), false);
    }

    $this->get('/ar/products/grapes')
        ->assertOk()
        ->assertSee(parse_url(\App\Support\EncapProductImage::url($categories['grapes']), PHP_URL_PATH), false);
});

test('an uploaded category image remains preferred over a stock fallback', function () {
    $category = Category::query()->create([
        'name_en' => 'Uploaded Grapes',
        'name_ar' => 'عنب بصور مرفوعة',
        'slug' => 'grapes',
        'image' => 'categories/uploaded-grapes.webp',
        'is_published' => true,
        'sort_order' => 1,
    ]);

    $expectedImage = \App\Support\EncapImage::url($category->image);

    $this->get('/en/products')
        ->assertOk()
        ->assertSee($expectedImage, false)
        ->assertDontSee('images.unsplash.com/photo-1626964146632', false);
});

test('a dashboard-uploaded legacy mango photo is replaced by the curated image', function () {
    \Illuminate\Support\Facades\Storage::fake('public');
    \Illuminate\Support\Facades\Storage::fake('encap_images');

    Category::query()->create([
        'name_en' => 'Mango',
        'name_ar' => 'مانجو',
        'slug' => 'mango',
        'image' => 'categories/ndGv4yhjoLuLommuuXX34GErfjuIUG1wJyiMaDef.jpg',
        'gallery' => ['categories/gallery/mango-detail.jpg'],
        'is_published' => true,
        'show_product_images' => true,
        'sort_order' => 1,
    ]);

    $this->get('/ar/products/mango')
        ->assertOk()
        ->assertSee('photos/29782357', false)
        ->assertDontSee('ndGv4yhjoLuLommuuXX34GErfjuIUG1wJyiMaDef.jpg', false)
        ->assertDontSee('categories/gallery/mango-detail.jpg', false)
        ->assertDontSee('/encap-img/https://', false);

    $this->get('/ar/products')
        ->assertOk()
        ->assertSee('photos/29782357', false)
        ->assertDontSee('ndGv4yhjoLuLommuuXX34GErfjuIUG1wJyiMaDef.jpg', false);
});


test('French Russian and Turkish public pages are localized while editable SEO stays Arabic and English', function () {
    $locales = [
        'fr' => [
            'about' => 'Une agriculture reliée clairement aux marchés',
            'farms' => 'Tout commence dans les champs',
            'packing-stations' => 'De la réception des récoltes à la préparation pour l’expédition',
            'quality' => 'La qualité est intégrée à chaque étape.',
            'export' => 'Une coordination des exportations adaptée à votre marché',
            'product' => 'Des produits pour les marchés mondiaux',
        ],
        'ru' => [
            'about' => 'Сельское хозяйство с понятным выходом на рынок',
            'farms' => 'Всё начинается в поле',
            'packing-stations' => 'От приёмки урожая до подготовки к отправке',
            'quality' => 'Качество закладывается на каждом этапе.',
            'export' => 'Координация экспорта с учётом вашего рынка',
            'product' => 'Продукция для мировых рынков',
        ],
        'tr' => [
            'about' => 'Pazara açık bir yol sunan tarım',
            'farms' => 'Tarladan başlayarak',
            'packing-stations' => 'Gelen üründen sevkiyata hazır hazırlığa',
            'quality' => 'Kalite her aşamanın bir parçasıdır.',
            'export' => 'Pazarınıza göre ihracat koordinasyonu',
            'product' => 'Küresel pazarlar için ürünler',
        ],
    ];

    foreach ($locales as $locale => $phrases) {
        $this->get('/' . $locale . '/about')
            ->assertOk()
            ->assertSee('lang="' . $locale . '"', false)
            ->assertSee($phrases['about'])
            ->assertSee($phrases['farms'] === '' ? '' : '/'. $locale . '/farms', false)
            ->assertSee('/' . $locale . '/products', false)
            ->assertSee('hreflang="' . $locale . '"', false);

        foreach (['farms', 'packing-stations', 'quality', 'export'] as $page) {
            $this->get('/' . $locale . '/' . $page)
                ->assertOk()
                ->assertSee($phrases[$page]);
        }

        $this->get('/' . $locale)
            ->assertOk()
            ->assertSee('lang="' . $locale . '"', false)
            ->assertSee('href="' . route('home', ['locale' => $locale]) . '"', false)
            ->assertSee('hreflang="en"', false)
            ->assertSee('hreflang="ar"', false)
            ->assertSee('hreflang="' . $locale . '"', false);

        $this->get('/' . $locale . '/products')
            ->assertOk()
            ->assertSee($phrases['product']);

        $this->get('/' . $locale . '/contact')->assertOk();
        $this->get('/' . $locale . '/request-a-quote')->assertOk();
    }
});


test('social link previews consistently use the ENCAP logo instead of produce photography', function () {
    $html = $this->get('/en')->assertOk()->getContent();

    expect($html)
        ->toMatch('/<meta property="og:image" content="[^"]*\/encap-img\/logo\.png"/')
        ->and($html)->toMatch('/<meta name="twitter:image" content="[^"]*\/encap-img\/logo\.png"/')
        ->and($html)->toContain('property="og:image:alt" content="ENCAP company logo"')
        ->and($html)->toContain('name="twitter:image:alt" content="ENCAP company logo"');
});
