<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('fee_id')->constrained('fees')->cascadeOnDelete();
            $table->foreignId('received_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('balance', 12, 2)->default(0); // fee amount - amount_paid
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'transfer', 'pos', 'cheque'])->default('cash');
            $table->string('reference', 100)->nullable(); // receipt number / transaction ref
            $table->enum('status', ['paid', 'partial', 'owing'])->default('owing');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
