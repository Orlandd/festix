<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()->delete();
        $data = [
            'user',
            'admin',
            'manager',
            'superadmin',
        ];

        foreach ($data as $value) {
            Role::insert([
                'id' => Str::uuid(),
                "name" => $value,
                "created_at" => now(),
                "updated_at" => now()
            ]);
        }
    }
}
