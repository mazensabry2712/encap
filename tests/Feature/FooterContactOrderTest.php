<?php

test('footer shows group companies before regional contacts and places phone numbers before contact details', function () {
    $layout = file_get_contents(resource_path('views/website/layout.blade.php'));

    $businessGridPosition = strpos($layout, '<div class="encap-footer-business-grid">');
    $lastPhoneMarkupPosition = strpos($layout, 'href="tel:+201060113074"');
    $followSectionPosition = strpos($layout, 'class="encap-footer-contact-follow"');
    $socialMarkupPosition = strpos($layout, 'class="encap-footer-contact-socials"');

    expect($businessGridPosition)->not->toBeFalse();
    expect($lastPhoneMarkupPosition)->not->toBeFalse();
    expect($followSectionPosition)->not->toBeFalse();
    expect($socialMarkupPosition)->not->toBeFalse();
    expect($businessGridPosition)->toBeLessThan($lastPhoneMarkupPosition);
    expect($lastPhoneMarkupPosition)->toBeLessThan($followSectionPosition);
    expect($followSectionPosition)->toBeLessThan($socialMarkupPosition);

    foreach ([
        '/ar' => ['contactName' => 'أحمد الشامي', 'tagline' => 'إنتاج زراعي · توريد · تجهيز · تصدير'],
        '/en' => ['contactName' => 'Ahmed Elshamy', 'tagline' => 'Agricultural production · Sourcing · Processing · Export'],
    ] as $path => $expected) {
        $html = $this->get($path)->assertOk()->getContent();

        $companiesPosition = strpos($html, 'class="encap-footer-group-companies"');
        $businessPosition = strpos($html, 'class="encap-footer-business"');

        expect($companiesPosition)->not->toBeFalse();
        expect($businessPosition)->not->toBeFalse();
        expect($companiesPosition)->toBeLessThan($businessPosition);

        $businessHtml = substr($html, $businessPosition);
        $firstPhonePosition = strpos($businessHtml, 'href="tel:+966568309656"');
        $lastPhonePosition = strpos($businessHtml, 'href="tel:+201060113074"');
        $contactNamePosition = strpos($businessHtml, $expected['contactName']);
        $taglinePosition = strpos($businessHtml, $expected['tagline']);

        expect($firstPhonePosition)->not->toBeFalse();
        expect($lastPhonePosition)->not->toBeFalse();
        expect($contactNamePosition)->not->toBeFalse();
        expect($taglinePosition)->not->toBeFalse();
        expect($firstPhonePosition)->toBeLessThan($contactNamePosition);
        expect($lastPhonePosition)->toBeLessThan($taglinePosition);

        $renderedSocialsPosition = strpos($businessHtml, 'class="encap-footer-contact-socials"');
        if ($renderedSocialsPosition !== false) {
            expect($taglinePosition)->toBeLessThan($renderedSocialsPosition);
        }
    }
});
