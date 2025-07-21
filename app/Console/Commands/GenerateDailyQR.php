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
        $today = Carbon::today()->toDateString();

        $qr = QRValidasi::where('tanggal', $today)->first();

        if (!$qr) {
            $kode = "ABSEN-" . date('Ymd-His') . "-" . strtoupper(Str::random(6));
            $expiredAt = now()->addMinutes(30);

            QRValidasi::create([
                'kode_qr' => $kode,
                'tanggal' => $today,
                'expired_at' => $expiredAt
            ]);

            $this->info('QR baru berhasil dibuat: ' . $kode);
        } else {
            $this->info('QR hari ini sudah ada.');
        }
    }

}
