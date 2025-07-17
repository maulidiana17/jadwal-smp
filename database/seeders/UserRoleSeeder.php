<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::find(2); // sesuaikan ID
        if ($admin) {
            $admin->assignRole('admin');
        }

        $kurikulum = User::find(7);
        if ($kurikulum) {
            $kurikulum->assignRole('kurikulum');
        }
    }
}
