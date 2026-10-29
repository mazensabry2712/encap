<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('companies', 'content_sections')) {
            Schema::table('companies', function (Blueprint $table): void {
                $table->json('content_sections')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('companies', 'content_sections')) {
            Schema::table('companies', function (Blueprint $table): void {
                $table->dropColumn('content_sections');
            });
        }
    }
};
