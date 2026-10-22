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

test('category gallery cannot exceed twelve additional images across repeated edits', function () {
    Storage::fake('public');
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

    Storage::disk('public')->assertMissing('categories/gallery/remove-me.png');
    expect(Storage::disk('public')->exists($category->gallery[11]))->toBeTrue();
});

test('category gallery rejects a thirteenth image when no existing image is removed', function () {
    Storage::fake('public');

    $gallery = array_map(
        fn ($number) => "categories/gallery/image-{$number}.png",
        range(1, 12)
    );
    $category = Category::query()->create([
        'name_en' => 'Full Gallery',
        'slug' => 'full-gallery',
        'gallery' => $gallery,
        'is_published' => true,
        'sort_order' => 0,
    ]);

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAFklEQVR4nGNUjjJiYGBgYmBgYGBgAAAITwCzmz5grAAAAABJRU5ErkJggg==', true);
    $extraImage = UploadedFile::fake()->createWithContent('extra.png', $png);

    $this->actingAs(User::factory()->create())
        ->from(route('admin.categories.edit', $category))
        ->put(route('admin.categories.update', $category), [
            'name_en' => 'Full Gallery',
            'slug' => 'full-gallery',
            'sort_order' => 0,
            'gallery' => [$extraImage],
        ])
        ->assertRedirect(route('admin.categories.edit', $category))
        ->assertSessionHasErrors('gallery');

    expect($category->fresh()->gallery)->toHaveCount(12);
});
