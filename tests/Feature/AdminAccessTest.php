<?php

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\SeoSetting;
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
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseHas('seo_settings', [
        'site_title_en' => 'ENCAP Export Company',
        'contact_email' => 'exports@encap.example',
        'default_robots' => 'index,follow',
    ]);

    $this->get('/en')->assertOk()->assertSee('<title>ENCAP Export Company</title>', false);
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
        'sort_order' => 1,
        'is_published' => '1',
        'available_forms' => [],
    ])->assertRedirect(route('admin.categories.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('categories', [
        'slug' => 'mango',
        'is_published' => true,
    ]);
    expect(Category::query()->count())->toBe(1);
});
