<?php

test('footer places group companies first, phone numbers second, and ENCAP brand navigation underneath contacts', function () {
    $layout = file_get_contents(resource_path('views/website/layout.blade.php'));

    $companiesMarkupPosition = strpos($layout, 'class="encap-footer-group-companies"');
    $businessMarkupPosition = strpos($layout, 'class="encap-footer-business"');
    $firstPhoneMarkupPosition = strpos($layout, 'href="tel:+966568309656"');
    $lastPhoneMarkupPosition = strpos($layout, 'href="tel:+201060113074"');
    $taglineMarkupPosition = strpos($layout, 'class="encap-footer-contact-tagline"');
    $wordmarkMarkupPosition = strpos($layout, 'class="encap-footer-wordmark"');
    $mainMarkupPosition = strpos($layout, 'class="encap-footer-main"');

    foreach ([
        $companiesMarkupPosition,
        $businessMarkupPosition,
        $firstPhoneMarkupPosition,
        $lastPhoneMarkupPosition,
        $taglineMarkupPosition,
        $wordmarkMarkupPosition,
        $mainMarkupPosition,
    ] as $position) {
        expect($position)->not->toBeFalse();
    }

    expect($companiesMarkupPosition)->toBeLessThan($businessMarkupPosition);
    expect($businessMarkupPosition)->toBeLessThan($firstPhoneMarkupPosition);
    expect($firstPhoneMarkupPosition)->toBeLessThan($lastPhoneMarkupPosition);
    expect($lastPhoneMarkupPosition)->toBeLessThan($taglineMarkupPosition);
    expect($taglineMarkupPosition)->toBeLessThan($wordmarkMarkupPosition);
    expect($wordmarkMarkupPosition)->toBeLessThan($mainMarkupPosition);

    foreach ([
        '/ar' => [
            'contactName' => 'أحمد الشامي',
            'tagline' => 'إنتاج زراعي · توريد · تجهيز · تصدير',
        ],
        '/en' => [
            'contactName' => 'Ahmed Elshamy',
            'tagline' => 'Agricultural production · Sourcing · Processing · Export',
        ],
    ] as $path => $expected) {
        $html = $this->get($path)->assertOk()->getContent();

        $companiesPosition = strpos($html, 'class="encap-footer-group-companies"');
        $businessPosition = strpos($html, 'class="encap-footer-business"');
        $businessHtml = $businessPosition === false ? '' : substr($html, $businessPosition);

        $firstPhoneOffset = strpos($businessHtml, 'href="tel:+966568309656"');
        $lastPhoneOffset = strpos($businessHtml, 'href="tel:+201060113074"');
        $contactNameOffset = strpos($businessHtml, $expected['contactName']);
        $taglineOffset = strpos($businessHtml, $expected['tagline']);

        $firstPhonePosition = $firstPhoneOffset === false ? false : $businessPosition + $firstPhoneOffset;
        $lastPhonePosition = $lastPhoneOffset === false ? false : $businessPosition + $lastPhoneOffset;
        $contactNamePosition = $contactNameOffset === false ? false : $businessPosition + $contactNameOffset;
        $taglinePosition = $taglineOffset === false ? false : $businessPosition + $taglineOffset;
        $wordmarkPosition = strpos($html, 'class="encap-footer-wordmark"');
        $mainPosition = strpos($html, 'class="encap-footer-main"');
        $bottomPosition = strpos($html, 'class="encap-footer-bottom"');

        foreach ([
            $companiesPosition,
            $businessPosition,
            $firstPhonePosition,
            $lastPhonePosition,
            $contactNamePosition,
            $taglinePosition,
            $wordmarkPosition,
            $mainPosition,
            $bottomPosition,
        ] as $position) {
            expect($position)->not->toBeFalse();
        }

        expect($companiesPosition)->toBeLessThan($businessPosition);
        expect($businessPosition)->toBeLessThan($firstPhonePosition);
        expect($firstPhonePosition)->toBeLessThan($contactNamePosition);
        expect($lastPhonePosition)->toBeLessThan($taglinePosition);
        expect($taglinePosition)->toBeLessThan($wordmarkPosition);
        expect($wordmarkPosition)->toBeLessThan($mainPosition);
        expect($mainPosition)->toBeLessThan($bottomPosition);

        $renderedSocialsPosition = strpos($html, 'class="encap-footer-contact-socials"');
        if ($renderedSocialsPosition !== false) {
            expect($taglinePosition)->toBeLessThan($renderedSocialsPosition);
            expect($renderedSocialsPosition)->toBeLessThan($wordmarkPosition);
        }
    }
});
