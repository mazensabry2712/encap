<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->string('slug')->unique();
            $table->string('short_description_en', 500)->nullable();
            $table->string('short_description_ar', 500)->nullable();
            $table->longText('description_en')->nullable();
            $table->longText('description_ar')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('available_forms')->nullable();
            $table->string('packaging_en')->nullable();
            $table->string('packaging_ar')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('seo_title_en')->nullable();
            $table->string('seo_title_ar')->nullable();
            $table->string('seo_description_en', 500)->nullable();
            $table->string('seo_description_ar', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
