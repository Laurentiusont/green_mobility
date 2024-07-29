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
        Schema::create('offers', function (Blueprint $table) {
            $table->uuid('guid')->primary();
            $table->string('name');
            $table->integer('stock');
            $table->integer('point');
            $table->longText('description');
            $table->char('merchant_master_guid', 36)->index();
            $table->foreign('merchant_master_guid')->references('guid')->on('merchant_masters')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
