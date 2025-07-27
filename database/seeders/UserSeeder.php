<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Siswa;
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
        // Admin
        $user = User::updateOrCreate(
            ['email' => 'dini123@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('dini123'),
                'updated_at' => now(),
            ]
        );
        $user->assignRole('admin');

        // Kurikulum
        $kurikulum = User::updateOrCreate(
            ['email' => 'kurikulum@gmail.com'],
            [
                'name' => 'kurikulum',
                'password' => Hash::make('kurikulum'),
                'updated_at' => now(),
            ]
        );
        $kurikulum->assignRole('kurikulum');

        // Guru
        $guru = User::updateOrCreate(
            ['email' => 'spensa@gmail.com'],
            [
                'name' => 'guru',
                'password' => Hash::make('spensa123'),
                'updated_at' => now(),
            ]
        );
        $guru->assignRole('guru');

        // Siswa
        Siswa::updateOrCreate(
            ['nis' => '1234567890'],
            [
                'nama_lengkap' => 'siswa',
                'kelas' => '7A',
                'no_hp' => '6281310703603',
                'password' => Hash::make('1234567890'),
                'status' => 'aktif',
            ]
        );
    }


    // public function run(): void
    // {
    //     $user = User::create(
    //         [
    //             'email' => 'dini123@gmail.com',
    //             'name'      => 'admin',
    //             'password'  => Hash::make('dini123'),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]
    //     );

    //     $user->assignRole('admin');
    //     $kurikulum = User::create(
    //         [
    //             'email' => 'kurikulum@gmail.com',
    //             'name'      => 'kurikulum',
    //             'password'  => Hash::make('kurikulum'),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]
    //     );

    //     $kurikulum->assignRole('kurikulum');
    //     $guru= User::create(
    //         [
    //             'email' => 'spensa@gmail.com',
    //             'name'      => 'guru',
    //             'password'  => Hash::make('spensa123'),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]
    //     );

    //     $guru->assignRole('guru');


    //         $siswa = Siswa::create(
    //         [
    //             'nis' => '1234567890',
    //             'nama_lengkap'      => 'siswa',
    //             'kelas' =>'7A',
    //             'no_hp' =>'6281310703603',
    //             'password'  => Hash::make('1234567890'),
    //             'status'=>'aktif',

    //         ]
    //     );
    // }


}
