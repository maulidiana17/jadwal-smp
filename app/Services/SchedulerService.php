<?php

namespace App\Services;

use App\Helpers\GeneticScheduler;
use App\Models\Pengampu;
use App\Models\Ruangan;
use App\Models\Waktu;
use App\Models\Jadwal;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;

class SchedulerService
{
    /**
     * Generate the schedule using GeneticScheduler.
     *
     * @param  array  $params  Validated params: popSize, crossRate, mutRate, generations, tries(optional)
     * @return array [$bestSchedule, $conflicts, $bestSkipped]
     */

    protected function generateRequirements(array $mapelJamPerMinggu): array
    {
        $requirements = [];
        $kelasSemua = \App\Models\Kelas::pluck('id', 'nama')->toArray();
        $pengampus = \App\Models\Pengampu::with(['guru', 'mapel'])->get();

        foreach ($pengampus as $p) {
            $kelasList = [];

            // Jika punya kolom kelas_id
            if (!empty($p->kelas_id)) {
                $kelasList[] = $p->kelas_id;
            }

            // Jika benar-benar ada kolom kelas di tabel pengampu
            if (array_key_exists('kelas', $p->getAttributes()) && !empty($p->kelas)) {
                $namaKelasArray = explode(',', str_replace(' ', '', $p->kelas));
                foreach ($namaKelasArray as $namaKelas) {
                    if (isset($kelasSemua[$namaKelas])) {
                        $kelasList[] = $kelasSemua[$namaKelas];
                    } else {
                        \Log::warning("Kelas tidak ditemukan: {$namaKelas} pada pengampu ID {$p->id}");
                    }
                }
            }

            $kelasList = array_unique($kelasList);

            foreach ($kelasList as $kelasId) {
                $requirements[] = [
                    'kelas_id'       => $kelasId,
                    'mapel_id'       => $p->mapel_id,
                    'guru_options'   => [$p->guru_id],
                    'jumlah_jam'     => isset($mapelJamPerMinggu[$p->mapel_id]) 
                                        ? (int) $mapelJamPerMinggu[$p->mapel_id]
                                        : 2,
                    'requires_ruang' => $p->mapel->ruang_khusus ?? null,
                ];
            }
        }
        // dd($requirements);

        return $requirements;
    }


    public function generate(array $params): array
    {
    set_time_limit(1800);

    // Ambil jam pelajaran per minggu dari mapel
    $mapelJamPerMinggu = DB::table('mapel')->pluck('jam_per_minggu', 'id')->toArray();

    // Debug data mapelJamPerMinggu
    // dd($mapelJamPerMinggu);

    // Panggil dengan parameter
    $requirements = $this->generateRequirements($mapelJamPerMinggu);

    $ruangans = Ruangan::all();
    $waktus = Waktu::whereRaw('LOWER(ket) = ?', ['pelajaran'])->get();

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

            // Ambil semua waktu_id yang dipakai di schedule
            $waktuIds = array_column($schedule, 'waktu_id');

            $existingJadwal = Jadwal::whereIn('waktu_id', $waktuIds)
                ->get(['waktu_id', 'kelas_id', 'guru_id', 'ruangan_id']);

            foreach ($schedule as $jadwal) {
                $isConflict = $existingJadwal->contains(function ($item) use ($jadwal) {
                    return $item->waktu_id == $jadwal['waktu_id'] &&
                        (
                            $item->kelas_id == $jadwal['kelas_id'] ||
                            $item->guru_id == $jadwal['guru_id'] ||
                            $item->ruangan_id == $jadwal['ruangan_id']
                        );
                });

                if ($isConflict) {
                    $conflicts[] = $jadwal;
                }
            }

            $skipped = count($conflicts);

            // Logging lebih detail
            \Log::info("Try-$i: Conflicts=$skipped | Fitness=$fitness", [
                'conflict_details' => $conflicts
            ]);
    
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
        $skipped = 0;

        return DB::transaction(function () use ($schedule, &$skipped) {
            \Log::info("Jumlah jadwal yang akan disimpan: " . count($schedule));

            foreach ($schedule as $j) {
                $missingFields = [];
                foreach (['kelas_id', 'mapel_id', 'guru_id', 'waktu_id'] as $field) {
                    if (empty($j[$field])) {
                        $missingFields[] = $field;
                    }
                }

                if (!empty($missingFields)) {
                    $skipped++;
                    $msg = "Data tidak lengkap, missing fields: " . implode(', ', $missingFields) . " - Data: " . json_encode($j);
                    \Log::warning($msg);
                    // Simpan ke file khusus agar mudah dianalisa
                    file_put_contents(storage_path('logs/skipped_jadwal.log'), $msg.PHP_EOL, FILE_APPEND);
                    continue;
                }

                try {
                    Jadwal::create([
                        'kelas_id'   => $j['kelas_id'],
                        'mapel_id'   => $j['mapel_id'],
                        'guru_id'    => $j['guru_id'],
                        'waktu_id'   => $j['waktu_id'],
                        'ruangan_id' => $j['ruangan_id'] ?? null,
                    ]);
                } catch (\Throwable $e) {
                    $skipped++;
                    $errMsg = "Gagal menyimpan jadwal: " . $e->getMessage() . " - Data: " . json_encode($j);
                    \Log::error($errMsg);
                    file_put_contents(storage_path('logs/skipped_jadwal.log'), $errMsg.PHP_EOL, FILE_APPEND);
                }
            }

            \Log::info("Jumlah jadwal yang berhasil disimpan: " . (count($schedule) - $skipped));
            \Log::info("Jumlah jadwal yang dilewati/skipped: " . $skipped);

            return count($schedule) - $skipped;
        });
    }

}