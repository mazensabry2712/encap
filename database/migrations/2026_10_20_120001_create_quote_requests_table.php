<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contact_name');
            $table->string('company_name')->nullable();
            $table->string('email')->index();
            $table->string('country');
            $table->decimal('quantity', 14, 3)->nullable();
            $table->string('quantity_unit', 40)->nullable();
            $table->string('product_form', 30)->nullable();
            $table->string('packaging_requirements')->nullable();
            $table->text('message');
            $table->string('status', 30)->default('new')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};
