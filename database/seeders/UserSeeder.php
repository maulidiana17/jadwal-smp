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
        $user = User::create(
            [
                'email' => 'dini123@gmail.com',
                'name'      => 'admin',
                'password'  => Hash::make('dini123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $user->assignRole('admin');
    }
}
