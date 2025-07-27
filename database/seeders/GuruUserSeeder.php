<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $gurus = Guru::whereNotNull('email')->get();

        foreach ($gurus as $guru) {
            // Buat atau perbarui user berdasarkan email
            $user = User::updateOrCreate(
                ['email' => $guru->email],
                [
                    'name' => $guru->nama,
                    'password' => Hash::make('12345678'),
                ]
            );

            // Pastikan role 'guru' diberikan
            if (!$user->hasRole('guru')) {
                $user->assignRole('guru');
            }
        }

        echo "Sinkronisasi guru ke users selesai.\n";
    }
}

    //  public function run()
    // {
    //     $gurus = Guru::whereNotNull('email')->get();

    //     foreach ($gurus as $guru) {
    //         // Cek apakah sudah ada user dengan email itu
    //         $user = User::firstOrCreate(
    //             ['email' => $guru->email],
    //             [
    //                 'name' => $guru->nama,
    //                 'password' => Hash::make('12345678'), // Default password
    //             ]
    //         );

    //         // Assign role 'guru' jika belum ada
    //         if (!$user->hasRole('guru')) {
    //             $user->assignRole('guru');
    //         }
    //     }

    //     echo "Sinkronisasi guru ke users selesai.\n";
    // }
//}
