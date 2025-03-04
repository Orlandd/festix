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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('status');
            $table->timestamp('purchased_at');
            $table->uuid('event_seat_id')->nullable();
            $table->uuid('order_id')->nullable();
            $table->foreign('event_seat_id')->references('id')->on('event_seats')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
