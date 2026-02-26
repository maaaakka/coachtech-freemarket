<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
                'id' => 1,
                'name' => 'テストユーザー',
                'email' => 'test@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => null,
                'profile_completed' => false,
                'created_at' => now(),
                'updated_at' => now(),

        ]);
    }
}
