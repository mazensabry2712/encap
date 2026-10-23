<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class PrepareEncapImages extends Command
{
    protected $signature = 'encap:images:prepare {--force : Re-download the bundled editorial images}';

    protected $description = 'Download the site photography and copy existing uploaded images into encap-img outside public.';

    private const STOCK_IMAGES = [
        'farm-landscape.jpg' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?fit=crop&w=2000&q=78',
        'fresh-produce.jpg' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?fit=crop&w=1600&q=78',
        'cultivated-field.jpg' => 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?fit=crop&w=1400&q=78',
        'orchard-harvest.jpg' => 'https://images.unsplash.com/photo-1761839259494-71caddcdd6b3?fit=crop&w=1400&q=78',
        'citrus-orchard.jpg' => 'https://images.unsplash.com/photo-1772638583263-a4f953c856cf?fit=crop&w=1000&q=76',
        'potato-sorting.jpg' => 'https://images.unsplash.com/photo-1767982619694-32f8450ea5f7?fit=crop&w=1400&q=78',
        'packing-line-berries.jpg' => 'https://images.pexels.com/photos/5085939/pexels-photo-5085939.jpeg?auto=compress&cs=tinysrgb&w=1000',
        'packing-warehouse.jpg' => 'https://images.unsplash.com/photo-1755599604342-186a96a57570?fit=crop&w=1000&q=76',
        'carton-labels.jpg' => 'https://images.unsplash.com/photo-1768656089428-111196e0b1ab?fit=crop&w=1000&q=76',
        'dispatch-cartons.jpg' => 'https://images.unsplash.com/photo-1775880306542-57d598d773a0?fit=crop&w=1000&q=76',
        'cargo-port.jpg' => 'https://images.unsplash.com/photo-1774929104515-ac1b747a8c4c?fit=crop&w=1400&q=78',
    ];

    public function handle(): int
    {
        $images = Storage::disk('encap_images');
        $legacy = Storage::disk('public');
        $failed = false;
        $downloaded = 0;
        $copied = 0;

        foreach (self::STOCK_IMAGES as $filename => $url) {
            if (!$this->option('force') && $images->exists($filename)) {
                $this->line("Kept existing {$filename}");
                continue;
            }

            try {
                $response = Http::timeout(45)->retry(2, 500)->get($url);
                $body = $response->body();
                $contentType = strtolower((string) $response->header('Content-Type'));
                $isJpeg = strlen($body) > 5000 && str_starts_with($body, "\xFF\xD8\xFF");

                if (!$response->successful() || !Str::startsWith($contentType, 'image/') || !$isJpeg) {
                    $this->error("Could not download a valid image: {$filename} (HTTP {$response->status()}, {$contentType})");
                    $failed = true;
                    continue;
                }

                if (!$images->put($filename, $body)) {
                    $this->error("Could not save {$filename} into encap-img.");
                    $failed = true;
                    continue;
                }

                $downloaded++;
                $this->info("Downloaded {$filename}");
            } catch (Throwable $exception) {
                $this->error("Failed {$filename}: {$exception->getMessage()}");
                $failed = true;
            }
        }

        $allowedImage = '/\\.(?:jpe?g|png|webp|avif|gif|svg)$/i';
        foreach ($legacy->allFiles() as $path) {
            if (!preg_match('#^(?:categories|seo)/#', $path) || !preg_match($allowedImage, $path)) {
                continue;
            }

            if ($images->exists($path)) {
                continue;
            }

            try {
                if ($images->put($path, $legacy->get($path))) {
                    $copied++;
                } else {
                    $this->error("Could not copy the existing upload: {$path}");
                    $failed = true;
                }
            } catch (Throwable $exception) {
                $this->error("Failed to copy {$path}: {$exception->getMessage()}");
                $failed = true;
            }
        }

        $this->newLine();
        $this->info("New stock images downloaded: {$downloaded}");
        $this->info("Existing category/SEO uploads copied: {$copied}");
        $this->comment('Legacy files in storage/app/public were kept in place as a rollback safety copy.');

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
