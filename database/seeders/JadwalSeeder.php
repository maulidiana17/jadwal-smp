<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KonfigurasiLokasi::create([
                'waktu_id'=>'2',
                'kelas_id'=>'7',
                'mapel_id'=>'23',
                'guru_id'=> '2',
                'ruangan_id'=>'9'

            ]);
    }
}
