<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('downloaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('report_type', 50); // e.g. results, payments, students
            $table->json('filters')->nullable(); // store term_id, class_id, session_id etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_downloads');
    }
};
