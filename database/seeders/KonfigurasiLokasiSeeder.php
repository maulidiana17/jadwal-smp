<?php

namespace Database\Seeders;

use App\Models\KonfigurasiLokasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KonfigurasiLokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         KonfigurasiLokasi::create([
                'lokasi_sekolah' => '-8.360791113959706, 114.1471384659776',
                'radius' => '55'

            ]);

    }
}
