<?php

use App\Models\User;
use App\Services\EncapImageUpload;
use App\Support\EncapImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('encap image storage lives outside public and takes precedence over legacy uploads', function () {
    expect(config('filesystems.disks.encap_images.root'))->toBe(base_path('encap-img'))
        ->and(base_path('encap-img'))->not->toBe(base_path('public/encap-img'))
        ->and(config('filesystems.links'))->not->toHaveKey(public_path('encap-img'));

    Storage::fake('public');
    Storage::fake('encap_images');
    Storage::disk('public')->put('categories/sample.jpg', 'legacy image');
    Storage::disk('encap_images')->put('categories/sample.jpg', 'new image');

    expect(EncapImage::url('categories/sample.jpg'))
        ->toStartWith(Storage::disk('encap_images')->url('categories/sample.jpg'))
        ->and(EncapImage::url('categories/sample.jpg'))->toContain('?v=');

    $response = $this->get('/encap-img/categories/sample.jpg')
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff');

    expect($response->headers->get('Cache-Control'))
        ->toContain('no-cache')
        ->toContain('must-revalidate');

    $this->get('/encap-img/categories/sample.svg')->assertNotFound();
});

test('legacy category uploads remain visible through the encap image route before migration', function () {
    Storage::fake('public');
    Storage::fake('encap_images');
    Storage::disk('public')->put('categories/legacy-mango.jpg', 'legacy mango image');

    expect(EncapImage::url('categories/legacy-mango.jpg'))
        ->toContain('/encap-img/categories/legacy-mango.jpg');

    $response = $this->get('/encap-img/categories/legacy-mango.jpg')
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff');

    expect($response->headers->get('Cache-Control'))
        ->toContain('no-cache')
        ->toContain('must-revalidate');
});

test('new image uploads fall back to laravel storage when encap-img cannot be written', function () {
    Storage::fake('public');

    $blockedRoot = tempnam(sys_get_temp_dir(), 'encap-image-root-blocker-');
    expect($blockedRoot)->toBeString();
    file_put_contents($blockedRoot, 'this file intentionally blocks a directory path');

    $originalRoot = config('filesystems.disks.encap_images.root');
    config(['filesystems.disks.encap_images.root' => $blockedRoot]);
    Storage::forgetDisk('encap_images');

    try {
        $image = UploadedFile::fake()->image('mango.png', 2, 2);
        $stored = app(EncapImageUpload::class)->store($image, 'categories', 'images');

        expect($stored['disk'])->toBe('public')
            ->and(EncapImage::url($stored['path']))->toContain('/encap-img/categories/');

        Storage::disk('public')->assertExists($stored['path']);
        $this->get('/encap-img/'.$stored['path'])->assertOk();
    } finally {
        config(['filesystems.disks.encap_images.root' => $originalRoot]);
        Storage::forgetDisk('encap_images');
        @unlink($blockedRoot);
    }
});

test('the first central-library image upload is saved and immediately servable', function () {
    Storage::fake('public');
    Storage::fake('encap_images');

    $image = UploadedFile::fake()->image('farm-overview.png', 40, 30);

    $this->actingAs(User::factory()->create())
        ->post(route('admin.media.upload'), [
            'label' => 'New farm overview',
            'image' => $image,
        ])
        ->assertRedirect(route('admin.media.index'))
        ->assertSessionHas('success');

    $path = collect(Storage::disk('encap_images')->allFiles('site-library'))->first();

    expect($path)->toBeString()
        ->toStartWith('site-library/new-farm-overview-');

    Storage::disk('encap_images')->assertExists($path);
    $this->get('/encap-img/'.$path)->assertOk();
});

test('central-library uploads use the fallback disk if the main image folder cannot be written', function () {
    Storage::fake('public');

    $blockedRoot = tempnam(sys_get_temp_dir(), 'encap-media-root-blocker-');
    expect($blockedRoot)->toBeString();
    file_put_contents($blockedRoot, 'this file intentionally blocks a directory path');

    $originalRoot = config('filesystems.disks.encap_images.root');
    config(['filesystems.disks.encap_images.root' => $blockedRoot]);
    Storage::forgetDisk('encap_images');

    try {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.media.upload'), [
                'label' => 'Fallback farm image',
                'image' => UploadedFile::fake()->image('farm.png', 40, 30),
            ])
            ->assertRedirect(route('admin.media.index'))
            ->assertSessionHas('image_storage_fallback', true);

        $path = collect(Storage::disk('public')->allFiles('site-library'))->first();

        expect($path)->toBeString()
            ->toStartWith('site-library/fallback-farm-image-');

        Storage::disk('public')->assertExists($path);
        $this->get('/encap-img/'.$path)->assertOk();
    } finally {
        config(['filesystems.disks.encap_images.root' => $originalRoot]);
        Storage::forgetDisk('encap_images');
        @unlink($blockedRoot);
    }
});

test('replacing one central image usage creates a separate file and preserves the library original', function () {
    Storage::fake('public');
    Storage::fake('encap_images');

    $original = UploadedFile::fake()->image('cargo-original.jpg', 12, 12);
    Storage::disk('encap_images')->put(
        'cargo-port-2026.jpg',
        file_get_contents($original->getRealPath())
    );
    $oldUrl = EncapImage::url('cargo-port-2026.jpg');
    $oldBytes = Storage::disk('encap_images')->get('cargo-port-2026.jpg');

    $replacement = UploadedFile::fake()->image('cargo-replacement.jpg', 160, 90);
    $admin = User::factory()->create();

    // A library filename alone is ambiguous and must not overwrite a shared original.
    $this->actingAs($admin)
        ->post(route('admin.media.replace'), [
            'path' => 'cargo-port-2026.jpg',
            'image' => UploadedFile::fake()->image('rejected-replacement.jpg', 80, 80),
        ])
        ->assertNotFound();

    expect(Storage::disk('encap_images')->get('cargo-port-2026.jpg'))->toBe($oldBytes);

    $this->actingAs($admin)
        ->from(route('admin.media.index'))
        ->post(route('admin.media.replace'), [
            'path' => 'cargo-port-2026.jpg',
            'usage' => 'site:home.service_export',
            'image' => $replacement,
        ])
        ->assertRedirect(route('admin.media.index'))
        ->assertSessionHas('success');

    $settings = \App\Models\SeoSetting::query()->firstOrFail();
    $assignedPath = data_get($settings->site_media_slots, 'home.service_export');
    $newUrl = EncapImage::slot('home.service_export', 'cargo-port-2026.jpg');

    expect($assignedPath)->toStartWith('media-replacements/')
        ->and($newUrl)->not->toBe($oldUrl)
        ->and(Storage::disk('encap_images')->get('cargo-port-2026.jpg'))->toBe($oldBytes);

    Storage::disk('encap_images')->assertExists($assignedPath);
    expect(Storage::disk('encap_images')->get($assignedPath))->not->toBe($oldBytes);

    // Exercise the application's image endpoint using the stored relative path.
    // The storage URL may vary by disk implementation in tests; the route path is stable.
    $response = $this->get('/encap-img/'.$assignedPath)->assertOk();

    expect($response->headers->get('Cache-Control'))
        ->toContain('no-cache')
        ->toContain('must-revalidate');
});

test('image library preparation is split into bounded batches', function () {
    Storage::fake('public');
    Storage::fake('encap_images');
    Http::preventStrayRequests();
    $jpeg = "\xFF\xD8\xFF" . str_repeat('B', 6000);
    Http::fake([
        'https://images.unsplash.com/*' => Http::response($jpeg, 200, ['Content-Type' => 'image/jpeg']),
        'https://images.pexels.com/*' => Http::response($jpeg, 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $library = app(\App\Services\EncapImageLibrary::class);
    $first = $library->prepare(false, 0, 4);

    expect($first['downloaded'])->toBe(4)
        ->and($first['finished'])->toBeFalse()
        ->and($first['complete'])->toBeFalse()
        ->and($first['next_offset'])->toBe(4)
        ->and($first['total'])->toBeGreaterThan(4);

    $second = $library->prepare(false, $first['next_offset'], 4);
    expect($second['downloaded'])->toBe(4)
        ->and($second['offset'])->toBe(4)
        ->and($second['finished'])->toBeFalse();
});

test('image preparation copies uploaded category photos before slow stock image downloads', function () {
    Storage::fake('public');
    Storage::fake('encap_images');
    Storage::disk('public')->put('categories/legacy-mango.jpg', 'legacy mango image');

    $jpeg = "\xFF\xD8\xFF" . str_repeat('A', 6000);
    $legacyCopiedBeforeDownload = false;
    Http::preventStrayRequests();
    Http::fake([
        'https://images.unsplash.com/*' => function ($request) use (&$legacyCopiedBeforeDownload, $jpeg) {
            $legacyCopiedBeforeDownload = Storage::disk('encap_images')->exists('categories/legacy-mango.jpg');

            return Http::response($jpeg, 200, ['Content-Type' => 'image/jpeg']);
        },
        'https://images.pexels.com/*' => Http::response($jpeg, 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $this->artisan('encap:images:prepare')->assertSuccessful();

    expect($legacyCopiedBeforeDownload)->toBeTrue();

    Storage::disk('encap_images')->assertMissing('field-origin-2026.jpg');
    Storage::disk('encap_images')->assertExists('produce-mix-2026.jpg');
    Storage::disk('encap_images')->assertExists('farm-cultivation-2026.jpg');
    Storage::disk('encap_images')->assertExists('orchard-harvest-2026.jpg');
    Storage::disk('encap_images')->assertExists('citrus-orchard-2026.jpg');
    Storage::disk('encap_images')->assertExists('potato-sorting-2026.jpg');
    Storage::disk('encap_images')->assertExists('produce-packing-2026.jpg');
    Storage::disk('encap_images')->assertExists('crate-labels-2026.jpg');
    Storage::disk('encap_images')->assertExists('export-crates-2026.jpg');
    Storage::disk('encap_images')->assertExists('frozen-berries-2026.jpg');
    Storage::disk('encap_images')->assertExists('cargo-port-2026.jpg');
    Storage::disk('encap_images')->assertExists('mango-replacement-2026.jpg');
    Storage::disk('encap_images')->assertExists('product-citrus-2026.jpg');
    Storage::disk('encap_images')->assertExists('product-grapes-2026.jpg');
    Storage::disk('encap_images')->assertExists('product-potatoes-2026.jpg');
    Storage::disk('encap_images')->assertExists('product-onions-2026.jpg');
    Storage::disk('encap_images')->assertExists('product-garlic-2026.jpg');
    Storage::disk('encap_images')->assertExists('product-pomegranates-2026.jpg');
    Storage::disk('encap_images')->assertExists('product-peppers-2026.jpg');
    Storage::disk('encap_images')->assertExists('product-strawberries-2026.jpg');
    Storage::disk('encap_images')->assertExists('categories/legacy-mango.jpg');
    Storage::disk('public')->assertExists('categories/legacy-mango.jpg');
});
