<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'ユーザー1',
                'email' => 'user1@test.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'ユーザー2',
                'email' => 'user2@test.com',
                'password' => Hash::make('password'),
            ],
        ]);
    }
}
