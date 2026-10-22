<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->text('varieties_en')->nullable();
            $table->text('varieties_ar')->nullable();
            $table->string('harvest_window_en')->nullable();
            $table->string('harvest_window_ar')->nullable();
            $table->text('specifications_en')->nullable();
            $table->text('specifications_ar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'varieties_en',
                'varieties_ar',
                'harvest_window_en',
                'harvest_window_ar',
                'specifications_en',
                'specifications_ar',
            ]);
        });
    }
};
