<?php

use App\\Models\\SeoSetting;
use Illuminate\\Support\\Facades\\Cache;

test('footer renders a large accessible icon for each configured social profile', function () {
    SeoSetting::query()->create([
        'facebook_url' => 'https://www.facebook.com/encap-example',
        'instagram_url' => 'https://www.instagram.com/encap-example/',
        'linkedin_url' => 'https://www.linkedin.com/company/encap-example/',
        'youtube_url' => 'https://www.youtube.com/@encap-example',
    ]);
    Cache::forget('encap.seo-settings');

    $html = $this->get('/ar')->assertOk()->getContent();

    foreach (['facebook', 'instagram', 'linkedin', 'youtube'] as $network) {
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
        ->toContain('target="_blank"')
        ->toContain('rel="noopener noreferrer"');
});

test('footer hides empty or non-official social profile URLs', function () {
    SeoSetting::query()->create([
        'facebook_url' => 'https://example.com/not-facebook',
        'instagram_url' => 'http://www.instagram.com/encap-example/',
        'linkedin_url' => 'javascript:alert(1)',
        'youtube_url' => null,
    ]);
    Cache::forget('encap.seo-settings');

    $html = $this->get('/ar')->assertOk()->getContent();

    expect($html)
        ->not->toContain('data-social-network="facebook"')
        ->not->toContain('data-social-network="instagram"')
        ->not->toContain('data-social-network="linkedin"')
        ->not->toContain('data-social-network="youtube"')
        ->not->toContain('class="encap-footer-socials"');
});
