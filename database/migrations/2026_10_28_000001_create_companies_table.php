<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 160);
            $table->string('name_ar', 160)->nullable();
            $table->string('slug', 180)->unique();
            $table->string('short_description_en', 500)->nullable();
            $table->string('short_description_ar', 500)->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('country_en', 120)->nullable();
            $table->string('country_ar', 120)->nullable();
            $table->string('location_en', 160)->nullable();
            $table->string('location_ar', 160)->nullable();
            $table->string('website_url', 500)->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('companies')->insert([
            'name_en' => 'Hadaeq Al-Shamal Company',
            'name_ar' => 'شركة حدائق الشمال',
            'slug' => 'hadaeq-al-shamal',
            'short_description_en' => 'A regional company within the ENCAP group.',
            'short_description_ar' => 'شركة إقليمية ضمن مجموعة ENCAP.',
            'description_en' => 'Hadaeq Al-Shamal is the Saudi company represented within the ENCAP group directory. Contact the ENCAP team for business and supply enquiries.',
            'description_ar' => 'حدائق الشمال هي الشركة السعودية المعروضة ضمن دليل شركات مجموعة ENCAP. تواصل مع فريق ENCAP للاستفسارات التجارية والتوريد.',
            'country_en' => 'Kingdom of Saudi Arabia',
            'country_ar' => 'المملكة العربية السعودية',
            'location_en' => 'Al-Jouf, Saudi Arabia',
            'location_ar' => 'الجوف، المملكة العربية السعودية',
            'website_url' => null,
            'image' => 'farm-cultivation-2026.jpg',
            'gallery' => json_encode([]),
            'is_published' => true,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
