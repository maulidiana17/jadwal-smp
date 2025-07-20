<?php

namespace App\Console\Commands;

use App\Models\QRValidasi;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

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

        // Cek apakah dalam rentang waktu 05:00 - 08:00
        if ($now->between(Carbon::today()->setTime(5, 0), Carbon::today()->setTime(8, 0))) {
            QRValidasi::where('expired_at', '<', $now)->delete();

            QRValidasi::create([
                'kode_qr' => 'ABSEN-' . $now->format('Ymd-His') . '-' . Str::random(5),
                'tanggal' => $now->toDateString(),
                'expired_at' => $now->copy()->addSeconds(30) // expired 30 detik setelah dibuat
            ]);

            $this->info('QR Code generated at: ' . $now->toDateTimeString());
        } else {
            $this->info('Di luar jadwal pembuatan QR (05:00 - 08:00)');
        }
    }
}
