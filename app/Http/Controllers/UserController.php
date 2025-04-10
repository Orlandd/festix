<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\AuthToken;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'max:255'],
                'username' => ['required', 'unique:users'],
                'email' => ['required', 'unique:users'],
                'password' => ['required', 'max:255'],
                'role_id' => ['required'],
            ]);

            $request['id'] = Str::uuid()->toString();
            $request['password'] = Hash::make($request->password);
            $request['email_verified_at'] = Carbon::now();

            $user = User::create($request->all());

            return response(['data' => $user]);
        } catch (\Exception $e) {
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return response([
                'status' => 'error',
                'message' => 'User creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $users = User::with('role')->get();

            return response([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response([
                'status' => 'error',
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
    public function update(Request $request)
    {
        try {
            $user = User::find(Auth::user()->id);

            if ($user->role_id != 1) {
                return response([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            if (!$user) {
                return response([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $request->validate([
                'password' => ['required', 'max:255'],
                'newPassword' => ['nullable', 'max:255'],
            ]);


            if (Hash::make($request->password) !== Auth::user()->password) {
                return response([
                    'status' => 'error',
                    'message' => 'Password is incorrect'
                ], 403);
            }

            if ($request->newPassword) {
                $request['newPassword'] = Hash::make($request->newPassword);
                $request['password'] = $request['newPassword'];
                unset($request['newPassword']);
            }

            $user->update($request->all());

            return response([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response([
                'status' => 'error',
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $user->delete();

            return response([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response([
                'status' => 'error',
                'message' => 'Internal Server Error'
            ], 500);
        }
    }
}
