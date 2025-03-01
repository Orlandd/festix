<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\EventImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
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
    public function store(StoreEventRequest $request)
    {
        $request->merge(['id' => Str::uuid()->toString()]);
        $request->validate([
            'name' => ['required', 'max:100'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'description' => ['required', 'string'],
            'image_file' => ['nullable', 'mimes:jpg,png'],
        ]);

        if ($request->file('image_file')) {
            $file = $request->file('image_file');
            // $filename = $file->getClientOriginalName();
            $fileExtension = $file->getClientOriginalExtension();
            $newName = Carbon::now()->timestamp . '_' . $request->id . '.' . $fileExtension;

            Storage::disk('public')->putFileAs('quizzes', $file, $newName);
            $request['name'] = $newName;
            $request['url'] = env('APP_URL') . '/storage/event/' . $newName;
        }

        Event::create(
            $request->only([
                'id',
                'name',
                'date',
                'time',
                'description',
            ])
        );

        EventImage::create([
            'id' => Str::uuid()->toString(),
            'name' => $newName,
            'url' => $request->url,
            'event_id' => $request->id,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
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
        $request->validate([
            'name' => ['required', 'max:100'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'description' => ['required', 'string'],
            'image_file' => ['nullable', 'mimes:jpg,png'],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }
}
