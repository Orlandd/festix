<?php

namespace App\Http\Controllers;

use App\Models\EventPrice;
use App\Http\Requests\StoreEventPriceRequest;
use App\Http\Requests\UpdateEventPriceRequest;

class EventPriceController extends Controller
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
    public function store(StoreEventPriceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EventPrice $eventPrice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventPrice $eventPrice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventPriceRequest $request, EventPrice $eventPrice)
    {
        $eventPrice->update([
            'price' => $request->price,
            'total_seat' => $request->total_seat,
            'event_id' => $request->event_id,
            'seat_category_id' => $request->seat_category_id,
        ]);

        return response()->json([
            'message' => 'success',
            'data' => $eventPrice
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventPrice $eventPrice)
    {
        return response()->json([
            'message' => 'deleted'
        ]);
    }
}
