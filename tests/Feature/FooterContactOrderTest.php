<?php

test('footer places companies first, gives each contact name and role before phone numbers, and keeps ENCAP branding underneath', function () {
    foreach ([
        '/en' => [
            'contacts' => [
                ['name' => 'Ahmed Elshamy', 'role' => 'General Manager - Saudi Arabia', 'phone' => 'tel:+966568309656', 'email' => 'ahmedelshamy@encap-global.com'],
                ['name' => 'Shaban Shahen', 'role' => 'General Manager - Egypt', 'phone' => 'tel:+966530750780', 'email' => 'shabanshahen@encap-global.com'],
                ['name' => 'Mohamed Saad', 'role' => 'Export Sector Manager', 'phone' => 'tel:+201060113074', 'email' => 'mohamedsaad@encap-global.com'],
            ],
            'tagline' => 'Agricultural production · Sourcing · Processing · Export',
        ],
        '/fr' => [
            'contacts' => [
                ['name' => 'Ahmed Elshamy', 'role' => 'Directeur général - Arabie saoudite', 'phone' => 'tel:+966568309656', 'email' => 'ahmedelshamy@encap-global.com'],
                ['name' => 'Shaban Shahen', 'role' => 'Directeur général - Égypte', 'phone' => 'tel:+966530750780', 'email' => 'shabanshahen@encap-global.com'],
                ['name' => 'Mohamed Saad', 'role' => 'Responsable du secteur export', 'phone' => 'tel:+201060113074', 'email' => 'mohamedsaad@encap-global.com'],
            ],
            'tagline' => 'Production · Approvisionnement · Transformation · Exportation',
        ],
        '/ar' => [
            'contacts' => [
                ['name' => 'أحمد الشامي', 'role' => 'المدير العام - المملكة العربية السعودية', 'phone' => 'tel:+966568309656', 'email' => 'ahmedelshamy@encap-global.com'],
                ['name' => 'شعبان شاهين', 'role' => 'المدير العام - مصر', 'phone' => 'tel:+966530750780', 'email' => 'shabanshahen@encap-global.com'],
                ['name' => 'محمد سعد', 'role' => 'مدير قطاع التصدير', 'phone' => 'tel:+201060113074', 'email' => 'mohamedsaad@encap-global.com'],
            ],
            'tagline' => 'إنتاج زراعي · توريد · تجهيز · تصدير',
        ],
        '/ru' => [
            'contacts' => [
                ['name' => 'Ahmed Elshamy', 'role' => 'Генеральный директор — Саудовская Аравия', 'phone' => 'tel:+966568309656', 'email' => 'ahmedelshamy@encap-global.com'],
                ['name' => 'Shaban Shahen', 'role' => 'Генеральный директор — Египет', 'phone' => 'tel:+966530750780', 'email' => 'shabanshahen@encap-global.com'],
                ['name' => 'Mohamed Saad', 'role' => 'Руководитель экспортного направления', 'phone' => 'tel:+201060113074', 'email' => 'mohamedsaad@encap-global.com'],
            ],
            'tagline' => 'Сельхозпроизводство · Закупка · Переработка · Экспорт',
        ],
        '/tr' => [
            'contacts' => [
                ['name' => 'Ahmed Elshamy', 'role' => 'Genel Müdür - Suudi Arabistan', 'phone' => 'tel:+966568309656', 'email' => 'ahmedelshamy@encap-global.com'],
                ['name' => 'Shaban Shahen', 'role' => 'Genel Müdür - Mısır', 'phone' => 'tel:+966530750780', 'email' => 'shabanshahen@encap-global.com'],
                ['name' => 'Mohamed Saad', 'role' => 'İhracat Bölüm Müdürü', 'phone' => 'tel:+201060113074', 'email' => 'mohamedsaad@encap-global.com'],
            ],
            'tagline' => 'Tarımsal üretim · Tedarik · İşleme · İhracat',
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
            $roleTextPosition = strpos($card, $contact['role']);
            $phoneListPosition = strpos($card, 'class="encap-footer-phone-list"');
            $phonePosition = strpos($card, 'href="' . $contact['phone'] . '"');
            $emailPosition = strpos($card, 'href="mailto:' . $contact['email'] . '"');
            $locationPosition = strpos($card, 'class="encap-footer-person-location"');

            foreach ([$namePosition, $rolePosition, $roleTextPosition, $phoneListPosition, $phonePosition, $emailPosition, $locationPosition] as $position) {
                expect($position)->not->toBeFalse();
            }

            expect($namePosition)->toBeLessThan($rolePosition);
            expect($rolePosition)->toBeLessThan($roleTextPosition);
            expect($roleTextPosition)->toBeLessThan($phoneListPosition);
            expect($phoneListPosition)->toBeLessThan($phonePosition);
            expect($phonePosition)->toBeLessThan($emailPosition);
            expect($emailPosition)->toBeLessThan($locationPosition);
        }

        $renderedSocialsPosition = strpos($footerHtml, 'class="encap-footer-contact-socials"');
        if ($renderedSocialsPosition !== false) {
            expect($taglinePosition)->toBeLessThan($renderedSocialsPosition);
            expect($renderedSocialsPosition)->toBeLessThan($wordmarkPosition);
        }
    }
});


test('contact page displays the regional general manager titles in every supported locale', function () {
    $titlesByLocale = [
        'en' => ['General Manager - Saudi Arabia', 'General Manager - Egypt'],
        'ar' => ['المدير العام - المملكة العربية السعودية', 'المدير العام - مصر'],
        'fr' => ['Directeur général - Arabie saoudite', 'Directeur général - Égypte'],
        'ru' => ['Генеральный директор — Саудовская Аравия', 'Генеральный директор — Египет'],
        'tr' => ['Genel Müdür - Suudi Arabistan', 'Genel Müdür - Mısır'],
    ];

    foreach ($titlesByLocale as $locale => $titles) {
        $response = $this->get('/'.$locale.'/contact')->assertOk();

        foreach ($titles as $title) {
            $response->assertSee($title);
        }
    }
});
