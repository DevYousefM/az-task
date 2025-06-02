<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrNew(
            ['email' => 'user@example.com'],
            [
                'name' => 'User',
                'password' => Hash::make('123456'),
            ]
        )->save();
    }
}
