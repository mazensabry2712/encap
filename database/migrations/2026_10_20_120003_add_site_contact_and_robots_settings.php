<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->string('default_robots', 40)->default('index,follow');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->string('whatsapp_number', 50)->nullable();
            $table->string('address_en')->nullable();
            $table->string('address_ar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn([
                'default_robots', 'contact_email', 'contact_phone',
                'whatsapp_number', 'address_en', 'address_ar',
            ]);
        });
    }
};
