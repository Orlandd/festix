<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthTokenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SeatCategoryController;
use App\Http\Controllers\TicketController;
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

    // admin

});

Route::get('/venues', [VenueController::class, 'index'])->middleware([]);
Route::get('/events', [EventController::class, 'index'])->middleware([]);
Route::get('/events/{id}', [EventController::class, 'show'])->middleware([]);


Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/admin/users/create', [UserController::class, 'store'])->middleware([]);
    Route::get('/admin/users', [UserController::class, 'index'])->middleware([]);
    Route::patch('/admin/users', [UserController::class, 'update'])->middleware([]);
    Route::delete('/admin/users/delete/{id}', [UserController::class, 'destroy'])->middleware([]);
    Route::get('/admin/roles', [RoleController::class, 'index'])->middleware([]);
    // event
    Route::post('/events/create', [EventController::class, 'store'])->middleware(['AbleCreateEvent']);
    Route::patch('/events/update/{id}', [EventController::class, 'update'])->middleware(['AbleCreateEvent']);
    Route::delete('/events/delete/{id}', [EventController::class, 'destroy'])->middleware(['AbleCreateEvent']);

    // Vanue
    Route::post('/venues/create', [VenueController::class, 'store'])->middleware(['AbleCreateVenue']);
    Route::patch('/venues/update/{id}', [VenueController::class, 'update'])->middleware(['AbleCreateVenue']);
    Route::delete('/venues/delete/{id}', [VenueController::class, 'destroy'])->middleware(['AbleCreateVenue']);
    Route::get('/venues/{id}', [VenueController::class, 'show'])->middleware([]);

    // Seat Category
    Route::get('/seat-categories', [SeatCategoryController::class, 'index'])->middleware([]);
    Route::get('/seat-categories/{id}', [SeatCategoryController::class, 'show'])->middleware([]);
    Route::post('/seat-categories/create', [SeatCategoryController::class, 'store'])->middleware(['AbleCreateVenue']);
    Route::patch('/seat-categories/update/{id}', [SeatCategoryController::class, 'update'])->middleware(['AbleCreateVenue']);
    Route::delete('/seat-categories/delete/{id}', [SeatCategoryController::class, 'destroy'])->middleware(['AbleCreateVenue']);

    // Event
    Route::get('/events/{id}/seats', [EventController::class, 'showSeat'])->middleware([]);
    Route::get('/events/{id}/seats/{seat_id}', [EventController::class, 'showSeat'])->middleware([]);
    Route::post('/events/create', [EventController::class, 'store'])->middleware([]);

    // payment
    Route::get('/payments', [PaymentController::class, 'index'])->middleware([]);
    Route::get('/payments/{id}', [PaymentController::class, 'show'])->middleware([]);
    Route::post('/payments/create', [PaymentController::class, 'store2'])->middleware([]);
    Route::post('/payments/create/manual', [PaymentController::class, 'manual2'])->middleware([]);
    Route::post('/payments/confirm', [PaymentController::class, 'confirm'])->middleware([]);
    Route::post('/payments/create/success', [PaymentController::class, 'success2'])->middleware([]);

    Route::get('/admin/payments/manual', [PaymentController::class, 'indexManual'])->middleware([]);
    Route::get('/admin/payments', [PaymentController::class, 'indexData'])->middleware([]);
    Route::post('/admin/payments/manual/confirm', [PaymentController::class, 'success2'])->middleware([]);
    Route::post('/admin/payments/manual/deny', [PaymentController::class, 'failed'])->middleware([]);

    Route::get('/recipt/{id}', [PaymentController::class, 'recipt'])->middleware([]);

    // History Ticket
    Route::get('/history-tickets', [TicketController::class, 'history'])->middleware([]);
    Route::get('/history-tickets/{id}', [TicketController::class, 'show'])->middleware([]);

    // verify ticket
    Route::post('/verify-ticket', [TicketController::class, 'verify'])->middleware([]);

    // cash
    Route::get('/cash/global/index', [PaymentController::class, 'indexCash'])->middleware([]);

    // dashboard
    Route::get('/admin/dashboard/ongoing', [DashboardController::class, 'ongoing'])->middleware([]);
    Route::get('/admin/dashboard/users', [DashboardController::class, 'user'])->middleware([]);
    Route::get('/admin/dashboard/revenue', [DashboardController::class, 'revenue'])->middleware([]);
});
