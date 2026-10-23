<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

final class EncapImageLibrary
{
    private const STOCK_IMAGES = [
        'farm-landscape.jpg' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?fit=crop&fm=jpg&w=2000&q=78',
        'fresh-produce.jpg' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?fit=crop&fm=jpg&w=1600&q=78',
        'product-mangoes.jpg' => 'https://images.pexels.com/photos/5767159/pexels-photo-5767159.jpeg?auto=compress&cs=tinysrgb&w=1400',
        'product-mangoes-export.jpg' => 'https://images.pexels.com/photos/5640033/pexels-photo-5640033.jpeg?auto=compress&cs=tinysrgb&w=1600',
        'product-onions-export.jpg' => 'https://images.pexels.com/photos/6316541/pexels-photo-6316541.jpeg?auto=compress&cs=tinysrgb&w=1600',
        'product-potatoes-export.jpg' => 'https://images.pexels.com/photos/10899606/pexels-photo-10899606.jpeg?auto=compress&cs=tinysrgb&w=1600',
        'product-grapes.jpg' => 'https://images.pexels.com/photos/9192252/pexels-photo-9192252.jpeg?auto=compress&cs=tinysrgb&w=1400',
        'product-onions.jpg' => 'https://images.pexels.com/photos/10444827/pexels-photo-10444827.jpeg?auto=compress&cs=tinysrgb&w=1400',
        'product-garlic.jpg' => 'https://images.pexels.com/photos/4024739/pexels-photo-4024739.jpeg?auto=compress&cs=tinysrgb&w=1400',
        'product-pomegranates.jpg' => 'https://images.pexels.com/photos/29310033/pexels-photo-29310033/free-photo-of-close-up-of-fresh-pomegranates-on-display.jpeg?auto=compress&cs=tinysrgb&w=1400',
        'product-peppers.jpg' => 'https://images.pexels.com/photos/35542734/pexels-photo-35542734/free-photo-of-colorful-bell-peppers-display-at-market.jpeg?auto=compress&cs=tinysrgb&w=1400',
        'product-strawberries.jpg' => 'https://images.pexels.com/photos/12095219/pexels-photo-12095219.jpeg?auto=compress&cs=tinysrgb&w=1400',
        'cultivated-field.jpg' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?fit=crop&fm=jpg&w=1400&q=78',
        'orchard-harvest.jpg' => 'https://images.unsplash.com/photo-1761839259494-71caddcdd6b3?fit=crop&fm=jpg&w=1400&q=78',
        'citrus-orchard.jpg' => 'https://images.unsplash.com/photo-1772638583263-a4f953c856cf?fit=crop&fm=jpg&w=1000&q=76',
        'potato-sorting.jpg' => 'https://images.unsplash.com/photo-1767982619694-32f8450ea5f7?fit=crop&fm=jpg&w=1400&q=78',
        'packing-line-berries.jpg' => 'https://images.pexels.com/photos/5085939/pexels-photo-5085939.jpeg?auto=compress&cs=tinysrgb&w=1000',
        'packing-warehouse.jpg' => 'https://images.unsplash.com/photo-1755599604342-186a96a57570?fit=crop&fm=jpg&w=1000&q=76',
        'carton-labels.jpg' => 'https://images.unsplash.com/photo-1768656089428-111196e0b1ab?fit=crop&fm=jpg&w=1000&q=76',
        'dispatch-cartons.jpg' => 'https://images.unsplash.com/photo-1775880306542-57d598d773a0?fit=crop&fm=jpg&w=1000&q=76',
        'frozen-berries.jpg' => 'https://images.unsplash.com/photo-1438907046657-4ae137eb8c5e?fit=crop&fm=jpg&w=1600&q=78',
        'frozen-transport.jpg' => 'https://images.pexels.com/photos/27099094/pexels-photo-27099094/free-photo-of-view-of-parked-trucks.jpeg?auto=compress&dpr=1&h=750&w=1260',
        'cargo-port.jpg' => 'https://images.unsplash.com/photo-1774929104515-ac1b747a8c4c?fit=crop&fm=jpg&w=1400&q=78',
    ];

    /**
     * @return array{downloaded: int, kept: int, copied: int, errors: list<string>, complete: bool}
     */
    public function prepare(bool $force = false): array
    {
        $images = Storage::disk('encap_images');
        $legacy = Storage::disk('public');
        $result = ['downloaded' => 0, 'kept' => 0, 'copied' => 0, 'errors' => [], 'complete' => true];

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

        foreach (self::STOCK_IMAGES as $filename => $url) {
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

        $result['complete'] = $result['errors'] === [];

        return $result;
    }
}
