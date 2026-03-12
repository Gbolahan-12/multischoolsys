<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('admission_number', 50);
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('other_name', 100)->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth')->nullable();
            $table->string('photo')->nullable();
            $table->string('guardian_name');
            $table->string('guardian_phone', 20);
            $table->string('guardian_email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'admission_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
