<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Console\Command;

class ExpirePendingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-pending-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredOrders = Payment::where('status', 'pending')
            ->where('expired_at', '<', now())
            ->get();

        foreach ($expiredOrders as $order) {
            $order->update(['status' => 'expired']);

            Ticket::where('payment_id', $order->id)->delete();
        }
    }
}
