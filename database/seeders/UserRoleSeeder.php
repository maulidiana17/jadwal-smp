<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first(); // Ganti dengan email admin Anda
        if ($admin) {
            $admin->assignRole('admin');
        }

        $kurikulum = User::where('email', 'kuri123@gmail.com')->first();
        if ($kurikulum) {
            $kurikulum->assignRole('kurikulum');
        }
    }
}
