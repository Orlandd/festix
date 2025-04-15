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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('date');
            $table->string('method');
            $table->string('status');
            $table->decimal('amount_ticket', 10, 2);
            $table->string('total_payment');
            $table->string('price');
            $table->uuid('event_price_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->foreign('event_price_id')->references('id')->on('event_prices')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('expired_at')->nullable();
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
