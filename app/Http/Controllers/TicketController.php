<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{

    public function history()
    {
        try {
            $tickets = Ticket::with(['eventPrice.event.eventImage', 'eventPrice.event.vanue', 'payment'])
                ->where('user_id', Auth::id())
                ->whereHas('payment', function ($query) {
                    $query->where('status', 'success');
                })
                ->orderByRaw('status DESC, created_at DESC') // Status 1 dahulu, lalu status 0, dan urutkan berdasarkan created_at terbaru
                ->get();

            return response()->json([
                'status' => "success",
                'data' => $tickets,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function verify(Request $request)
    {
        try {
            $ticket = Ticket::with(['eventPrice'])->where('code', $request->code)->first();

            if (!$ticket) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket not found',
                    'data' => [
                        'code' => $request->code,
                        'event_id' => $request->event_id,
                    ]
                ], 404);
            }

            if ($ticket->status == true && $ticket->eventPrice->event_id == $request->event_id) {
                $ticket->status = false;
                $ticket->save();
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket already verified',
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket verified',
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $tickets = Ticket::with(['eventPrice.event.eventImage', 'eventPrice.seatCategory', 'eventPrice.event.vanue'])
                ->where('user_id', Auth::id())
                ->where('id', $id)
                ->first();
            return response()->json([
                'status' => "success",
                'data' => $tickets,
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
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        try {
            $ticket->update($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'success',
                'data' => $ticket
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
    public function destroy(Ticket $ticket)
    {
        try {
            $ticket->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'success'
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed'
            ], 500);
        }
    }
}
