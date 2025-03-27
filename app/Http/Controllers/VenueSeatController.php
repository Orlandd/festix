<?php

namespace App\Http\Controllers;

use App\Models\VanueSeat;
use App\Http\Requests\StoreVanueSeatRequest;
use App\Http\Requests\UpdateVanueSeatRequest;

class VanueSeatController extends Controller
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
    public function store(StoreVanueSeatRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(VanueSeat $vanueSeat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VanueSeat $vanueSeat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVanueSeatRequest $request, VanueSeat $vanueSeat)
    {
        try {
            $venueSeat->update($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'success',
                'data' => $venueSeat
            ]);
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VanueSeat $vanueSeat)
    {
        try {
            $venueSeat->delete(); // Soft delete

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
