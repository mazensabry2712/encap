<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\User;

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
        'name_en' => $company->name_en,
        'name_ar' => $company->name_ar,
        'slug' => $company->slug,
        'sort_order' => $company->sort_order,
        'is_published' => '1',
        'category_ids' => [$published->id, $unpublished->id],
    ])->assertRedirect(route('admin.companies.edit', $company))
        ->assertSessionHasNoErrors();

    expect($company->categories()->pluck('categories.id')->all())
        ->toContain($published->id)
        ->toContain($unpublished->id);

    $this->get('/ar/companies/hadaeq-al-shamal')
        ->assertOk()
        ->assertSee('المنتجات التي توفرها هذه الشركة')
        ->assertSee('طماطم')
        ->assertDontSee('رمان غير منشور');
});