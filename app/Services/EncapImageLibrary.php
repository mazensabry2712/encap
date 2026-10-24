<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

final class EncapImageLibrary
{
    private const STOCK_IMAGES = [
        'orange-orchard-hero-2026.jpg' => 'https://images.pexels.com/photos/10970006/pexels-photo-10970006.jpeg?auto=compress&cs=tinysrgb&w=2000',
        'produce-mix-2026.jpg' => 'https://images.unsplash.com/photo-1764390745408-74f0dbbb5bca?fit=crop&fm=jpg&w=1600&q=85',
        'farm-cultivation-2026.jpg' => 'https://images.unsplash.com/photo-1719757168062-3ceb06988532?fit=crop&fm=jpg&w=1400&q=85',
        'orchard-harvest-2026.jpg' => 'https://images.unsplash.com/photo-1643138411076-238fda8c1e1d?fit=crop&fm=jpg&w=1400&q=85',
        'citrus-orchard-2026.jpg' => 'https://images.unsplash.com/photo-1705573382809-bbf1a337746f?fit=crop&fm=jpg&w=1400&q=85',
        'product-mangoes-2026.jpg' => 'https://images.unsplash.com/photo-1605027990121-cbae9e0642df?fit=crop&fm=jpg&w=1400&q=85',
        'product-citrus-2026.jpg' => 'https://images.unsplash.com/photo-1705573382809-bbf1a337746f?fit=crop&fm=jpg&w=1400&q=85',
        'product-grapes-2026.jpg' => 'https://images.unsplash.com/photo-1626964146632-d08eadf9f5bb?fit=crop&fm=jpg&w=1400&q=85',
        'product-potatoes-2026.jpg' => 'https://images.unsplash.com/photo-1756361947495-7f56ed41ac38?fit=crop&fm=jpg&w=1400&q=85',
        'product-onions-2026.jpg' => 'https://images.unsplash.com/photo-1746499866328-d11aea3ed522?fit=crop&fm=jpg&w=1400&q=85',
        'product-garlic-2026.jpg' => 'https://images.unsplash.com/photo-1685130055879-c4e72e7281c9?fit=crop&fm=jpg&w=1400&q=85',
        'product-pomegranates-2026.jpg' => 'https://images.unsplash.com/photo-1642359548284-80879b7677dc?fit=crop&fm=jpg&w=1400&q=85',
        'product-peppers-2026.jpg' => 'https://images.unsplash.com/photo-1742304267119-330daeadcdca?fit=crop&fm=jpg&w=1400&q=85',
        'product-strawberries-2026.jpg' => 'https://images.unsplash.com/photo-1633360819525-fc421f11c65c?fit=crop&fm=jpg&w=1400&q=85',
        'potato-sorting-2026.jpg' => 'https://images.pexels.com/photos/2889093/pexels-photo-2889093.jpeg?auto=compress&cs=tinysrgb&w=1400',
        'produce-packing-2026.jpg' => 'https://images.pexels.com/photos/8877381/pexels-photo-8877381.jpeg?auto=compress&cs=tinysrgb&w=1400',
        'crate-labels-2026.jpg' => 'https://images.unsplash.com/photo-1625207883754-c0314540c03c?fit=crop&fm=jpg&w=1000&q=85',
        'export-crates-2026.jpg' => 'https://images.pexels.com/photos/7843989/pexels-photo-7843989.jpeg?auto=compress&cs=tinysrgb&w=1400',
        'frozen-berries-2026.jpg' => 'https://images.pexels.com/photos/9814690/pexels-photo-9814690.jpeg?auto=compress&cs=tinysrgb&w=1600',
        'cargo-port-2026.jpg' => 'https://images.unsplash.com/photo-1759272840538-ae4b07214c71?fit=crop&fm=jpg&w=1400&q=85',
    ];

    /**
     * @return array{downloaded: int, kept: int, copied: int, errors: list<string>, complete: bool}
     */
    public function prepare(bool $force = false, int $offset = 0, int $limit = 4): array
    {
        $images = Storage::disk('encap_images');
        $legacy = Storage::disk('public');
        $total = count(self::STOCK_IMAGES);
        $offset = max(0, min($offset, $total));
        $limit = max(1, min($limit, 5));
        $batch = array_slice(self::STOCK_IMAGES, $offset, $limit, true);
        $result = [
            'downloaded' => 0,
            'kept' => 0,
            'copied' => 0,
            'errors' => [],
            'complete' => false,
            'finished' => false,
            'offset' => $offset,
            'next_offset' => $offset + count($batch),
            'total' => $total,
        ];

        // Copy uploaded product/SEO images first. Shared hosting may stop a request
        // while the later third-party image downloads are still running.
        $allowedImage = '/\.(?:jpe?g|png|webp|avif|gif)$/i';

        foreach ($legacy->allFiles() as $path) {
            if (! preg_match('#^(?:categories|seo)/#', $path) || ! preg_match($allowedImage, $path)) {
                continue;
            }

            if ($images->exists($path)) {
                continue;
            }

            try {
                if ($images->put($path, $legacy->get($path))) {
                    $result['copied']++;
                } else {
                    $result['errors'][] = "{$path}: could not copy the existing upload.";
                }
            } catch (Throwable $exception) {
                $result['errors'][] = "{$path}: {$exception->getMessage()}";
            }
        }

        foreach ($batch as $filename => $url) {
            if (! $force && $images->exists($filename)) {
                $result['kept']++;
                continue;
            }

            try {
                $response = Http::connectTimeout(3)->timeout(7)->get($url);
                $body = $response->body();
                $contentType = strtolower((string) $response->header('Content-Type'));
                $isJpeg = strlen($body) > 5000 && str_starts_with($body, "\xFF\xD8\xFF");

                if (! $response->successful() || ! Str::startsWith($contentType, 'image/') || ! $isJpeg) {
                    $result['errors'][] = "{$filename}: download did not return a valid JPEG (HTTP {$response->status()}, {$contentType}).";
                    continue;
                }

                if (! $images->put($filename, $body)) {
                    $result['errors'][] = "{$filename}: could not save the downloaded image.";
                    continue;
                }

                $result['downloaded']++;
            } catch (Throwable $exception) {
                $result['errors'][] = "{$filename}: {$exception->getMessage()}";
            }
        }

        $result['finished'] = $result['next_offset'] >= $result['total'];
        $result['complete'] = $result['finished'] && $result['errors'] === [];

        return $result;
    }
}
