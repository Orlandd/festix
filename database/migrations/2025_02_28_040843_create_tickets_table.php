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
            $table->string('seat_number');
            $table->timestamp('purchased_at');
            $table->uuid('payment_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->uuid('event_price_id')->nullable();;
            $table->foreign('payment_id')->references('id')->on('payments')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('event_price_id')->references('id')->on('event_prices')->onUpdate('cascade')->onDelete('set null');
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
