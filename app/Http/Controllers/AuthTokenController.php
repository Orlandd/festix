<?php

namespace App\Http\Controllers;

use App\Models\AuthToken;
use App\Http\Requests\StoreAuthTokenRequest;
use App\Http\Requests\UpdateAuthTokenRequest;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;

class AuthTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($userId, Request $request)
    {
        $otp = AuthToken::where('otp_code', $request->otp_code)->where('expired_at', '>', now())->first();
        if (!$otp) {
            return response(['error' => 'OTP code is invalid or expired'], 400);
        }
        $otp->user->email_verified_at = Date::now();
        $otp->user->save();

        AuthToken::where('user_id', $userId)->delete();

        return response(['message' => 'Success']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function token(Request $request)
    {

        $user = User::where('email', $request->email)->first();

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

        return response(['data' => $user]);
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
