<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->string('business_hours_en', 255)->nullable();
            $table->string('business_hours_ar', 255)->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('youtube_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn([
                'business_hours_en',
                'business_hours_ar',
                'facebook_url',
                'instagram_url',
                'linkedin_url',
                'youtube_url',
            ]);
        });
    }
};
