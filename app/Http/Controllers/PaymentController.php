<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Event;
use App\Models\EventPrice;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $payments = Payment::with('eventPrice.event.eventImage')->where('user_id', Auth::user()->id)->latest()->get();

            return response()->json([
                'status' => 'success',
                'data' => $payments
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

            // cek kuota ticket
            $eventPrice = EventPrice::with('seatCategory')->where('id', $data['event_price_id'])->first();

            $tickets = Ticket::with('eventPrice')
                ->whereHas('eventPrice', function ($query) use ($eventPrice) {
                    $query->where('event_id', optional($eventPrice)->event_id);
                })
                ->get();

            $ticketCount = $tickets->count();

            if ($ticketCount + intval($data['amount_ticket']) >= $eventPrice->total_seat) {
                return response()->json([
                    'status' => 'failed',
                    'data' => "Ticket Sold",
                ]);
            }


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

            $data = [
                'paymentId' => $request->paymentId,
            ];

            $payment = Payment::find($data['paymentId']);

            if ($payment->status == 'success') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Payment has been confirm!',
                ]);
            }

            $payment->status = 'success';
            $payment->save();

            $eventPrice = EventPrice::with('seatCategory')->where('id', $payment->event_price_id)->first();

            // $test = Ticket::with('eventPrice')->get();

            // return response()->json([
            //     'status' => 'success',
            //     'data' => $eventPrice->seatCategory,
            // ]);

            $tickets = Ticket::with('eventPrice')
                ->whereHas('eventPrice', function ($query) use ($eventPrice) {
                    $query->where('event_id', optional($eventPrice)->event_id);
                })
                ->get();

            $ticketCount = $tickets->count();

            // if ($ticketCount == $eventPrice->total_seat) {
            //     return response()->json([
            //         'status' => 'failed',
            //         'data' => "Ticket Sold",
            //     ]);
            // }

            for ($i = 0; $i < $payment->amount_ticket; $i++) {
                if ($ticketCount == 0) {
                    $seat_number = "1";
                } else {
                    $seat_number = $ticketCount++;
                }
                $ticket = new Ticket();
                $ticket->id = Str::uuid()->toString();
                $ticket->code = $eventPrice->seatCategory->name . time() . rand(100, 999);
                $ticket->seat_number = $seat_number;
                $ticket->status = true;
                $ticket->purchased_at = $payment->date;
                $ticket->user_id = $payment->user_id;
                $ticket->payment_id = $payment->id;
                $ticket->event_price_id = $eventPrice->id;
                $ticket->save();
            }

            return response()->json([
                'status' => 'success',
                'data' => $ticket,
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
        try {
            $payment->update([
                'method' => $request->method ?? $payment->method,
                'status' => $request->status ?? $payment->status,
                'amount_ticket' => $request->amount_ticket ?? $payment->amount_ticket,
                'price' => $request->price ?? $payment->price,
                'total_payment' => $request->total_payment ?? $payment->total_payment,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'success',
                'data' => $payment
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        try {
            $payment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'successy'
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed'
            ], 500);
        }
    }

    public function manual(Request $request)
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
                'method' => "manual",
                'status' => "pending",
                'amount_ticket' => $request->quantity,
                'price' => $request->price,
                'total_payment' => $request->total,
                'event_price_id' => $request->eventPriceId,
                'user_id' => Auth::user()->id,
            ];

            // cek kuota ticket
            $eventPrice = EventPrice::with('seatCategory')->where('id', $data['event_price_id'])->first();

            $tickets = Ticket::with('eventPrice')
                ->whereHas('eventPrice', function ($query) use ($eventPrice) {
                    $query->where('event_id', optional($eventPrice)->event_id);
                })
                ->get();

            $ticketCount = $tickets->count();

            if ($ticketCount + intval($data['amount_ticket']) >= $eventPrice->total_seat) {
                return response()->json([
                    'status' => 'failed',
                    'data' => "Ticket Sold",
                ]);
            }
            $payment = Payment::create($data);

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

    public function manual2(Request $request)
    {
        try {
            $request->validate([
                'method' => 'nullable',
                'quantity' => 'required',
                'price' => 'required',
                'total' => 'required',
                'eventPriceId' => 'required',
                'image_file' => ['required', 'mimes:jpg,png,jpeg'],
            ]);

            $id = Str::uuid()->toString();

            $file = $request->file('image_file');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $newName = Carbon::now()->timestamp . '_' . $id . '' . '.' . $extension;

            // Simpan file ke storage public
            Storage::disk('public')->putFileAs('payments', $file, $newName);

            $data = [
                'id' => $id,
                'date' => Date::now(),
                'method' => "manual",
                'status' => "pending",
                'amount_ticket' => $request->quantity,
                'price' => $request->price,
                'total_payment' => $request->total,
                'event_price_id' => $request->eventPriceId,
                'user_id' => Auth::user()->id,
                'image' => Storage::url('payments/' . $newName),
            ];

            // cek kuota ticket
            $eventPrice = EventPrice::with('seatCategory')->where('id', $data['event_price_id'])->first();

            $tickets = Ticket::with('eventPrice')
                ->whereHas('eventPrice', function ($query) use ($eventPrice) {
                    $query->where('event_id', optional($eventPrice)->event_id);
                })
                ->get();

            $ticketCount = $tickets->count();

            if ($ticketCount + intval($data['amount_ticket']) >= $eventPrice->total_seat) {
                return response()->json([
                    'status' => 'failed',
                    'data' => "Ticket Sold",
                ]);
            }
            $payment = Payment::create($data);

            // Create Ticket
            $eventPrice = EventPrice::with('seatCategory')->where('id', $data['event_price_id'])->first();

            // $test = Ticket::with('eventPrice')->get();

            // return response()->json([
            //     'status' => 'success',
            //     'data' => $eventPrice->seatCategory,
            // ]);

            $tickets = Ticket::with('eventPrice')
                ->whereHas('eventPrice', function ($query) use ($eventPrice) {
                    $query->where('event_id', optional($eventPrice)->event_id);
                })
                ->get();

            $ticketCount = $tickets->count();

            // if ($ticketCount == $eventPrice->total_seat) {
            //     return response()->json([
            //         'status' => 'failed',
            //         'data' => "Ticket Sold",
            //     ]);
            // }

            for ($i = 0; $i < $payment->amount_ticket; $i++) {
                if ($ticketCount == 0) {
                    $seat_number = "1";
                } else {
                    $seat_number = $ticketCount++;
                }
                $ticket = new Ticket();
                $ticket->id = Str::uuid()->toString();
                $ticket->code = $eventPrice->seatCategory->name . time() . rand(100, 999);
                $ticket->seat_number = $seat_number;
                $ticket->status = true;
                $ticket->purchased_at = $payment->date;
                $ticket->user_id = $payment->user_id;
                $ticket->payment_id = $payment->id;
                $ticket->event_price_id = $eventPrice->id;
                $ticket->save();
            }


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

    //Confirm manual
    public function success1(Request $request)
    {
        try {

            $data = [
                'paymentId' => $request->paymentId,
            ];

            $payment = Payment::find($data['paymentId']);

            if ($payment->status == 'success') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Payment has been confirm!',
                ]);
            }

            $payment->status = 'success';
            $payment->save();

            $eventPrice = EventPrice::with('seatCategory')->where('id', $payment->event_price_id)->first();

            // $test = Ticket::with('eventPrice')->get();

            // return response()->json([
            //     'status' => 'success',
            //     'data' => $eventPrice->seatCategory,
            // ]);

            $tickets = Ticket::with('eventPrice')
                ->whereHas('eventPrice', function ($query) use ($eventPrice) {
                    $query->where('event_id', optional($eventPrice)->event_id);
                })
                ->get();

            $ticketCount = $tickets->count();

            // if ($ticketCount == $eventPrice->total_seat) {
            //     return response()->json([
            //         'status' => 'failed',
            //         'data' => "Ticket Sold",
            //     ]);
            // }

            for ($i = 0; $i < $payment->amount_ticket; $i++) {
                if ($ticketCount == 0) {
                    $seat_number = "1";
                } else {
                    $seat_number = $ticketCount++;
                }
                $ticket = new Ticket();
                $ticket->id = Str::uuid()->toString();
                $ticket->code = $eventPrice->seatCategory->name . time() . rand(100, 999);
                $ticket->seat_number = $seat_number;
                $ticket->status = true;
                $ticket->purchased_at = $payment->date;
                $ticket->user_id = $payment->user_id;
                $ticket->payment_id = $payment->id;
                $ticket->event_price_id = $eventPrice->id;
                $ticket->save();
            }

            return response()->json([
                'status' => 'success',
                'data' => $ticket,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    // confirm manual failed 
    public function failed(Request $request)
    {
        try {

            $data = [
                'paymentId' => $request->paymentId,
            ];

            $payment = Payment::find($data['paymentId']);

            if ($payment->status == 'success' || $payment->status == 'failed') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Payment has been confirm!',
                ]);
            }

            $payment->status = 'failed';
            $payment->save();

            Ticket::where('payment_id', $request->paymentId)->delete();

            return response()->json([
                'status' => 'success',
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function indexManual()
    {
        try {
            $tickets = Payment::with('eventPrice.event.eventImage')->where('user_id', Auth::user()->id)->latest()->get();
            return response()->json([
                'status' => 'success',
                'data' => $tickets
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function showManual($id)
    {
        try {
            $payment = Payment::with('eventPrice.event.eventImage')->find($id);
            if (!$payment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found'
                ], 404);
            }
            return response()->json([
                'status' => 'success',
                'data' => $payment
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function store2(Request $request)
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
                'expired_at' => Date::now()->addMinutes(15),
            ];

            // cek kuota ticket
            $eventPrice = EventPrice::with('seatCategory')->where('id', $data['event_price_id'])->first();

            $tickets = Ticket::with('eventPrice')
                ->whereHas('eventPrice', function ($query) use ($eventPrice) {
                    $query->where('event_id', optional($eventPrice)->event_id);
                })
                ->get();

            $ticketCount = $tickets->count();

            if ($ticketCount + intval($data['amount_ticket']) >= $eventPrice->total_seat) {
                return response()->json([
                    'status' => 'failed',
                    'data' => "Ticket Sold",
                ]);
            }


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


            // Create Ticket
            $eventPrice = EventPrice::with('seatCategory')->where('id', $data['event_price_id'])->first();

            // $test = Ticket::with('eventPrice')->get();

            // return response()->json([
            //     'status' => 'success',
            //     'data' => $eventPrice->seatCategory,
            // ]);

            $tickets = Ticket::with('eventPrice')
                ->whereHas('eventPrice', function ($query) use ($eventPrice) {
                    $query->where('event_id', optional($eventPrice)->event_id);
                })
                ->get();

            $ticketCount = $tickets->count();

            // if ($ticketCount == $eventPrice->total_seat) {
            //     return response()->json([
            //         'status' => 'failed',
            //         'data' => "Ticket Sold",
            //     ]);
            // }

            for ($i = 0; $i < $payment->amount_ticket; $i++) {
                if ($ticketCount == 0) {
                    $seat_number = "1";
                } else {
                    $seat_number = $ticketCount++;
                }
                $ticket = new Ticket();
                $ticket->id = Str::uuid()->toString();
                $ticket->code = $eventPrice->seatCategory->name . time() . rand(100, 999);
                $ticket->seat_number = $seat_number;
                $ticket->status = true;
                $ticket->purchased_at = $payment->date;
                $ticket->user_id = $payment->user_id;
                $ticket->payment_id = $payment->id;
                $ticket->event_price_id = $eventPrice->id;
                $ticket->save();
            }

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

    public function success2(Request $request)
    {
        try {

            $data = [
                'paymentId' => $request->paymentId,
            ];

            $payment = Payment::find($data['paymentId']);

            if ($payment->status == 'success') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Payment has been confirm!',
                ]);
            }

            $payment->status = 'success';
            $payment->save();


            return response()->json([
                'status' => 'success',
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
