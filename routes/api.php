<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthTokenController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SeatCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenueController;
use App\Models\AuthToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login-admin', [AuthController::class, 'loginAdmin']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/reset-password/new', [UserController::class, 'newPassword']);
    Route::get('/me', [AuthController::class, 'me'])->middleware(['auth:sanctum']);
    Route::get('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/{user_id}/otp-validation', [AuthTokenController::class, 'index']);
    Route::post('/get-token', [AuthTokenController::class, 'token']);
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/user', [UserController::class, 'store'])->middleware(['AbleCreateUser']);

    // event 
    Route::post('/events/create', [EventController::class, 'store'])->middleware(['AbleCreateEvent']);
    Route::post('/events/update/{id}', [EventController::class, 'update'])->middleware(['AbleCreateEvent']);
    Route::post('/events/delete/{id}', [EventController::class, 'destroy'])->middleware(['AbleCreateEvent']);

    // Vanue
    Route::post('/venues/create', [VenueController::class, 'store'])->middleware(['AbleCreateVenue']);
    Route::patch('/venues/update/{id}', [VenueController::class, 'update'])->middleware(['AbleCreateVenue']);
    Route::delete('/venues/delete/{id}', [VenueController::class, 'destroy'])->middleware(['AbleCreateVenue']);
    Route::get('/venues', [VenueController::class, 'index'])->middleware([]);
    Route::get('/venues/{id}', [VenueController::class, 'show'])->middleware([]);

    // Seat Category
    Route::post('/seat-categories/create', [SeatCategoryController::class, 'store'])->middleware(['AbleCreateVenue']);

    // Event
    Route::get('/events', [EventController::class, 'index'])->middleware([]);
    Route::get('/events/{id}', [EventController::class, 'show'])->middleware([]);
    Route::get('/events/{id}/seats', [EventController::class, 'showSeat'])->middleware([]);
    Route::get('/events/{id}/seats/{seat_id}', [EventController::class, 'showSeat'])->middleware([]);
    Route::post('/events/create', [EventController::class, 'store'])->middleware([]);
});
