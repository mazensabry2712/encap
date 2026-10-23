<?php

namespace App\Support;

use App\Models\Category;

final class EncapProductImage
{
    /**
     * Real stock photographs used only when a category has no uploaded main image.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const FALLBACKS = [
        'mango' => [
            'product-mangoes.jpg',
            'https://images.pexels.com/photos/5767159/pexels-photo-5767159.jpeg?auto=compress&cs=tinysrgb&w=1400',
        ],
        'citrus' => [
            'citrus-orchard.jpg',
            'https://images.unsplash.com/photo-1772638583263-a4f953c856cf?fit=crop&fm=jpg&w=1000&q=76',
        ],
        'grapes' => [
            'product-grapes.jpg',
            'https://images.pexels.com/photos/9192252/pexels-photo-9192252.jpeg?auto=compress&cs=tinysrgb&w=1400',
        ],
        'potatoes' => [
            'potato-sorting.jpg',
            'https://images.unsplash.com/photo-1767982619694-32f8450ea5f7?fit=crop&fm=jpg&w=1400&q=78',
        ],
        'onions' => [
            'product-onions.jpg',
            'https://images.pexels.com/photos/10444827/pexels-photo-10444827.jpeg?auto=compress&cs=tinysrgb&w=1400',
        ],
        'garlic' => [
            'product-garlic.jpg',
            'https://images.pexels.com/photos/4024739/pexels-photo-4024739.jpeg?auto=compress&cs=tinysrgb&w=1400',
        ],
        'pomegranates' => [
            'product-pomegranates.jpg',
            'https://images.pexels.com/photos/29310033/pexels-photo-29310033/free-photo-of-close-up-of-fresh-pomegranates-on-display.jpeg?auto=compress&cs=tinysrgb&w=1400',
        ],
        'peppers' => [
            'product-peppers.jpg',
            'https://images.pexels.com/photos/35542734/pexels-photo-35542734/free-photo-of-colorful-bell-peppers-display-at-market.jpeg?auto=compress&cs=tinysrgb&w=1400',
        ],
        'strawberries' => [
            'product-strawberries.jpg',
            'https://images.pexels.com/photos/12095219/pexels-photo-12095219.jpeg?auto=compress&cs=tinysrgb&w=1400',
        ],
        'other-fruits-vegetables' => [
            'fresh-produce.jpg',
            'https://images.unsplash.com/photo-1542838132-92c53300491e?fit=crop&fm=jpg&w=1600&q=78',
        ],
    ];


    /**
     * Better curated photography for categories whose current live images need replacement.
     * The known legacy mango upload is replaced; any newer admin upload remains preferred.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const PREFERRED_PHOTOGRAPHY = [
        'mango' => [
            'product-mangoes-export.jpg',
            'https://images.pexels.com/photos/5640033/pexels-photo-5640033.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
        'onions' => [
            'product-onions-export.jpg',
            'https://images.pexels.com/photos/6316541/pexels-photo-6316541.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
        'potatoes' => [
            'product-potatoes-export.jpg',
            'https://images.pexels.com/photos/10899606/pexels-photo-10899606.jpeg?auto=compress&cs=tinysrgb&w=1600',
        ],
    ];

    private const LEGACY_MANGO_IMAGE = 'categories/ndGv4yhjoLuLommuuXX34GErfjuIUG1wJyiMaDef.jpg';

    public static function url(Category $category): ?string
    {
        $preferred = self::PREFERRED_PHOTOGRAPHY[$category->slug] ?? null;
        $replaceLegacyMango = $category->slug === 'mango'
            && $category->image === self::LEGACY_MANGO_IMAGE;

        if ($preferred && (!filled($category->image) || $replaceLegacyMango)) {
            return EncapImage::url($preferred[0], $preferred[1]);
        }

        if (filled($category->image)) {
            return EncapImage::url($category->image);
        }

        $fallback = self::FALLBACKS[$category->slug] ?? self::FALLBACKS['other-fruits-vegetables'];

        return EncapImage::url($fallback[0], $fallback[1]);
    }

    /**
     * @return list<string>
     */
    public static function galleryUrls(Category $category): array
    {
        $mainImage = self::url($category);
        $galleryImages = array_map(
            static fn (string $path): string => EncapImage::url($path),
            $category->gallery ?? []
        );

        return array_values(array_filter(array_merge(
            $mainImage ? [$mainImage] : [],
            $galleryImages
        )));
    }
}
