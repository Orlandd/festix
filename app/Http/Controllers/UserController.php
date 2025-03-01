<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

            return response(['data' => $user]);
        } catch (\Exception $e) {
            Log::error('User creation failed', ['error' => $e->getMessage()]);
            return response(['error' => 'User creation failed'], 500);
        }
    }
}
