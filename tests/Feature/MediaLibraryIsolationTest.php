<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\SeoSetting;
use App\Models\User;
use App\Services\EncapImageAssignment;
use App\Services\EncapMediaInventory;
use App\Support\EncapImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('replacing an image from the media centre changes only the selected product location', function () {
    Storage::fake('encap_images');
    Storage::fake('public');

    $sharedPath = 'categories/shared-product.jpg';
    Storage::disk('encap_images')->put($sharedPath, 'original-shared-image');

    $first = Category::query()->create([
        'name_en' => 'First product',
        'name_ar' => 'المنتج الأول',
        'slug' => 'first-product',
        'image' => $sharedPath,
        'is_published' => true,
    ]);

    $second = Category::query()->create([
        'name_en' => 'Second product',
        'name_ar' => 'المنتج الثاني',
        'slug' => 'second-product',
        'image' => $sharedPath,
        'is_published' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->post(route('admin.media.replace'), [
            'path' => $sharedPath,
            'usage' => "category:{$first->id}:image",
            'image' => UploadedFile::fake()->image('first-product.png', 8, 8),
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $first->refresh();
    $second->refresh();

    expect($first->image)->not->toBe($sharedPath)
        ->and($second->image)->toBe($sharedPath)
        ->and($first->image)->toStartWith('media-replacements/');

    Storage::disk('encap_images')->assertExists($first->image);
    Storage::disk('encap_images')->assertExists($sharedPath);
});

test('assigning the same library photo to two content locations creates independent copies', function () {
    Storage::fake('encap_images');
    Storage::fake('public');

    $source = 'site-library/orchard.jpg';
    Storage::disk('encap_images')->put($source, 'library-image-bytes');

    $assigner = app(EncapImageAssignment::class);
    $firstAssignment = $assigner->duplicate($source, 'category-first-product-main');
    $secondAssignment = $assigner->duplicate($source, 'category-second-product-main');

    expect($firstAssignment)->not->toBe($secondAssignment)
        ->and(str_starts_with($firstAssignment, 'assigned/category-first-product-main/'))->toBeTrue()
        ->and(str_starts_with($secondAssignment, 'assigned/category-second-product-main/'))->toBeTrue();

    Storage::disk('encap_images')->assertExists($firstAssignment);
    Storage::disk('encap_images')->assertExists($secondAssignment);
    Storage::disk('encap_images')->assertExists($source);
});

test('replacing one fixed page image slot does not change another slot using the same original photo', function () {
    Storage::fake('encap_images');
    Storage::fake('public');
    Cache::forget('encap.seo-settings');

    $slots = EncapImage::defaultSlots();
    $sourcePath = $slots['home.hero_feature']['path'];
    $replacementPath = 'media-replacements/home-quality-only.jpg';

    expect($sourcePath)->toBe($slots['home.quality_band']['path']);

    Storage::disk('encap_images')->put($replacementPath, 'replacement-image-bytes');

    $replaced = app(EncapMediaInventory::class)->replaceUsage(
        'site:home.quality_band',
        $sourcePath,
        $replacementPath
    );

    $settings = SeoSetting::query()->firstOrFail();

    expect($replaced)->toBeTrue()
        ->and(data_get($settings->site_media_slots, 'home.quality_band'))->toBe($replacementPath)
        ->and(data_get($settings->site_media_slots, 'home.hero_feature'))->toBeNull()
        ->and(EncapImage::slot('home.quality_band', $sourcePath))->toContain($replacementPath)
        ->and(EncapImage::slot('home.hero_feature', $sourcePath))->toContain($sourcePath);
});


test('a stock image that still exists as an external fallback is copied into the library assignment', function () {
    Storage::fake('encap_images');
    Storage::fake('public');

    $jpeg = str_repeat('A', 2048);
    Http::fake([
        'https://images.pexels.com/*' => Http::response($jpeg, 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $assigned = app(EncapImageAssignment::class)->duplicate(
        'orange-orchard-hero-2026.jpg',
        'homepage-test-slot'
    );

    expect(str_starts_with($assigned, 'assigned/homepage-test-slot/'))->toBeTrue();
    Storage::disk('encap_images')->assertExists($assigned);
});


test('default company content photos are tracked and replaced by section position only', function () {
    // The repair migration seeds the fixed Hadaeq Al-Shamal company when needed.
    // Reuse that record instead of inserting a duplicate unique slug.
    $company = Company::query()->where('slug', 'hadaeq-al-shamal')->firstOrFail();

    $inventory = app(EncapMediaInventory::class);
    $usageKey = "company:{$company->id}:section:0:image:0";
    $usage = $inventory->usage($usageKey);
    $replacementPath = 'media-replacements/hadaeq-potato-section.jpg';

    expect($usage)->not->toBeNull()
        ->and($usage['path'])->toBe('product-potatoes-2026.jpg');

    expect($inventory->replaceUsage($usageKey, $usage['path'], $replacementPath))->toBeTrue();

    $company->refresh();

    expect($company->content_sections[0]['images'][0])->toBe($replacementPath)
        ->and($company->content_sections[0]['images'][1])->toBe('potato-sorting-2026.jpg')
        ->and($company->content_sections[1]['images'][0])->toBe('farm-cultivation-2026.jpg');
});
