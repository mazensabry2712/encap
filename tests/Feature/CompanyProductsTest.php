<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


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

test('company editor has optional fields and allows adding any number of images', function () {
    $company = Company::query()->where('slug', 'hadaeq-al-shamal')->firstOrFail();

    $this->actingAs(User::factory()->create())
        ->get(route('admin.companies.edit', $company))
        ->assertOk()
        ->assertDontSee('required', false)
        ->assertDontSee('maxlength=', false)
        ->assertDontSee('type="url"', false)
        ->assertDontSee('12 images total')
        ->assertDontSee('two images')
        ->assertSee('name="gallery[]" multiple', false)
        ->assertSee('name="category_ids_present"', false)
        ->assertSee('name="sections_present"', false)
        ->assertSee('data-add-company-section-image', false)
        ->assertSee('company-section-image-template', false);
});

test('a partial company update preserves every field that was not submitted', function () {
    $company = Company::query()->where('slug', 'hadaeq-al-shamal')->firstOrFail();
    $category = Category::query()->create([
        'name_en' => 'Preserved Company Category',
        'slug' => 'preserved-company-category',
        'is_published' => true,
        'sort_order' => 1,
    ]);
    $existingSections = [[
        'title_ar' => 'قسم محفوظ',
        'title_en' => 'Preserved section',
        'images' => ['preserved-image-1.jpg', 'preserved-image-2.jpg', 'preserved-image-3.jpg'],
    ]];

    $company->update([
        'short_description_ar' => 'وصف عربي محفوظ',
        'description_en' => 'Existing English company description',
        'country_ar' => 'المملكة العربية السعودية',
        'location_ar' => 'الجوف',
        'website_url' => 'https://example.com',
        'image' => 'company-existing-main.jpg',
        'gallery' => ['company-existing-gallery-1.jpg', 'company-existing-gallery-2.jpg'],
        'content_sections' => $existingSections,
    ]);
    $company->categories()->sync([$category->id]);

    $this->actingAs(User::factory()->create())
        ->put(route('admin.companies.update', $company), [
            'description_ar' => 'تم تعديل هذا الحقل فقط',
        ])
        ->assertRedirect(route('admin.companies.edit', $company))
        ->assertSessionHasNoErrors();

    $company->refresh();

    expect($company->description_ar)->toBe('تم تعديل هذا الحقل فقط')
        ->and($company->short_description_ar)->toBe('وصف عربي محفوظ')
        ->and($company->description_en)->toBe('Existing English company description')
        ->and($company->country_ar)->toBe('المملكة العربية السعودية')
        ->and($company->location_ar)->toBe('الجوف')
        ->and($company->website_url)->toBe('https://example.com')
        ->and($company->image)->toBe('company-existing-main.jpg')
        ->and($company->gallery)->toBe(['company-existing-gallery-1.jpg', 'company-existing-gallery-2.jpg'])
        ->and($company->content_sections)->toBe($existingSections)
        ->and($company->categories()->pluck('categories.id')->all())->toBe([$category->id]);
});

test('company profile gallery accepts more than twelve uploaded images', function () {
    Storage::fake('public');
    Storage::fake('encap_images');

    $company = Company::query()->where('slug', 'hadaeq-al-shamal')->firstOrFail();
    $company->update(['gallery' => []]);

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAFklEQVR4nGNUjjJiYGBgYmBgYGBgAAAITwCzmz5grAAAAABJRU5ErkJggg==');
    $files = array_map(
        fn ($number) => UploadedFile::fake()->createWithContent("company-gallery-{$number}.png", $png),
        range(1, 15)
    );

    $this->actingAs(User::factory()->create())
        ->put(route('admin.companies.update', $company), [
            'gallery' => $files,
        ])
        ->assertRedirect(route('admin.companies.edit', $company))
        ->assertSessionHasNoErrors();

    $company->refresh();

    expect($company->gallery)->toHaveCount(15);

    foreach ($company->gallery as $imagePath) {
        Storage::disk('encap_images')->assertExists($imagePath);
    }
});

test('a company content section accepts more than two images and displays all of them publicly', function () {
    Storage::fake('public');
    Storage::fake('encap_images');

    $company = Company::query()->where('slug', 'hadaeq-al-shamal')->firstOrFail();
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAFklEQVR4nGNUjjJiYGBgYmBgYGBgAAAITwCzmz5grAAAAABJRU5ErkJggg==');
    $files = array_map(
        fn ($number) => UploadedFile::fake()->createWithContent("section-image-{$number}.png", $png),
        range(1, 4)
    );

    $this->actingAs(User::factory()->create())
        ->put(route('admin.companies.update', $company), [
            'sections_present' => '1',
            'sections' => [
                0 => [
                    'title_ar' => 'قسم بأربع صور',
                    'title_en' => 'Four-image section',
                    'existing_images' => ['', '', '', ''],
                    'library_images' => ['', '', '', ''],
                    'upload_images' => $files,
                ],
            ],
        ])
        ->assertRedirect(route('admin.companies.edit', $company))
        ->assertSessionHasNoErrors();

    $company->refresh();

    expect($company->content_sections)->toHaveCount(1)
        ->and($company->content_sections[0]['images'])->toHaveCount(4)
        ->and($company->content_sections[0]['title_en'])->toBe('Four-image section');

    $response = $this->get('/ar/companies')->assertOk()->assertSee('قسم بأربع صور');
    expect(substr_count($response->getContent(), '<figure class="hs-company__photo">'))->toBe(4)
        ->and($response->getContent())->toContain('/ 04');

    foreach ($company->content_sections[0]['images'] as $imagePath) {
        Storage::disk('encap_images')->assertExists($imagePath);
    }
});


test('administrators can intentionally clear every company content section', function () {
    $company = Company::query()->where('slug', 'hadaeq-al-shamal')->firstOrFail();
    $company->update([
        'content_sections' => [[
            'title_ar' => 'قسم سيتم حذفه',
            'title_en' => 'Section to clear',
            'images' => ['temporary-company-section.jpg'],
        ]],
    ]);

    $this->actingAs(User::factory()->create())
        ->put(route('admin.companies.update', $company), [
            'sections_present' => '1',
        ])
        ->assertRedirect(route('admin.companies.edit', $company))
        ->assertSessionHasNoErrors();

    expect($company->fresh()->content_sections)->toBe([]);

    $this->get('/ar/companies')
        ->assertOk()
        ->assertDontSee('قسم سيتم حذفه')
        ->assertDontSee('Production begins in the field');
});

test('an invalid company website link does not block other changes or erase the saved link', function () {
    $company = Company::query()->where('slug', 'hadaeq-al-shamal')->firstOrFail();
    $company->update(['website_url' => 'https://saved-company.example']);

    $this->actingAs(User::factory()->create())
        ->put(route('admin.companies.update', $company), [
            'website_url' => 'not a url',
            'short_description_ar' => 'تحديث الوصف بدون تعطيل',
        ])
        ->assertRedirect(route('admin.companies.edit', $company))
        ->assertSessionHasNoErrors();

    expect($company->fresh()->website_url)->toBe('https://saved-company.example')
        ->and($company->fresh()->short_description_ar)->toBe('تحديث الوصف بدون تعطيل');
});

