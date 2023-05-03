<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('administrator'),
            'name' => 'Admin',
            'is_admin' => '1'
        ]);
    }
}
