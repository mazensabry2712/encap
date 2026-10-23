<?php

use App\Support\EncapImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('encap image storage lives outside public and takes precedence over legacy uploads', function () {
    expect(config('filesystems.disks.encap_images.root'))->toBe(base_path('encap-img'))
        ->and(base_path('encap-img'))->not->toBe(base_path('public/encap-img'));

    Storage::fake('public');
    Storage::fake('encap_images');
    Storage::disk('public')->put('categories/sample.jpg', 'legacy image');
    Storage::disk('encap_images')->put('categories/sample.jpg', 'new image');

    expect(EncapImage::url('categories/sample.jpg'))
        ->toBe(Storage::disk('encap_images')->url('categories/sample.jpg'));
});

test('image preparation downloads site photography and copies legacy uploads without deleting rollback copies', function () {
    Storage::fake('public');
    Storage::fake('encap_images');
    Storage::disk('public')->put('categories/legacy.jpg', 'legacy image');

    $jpeg = "\xFF\xD8\xFF" . str_repeat('A', 6000);
    Http::fake([
        '*' => Http::response($jpeg, 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $this->artisan('encap:images:prepare')->assertSuccessful();

    Storage::disk('encap_images')->assertExists('farm-landscape.jpg');
    Storage::disk('encap_images')->assertExists('cargo-port.jpg');
    Storage::disk('encap_images')->assertExists('categories/legacy.jpg');
    Storage::disk('public')->assertExists('categories/legacy.jpg');
});
