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
        Schema::create('venue_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('link');
            $table->uuid('venue_id');
            $table->foreign('venue_id')->references('id')->on('venues')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_images');
    }
};
