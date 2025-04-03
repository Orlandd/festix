<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\EventImage;
use App\Models\EventPrice;
use App\Models\EventSeat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Event::with([
                'vanue',
                'eventImage',
                'eventPrice' => function ($query) {
                    $query->with(['seatCategory'])->withCount('tickets');
                }
            ])
                ->withMin('eventPrice', 'price') // Menambahkan harga tiket paling kecil dalam setiap event
                ->orderBy('created_at', 'desc') // Mengurutkan berdasarkan yang terakhir ditambahkan
                ->get();

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
                'name' => ['required', 'max:100'],
                'date' => ['required', 'date'],
                'time' => ['required',],
                'description' => ['required', 'string'],
                'image_file.*' => ['nullable', 'mimes:jpg,png'], // Bisa menerima banyak file
                'cover_image' => ['nullable', 'mimes:jpg,png'], // Bisa menerima banyak file
                'seat_map' => ['nullable', 'mimes:jpg,png'], // Bisa menerima banyak file
                'venue_id' => ['required'],
                'seats' => ['nullable', 'array'], // Pastikan 'seats' adalah array
                'seats.*.price' => ['required', 'numeric'],
                'seats.*.total_seat' => ['required', 'integer'],
                'seats.*.category_seat' => ['required']
            ]);

            if ($request->hasFile('seat_map')) {
                $file = $request->file('seat_map');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $newName = Carbon::now()->timestamp . '_' . Str::slug($request->name) . '_seat' . '.' . $extension;

                // Simpan file ke storage public
                Storage::disk('public')->putFileAs('event', $file, $newName);
            }


            // Simpan event
            $eventId = Str::uuid()->toString();
            $event = Event::create([
                'id' => $eventId,
                'name' => $request->name,
                'date' => $request->date,
                'time' => $request->time,
                'seat_image' => $request->hasFile('seat_map') ? Storage::url('event/' . $newName) : null,
                'description' => $request->description,
                'venue_id' => $request->venue_id
            ]);

            // Simpan gambar jika ada
            // if ($request->hasFile('image_file')) {
            //     foreach ($request->file('image_file') as $file) {
            //         $image = [
            //             'id' => Str::uuid()->toString(), // UUID untuk tabel venue_images
            //             'event_id' => $event->id, // Pastikan venue_id sesuai dengan venue yang baru dibuat
            //         ];

            //         $file = $request->file('image_file');
            //         $filename = $file->getClientOriginalName();
            //         $extension = $file->getClientOriginalExtension();
            //         $newName = Carbon::now()->timestamp . '_' . Str::slug($request->name) . '.' . $extension;

            //         // Simpan file ke storage public
            //         Storage::disk('public')->putFileAs('venue', $file, $newName);

            //         // Simpan nama file dan link di database
            //         $image['name'] = $newName;
            //         $image['link'] = Storage::url('event/' . $newName);

            //         $venueImage = EventImage::create($image);
            //     }
            // }


            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $newName = Carbon::now()->timestamp . '_' . Str::slug($request->name) . '.' . $extension;

                // Simpan file ke storage public
                Storage::disk('public')->putFileAs('event', $file, $newName);

                // Simpan nama file dan link di database
                EventImage::create([
                    'id' => Str::uuid()->toString(),
                    'event_id' => $event->id,
                    'name' => $newName,
                    'link' => Storage::url('event/' . $newName),
                ]);
            }

            // Simpan kursi jika ada
            if ($request->has('seats')) {
                foreach ($request->seats as $seat) {
                    EventPrice::create([
                        'id' => Str::uuid()->toString(),
                        'event_id' => $event->id,
                        'price' => $seat['price'],
                        'total_seat' => $seat['total_seat'],
                        'seat_category_id' => $seat['category_seat']
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'event' => $event,
                    'images' => EventImage::where('event_id', $event->id)->get(),
                    'seats' => EventPrice::where('event_id', $event->id)->get()
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => $request
            ], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $event = Event::with(['vanue', 'eventPrice.tickets', 'eventImage'])->find($id);
            if (!$event) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found'
                ], 404);
            }

            $eventPrice = $event->eventPrice;
            $vanue = $event->vanue;
            $totalSeatsRemaining = $event->eventPrice->map(function ($price) {
                $totalSeat = $price->total_seat;
                $ticketSold = $price->tickets->count(); // Menghitung jumlah tiket yang terjual
                return [
                    'event_price_id' => $price->id,
                    'name' => $price->seatCategory->name,
                    'price' => $price->price,
                    'total_seat' => $totalSeat,
                    'ticket_sold' => $ticketSold,
                    'remaining_seat' => max(0, $totalSeat - $ticketSold) // Pastikan tidak negatif
                ];
            });


            return response()->json([
                'status' => 'success',
                'data' => [
                    'event' => $event,
                    'vanue' => $vanue,
                    'event_price' => $eventPrice,
                    'total_seats_remaining' => $totalSeatsRemaining
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
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        try {
            $request->validate([
                'name' => ['required', 'max:100'],
                'date' => ['required', 'date'],
                'time' => ['required', 'date_format:H:i'],
                'description' => ['required', 'string'],
                'venue_id' => ['required']
            ]);

            $event->update($request->only([
                'name',
                'date',
                'time',
                'description',
                'venue_id'
            ]));
            $event->save();
            return response()->json([
                'status' => 'success',
                'data' => $event
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
    public function destroy(Event $event)
    {
        try {
            $event->delete();
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
