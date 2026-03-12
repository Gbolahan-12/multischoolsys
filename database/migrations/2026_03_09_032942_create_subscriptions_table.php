<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Subscriptions table ───────────────────────────────
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete(); // super-admin
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'transfer', 'pos', 'cheque'])->default('cash');
            $table->string('reference', 100)->nullable();
            $table->integer('duration_months'); // 1, 3, 6, 12
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['subscription_expires_at', 'subscription_status']);
        });
    }
};