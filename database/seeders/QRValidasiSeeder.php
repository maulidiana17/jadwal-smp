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
        $now = Carbon::now();

        if ($now->between(Carbon::today()->setTime(11, 0), Carbon::today()->setTime(12, 0))) {
            QRValidasi::where('expired_at', '<', $now)->delete();

            QRValidasi::create([
                'kode_qr' => 'ABSEN-' . $now->format('Ymd-His') . '-' . Str::random(5),
                'tanggal' => $now->toDateString(),
                'expired_at' => $now->copy()->addSeconds(30)
            ]);
        }
    }
}
