<?php

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\SeoSetting;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

test('guests are sent to the admin login screen', function () {
    $this->get('/admin/categories')->assertRedirect(route('admin.login'));
    $this->get('/admin/seo')->assertRedirect(route('admin.login'));
    $this->get('/admin/quote-requests')->assertRedirect(route('admin.login'));
    $this->get('/admin/contact-messages')->assertRedirect(route('admin.login'));
});

test('authenticated administrators can access the admin inboxes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get('/admin/categories')->assertOk()->assertSee('Product categories');
    $this->get('/admin/seo')->assertOk()->assertSee('Central SEO');
    $this->get('/admin/quote-requests')->assertOk()->assertSee('Quote requests');
    $this->get('/admin/contact-messages')->assertOk()->assertSee('Contact messages');
});

test('seo settings cache stores attributes and discards legacy model cache entries', function () {
    Cache::forget('encap.seo-settings');

    $stored = SeoSetting::query()->create([
        'site_title_en' => 'Cached ENCAP title',
        'site_title_ar' => 'عنوان ENCAP',
        'default_robots' => 'index,follow',
        'organization_name' => 'ENCAP',
    ]);

    Cache::put(
        'encap.seo-settings',
        new SeoSetting(['site_title_en' => 'Stale cached title']),
        300
    );

    $settings = SeoSetting::current();

    expect(Cache::get('encap.seo-settings'))->toBeArray()
        ->and($settings->getKey())->toBe($stored->getKey())
        ->and($settings->site_title_en)->toBe('Cached ENCAP title');

    Cache::forget('encap.seo-settings');
});

test('admin can save shared seo defaults and configured contact details', function () {
    $this->actingAs(User::factory()->create());

    $this->put('/admin/seo', [
        'site_title_en' => 'ENCAP Export Company',
        'site_title_ar' => 'ENCAP للتصدير',
        'site_description_en' => 'Egyptian agricultural produce for importers.',
        'site_description_ar' => 'منتجات زراعية مصرية للمستوردين.',
        'default_robots' => 'index,follow',
        'organization_name' => 'ENCAP',
        'organization_url' => 'https://encap.example',
        'contact_email' => 'exports@encap.example',
        'contact_phone' => '+201000000000',
        'whatsapp_number' => '+201000000000',
        'address_en' => 'Egypt',
        'address_ar' => 'مصر',
        'business_hours_en' => 'Sunday–Thursday, 09:00–17:00',
        'business_hours_ar' => 'الأحد–الخميس، 09:00–17:00',
        'facebook_url' => 'https://www.facebook.com/encap-example',
        'instagram_url' => 'https://www.instagram.com/encap-example',
        'linkedin_url' => 'https://www.linkedin.com/company/encap-example',
        'youtube_url' => 'https://www.youtube.com/@encap-example',
        'page_metadata' => [
            'about' => [
                'en' => [
                    'title' => 'ENCAP | About & Importers',
                    'description' => 'Learn how ENCAP connects Egyptian agricultural production and buyers.',
                ],
            ],
            'packing-stations' => [
                'en' => [
                    'title' => 'ENCAP Packing Operations',
                    'description' => 'Information for crop preparation, packing and export enquiries.',
                ],
            ],
        ],
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseHas('seo_settings', [
        'site_title_en' => 'ENCAP Export Company',
        'contact_email' => 'exports@encap.example',
        'default_robots' => 'index,follow',
    ]);

    $this->get('/en')->assertOk()->assertSee('<title>ENCAP Export Company</title>', false);
    $this->get('/en/about')->assertOk()
        ->assertSee('<title>ENCAP | About &amp; Importers</title>', false)
        ->assertSee('<meta name="description" content="Learn how ENCAP connects Egyptian agricultural production and buyers.">', false);

    $this->get('/en/packing-stations')->assertOk()
        ->assertSee('<title>ENCAP Packing Operations</title>', false)
        ->assertSee('<meta name="description" content="Information for crop preparation, packing and export enquiries.">', false);

    $this->get('/en/contact')->assertOk()
        ->assertSee('Sunday–Thursday, 09:00–17:00')
        ->assertSee('https://www.facebook.com/encap-example', false)
        ->assertSee('Open in Maps');
});

test('admin can configure product detail copy and control visible sections', function () {
    \Illuminate\Support\Facades\Storage::fake('public');
    $category = Category::query()->create([
        'name_en' => 'Managed Mango',
        'name_ar' => 'مانجو بإدارة لوحة التحكم',
        'slug' => 'managed-mango',
        'packaging_en' => 'Export carton',
        'packaging_ar' => 'كرتونة تصدير',
        'is_published' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.categories.edit', $category))
        ->assertOk()
        ->assertSee('overview_heading_en', false)
        ->assertSee('show_product_images', false)
        ->assertSee('bottom_cta_text_ar', false);

    $this->put(route('admin.categories.update', $category), [
        'name_en' => 'Managed Mango',
        'name_ar' => 'مانجو بإدارة لوحة التحكم',
        'slug' => 'managed-mango',
        'short_description_ar' => 'وصف مختصر يتحكم فيه المدير.',
        'overview_heading_en' => 'Mango supply details',
        'overview_heading_ar' => 'تفاصيل توريد المانجو',
        'enquiry_note_heading_en' => 'Talk to our export team',
        'enquiry_note_heading_ar' => 'تواصل مع فريق التصدير',
        'enquiry_note_text_en' => 'Tell us your target market.',
        'enquiry_note_text_ar' => 'حدد السوق المطلوب للتوريد.',
        'bottom_cta_heading_en' => 'Plan a mango shipment',
        'bottom_cta_heading_ar' => 'خطط لشحنة المانجو',
        'bottom_cta_text_en' => 'Share quantity and destination.',
        'bottom_cta_text_ar' => 'شارك الكمية والوجهة.',
        'sort_order' => 1,
        'is_published' => '1',
        'show_enquiry_note' => '1',
        'show_description' => '1',
        'show_available_forms' => '1',
        'show_specifications' => '1',
        'show_bottom_cta' => '1',
    ])->assertRedirect(route('admin.categories.index'))->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'overview_heading_ar' => 'تفاصيل توريد المانجو',
        'enquiry_note_heading_ar' => 'تواصل مع فريق التصدير',
        'bottom_cta_text_ar' => 'شارك الكمية والوجهة.',
        'show_product_images' => false,
        'show_packaging' => false,
    ]);

    $this->get('/ar/products/managed-mango')
        ->assertOk()
        ->assertSee('تفاصيل توريد المانجو')
        ->assertSee('تواصل مع فريق التصدير')
        ->assertSee('حدد السوق المطلوب للتوريد.')
        ->assertSee('خطط لشحنة المانجو')
        ->assertSee('شارك الكمية والوجهة.')
        ->assertDontSee('data-gallery-main', false)
        ->assertDontSee('كرتونة تصدير');
});


test('admin can update contact message status', function () {
    $message = ContactMessage::query()->create([
        'name' => 'Test User',
        'email' => 'test@example.test',
        'subject' => 'Test subject',
        'message' => 'Test message',
        'status' => 'new',
    ]);

    $this->actingAs(User::factory()->create())
        ->patch(route('admin.contact-messages.update', $message), ['status' => 'resolved'])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('contact_messages', [
        'id' => $message->id,
        'status' => 'resolved',
    ]);
});

test('admin can create a published category', function () {
    \Illuminate\Support\Facades\Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $this->post('/admin/categories', [
        'name_en' => 'Mango',
        'name_ar' => 'مانجو',
        'slug' => 'mango',
        'short_description_en' => 'Export mango enquiries.',
        'varieties_en' => 'Kensington Pride; Kent',
        'harvest_window_en' => 'Season confirmed per enquiry',
        'specifications_en' => 'Grade and size agreed with buyer',
        'sort_order' => 1,
        'is_published' => '1',
        'available_forms' => [],
    ])->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'slug' => 'mango',
        'is_published' => true,
        'varieties_en' => 'Kensington Pride; Kent',
        'harvest_window_en' => 'Season confirmed per enquiry',
        'specifications_en' => 'Grade and size agreed with buyer',
    ]);
    expect(Category::query()->count())->toBe(1);
});
