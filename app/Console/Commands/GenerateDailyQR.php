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

        // Cari QR Code yang masih aktif hari ini
        $qrAktif = QRValidasi::where('tanggal', $now->toDateString())
                    ->where('expired_at', '>', $now)
                    ->first();

        if ($qrAktif) {
            $this->info('QR Code masih aktif, tidak membuat baru. Kode: ' . $qrAktif->kode_qr);
            return;
        }

        // Hapus QR lama yang sudah expired
        QRValidasi::where('expired_at', '<', $now)->delete();

        // Buat QR baru
        QRValidasi::create([
            'kode_qr' => 'ABSEN-' . $now->format('Ymd-His') . '-' . Str::random(5),
            'tanggal' => $now->toDateString(),
            'expired_at' => $now->copy()->addMinutes(30)
        ]);

        $this->info('QR Code baru berhasil dibuat pada: ' . $now->toDateTimeString());

    } else {
        $this->info('Di luar jadwal pembuatan QR (05:00 - 08:00)');
    }
}

}
