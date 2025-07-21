<?php

namespace App\Console\Commands;

use App\Models\QRValidasi;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateDailyQR extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'qr:generate-daily';

    /**
     * The console command description.
     *
     * @var string
     */
       protected $description = 'Generate QR validasi harian';

    /**
     * Execute the console command.
     */
   public function handle()
    {
        $now = Carbon::now();

        // Cek jika sudah ada QR aktif untuk hari ini
        $existing = DB::table('qr_validasi')
            ->where('tanggal', $now->toDateString())
            ->where('expired_at', '>', $now)
            ->first();

        if ($existing) {
            $this->info('QR sudah tersedia untuk hari ini.');
            return 0;
        }

        // Generate kode QR baru
        $random = strtoupper(Str::random(6));
        $kode = 'ABSEN-SPENSA-' . $now->toDateString() . '-' . $random;

        DB::table('qr_validasi')->insert([
            'kode_qr'    => $kode,
            'tanggal'    => $now->toDateString(),
            'created_at' => $now,
            'updated_at' => $now,
            'expired_at' => $now->copy()->addMinutes(30), // atau sesuai kebutuhan
        ]);

        $this->info('QR berhasil dibuat: ' . $kode);
        return 0;
    }

}
