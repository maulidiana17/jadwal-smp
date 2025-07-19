<?php

namespace Database\Seeders;


use App\Models\QRValidasi;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QrValidasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         QRValidasi::create([
                'kode_qr' => 'ABSEN-' . Carbon::now()->format('Ymd-His') . '-' . Str::random(5),
                'tanggal' => Carbon::now()->toDateString()
            ]);

    }
}
