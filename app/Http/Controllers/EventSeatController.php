<?php

namespace App\Http\Controllers;

use App\Models\EventSeat;
use App\Http\Requests\StoreEventSeatRequest;
use App\Http\Requests\UpdateEventSeatRequest;

class EventSeatController extends Controller
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventSeatRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EventSeat $eventSeat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventSeat $eventSeat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventSeatRequest $request, EventSeat $eventSeat)
    {
        $eventSeat->update([
            'seat_category_id' => $request->seat_category_id,
            'venue_seat_id' => $request->venue_seat_id,
            'event_id' => $request->event_id,
        ]);

        return response()->json([
            'message' => 'success',
            'data' => $eventSeat
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventSeat $eventSeat)
    {
        $eventSeat->delete();

        return response()->json([
            'message' => 'deleted'
        ]);
    }
}
