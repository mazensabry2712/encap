<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\User;


test('recovery migration restores the public company profile when its database row is missing', function () {
    $company = Company::query()
        ->where('slug', 'hadaeq-al-shamal')
        ->firstOrFail();

    $company->delete();

    $migration = require database_path('migrations/2026_10_29_000004_restore_hadaeq_al_shamal_company_profile.php');
    $migration->up();

    $restored = Company::query()
        ->where('slug', 'hadaeq-al-shamal')
        ->firstOrFail();

    expect($restored->name_ar)->toBe('شركة حدائق الشمال')
        ->and($restored->is_published)->toBeTrue();

    $this->get('/ar/companies')
        ->assertOk()
        ->assertSee('شركة حدائق الشمال')
        ->assertSee('ENCAP');
});

test('administrators can assign product categories to a company and unpublished products stay hidden', function () {
    $company = Company::query()
        ->where('slug', 'hadaeq-al-shamal')
        ->firstOrFail();

    $published = Category::query()->create([
        'name_en' => 'Test Tomatoes',
        'name_ar' => 'طماطم',
        'slug' => 'test-tomatoes',
        'short_description_en' => 'Tomato export category test.',
        'short_description_ar' => 'اختبار تصنيف الطماطم.',
        'is_published' => true,
        'sort_order' => 1,
    ]);

    $unpublished = Category::query()->create([
        'name_en' => 'Hidden Pomegranates',
        'name_ar' => 'رمان غير منشور',
        'slug' => 'hidden-pomegranates',
        'is_published' => false,
        'sort_order' => 2,
    ]);

    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.companies.edit', $company))
        ->assertOk()
        ->assertSee('name="category_ids[]"', false)
        ->assertSee('طماطم');

    $this->put(route('admin.companies.update', $company), [
        'category_ids' => [$published->id, $unpublished->id],
    ])->assertRedirect(route('admin.companies.edit', $company))
        ->assertSessionHasNoErrors();

    expect($company->categories()->pluck('categories.id')->all())
        ->toContain($published->id)
        ->toContain($unpublished->id);

    $this->get('/ar/companies')
        ->assertOk()
        ->assertSee('المنتجات التي توفرها هذه الشركة')
        ->assertSee('طماطم')
        ->assertDontSee('رمان غير منشور');
});