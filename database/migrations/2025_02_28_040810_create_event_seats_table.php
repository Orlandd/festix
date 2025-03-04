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
        // Schema::create('event_seats', function (Blueprint $table) {
        //     $table->uuid('id')->primary();
        //     $table->uuid('seat_category_id');
        //     $table->uuid('venue_seat_id');
        //     $table->uuid('event_id');
        //     $table->foreign('seat_category_id')->references('id')->on('seat_categories')->onDelete('cascade')->onUpdate('cascade');
        //     $table->foreign('venue_seat_id')->references('id')->on('venue_seats')->onDelete('cascade')->onUpdate('cascade');
        //     $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade')->onUpdate('cascade');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_seats');
    }
};
