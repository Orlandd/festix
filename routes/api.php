<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthTokenController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Models\AuthToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
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

    // Orders
});
