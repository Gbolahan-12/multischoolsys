<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 100)->unique();
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('motto')->nullable();

            // ── Activation / Ban ──────────────────────────────────────
            $table->enum('status', ['pending', 'active', 'banned'])->default('pending');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->string('ban_reason')->nullable();
            $table->unsignedBigInteger('activated_by')->nullable();
            $table->unsignedBigInteger('banned_by')->nullable();
            $table->timestamp('subscription_expires_at')->nullable();
            $table->enum('subscription_status', ['none', 'active', 'warning', 'expired'])->default('none');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
