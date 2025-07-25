<?php

namespace App\Services;

use App\Helpers\GeneticScheduler;
use App\Models\Pengampu;
use App\Models\Ruangan;
use App\Models\Waktu;
use App\Models\Jadwal;
use Illuminate\Support\Facades\DB;

class SchedulerService
{
    /**
     * Generate the schedule using GeneticScheduler.
     *
     * @param  array  $params  Validated params: popSize, crossRate, mutRate, generations, tries(optional)
     * @return array [$bestSchedule, $conflicts, $bestSkipped]
     */
    public function generate(array $params): array
    {
        set_time_limit(1800);
    
        // Ambil semua pengampu dan susun kebutuhan jadwal
        $pengampus = Pengampu::with(['guru', 'mapel', 'kelas'])->get();
        $requirements = [];
    
        foreach ($pengampus as $p) {
            $requirements[] = [
                'kelas_id'       => $p->kelas_id,
                'mapel_id'       => $p->mapel_id,
                'guru_options'   => [$p->guru_id],
                'requires_ruang' => $p->mapel->ruang_khusus ? strtolower($p->mapel->ruang_khusus) : null,
            ];
        }
    
        // Ambil semua ruangan dan waktu pelajaran (non-istirahat, hanya 'pelajaran')
        $ruangans = Ruangan::all();
        $waktus = Waktu::whereRaw('LOWER(ket) = ?', ['pelajaran'])->get();
    
        // Ambil jam pelajaran per minggu dari mapel
        $mapelJamPerMinggu = DB::table('mapel')->pluck('jam_per_minggu', 'id')->toArray();
    
        // Jalankan GeneticScheduler
        $scheduler = new GeneticScheduler(
            $requirements,
            $ruangans,
            $waktus,
            $params['popSize'],
            $params['crossRate'],
            $params['mutRate'],
            $params['generations'],
            $mapelJamPerMinggu
        );
    
        // Inisialisasi hasil terbaik
        $bestSchedule = [];
        $bestConflicts = [];
        $bestSkipped = PHP_INT_MAX;
        $bestFitness = -INF;
    
        $tries = $params['tries'] ?? 3;
    
        for ($i = 0; $i < $tries; $i++) {
            $result = $scheduler->run();
            $schedule = $result['jadwal'];
            $fitness = $result['fitness'];
    
            \Log::info("Hasil generate [try-$i]: Jumlah jadwal: " . count($schedule) . " | Fitness: $fitness");
    
            // Cek konflik terhadap jadwal yang sudah ada di DB (bisa juga dikosongkan dulu)
            $conflicts = [];
    
            foreach ($schedule as $jadwal) {
                $exists = Jadwal::where('waktu_id', $jadwal['waktu_id'])
                    ->where(function ($q) use ($jadwal) {
                        $q->where('kelas_id', $jadwal['kelas_id'])
                          ->orWhere('guru_id', $jadwal['guru_id'])
                          ->orWhere('ruangan_id', $jadwal['ruangan_id']);
                    })
                    ->exists();
    
                if ($exists) {
                    $conflicts[] = $jadwal;
                }
            }
    
            $skipped = count($conflicts);
    
            \Log::info("Try-$i: Conflicts=$skipped | Fitness=$fitness");
    
            // Update jika solusi lebih baik
            if ($skipped < $bestSkipped || ($skipped === $bestSkipped && $fitness > $bestFitness)) {
                $bestSkipped = $skipped;
                $bestFitness = $fitness;
                $bestSchedule = $schedule;
                $bestConflicts = $conflicts;
            }
    
            // Jika sudah 0 konflik, langsung hentikan
            if ($bestSkipped === 0) break;
        }
    
        return [$bestSchedule, $bestConflicts, $bestSkipped, $bestFitness];
    }


    /**
     * Save the generated schedule to database.
     *
     * @param  array  $schedule
     * @return int Number of saved records
     */
    public function save(array $schedule): int
    {  
        Jadwal::truncate();
        return DB::transaction(function () use ($schedule) {
            \Log::info("Jumlah jadwal yang akan disimpan: " . count($schedule));

            foreach ($schedule as $j) {
                if (
                    empty($j['kelas_id']) ||
                    empty($j['mapel_id']) ||
                    empty($j['guru_id']) ||
                    empty($j['waktu_id'])
                ) {
                    \Log::warning("Data tidak lengkap: " . json_encode($j));
                    continue;
                }

                try {
                    \Log::info("Menyimpan: " . json_encode($j));
                    Jadwal::create([
                        'kelas_id'   => $j['kelas_id'],
                        'mapel_id'   => $j['mapel_id'],
                        'guru_id'    => $j['guru_id'],
                        'waktu_id'   => $j['waktu_id'],
                        'ruangan_id' => $j['ruangan_id'] ?? null,
                    ]);
                } catch (\Throwable $e) {
                    \Log::error("Gagal menyimpan jadwal: " . $e->getMessage());
                }
            }

            return count($schedule);
        });
    }


}