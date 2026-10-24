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
        $summary = ['downloaded' => 0, 'kept' => 0, 'copied' => 0, 'errors' => []];
        $offset = 0;
        $total = null;

        do {
            $batch = $library->prepare((bool) $this->option('force'), $offset);
            foreach (['downloaded', 'kept', 'copied'] as $counter) {
                $summary[$counter] += $batch[$counter];
            }
            $summary['errors'] = array_merge($summary['errors'], $batch['errors']);
            $total = $batch['total'];
            $offset = $batch['next_offset'];

            if ($offset < $total) {
                $this->comment("Prepared image batch through {$offset} of {$total}.");
            }
        } while ($offset < $total);

        $this->info('New stock images downloaded: '.$summary['downloaded']);
        $this->info('Existing stock images kept: '.$summary['kept']);
        $this->info('Existing category/SEO uploads copied: '.$summary['copied']);

        foreach (array_unique($summary['errors']) as $error) {
            $this->error($error);
        }

        $this->comment('Original files under storage/app/public were preserved for rollback.');

        return $summary['errors'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
