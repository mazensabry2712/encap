<?php

test('public translation dictionaries stay complete and contain no blank values', function () {
    $english = json_decode(
        file_get_contents(lang_path('en.json')),
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    expect($english)->toBeArray();

    $englishKeys = array_keys($english);

    foreach (['fr', 'ar', 'ru', 'tr'] as $locale) {
        $translations = json_decode(
            file_get_contents(lang_path($locale . '.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        expect($translations)->toBeArray();
        expect(array_diff($englishKeys, array_keys($translations)))->toBe([]);

        $blankValues = array_filter(
            $translations,
            fn ($value) => ! is_string($value) || trim($value) === ''
        );

        expect($blankValues)->toBe([]);

        // French, Russian and Turkish share the public-site dictionary with English.
        // Arabic also contains additional admin-interface translations.
        if ($locale !== 'ar') {
            expect(array_diff(array_keys($translations), $englishKeys))->toBe([]);
        }
    }

    $blankEnglishValues = array_filter(
        $english,
        fn ($value) => ! is_string($value) || trim($value) === ''
    );

    expect($blankEnglishValues)->toBe([]);
});

test('public pages render in every supported locale', function () {
    $locales = ['en', 'ar', 'fr', 'ru', 'tr'];
    $pages = [
        '',
        '/about',
        '/farms',
        '/packing-stations',
        '/quality',
        '/export',
        '/products',
        '/contact',
        '/request-a-quote',
    ];

    foreach ($locales as $locale) {
        foreach ($pages as $page) {
            $this->get('/' . $locale . $page)->assertOk();
        }
    }
});

test('about-page journey labels use the correct locale', function () {
    $labelsByLocale = [
        'en' => [
            '01 / ORIGIN',
            '02 / SOURCING',
            '03 / PREPARATION',
            '04 / DESTINATION',
        ],
        'fr' => [
            '01 / ORIGINE',
            '02 / APPROVISIONNEMENT',
            '03 / PRÉPARATION',
            '04 / DESTINATION',
        ],
        'ar' => [
            '٠١ / المصدر',
            '٠٢ / التوريد',
            '٠٣ / التجهيز',
            '٠٤ / الوجهة',
        ],
        'ru' => [
            '01 / ПРОИСХОЖДЕНИЕ',
            '02 / ПОСТАВКИ',
            '03 / ПОДГОТОВКА',
            '04 / НАЗНАЧЕНИЕ',
        ],
        'tr' => [
            '01 / KÖKEN',
            '02 / TEDARİK',
            '03 / HAZIRLIK',
            '04 / VARIŞ NOKTASI',
        ],
    ];

    foreach ($labelsByLocale as $locale => $labels) {
        $response = $this->get('/' . $locale . '/about')->assertOk();

        foreach ($labels as $label) {
            $response->assertSee($label);
        }
    }
});
