<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
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

        // menghapus data yang akan direturn 
        // unset($user->email_verified_at);
        unset($user->created_at);
        unset($user->updated_at);
        unset($user->deleted_at);

        $user->tokens()->delete();
        $token = $user->createToken('sanctum')->plainTextToken;
        $user->token = $token;


        return response(['data' => $user]);
    }

    public function me()
    {
        return response(['data' => Auth::user()]);
    }

    public function logout()
    {
        $user = Auth::user();

        $user->tokens()->delete();
        return response(['message' => 'Logged out successfully']);
    }
}
