<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@eventplanner.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'John Doe',
            'email'    => 'user@eventplanner.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);
    }
}
