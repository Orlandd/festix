<?php

namespace App\Http\Controllers;

use App\Models\SeatCategory;
use App\Http\Requests\StoreSeatCategoryRequest;
use App\Http\Requests\UpdateSeatCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class SeatCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = SeatCategory::all();
            return response()->json([
                'status' => 'success',
                'data' => $data
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'unique:seat_categories'],
            ]);

            $seatCategoryId = Str::uuid()->toString();

            $seatCategory = new SeatCategory();
            $seatCategory->id = $seatCategoryId;
            $seatCategory->name = $request->name;
            $seatCategory->save();

            return response()->json([
                'status' => 'success',
                'data' => $seatCategory
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
     * Display the specified resource.
     */
    public function show(SeatCategory $seatCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SeatCategory $seatCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        try {
            $request->validate([
                'name' => ['required'],
            ]);

            $seatCategory = SeatCategory::find($id);
            $seatCategory->name = $request->name;
            $seatCategory->save();

            return response()->json([
                'status' => 'success',
                'data' => $seatCategory
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $seatCategory = SeatCategory::find($id);
            $seatCategory->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Data deleted successfully'
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
}
