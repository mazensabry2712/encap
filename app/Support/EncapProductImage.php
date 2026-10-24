<?php

namespace App\\Support;

use App\\Models\\Category;

final class EncapProductImage
{
    /**
     * Crop-specific stock photographs used only when a category has no main image.
     * An image uploaded through the admin dashboard always takes precedence,
     * except for mango, which uses one curated image to replace the old upload.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const FALLBACKS = [
        'mango' => [
            'product-mangoes-2026.jpg',
            'https://images.unsplash.com/photo-1605027990121-cbae9e0642df?fit=crop&fm=jpg&w=1400&q=85',
        ],
        'citrus' => [
            'product-citrus-2026.jpg',
            'https://images.unsplash.com/photo-1705573382809-bbf1a337746f?fit=crop&fm=jpg&w=1400&q=85',
        ],
        'grapes' => [
            'product-grapes-2026.jpg',
            'https://images.unsplash.com/photo-1626964146632-d08eadf9f5bb?fit=crop&fm=jpg&w=1400&q=85',
        ],
        'potatoes' => [
            'product-potatoes-2026.jpg',
            'https://images.unsplash.com/photo-1756361947495-7f56ed41ac38?fit=crop&fm=jpg&w=1400&q=85',
        ],
        'onions' => [
            'product-onions-2026.jpg',
            'https://images.unsplash.com/photo-1746499866328-d11aea3ed522?fit=crop&fm=jpg&w=1400&q=85',
        ],
        'garlic' => [
            'product-garlic-2026.jpg',
            'https://images.unsplash.com/photo-1685130055879-c4e72e7281c9?fit=crop&fm=jpg&w=1400&q=85',
        ],
        'pomegranates' => [
            'product-pomegranates-2026.jpg',
            'https://images.unsplash.com/photo-1642359548284-80879b7677dc?fit=crop&fm=jpg&w=1400&q=85',
        ],
        'peppers' => [
            'product-peppers-2026.jpg',
            'https://images.unsplash.com/photo-1742304267119-330daeadcdca?fit=crop&fm=jpg&w=1400&q=85',
        ],
        'strawberries' => [
            'product-strawberries-2026.jpg',
            'https://images.unsplash.com/photo-1633360819525-fc421f11c65c?fit=crop&fm=jpg&w=1400&q=85',
        ],
        'other-fruits-vegetables' => [
            'produce-mix-2026.jpg',
            'https://images.unsplash.com/photo-1764390745408-74f0dbbb5bca?fit=crop&fm=jpg&w=1600&q=85',
        ],
    ];

    public static function url(Category $category): ?string
    {
        // Use one controlled stock photo for mango even if an older admin upload remains
        // attached to the database record. This prevents stale uploads from winning.
        if ($category->slug === 'mango') {
            [$path, $fallbackUrl] = self::FALLBACKS['mango'];

            return EncapImage::url($path, $fallbackUrl);
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

        // The previous uploaded mango image and its gallery are being retired together.
        $galleryImages = $category->slug === 'mango'
            ? []
            : array_map(
                static fn (string $path): string => EncapImage::url($path),
                $category->gallery ?? []
            );

        return array_values(array_filter(array_merge(
            $mainImage ? [$mainImage] : [],
            $galleryImages
        )));
    }
}
