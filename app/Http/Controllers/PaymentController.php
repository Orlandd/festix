<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function confirm(Request $request)
    {
        try {
            $data = [
                'quantity' => $request->quantity,
                'price' => $request->price,
                'total_payment' => $request->quantity * $request->price,
                'category' => $request->category,
                'eventId' => $request->eventId
            ];

            $event = Event::find($data["eventId"]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'quantity' => $request->quantity,
                    'price' => $request->price,
                    'total_payment' => $request->quantity * $request->price,
                    'category' => $request->category,
                    'eventId' => $request->eventId,
                    'event' => $event,
                    'admin' => "2000"
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'method' => 'nullable',
                'quantity' => 'required',
                'price' => 'required',
                'total' => 'required',
                'eventPriceId' => 'required'
            ]);

            $data = [
                'id' => Str::uuid()->toString(),
                'date' => Date::now(),
                'method' => "midtrans",
                'status' => "pending",
                'amount_ticket' => $request->quantity,
                'price' => $request->price,
                'total_payment' => $request->total,
                'event_price_id' => $request->eventPriceId,
                'user_id' => Auth::user()->id,
            ];



            $payment = Payment::create($data);

            // Set your Merchant Server Key
            \Midtrans\Config::$serverKey = config('midtrans.serverKey');
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            \Midtrans\Config::$isProduction = false;
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = true;
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = true;

            $params = array(
                'transaction_details' => array(
                    'order_id' => rand(),
                    'gross_amount' => $data['total_payment'],
                ),
                'customer_details' => array(
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ),
            );

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $payment->snap_token = $snapToken;
            $payment->save();

            return response()->json([
                'status' => 'success',
                'data' => $payment,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function success(Request $request)
    {
        try {
            $payment = Payment::find($request->paymentId);
            $payment->status = 'success';
            $payment->save();

            return response()->json([
                'status' => 'success',
                'data' => $payment,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
