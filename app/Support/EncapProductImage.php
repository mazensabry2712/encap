<?php

namespace App\Support;

use App\Models\Category;

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
            'mango-replacement-2026.jpg',
            'https://images.pexels.com/photos/29782357/pexels-photo-29782357/free-photo-of-ripe-mangoes-hanging-from-tree-in-tropical-setting.jpeg?auto=compress&w=1600',
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

    /**
     * Resolve the default photo and an independent slot for a category without
     * an explicit uploaded image. The slot key uses the category ID so two
     * categories can never share a replaceable fallback by accident.
     *
     * @return array{path: string, url: string, slot: string}
     */
    public static function fallback(Category $category): array
    {
        $fallback = self::FALLBACKS[$category->slug] ?? self::FALLBACKS['other-fruits-vegetables'];

        return [
            'path' => $fallback[0],
            'url' => $fallback[1],
            'slot' => 'products.category.'.$category->id,
        ];
    }

    public static function url(Category $category): ?string
    {
        // Mango deliberately uses a curated image instead of an older upload.
        if ($category->slug === 'mango') {
            $fallback = self::fallback($category);

            return EncapImage::slot($fallback['slot'], $fallback['path'], $fallback['url']);
        }

        if (filled($category->image)) {
            return EncapImage::url($category->image);
        }

        $fallback = self::fallback($category);

        return EncapImage::slot($fallback['slot'], $fallback['path'], $fallback['url']);
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
