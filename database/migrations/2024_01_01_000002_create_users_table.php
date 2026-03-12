<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->cascadeOnDelete();
            // $table->foreignId('role_id')->nullable()->constrained('roles')->cascadeOnDelete();
            $table->string('staff_id', 20)->nullable()->unique();
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['super-admin', 'proprietor', 'admin', 'staff', 'school-user'])
                ->default('proprietor');
            $table->boolean('is_active')->default(true);
            $table->timestamp('banned_at')->nullable();
            $table->foreignId('banned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('remember_token', 100)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
