<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('ExpirePendingOrders')->everyFiveMinutes();

Schedule::call(function () {
    $expiredOrders = \App\Models\Payment::where('status', 'pending')
        ->where('expired_at', '<', now())
        ->get();

    foreach ($expiredOrders as $order) {
        $order->update(['status' => 'expired']);

        \App\Models\Ticket::where('payment_id', $order->id)->delete();
    }
})->everyFiveMinutes();
