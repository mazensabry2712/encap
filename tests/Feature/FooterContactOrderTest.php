<?php

test('footer shows group companies before regional contacts and phone numbers before contact details', function () {
    foreach ([
        '/ar' => 'أحمد الشامي',
        '/en' => 'Ahmed Elshamy',
    ] as $path => $contactName) {
        $html = $this->get($path)->assertOk()->getContent();

        $companiesPosition = strpos($html, 'class="encap-footer-group-companies"');
        $businessPosition = strpos($html, 'class="encap-footer-business"');

        expect($companiesPosition)->not->toBeFalse();
        expect($businessPosition)->not->toBeFalse();
        expect($companiesPosition)->toBeLessThan($businessPosition);

        $businessHtml = substr($html, $businessPosition);
        $phonePosition = strpos($businessHtml, 'href="tel:+966568309656"');
        $contactNamePosition = strpos($businessHtml, $contactName);

        expect($phonePosition)->not->toBeFalse();
        expect($contactNamePosition)->not->toBeFalse();
        expect($phonePosition)->toBeLessThan($contactNamePosition);
    }
});
