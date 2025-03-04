<?php

namespace App\Http\Controllers;

use App\Models\AuthToken;
use App\Http\Requests\StoreAuthTokenRequest;
use App\Http\Requests\UpdateAuthTokenRequest;
use App\Mail\OtpMail;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($userId, Request $request)
    {
        try {
            $otp = AuthToken::where('user_id', $userId)
                ->where('expired_at', '>', now())
                ->orderBy('created_at', 'desc') // Mengambil OTP terbaru berdasarkan waktu pembuatan
                ->first();

            Log::info('OTP received:', [
                'stored_otp' => $otp ? $otp->otp_code : null,
                'input_otp' => $request->otp_code
            ]);

            if (!$otp) {
                Log::warning('No valid OTP found', ['user_id' => $userId]);
                return response()->json(['error' => 'OTP code is invalid or expired'], 400);
            }

            if ($otp->otp_code != $request->otp_code) {
                Log::warning('Invalid OTP attempt', ['user_id' => $userId, 'input_otp' => $request->otp_code]);
                return response()->json(['error' => 'OTP code is invalid'], 400);
            }


            $user = User::find($userId);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $user->email_verified_at = Carbon::now();
            $user->save();

            AuthToken::where('user_id', $userId)->delete();
            Log::info('OTP verified successfully', ['user_id' => $userId]);

            return response(['message' => 'Success']);
        } catch (Exception $e) {
            Log::error('OTP Verification Error: ' . $e->getMessage(), ['exception' => $e]);
            return response(['message' => 'An error occurred while verifying OTP.'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function token(Request $request)
    {
        try {
            Log::info('Request received:', $request->all()); // Cek isi request di log

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                Log::warning('OTP request for non-existent email', ['email' => $request->email]);
                return response(['error' => 'User not found'], 404);
            }

            $token = AuthToken::create([
                'user_id' => $user->id,
                'otp_code' => rand(100000, 999999),
                'expired_at' => Date::now()->addMinutes(5)
            ]);

            $data = [
                'name' => $user->name,
                'username' => $user->username,
                'otp' => $token->otp_code
            ];

            Mail::to($user->email)->send(new OtpMail($data));
            Log::info('OTP sent successfully', ['user_id' => $user->id, 'otp' => $token->otp_code]);

            return response(['message' => 'OTP sent successfully', 'data' => $user]);
        } catch (Exception $e) {
            Log::error('OTP Generation Error: ' . $e->getMessage(), ['exception' => $e]);
            return response(['message' => 'An error occurred while sending OTP.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthTokenRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AuthToken $authToken)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AuthToken $authToken)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthTokenRequest $request, AuthToken $authToken)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AuthToken $authToken)
    {
        //
    }
}
