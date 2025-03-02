<?php

use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/{user_id}/otp-verification', function () {
//     $data = [
//         'otp' => '123456'
//     ];
//     Mail::to('alvenorlando@gmail.com')->send(new OtpMail($data));
// });
