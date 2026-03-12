<?php

// Replace your original results migration file with this one
// Then run: php artisan migrate:fresh

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->decimal('ca1_score', 5, 2)->nullable()->default(null);
            $table->decimal('ca2_score', 5, 2)->nullable()->default(null);
            $table->decimal('exam_score', 5, 2)->nullable()->default(null);
            $table->decimal('bonus_mark', 5, 2)->default(0);
            $table->string('bonus_component')->nullable(); // ca1 / ca2 / exam
            $table->decimal('total_score', 5, 2)->default(0);
            $table->string('grade', 2)->nullable();
            $table->string('remark', 20)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // One result per student per subject per term
            $table->unique(['school_id', 'student_id', 'subject_id', 'session_id', 'term_id'], 'unique_student_result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
