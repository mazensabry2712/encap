<?php

use App\Support\EncapImage;
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
        ->toBe(Storage::disk('encap_images')->url('categories/legacy-mango.jpg'));

    $this->get('/encap-img/categories/legacy-mango.jpg')
        ->assertOk()
        ->assertHeader('Cache-Control', 'max-age=86400, public')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
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

    Storage::disk('encap_images')->assertExists('farm-landscape.jpg');
    Storage::disk('encap_images')->assertExists('cargo-port.jpg');
    Storage::disk('encap_images')->assertExists('categories/legacy-mango.jpg');
    Storage::disk('public')->assertExists('categories/legacy-mango.jpg');
});
