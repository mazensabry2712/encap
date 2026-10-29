<?php

test('footer places companies first, gives each contact name and role before phone numbers, and keeps ENCAP branding underneath', function () {
    foreach ([
        '/ar' => [
            'contacts' => [
                ['name' => 'أحمد الشامي', 'phone' => 'tel:+966568309656'],
                ['name' => 'شعبان شاهين', 'phone' => 'tel:+966530750780'],
                ['name' => 'محمد سعد', 'phone' => 'tel:+201060113074'],
            ],
            'tagline' => 'إنتاج زراعي · توريد · تجهيز · تصدير',
        ],
        '/en' => [
            'contacts' => [
                ['name' => 'Ahmed Elshamy', 'phone' => 'tel:+966568309656'],
                ['name' => 'Shaban Shahen', 'phone' => 'tel:+966530750780'],
                ['name' => 'Mohamed Saad', 'phone' => 'tel:+201060113074'],
            ],
            'tagline' => 'Agricultural production · Sourcing · Processing · Export',
        ],
    ] as $path => $expected) {
        $html = $this->get($path)->assertOk()->getContent();
        $footerPosition = strpos($html, '<footer class="footer encap-footer">');
        expect($footerPosition)->not->toBeFalse();
        $footerHtml = substr($html, $footerPosition);

        $companiesPosition = strpos($footerHtml, 'class="encap-footer-group-companies"');
        $companyProfilePosition = strpos($footerHtml, 'class="encap-footer-group-company-identity"');
        $businessPosition = strpos($footerHtml, 'class="encap-footer-business"');
        $taglinePosition = strpos($footerHtml, $expected['tagline']);

        expect($companyProfilePosition)->not->toBeFalse();
        expect(str_contains($footerHtml, '/'.substr($path, 1).'/companies'))->toBeTrue();
        $wordmarkPosition = strpos($footerHtml, 'class="encap-footer-wordmark"');
        $mainPosition = strpos($footerHtml, 'class="encap-footer-main"');
        $bottomPosition = strpos($footerHtml, 'class="encap-footer-bottom"');

        foreach ([$companiesPosition, $businessPosition, $taglinePosition, $wordmarkPosition, $mainPosition, $bottomPosition] as $position) {
            expect($position)->not->toBeFalse();
        }

        expect($companiesPosition)->toBeLessThan($businessPosition);
        expect($businessPosition)->toBeLessThan($taglinePosition);
        expect($taglinePosition)->toBeLessThan($wordmarkPosition);
        expect($wordmarkPosition)->toBeLessThan($mainPosition);
        expect($mainPosition)->toBeLessThan($bottomPosition);

        preg_match_all(
            '/<article class="encap-footer-business-card encap-footer-person-card">(.*?)<\/article>/s',
            substr($footerHtml, $businessPosition),
            $cardMatches
        );

        expect($cardMatches[1])->toHaveCount(3);

        foreach ($expected['contacts'] as $index => $contact) {
            $card = $cardMatches[1][$index];
            $namePosition = strpos($card, $contact['name']);
            $rolePosition = strpos($card, 'class="encap-footer-person-role"');
            $phoneListPosition = strpos($card, 'class="encap-footer-phone-list"');
            $phonePosition = strpos($card, 'href="' . $contact['phone'] . '"');
            $locationPosition = strpos($card, 'class="encap-footer-person-location"');

            foreach ([$namePosition, $rolePosition, $phoneListPosition, $phonePosition, $locationPosition] as $position) {
                expect($position)->not->toBeFalse();
            }

            expect($namePosition)->toBeLessThan($rolePosition);
            expect($rolePosition)->toBeLessThan($phoneListPosition);
            expect($phoneListPosition)->toBeLessThan($phonePosition);
            expect($phonePosition)->toBeLessThan($locationPosition);
        }

        $renderedSocialsPosition = strpos($footerHtml, 'class="encap-footer-contact-socials"');
        if ($renderedSocialsPosition !== false) {
            expect($taglinePosition)->toBeLessThan($renderedSocialsPosition);
            expect($renderedSocialsPosition)->toBeLessThan($wordmarkPosition);
        }
    }
});
