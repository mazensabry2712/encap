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
        ->assertSee('Good work starts here.')
        ->assertSee('Product categories')
        ->assertSee('Export quote requests')
        ->assertSee('Contact messages')
        ->assertSee('Ahmed Buyer')
        ->assertSee('Dashboard Mango')
        ->assertSee('Mona Visitor')
        ->assertSee('Export question')
        ->assertSee('Manage catalogue')
        ->assertSee('Manage site metadata and contact details');
});
