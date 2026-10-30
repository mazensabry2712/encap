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
        ->assertSee('ENCAP');
});


test('recovery migration preserves existing admin-managed company product assignments', function () {
    $company = Company::query()
        ->where('slug', 'hadaeq-al-shamal')
        ->firstOrFail();

    $selected = Category::query()->create([
        'name_en' => 'Selected Citrus',
        'name_ar' => 'موالح مختارة',
        'slug' => 'selected-citrus-recovery-test',
        'is_published' => true,
        'sort_order' => 1,
    ]);
    Category::query()->create([
        'name_en' => 'Unselected Grapes',
        'name_ar' => 'عنب غير مختار',
        'slug' => 'unselected-grapes-recovery-test',
        'is_published' => true,
        'sort_order' => 2,
    ]);

    $company->categories()->sync([$selected->id]);

    $migration = require database_path('migrations/2026_10_29_000004_restore_hadaeq_al_shamal_company_profile.php');
    $migration->up();

    expect($company->fresh()->categories()->pluck('categories.id')->all())
        ->toBe([$selected->id]);
});

test('administrators can assign company categories without overwriting the company sections', function () {
    $company = Company::query()
        ->where('slug', 'hadaeq-al-shamal')
        ->firstOrFail();

    $existingSections = [[
        'title_ar' => 'قسم موجود قبل تحديث التصنيفات',
        'title_en' => 'Existing section before category update',
        'images' => ['existing-first.jpg', 'existing-second.jpg'],
    ]];
    $company->update(['content_sections' => $existingSections]);

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

    expect($company->fresh()->categories()->pluck('categories.id')->all())
        ->toContain($published->id)
        ->toContain($unpublished->id)
        ->and($company->fresh()->content_sections)->toBe($existingSections);

    $this->get('/ar/companies')
        ->assertOk()
        ->assertSee('شركاتنا | ENCAP')
        ->assertDontSee('encap-company-detail-section', false)
        ->assertDontSee('المنتجات التي توفرها هذه الشركة')
        ->assertDontSee('Test Tomatoes')
        ->assertDontSee('رمان غير منشور');
});