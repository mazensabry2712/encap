<?php

use App\Models\SeoSetting;
use Illuminate\Support\Facades\Cache;

test('footer renders a large accessible icon for each configured social profile', function () {
    SeoSetting::query()->create([
        'facebook_url' => 'https://www.facebook.com/encap-example',
        'instagram_url' => 'https://www.instagram.com/encap-example/',
        'linkedin_url' => 'https://www.linkedin.com/company/encap-example/',
        'youtube_url' => 'https://www.youtube.com/@encap-example',
        'whatsapp_number' => '+20 123 456 7890',
    ]);
    Cache::forget('encap.seo-settings');

    $html = $this->get('/ar')->assertOk()->getContent();

    foreach (['facebook', 'instagram', 'linkedin', 'youtube', 'whatsapp'] as $network) {
        expect($html)
            ->toContain('data-social-network="'.$network.'"')
            ->toContain('encap-footer-social-link--'.$network)
            ->toContain('class="encap-footer-social-icon"')
            ->toContain('class="encap-footer-social-name"');
    }

    expect($html)
        ->toContain('aria-label="Facebook"')
        ->toContain('aria-label="Instagram"')
        ->toContain('aria-label="LinkedIn"')
        ->toContain('aria-label="YouTube"')
        ->toContain('aria-label="WhatsApp"')
        ->toContain('href="https://wa.me/201234567890"')
        ->toContain('target="_blank"')
        ->toContain('rel="noopener noreferrer"');
});

test('footer hides empty or non-official social profile URLs and invalid WhatsApp numbers', function () {
    SeoSetting::query()->create([
        'facebook_url' => 'https://example.com/not-facebook',
        'instagram_url' => 'http://www.instagram.com/encap-example/',
        'linkedin_url' => 'javascript:alert(1)',
        'youtube_url' => null,
        'whatsapp_number' => 'not an international number',
    ]);
    Cache::forget('encap.seo-settings');

    $html = $this->get('/ar')->assertOk()->getContent();

    expect($html)
        ->not->toContain('data-social-network="facebook"')
        ->not->toContain('data-social-network="instagram"')
        ->not->toContain('data-social-network="linkedin"')
        ->not->toContain('data-social-network="youtube"')
        ->not->toContain('data-social-network="whatsapp"')
        ->not->toContain('class="encap-footer-socials"');
});
