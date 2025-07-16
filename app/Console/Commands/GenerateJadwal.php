<?php

namespace App\Console\Commands;

use App\Helpers\GeneticScheduler;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pengampu;
use App\Models\Ruangan;
use App\Models\Waktu;
use Illuminate\Console\Command;

class GenerateJadwal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-jadwal';

    /**
     * The console command description.
     *
     * @var string
     */
   // protected $description = 'Command description';
    protected $description = 'Generate jadwal menggunakan algoritma genetika';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $popSize = 150;
        $crossRate = 0.8;
        $mutRate = 0.3;
        $generations = 300;

        $kelasList = Kelas::all();
        $pengampus = Pengampu::with(['guru', 'mapel'])->get();
        $ruangans = Ruangan::all();
        $waktus = Waktu::all();

        $this->info("📅 Total slot waktu: " . $waktus->count());

        $requirements = [];
        foreach ($pengampus as $p) {
            $requires_ruang = $p->mapel->ruang_khusus ? strtolower($p->mapel->ruang_khusus) : null;
            $requirements[] = [
                'kelas_id' => $p->kelas_id,
                'mapel_id' => $p->mapel_id,
                'guru_options' => [$p->guru_id],
                'requires_ruang' => $requires_ruang,
            ];
        }

        $this->info("📌 Total kebutuhan jadwal: " . count($requirements));

        $mapelJam = Mapel::pluck('jam_per_minggu', 'id')->toArray();

        $scheduler = new GeneticScheduler(
            $requirements,
            $ruangans,
            $waktus,
            $popSize,
            $crossRate,
            $mutRate,
            $generations,
            $mapelJam
        );

        $bestSchedule = null;
        $bestInserted = 0;
        $bestSkipped = PHP_INT_MAX;
        $bestConflictDetails = [];

        for ($i = 0; $i < 10; $i++) {
            $this->info("🚀 Iterasi ke-" . ($i + 1));
            $schedule = $scheduler->run();

            $inserted = 0;
            $conflicts = [];

            foreach ($schedule as $jadwal) {
                $conflict = Jadwal::where('waktu_id', $jadwal['waktu_id'])
                    ->where(function ($query) use ($jadwal) {
                        $query->where('kelas_id', $jadwal['kelas_id'])
                            ->orWhere('guru_id', $jadwal['guru_id'])
                            ->orWhere('ruangan_id', $jadwal['ruangan_id']);
                    })->exists();

                if (!$conflict) {
                    $inserted++;
                } else {
                    $conflicts[] = $jadwal;
                }
            }

            $skipped = count($schedule) - $inserted;

            if ($skipped < $bestSkipped) {
                $bestSchedule = $schedule;
                $bestInserted = $inserted;
                $bestSkipped = $skipped;
                $bestConflictDetails = $conflicts;
            }

            if ($bestSkipped <= 5) break;
        }

        // Hapus jadwal lama
        Jadwal::truncate();

        foreach ($bestSchedule as $jadwal) {
            $conflict = Jadwal::where('waktu_id', $jadwal['waktu_id'])
                ->where(function ($query) use ($jadwal) {
                    $query->where('kelas_id', $jadwal['kelas_id'])
                        ->orWhere('guru_id', $jadwal['guru_id'])
                        ->orWhere('ruangan_id', $jadwal['ruangan_id']);
                })->exists();

            if (!$conflict) {
                Jadwal::create([
                    'kelas_id'   => $jadwal['kelas_id'],
                    'mapel_id'   => $jadwal['mapel_id'],
                    'guru_id'    => $jadwal['guru_id'],
                    'waktu_id'   => $jadwal['waktu_id'],
                    'ruangan_id' => $jadwal['ruangan_id'] ?? null,
                ]);
            }
        }

        $this->info("✅ Jadwal selesai digenerate.");
        $this->info("✔️  Jadwal berhasil: $bestInserted");
        $this->info("❌  Konflik saat simpan: $bestSkipped");

        if (!empty($bestConflictDetails)) {
            $this->warn("⚠️  Detail konflik:");
            foreach ($bestConflictDetails as $conf) {
                $this->line("⛔ Kelas {$conf['kelas_id']} | Mapel {$conf['mapel_id']} | Guru {$conf['guru_id']} | Waktu {$conf['waktu_id']} | Ruangan {$conf['ruangan_id']}");
            }
        }
    }
}




