<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_title_en')->default('ENCAP | From Our Farms to Global Markets');
            $table->string('site_title_ar')->default('ENCAP | من مزارعنا إلى الأسواق العالمية');
            $table->string('site_description_en', 500)->nullable();
            $table->string('site_description_ar', 500)->nullable();
            $table->string('default_og_image')->nullable();
            $table->string('organization_name')->default('ENCAP');
            $table->string('organization_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
