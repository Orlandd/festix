<?php

namespace App\Http\Controllers;

use App\Models\VenueImage;
use App\Http\Requests\StoreVenueImageRequest;
use App\Http\Requests\UpdateVenueImageRequest;

class VenueImageController extends Controller
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
    public function store(StoreVenueImageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(VenueImage $venueImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VenueImage $venueImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVenueImageRequest $request, VenueImage $venueImage)
    {
        try {
            $venueImage->update($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'success',
                'data' => $venueImage
            ]);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Faile'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VenueImage $venueImage)
    {
        try {
            $venueImage->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'success'
            ]);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed'
            ], 500);
        }
    }
}
