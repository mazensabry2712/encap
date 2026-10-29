<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $addedCatalogueFlag = ! Schema::hasColumn('categories', 'show_in_catalogue');
        $addedFrozenFlag = ! Schema::hasColumn('categories', 'show_in_frozen_catalogue');
        $addedHomepageFlag = ! Schema::hasColumn('categories', 'show_on_homepage');

        Schema::table('categories', function (Blueprint $table) use ($addedCatalogueFlag, $addedFrozenFlag, $addedHomepageFlag) {
            if ($addedCatalogueFlag) {
                $table->boolean('show_in_catalogue')->default(true);
            }
            if ($addedFrozenFlag) {
                $table->boolean('show_in_frozen_catalogue')->default(false);
            }
            if ($addedHomepageFlag) {
                $table->boolean('show_on_homepage')->default(true);
            }
        });

        if ($addedCatalogueFlag || $addedFrozenFlag || $addedHomepageFlag) {
            foreach (DB::table('categories')->where('is_published', true)->get(['id', 'available_forms']) as $category) {
                $forms = is_array($category->available_forms)
                    ? $category->available_forms
                    : (json_decode((string) $category->available_forms, true) ?: []);

                DB::table('categories')->where('id', $category->id)->update([
                    'show_in_catalogue' => true,
                    'show_on_homepage' => true,
                    'show_in_frozen_catalogue' => in_array('Frozen', $forms, true),
                ]);
            }
        }

        if (! Schema::hasTable('company_products')) {
            Schema::create('company_products', function (Blueprint $table) {
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->primary(['company_id', 'category_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            foreach (['show_in_catalogue', 'show_in_frozen_catalogue', 'show_on_homepage'] as $column) {
                if (Schema::hasColumn('categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Keep company_products if it predates this migration on an existing local database.
        // Removing it here could delete relationships managed by an earlier local migration.
    }
};
