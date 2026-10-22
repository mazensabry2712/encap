<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('overview_heading_en')->nullable();
            $table->string('overview_heading_ar')->nullable();
            $table->string('enquiry_note_heading_en', 160)->nullable();
            $table->string('enquiry_note_heading_ar', 160)->nullable();
            $table->text('enquiry_note_text_en')->nullable();
            $table->text('enquiry_note_text_ar')->nullable();
            $table->string('bottom_cta_heading_en')->nullable();
            $table->string('bottom_cta_heading_ar')->nullable();
            $table->text('bottom_cta_text_en')->nullable();
            $table->text('bottom_cta_text_ar')->nullable();

            $table->boolean('show_product_images')->default(true);
            $table->boolean('show_enquiry_note')->default(true);
            $table->boolean('show_description')->default(true);
            $table->boolean('show_available_forms')->default(true);
            $table->boolean('show_packaging')->default(true);
            $table->boolean('show_specifications')->default(true);
            $table->boolean('show_bottom_cta')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'overview_heading_en', 'overview_heading_ar',
                'enquiry_note_heading_en', 'enquiry_note_heading_ar',
                'enquiry_note_text_en', 'enquiry_note_text_ar',
                'bottom_cta_heading_en', 'bottom_cta_heading_ar',
                'bottom_cta_text_en', 'bottom_cta_text_ar',
                'show_product_images', 'show_enquiry_note', 'show_description',
                'show_available_forms', 'show_packaging', 'show_specifications', 'show_bottom_cta',
            ]);
        });
    }
};
