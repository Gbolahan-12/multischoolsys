<?php
// FILE 2: 2024_01_01_000021_modify_classes_table_level_section.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Drop old string columns
            $table->dropColumn(['level', 'section']);
        });

        Schema::table('classes', function (Blueprint $table) {
            // Add FK columns
            $table->foreignId('level_id')->nullable()->after('name')->constrained('class_levels')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->after('level_id')->constrained('class_sections')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('level_id');
            $table->dropConstrainedForeignId('section_id');
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->string('level', 50)->nullable();
            $table->string('section', 10)->nullable();
        });
    }
};
