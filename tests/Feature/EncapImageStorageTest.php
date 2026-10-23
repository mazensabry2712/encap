<?php

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
        ->toBe(Storage::disk('encap_images')->url('categories/sample.jpg'));

    $this->get('/encap-img/categories/sample.jpg')
        ->assertOk()
        ->assertHeader('Cache-Control', 'max-age=86400, public')
        ->assertHeader('X-Content-Type-Options', 'nosniff');

    $this->get('/encap-img/categories/sample.svg')->assertNotFound();
});

test('legacy category uploads remain visible through the encap image route before migration', function () {
    Storage::fake('public');
    Storage::fake('encap_images');
    Storage::disk('public')->put('categories/legacy-mango.jpg', 'legacy mango image');

    expect(EncapImage::url('categories/legacy-mango.jpg'))
        ->toContain('/encap-img/categories/legacy-mango.jpg');

    $this->get('/encap-img/categories/legacy-mango.jpg')
        ->assertOk()
        ->assertHeader('Cache-Control', 'max-age=86400, public')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
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

    Storage::disk('encap_images')->assertExists('field-origin-2026.jpg');
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
    Storage::disk('encap_images')->assertExists('product-mangoes-2026.jpg');
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
