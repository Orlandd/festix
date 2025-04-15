<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\AuthToken;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Menghapus data yang akan dikembalikan
            // unset($user->email_verified_at);
            unset($user->created_at);
            unset($user->updated_at);
            unset($user->deleted_at);

            // Hapus token sebelumnya
            $user->tokens()->delete();

            // Generate token baru
            $token = $user->createToken('sanctum')->plainTextToken;
            $user->token = $token;

            return response(['data' => $user]);
        } catch (ValidationException $e) {
            Log::error('error: ' . $e->getMessage());
            return response(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('Authentication Error: ' . $e->getMessage(), ['exception' => $e]);
            return response(['message' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function loginAdmin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $role = Role::where('id', $user->role_id)->first();
            if ($role->name == "user") {
                return response(['message' => 'You are not an admin'], 403);
            }

            // Menghapus data yang akan dikembalikan
            // unset($user->email_verified_at);
            unset($user->created_at);
            unset($user->updated_at);
            unset($user->deleted_at);

            // Hapus token sebelumnya
            $user->tokens()->delete();

            // Generate token baru
            $token = $user->createToken('sanctum')->plainTextToken;
            $user->token = $token;

            return response(['data' => $user]);
        } catch (ValidationException $e) {
            Log::error('Validation Error: ' . $e->getMessage(), ['request' => $request->all()]);
            return response(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('Authentication Error: ' . $e->getMessage(), ['exception' => $e]);
            return response(['message' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user) {
                throw ValidationException::withMessages([
                    'email' => ['Email not found .'],
                ]);
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

            // Menghapus data yang akan dikembalikan
            // unset($user->email_verified_at);
            unset($user->created_at);
            unset($user->updated_at);
            unset($user->deleted_at);

            return response(['data' => $user]);
        } catch (ValidationException $e) {
            Log::error('Validation Error: ' . $e->getMessage(), ['request' => $request->all()]);
            return response(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('Authentication Error: ' . $e->getMessage(), ['exception' => $e]);
            return response(['message' => 'An error occurred while processing your request.'], 500);
        }
    }

    public function me()
    {
        $user = Auth::user()->load('role'); // load eager loading pada instance user
        return response(['data' => $user]);
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            if ($user) {
                $user->tokens()->delete();
                Log::info('User logged out successfully', ['user_id' => $user->id]);
                return response(['message' => 'Logged out successfully']);
            } else {
                return response(['message' => 'User not authenticated'], 401);
            }
        } catch (Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage(), ['exception' => $e]);
            return response(['message' => 'An error occurred while logging out.'], 500);
        }
    }
}
