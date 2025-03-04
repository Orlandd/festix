<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\AuthToken;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->merge(['id' => Str::uuid()->toString()]);

            $request->validate([
                'name' => ['required', 'max:255'],
                'username' => ['required', 'unique:users'],
                'email' => ['required', 'unique:users'],
                'password' => ['required', 'max:255'],
            ]);

            $roleId = Role::where('name', 'user')->first()->id;
            $request['role_id'] = $roleId;
            $request['password'] = Hash::make($request->password);

            $user = User::create($request->all());

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
        } catch (\Exception $e) {
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return response(['error' => 'User creation failed'], 500);
        }
    }

    public function newPassword(Request $request)
    {
        try {

            $request->validate([
                'userId' => ['required'],
                'password' => ['required', 'max:255'],
            ]);

            Log::info('OTP received:', [
                'userId' => $request->userId,
                'password' => $request->password
            ]);

            $user = User::where('id', $request->userId)->first();

            Log::info('OTP received:', [
                'user' => $user,
            ]);
            if (!$user) {
                return response(['error' => 'User not found'], 404);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return response(['data' => $user]);
        } catch (\Exception $e) {
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return response(['error' => 'User creation failed'], 500);
        }
    }
}
