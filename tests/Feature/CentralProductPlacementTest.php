<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\User;

test('one product editor controls catalogue, homepage, frozen catalogue and company placement', function () {
    $company = Company::query()->where('slug', 'hadaeq-al-shamal')->firstOrFail();
    $this->actingAs(User::factory()->create())
        ->get(route('admin.categories.create'))
        ->assertOk()
        ->assertSee('name="show_in_catalogue"', false)
        ->assertSee('name="show_in_frozen_catalogue"', false)
        ->assertSee('name="show_on_homepage"', false)
        ->assertSee('name="company_ids[]"', false)
        ->assertSee('شركة حدائق الشمال');

    $category = Category::query()->create([
        'name_en' => 'Central Frozen Mango',
        'name_ar' => 'مانجو مجمدة مركزية',
        'slug' => 'central-frozen-mango',
        'short_description_en' => 'Single-source placement test product.',
        'short_description_ar' => 'منتج اختبار من مصدر واحد.',
        'available_forms' => ['Fresh'],
        'is_published' => true,
        'show_in_catalogue' => true,
        'show_in_frozen_catalogue' => false,
        'show_on_homepage' => false,
        'sort_order' => 2,
    ]);

    $this->put(route('admin.categories.update', $category), [
        'name_en' => $category->name_en,
        'name_ar' => $category->name_ar,
        'slug' => $category->slug,
        'short_description_en' => $category->short_description_en,
        'short_description_ar' => $category->short_description_ar,
        'sort_order' => 2,
        'is_published' => '1',
        'show_in_catalogue' => '1',
        'show_in_frozen_catalogue' => '1',
        'show_on_homepage' => '1',
        'available_forms' => ['Fresh'],
        'company_ids' => [$company->id],
    ])->assertRedirect(route('admin.categories.index'))->assertSessionHasNoErrors();

    $category->refresh();

    expect($category->show_in_catalogue)->toBeTrue()
        ->and($category->show_in_frozen_catalogue)->toBeTrue()
        ->and($category->show_on_homepage)->toBeTrue()
        ->and($category->available_forms)->toContain('Frozen')
        ->and($category->companies()->pluck('companies.id')->all())->toContain($company->id);

    $this->get('/en/products')->assertOk()->assertSee('Central Frozen Mango');
    $this->get('/en/frozen-products')->assertOk()->assertSee('Central Frozen Mango');
    $this->get('/en')->assertOk()->assertSee('Central Frozen Mango');
    $this->get('/en/companies')->assertOk()->assertDontSee('Central Frozen Mango')->assertDontSee('encap-company-detail-section', false);
});

 
test('product form can clear all company links and available formats', function () {
    $company = Company::query()->where('slug', 'hadaeq-al-shamal')->firstOrFail();
    $category = Category::query()->create([
        'name_en' => 'Clear Product Options',
        'slug' => 'clear-product-options',
        'available_forms' => ['Fresh', 'Frozen'],
        'is_published' => true,
        'sort_order' => 3,
    ]);
    $category->companies()->attach($company->id);

    $this->actingAs(User::factory()->create())
        ->put(route('admin.categories.update', $category), [
            'name_en' => $category->name_en,
            'slug' => $category->slug,
            'sort_order' => $category->sort_order,
            'company_ids_present' => '1',
            'available_forms_present' => '1',
        ])
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHasNoErrors();

    $category->refresh();

    expect($category->available_forms)->toBe([])
        ->and($category->companies()->exists())->toBeFalse();
});

test('product placement can keep a published product out of selected catalogue locations', function () {
    Category::query()->create([
        'name_en' => 'Company-only product',
        'name_ar' => 'منتج للشركة فقط',
        'slug' => 'company-only-product',
        'available_forms' => ['Fresh'],
        'is_published' => true,
        'show_in_catalogue' => false,
        'show_in_frozen_catalogue' => false,
        'show_on_homepage' => false,
        'sort_order' => 1,
    ]);

    $this->get('/en/products')->assertOk()->assertDontSee('Company-only product');
    $this->get('/en/frozen-products')->assertOk()->assertDontSee('Company-only product');
    $this->get('/en')->assertOk()->assertDontSee('Company-only product');
});
