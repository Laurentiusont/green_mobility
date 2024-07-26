<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('guid')->primary();
            $table->string('name');
            $table->string('google_id')->nullable();
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('country')->nullable();
            $table->string('role');
            $table->enum('status', ['submitted', 'unsubmitted'])->default('unsubmitted');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
