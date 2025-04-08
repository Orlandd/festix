<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Http\Requests\StoreVenueRequest;
use App\Http\Requests\UpdateVenueRequest;
use App\Models\VenueImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class VenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Venue::with(['venueImage'])->latest()->get();
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
                'name' => ['required'],
                'address' => ['required', 'max:255'],
                'capacity' => ['required', 'integer'],
                'image_file' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:10048']
            ]);

            // Buat UUID untuk venue
            $venueId = Str::uuid()->toString();

            $venue = Venue::create([
                'id' => $venueId,
                'name' => $request->name,
                'address' => $request->address,
                'capacity' => $request->capacity,
            ]);

            $image = [
                'id' => Str::uuid()->toString(), // UUID untuk tabel venue_images
                'venue_id' => $venue->id, // Pastikan venue_id sesuai dengan venue yang baru dibuat
            ];

            if ($request->file('image_file')) {
                $file = $request->file('image_file');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $newName = Carbon::now()->timestamp . '_' . Str::slug($request->name) . '.' . $extension;

                // Simpan file ke storage public
                Storage::disk('public')->putFileAs('venue', $file, $newName);

                // Simpan nama file dan link di database
                $image['name'] = $newName;
                $image['link'] = Storage::url('venue/' . $newName);

                $venueImage = VenueImage::create($image);
            }


            return response()->json([
                'status' => 'success',
                'data' => [
                    'venue' => $venue,
                    'image' => $venueImage
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
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = Venue::find($id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found'
                ], 404);
            }

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
     * Show the form for editing the specified resource.
     */
    public function edit(Venue $venue)
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
                'address' => ['required', 'max:255'],
                'capacity' => ['required', 'integer'],
                'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
            ]);

            $venue = Venue::find($id);

            if (!$venue) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found'
                ], 404);
            }

            // Update venue data
            $venue->update($request->only(['name', 'address', 'capacity']));

            // Cek apakah ada gambar baru yang diupload
            if ($request->hasFile('image_file')) {
                // Hapus gambar lama jika ada
                $venueImage = VenueImage::where('venue_id', $venue->id)->first();
                if ($venueImage) {
                    // Hapus gambar yang ada di storage
                    Storage::disk('public')->delete(str_replace('/storage/', '', $venueImage->link));
                    // Hapus data gambar dari database
                    $venueImage->delete();
                }

                // Simpan gambar baru
                $file = $request->file('image_file');
                $extension = $file->getClientOriginalExtension();
                $newName = Carbon::now()->timestamp . '_' . Str::slug($request->name) . '.' . $extension;
                Storage::disk('public')->putFileAs('venue', $file, $newName);

                // Simpan data gambar baru ke database
                VenueImage::create([
                    'id' => Str::uuid()->toString(),
                    'venue_id' => $venue->id,
                    'name' => $newName,
                    'link' => Storage::url('venue/' . $newName),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => $venue
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
            $venue = Venue::find($id);
            if (!$venue) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found'
                ], 404);
            }

            // Hapus gambar terkait jika ada
            $venueImages = VenueImage::where('venue_id', $venue->id)->get();
            foreach ($venueImages as $image) {
                // Hapus gambar dari storage
                Storage::disk('public')->delete(str_replace('/storage/', '', $image->link));
                // Hapus data gambar dari database
                $image->delete();
            }

            // Hapus venue
            $venue->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data deleted'
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
