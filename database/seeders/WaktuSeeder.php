<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Waktu;
use Carbon\Carbon;

class WaktuSeeder extends Seeder
{
    public function run()
    {
        $jadwalHari = [
            'Senin'  => ['jam_max' => 9, 'jam_awal' => '07:00', 'istirahat_setelah' => 3, 'istirahat_durasi' => 40],
            'Selasa' => ['jam_max' => 9, 'jam_awal' => '07:00', 'istirahat_setelah' => 3, 'istirahat_durasi' => 40],
            'Rabu'   => ['jam_max' => 9, 'jam_awal' => '07:00', 'istirahat_setelah' => 3, 'istirahat_durasi' => 40],
            'Kamis'  => ['jam_max' => 9, 'jam_awal' => '07:00', 'istirahat_setelah' => 3, 'istirahat_durasi' => 40],
            'Jumat'  => ['jam_max' => 5, 'jam_awal' => '06:30', 'istirahat_setelah' => 2, 'istirahat_durasi' => 20],
            'Sabtu'  => ['jam_max' => 7, 'jam_awal' => '07:00', 'istirahat_setelah' => 3, 'istirahat_durasi' => 40],
        ];

        foreach ($jadwalHari as $hari => $config) {
            $this->buatHari($hari, $config);
        }
    }

    private function buatHari($hari, $config)
    {
        $jamMulai = Carbon::createFromFormat('H:i', $config['jam_awal']);

        for ($jamKe = 1; $jamKe <= $config['jam_max']; $jamKe++) {
            // Tambahkan istirahat setelah jam ke-N
            if ($jamKe === $config['istirahat_setelah'] + 1) {
                Waktu::create([
                    'hari' => $hari,
                    'jam_ke' => $jamKe - 0.5,
                    'jam_mulai' => $jamMulai->format('H:i'),
                    'jam_selesai' => $jamMulai->copy()->addMinutes($config['istirahat_durasi'])->format('H:i'),
                    'ket' => 'Istirahat'
                ]);
                $jamMulai->addMinutes($config['istirahat_durasi']);
            }

            // Tambahkan jam pelajaran
            Waktu::create([
                'hari' => $hari,
                'jam_ke' => $jamKe,
                'jam_mulai' => $jamMulai->format('H:i'),
                'jam_selesai' => $jamMulai->copy()->addMinutes(40)->format('H:i'),
                'ket' => 'Pelajaran'
            ]);

            $jamMulai->addMinutes(40);
        }
    }
}


