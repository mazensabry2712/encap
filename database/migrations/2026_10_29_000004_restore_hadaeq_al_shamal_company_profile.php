<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('companies')) {
            return;
        }

        $companyId = DB::table('companies')
            ->where('slug', 'hadaeq-al-shamal')
            ->value('id');

        // Older production databases may already have recorded the original
        // companies-table migration before its initial record was added to it.
        // Repair only the missing record; never overwrite a profile managed by admin.
        $restoredMissingCompany = ! $companyId;

        if ($restoredMissingCompany) {
            $now = now();

            $companyId = DB::table('companies')->insertGetId([
                'name_en' => 'Hadaeq Al-Shamal Company',
                'name_ar' => 'شركة حدائق الشمال',
                'slug' => 'hadaeq-al-shamal',
                'short_description_en' => 'A regional company within the ENCAP group.',
                'short_description_ar' => 'شركة إقليمية ضمن مجموعة ENCAP.',
                'description_en' => 'Hadaeq Al-Shamal is the Saudi company represented within ENCAP. Contact the ENCAP team for business and supply enquiries.',
                'description_ar' => 'حدائق الشمال هي الشركة السعودية المعروضة ضمن ENCAP. تواصل مع فريق ENCAP للاستفسارات التجارية والتوريد.',
                'country_en' => 'Kingdom of Saudi Arabia',
                'country_ar' => 'المملكة العربية السعودية',
                'location_en' => 'Al-Jouf, Saudi Arabia',
                'location_ar' => 'الجوف، المملكة العربية السعودية',
                'website_url' => null,
                'image' => 'farm-cultivation-2026.jpg',
                'gallery' => json_encode([]),
                'is_published' => true,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // The original setup linked published products when it created the
        // company. Recreate those initial links only when restoring a missing
        // company record, and leave any existing admin-managed links untouched.
        if (! $restoredMissingCompany
            || ! Schema::hasTable('company_products')
            || ! Schema::hasTable('categories')) {
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
        // Keep the recovered company profile and its relationships on rollback
        // so a rollback cannot silently remove business content or break the URL.
    }
};
