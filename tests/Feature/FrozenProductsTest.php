<?php

use App\Models\Category;

test('frozen products catalogue lists only published categories that include the frozen format', function () {
    Category::query()->create([
        'name_en' => 'Test Frozen Strawberries',
        'name_ar' => 'فراولة مجمدة للاختبار',
        'slug' => 'test-frozen-strawberries',
        'short_description_en' => 'Frozen strawberries test category.',
        'short_description_ar' => 'تصنيف فراولة مجمدة للاختبار.',
        'available_forms' => ['Frozen'],
        'show_in_frozen_catalogue' => true,
        'is_published' => true,
        'sort_order' => 1,
    ]);

    Category::query()->create([
        'name_en' => 'Test Fresh Citrus',
        'name_ar' => 'موالح طازجة للاختبار',
        'slug' => 'test-fresh-citrus',
        'available_forms' => ['Fresh'],
        'is_published' => true,
        'sort_order' => 2,
    ]);

    Category::query()->create([
        'name_en' => 'Test Unpublished Frozen Product',
        'name_ar' => 'منتج مجمد غير منشور للاختبار',
        'slug' => 'test-hidden-frozen-product',
        'available_forms' => ['Frozen'],
        'is_published' => false,
        'sort_order' => 3,
    ]);

    $this->get('/ar/frozen-products')
        ->assertOk()
        ->assertSee('المجمدات')
        ->assertSee('فراولة مجمدة للاختبار')
        ->assertSee('/ar/frozen-products/test-frozen-strawberries', false)
        ->assertDontSee('موالح طازجة للاختبار')
        ->assertDontSee('منتج مجمد غير منشور للاختبار');

    $this->get('/ar/frozen-products/test-frozen-strawberries')
        ->assertOk()
        ->assertSee('فراولة مجمدة للاختبار')
        ->assertSee('/ar/frozen-products', false)
        ->assertDontSee('/ar/products/test-frozen-strawberries', false);

    $this->get('/ar/frozen-products/test-fresh-citrus')->assertNotFound();
    $this->get('/ar/frozen-products/test-hidden-frozen-product')->assertNotFound();
});

test('frozen products page is linked from desktop and mobile navigation in each locale', function () {
    foreach (['en', 'ar', 'fr', 'ru', 'tr'] as $locale) {
        $response = $this->get('/'.$locale);

        $response->assertOk()
            ->assertSee('/'.$locale.'/frozen-products', false);
    }

    $this->get('/ar')
        ->assertSee('المجمدات');
});
