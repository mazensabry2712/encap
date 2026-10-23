<?php

namespace App\Console\Commands;

use App\Services\EncapImageLibrary;
use Illuminate\Console\Command;

class PrepareEncapImages extends Command
{
    protected $signature = 'encap:images:prepare {--force : Re-download the bundled editorial images}';

    protected $description = 'Download site photography and copy current category/SEO uploads into encap-img outside public.';

    public function handle(EncapImageLibrary $library): int
    {
        $result = $library->prepare((bool) $this->option('force'));

        $this->info('New stock images downloaded: '.$result['downloaded']);
        $this->info('Existing stock images kept: '.$result['kept']);
        $this->info('Existing category/SEO uploads copied: '.$result['copied']);

        foreach ($result['errors'] as $error) {
            $this->error($error);
        }

        $this->comment('Original files under storage/app/public were preserved for rollback.');

        return $result['complete'] ? self::SUCCESS : self::FAILURE;
    }
}
