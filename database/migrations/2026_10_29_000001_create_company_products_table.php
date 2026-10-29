<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('company_products')) {
            Schema::create('company_products', function (Blueprint $table) {
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->primary(['company_id', 'category_id']);
            });
        }

        $companyId = DB::table('companies')
            ->where('slug', 'hadaeq-al-shamal')
            ->value('id');

        if (! $companyId || ! Schema::hasTable('company_products')) {
            return;
        }

        $now = now();
        $rows = [];

        foreach (DB::table('categories')->where('is_published', true)->pluck('id') as $categoryId) {
            $rows[] = [
                'company_id' => $companyId,
                'category_id' => $categoryId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            DB::table('company_products')->insertOrIgnore($rows);
        }
    }

    public function down(): void
    {
        // Preserve company-product relationships during rollback.
        // The table may have been created by another migration.
    }
};