<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'      => 'admin',
            'email'     => 'kuri@gmail.com',
            'password'  => Hash::make('kuri123'),
             'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
