<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INTRODUCTION = 'تعمل الشركة المصرية الوطنية للإنتاج الزراعي في كلٍ من جمهورية مصر العربية والمملكة العربية السعودية، من خلال منظومة متكاملة تجمع بين إنتاج مزارعها، والتوريد من شبكة المزارعين الموثوقين، وعمليات التجهيز والإعداد وفق أعلى معايير الجودة؛ لتوفير منتجات زراعية موثوقة وربط الإنتاج الزراعي بالأسواق العالمية.';

    public function up(): void
    {
        if (! Schema::hasTable('seo_settings')) {
            return;
        }

        $settings = DB::table('seo_settings')->orderBy('id')->first(['id', 'page_content']);

        if (! $settings) {
            return;
        }

        $pageContent = json_decode((string) $settings->page_content, true);

        if (! is_array($pageContent)) {
            $pageContent = [];
        }

        if (! isset($pageContent['home']) || ! is_array($pageContent['home'])) {
            $pageContent['home'] = [];
        }

        if (! isset($pageContent['home']['ar']) || ! is_array($pageContent['home']['ar'])) {
            $pageContent['home']['ar'] = [];
        }

        if (($pageContent['home']['ar']['hero_intro'] ?? null) === self::INTRODUCTION) {
            return;
        }

        $pageContent['home']['ar']['hero_intro'] = self::INTRODUCTION;

        DB::table('seo_settings')->where('id', $settings->id)->update([
            'page_content' => json_encode($pageContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);

        Cache::forget('encap.seo-settings');
    }

    public function down(): void
    {
        // Keep the approved homepage copy on rollback rather than restoring
        // potentially outdated or admin-managed content.
    }
};
