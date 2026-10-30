<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('category editor presents an organized gallery manager with live image previews', function () {
    $category = Category::query()->create([
        'name_en' => 'Gallery Mango',
        'name_ar' => 'مانجو المعرض',
        'slug' => 'gallery-mango',
        'image' => 'categories/main.png',
        'gallery' => [
            'categories/gallery/one.png',
            'categories/gallery/two.png',
            'categories/gallery/three.png',
        ],
        'is_published' => true,
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('admin.categories.edit', $category))
        ->assertOk()
        ->assertSee('data-main-image-manager', false)
        ->assertSee('data-gallery-manager', false)
        ->assertSee('data-gallery-upload', false)
        ->assertSee('data-gallery-selection', false)
        ->assertSee('js-gallery-remove', false)
        ->assertSee('admin-gallery-manager.js', false)
        ->assertSee('data-gallery-total', false)
        ->assertSee('صور المنتج الإضافية');
});

test('product can be saved without required text values, slug or display order', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('admin.categories.store'), [
            'name_ar' => 'منتج بدون قيود',
        ])
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHasNoErrors();

    $category = Category::query()->where('name_ar', 'منتج بدون قيود')->firstOrFail();

    expect($category->name_en)->toBe('منتج بدون قيود')
        ->and($category->slug)->not->toBe('')
        ->and($category->sort_order)->toBe(0);
});

test('selecting multiple product photos saves the first as primary and all remaining photos to the gallery', function () {
    Storage::fake('public');
    Storage::fake('encap_images');
    $oldMain = 'categories/existing-main.png';
    Storage::disk('public')->put($oldMain, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAFklEQVR4nGNUjjJiYGBgYmBgYGBgAAAITwCzmz5grAAAAABJRU5ErkJggg=='));

    $category = Category::query()->create([
        'name_en' => 'Batch Upload Mango',
        'name_ar' => 'مانجو الرفع المتعدد',
        'slug' => 'batch-upload-mango',
        'image' => $oldMain,
        'gallery' => [],
        'is_published' => true,
        'sort_order' => 1,
    ]);

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAFklEQVR4nGNUjjJiYGBgYmBgYGBgAAAITwCzmz5grAAAAABJRU5ErkJggg==');
    $photos = [
        UploadedFile::fake()->createWithContent('main-photo.png', $png),
        UploadedFile::fake()->createWithContent('gallery-photo-1.png', $png),
        UploadedFile::fake()->createWithContent('gallery-photo-2.png', $png),
    ];

    $this->actingAs(User::factory()->create())
        ->put(route('admin.categories.update', $category), [
            'name_en' => 'Batch Upload Mango',
            'name_ar' => 'مانجو الرفع المتعدد',
            'slug' => 'batch-upload-mango',
            'sort_order' => 1,
            'images' => $photos,
        ])
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHasNoErrors();

    $category->refresh();

    expect($category->image)->not->toBe($oldMain)
        ->and($category->gallery)->toHaveCount(2);

    Storage::disk('public')->assertExists($oldMain);
    Storage::disk('encap_images')->assertExists($category->image);
    foreach ($category->gallery as $path) {
        Storage::disk('encap_images')->assertExists($path);
    }
});

test('category gallery edits preserve remaining images when replacing a selected photo', function () {
    Storage::fake('public');
    Storage::fake('encap_images');
    $gallery = ['categories/gallery/remove-me.png'];

    foreach (range(2, 12) as $number) {
        $gallery[] = "categories/gallery/keep-{$number}.png";
    }

    Storage::disk('public')->put('categories/gallery/remove-me.png', 'old image');
    $category = Category::query()->create([
        'name_en' => 'Capped Mango',
        'name_ar' => 'مانجو',
        'slug' => 'capped-mango',
        'gallery' => $gallery,
        'is_published' => true,
        'sort_order' => 0,
    ]);

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAFklEQVR4nGNUjjJiYGBgYmBgYGBgAAAITwCzmz5grAAAAABJRU5ErkJggg==', true);
    $extraImage = UploadedFile::fake()->createWithContent('extra.png', $png);

    $this->actingAs(User::factory()->create())
        ->from(route('admin.categories.edit', $category))
        ->put(route('admin.categories.update', $category), [
            'name_en' => 'Capped Mango',
            'name_ar' => 'مانجو',
            'slug' => 'capped-mango',
            'sort_order' => 0,
            'remove_gallery' => [0],
            'gallery' => [$extraImage],
        ])
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHasNoErrors();

    $category->refresh();

    expect($category->gallery)->toHaveCount(12)
        ->and($category->gallery)->not->toContain('categories/gallery/remove-me.png');

    Storage::disk('public')->assertExists('categories/gallery/remove-me.png');
    expect(Storage::disk('encap_images')->exists($category->gallery[11]))->toBeTrue();
});

test('category gallery accepts more than twelve images without blocking product save', function () {
    Storage::fake('public');
    Storage::fake('encap_images');

    $gallery = array_map(
        fn ($number) => "categories/gallery/image-{$number}.png",
        range(1, 12)
    );
    $category = Category::query()->create([
        'name_en' => 'Growing Gallery',
        'slug' => 'growing-gallery',
        'gallery' => $gallery,
        'is_published' => true,
        'sort_order' => 0,
    ]);

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAFklEQVR4nGNUjjJiYGBgYmBgYGBgAAAITwCzmz5grAAAAABJRU5ErkJggg==', true);
    $extraImage = UploadedFile::fake()->createWithContent('extra.png', $png);

    $this->actingAs(User::factory()->create())
        ->put(route('admin.categories.update', $category), [
            'name_en' => 'Growing Gallery',
            'slug' => 'growing-gallery',
            'sort_order' => 0,
            'gallery' => [$extraImage],
        ])
        ->assertRedirect(route('admin.categories.index'))
        ->assertSessionHasNoErrors();

    $category->refresh();

    expect($category->gallery)->toHaveCount(13)
        ->and($category->gallery)->toContain('categories/gallery/image-12.png');

    Storage::disk('encap_images')->assertExists($category->gallery[12]);
});
