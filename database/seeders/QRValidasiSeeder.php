<?php

namespace Database\Seeders;


use App\Models\QRValidasi;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QrValidasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $tanggal = Carbon::today();
        $kode = 'ABSEN-SPENSA-' . $tanggal->toDateString() . '-' . strtoupper(Str::random(6));

        DB::table('qr_validasi')->insert([
            'kode_qr'    => $kode,
            'tanggal'    => $tanggal->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
            'expired_at' => now()->addMinutes(30),
        ]);
    }
}
