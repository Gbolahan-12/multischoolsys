<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained('terms')->cascadeOnDelete();
            $table->string('file_path');
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->integer('rows_imported')->nullable();
            $table->json('errors')->nullable(); // store row-level errors from Excel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_imports');
    }
};
