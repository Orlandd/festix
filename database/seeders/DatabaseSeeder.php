<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $roleId = DB::table('roles')->first()->id ?? Str::uuid();

        User::insert([
            'id' => Str::uuid(),
            "name" => 'superadmin',
            "username" => 'superadmin',
            "email" => 'superadmin@email.com',
            "password" => Hash::make(123),
            "role_id" => $roleId,
        ]);
    }
}
