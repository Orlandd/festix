<?php

use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Buatan Billie 15/03/2025
use App\Http\Controllers\EventImageController;
use App\Http\Controllers\EventPriceController;
use App\Http\Controllers\EventSeatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VenueImageController;
use App\Http\Controllers\VenueSeatController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/{user_id}/otp-verification', function () {
//     $data = [
//         'otp' => '123456'
//     ];
//     Mail::to('alvenorlando@gmail.com')->send(new OtpMail($data));
// });

// Buatan Billie 15/03/2025
Route::put('/event-images/{eventImage}', [EventImageController::class, 'update'])->name('event-images.update');
Route::delete('/event-images/{eventImage}', [EventImageController::class, 'destroy'])->name('event-images.destroy');

Route::put('/event-prices/{eventPrice}', [EventPriceController::class, 'update'])->name('event-prices.update');
Route::delete('/event-prices/{eventPrice}', [EventPriceController::class, 'destroy'])->name('event-prices.destroy');

Route::put('/event-seats/{eventSeat}', [EventSeatController::class, 'update'])->name('event-seats.update');
Route::delete('/event-seats/{eventSeat}', [EventSeatController::class, 'destroy'])->name('event-seats.destroy');

Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

Route::put('/venue-images/{venueImage}', [VenueImageController::class, 'update'])->name('venue-images.update');
Route::delete('/venue-images/{venueImage}', [VenueImageController::class, 'destroy'])->name('venue-images.destroy');

Route::put('/venue-seats/{venueSeat}', [VenueSeatController::class, 'update'])->name('venue-seats.update');
Route::delete('/venue-seats/{venueSeat}', [VenueSeatController::class, 'destroy'])->name('venue-seats.destroy');


