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
        Schema::create('event_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('price', 10, 2);
            $table->uuid('event_id');
            $table->uuid('seat_category_id');
            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('seat_category_id')->references('id')->on('seat_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_prices');
    }
};
