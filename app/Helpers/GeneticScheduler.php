<?php

namespace App\Helpers;

use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneticScheduler
{
    // Properti utama yang digunakan untuk proses genetika
    protected $requirements, $rooms, $waktuList;
    protected $populationSize, $crossoverRate, $mutationRate, $maxGenerations;
    protected $elitism = 2, $stagnationLimit = 30, $noChange = 0;
    protected $mapelJamPerMinggu;
    protected $maxGuruJam;
    protected $kelasToRuangan = [];
    protected $waktuCache = [];
    protected $hariCache = [];

    // Konstruktor untuk inisialisasi data
    public function __construct($requirements, $rooms, $waktuList, $popSize = 100, $crossRate = 0.8, $mutRate = 0.2, $gens = 200, $mapelJamPerMinggu = [], $maxGuruJam = 60)
    {
        // Filter waktu yang hanya berjenis pelajaran dan urutkan berdasarkan hari dan jam
        $this->waktuList = collect($waktuList)
            ->filter(fn($w) => strtolower($w->ket) === 'pelajaran')
            ->sortBy(fn($w) => [$w->hari, $w->jam_ke])
            ->values();

        $this->requirements = $requirements;
        $this->rooms = collect($rooms);
        $this->populationSize = $popSize;
        $this->crossoverRate = $crossRate;
        $this->mutationRate = $mutRate;
        $this->maxGenerations = $gens;
        $this->mapelJamPerMinggu = $mapelJamPerMinggu;
        $this->maxGuruJam = $maxGuruJam;

        // Petakan kelas ke ruangan
        $this->mapKelasToRuangan();

        // Cache waktu dan hari untuk mempercepat lookup
        $this->waktuCache = $this->waktuList->pluck('jam_ke', 'id')->toArray();
        $this->hariCache = $this->waktuList->pluck('hari', 'id')->toArray();
    }

    // Fungsi utama untuk menjalankan algoritma genetika
    public function run()
    {
        try {
            $startTime = microtime(true);
            $maxSeconds = 60;

            // Inisialisasi populasi awal
            $pop = $this->initialPopulation();

            // Urutkan berdasarkan fitness terbaik
            usort($pop, fn($a, $b) => $this->fitness($b) <=> $this->fitness($a));
            $bestFit = $this->fitness($pop[0]);

            // Evolusi generasi
            for ($gen = 1; $gen <= $this->maxGenerations; $gen++) {
                if ((microtime(true) - $startTime) > $maxSeconds) break;

                $newPop = array_slice($pop, 0, $this->elitism); // elitism (mempertahankan individu terbaik)

                while (count($newPop) < $this->populationSize) {
                    $child = $this->crossoverMulti($pop); // crossover
                    $child = $this->mutate($child);       // mutasi
                    $newPop[] = $child;
                }

                $pop = $newPop;
                usort($pop, fn($a, $b) => $this->fitness($b) <=> $this->fitness($a));
                $curr = $this->fitness($pop[0]);

                // Cek stagnasi
                if ($curr <= $bestFit) {
                    $this->noChange++;
                    if ($this->noChange >= $this->stagnationLimit) break;
                } else {
                    $bestFit = $curr;
                    $this->noChange = 0;
                }
            }

            return [
                'jadwal' => $pop[0],
                'fitness' => $bestFit
            ];
        } catch (\Exception $e) {
            Log::error('GeneticScheduler error: ' . $e->getMessage());
            throw $e;
        }
    }

    // Membuat populasi awal
    // protected function initialPopulation()
    // {
    //     $pop = [];
    //     $groupedWaktu = $this->waktuList->groupBy('hari');

    //     for ($i = 0; $i < $this->populationSize; $i++) {
    //         $chrom = [];
    //         $kelasList = collect($this->requirements)->pluck('kelas_id')->unique();

    //         foreach ($kelasList as $kelasId) {
    //             $kelasReqs = collect($this->requirements)->where('kelas_id', $kelasId);
    //             $availableReqs = $kelasReqs->shuffle();

    //             foreach ($groupedWaktu as $hari => $waktus) {
    //                 $slotCount = 0;

    //                 // Aturan jumlah slot dan mapel per hari
    //                 if ($hari === 'Senin') {
    //                     $targetSlot = 6;
    //                     $mapelMin = 2;
    //                     $mapelMax = 3;
    //                     $jamValid = $waktus->whereBetween('jam_ke', [3, 8]);
    //                 } elseif (in_array($hari, ['Selasa', 'Rabu', 'Kamis'])) {
    //                     $targetSlot = 8;
    //                     $mapelMin = 2;
    //                     $mapelMax = 4;
    //                     $jamValid = $waktus->whereBetween('jam_ke', [1, 8]);
    //                 } elseif ($hari === 'Jumat') {
    //                     $targetSlot = 5;
    //                     $mapelMin = $mapelMax = 2;
    //                     $jamValid = $waktus->whereBetween('jam_ke', [1, 5]);
    //                 } elseif ($hari === 'Sabtu') {
    //                     $targetSlot = 5;
    //                     $mapelMin = $mapelMax = 1;
    //                     $jamValid = $waktus->whereBetween('jam_ke', [1, 5]);
    //                 } else {
    //                     continue;
    //                 }

    //                 $jamValidIds = $jamValid->pluck('id')->shuffle()->take($targetSlot);
    //                 $mapelDipakai = $availableReqs->take($mapelMax)->shuffle()->take(rand($mapelMin, $mapelMax));
    //                 $mapelList = $hari === 'Sabtu' ? $mapelDipakai->take(1) : $mapelDipakai;

    //                 // Alokasikan mapel ke waktu
    //                 $slotIdx = 0;
    //                 foreach ($mapelList as $req) {
    //                     $jamPerMapel = floor($targetSlot / $mapelList->count());
    //                     for ($j = 0; $j < $jamPerMapel && $slotIdx < count($jamValidIds); $j++) {
    //                         $chrom[] = [
    //                             'kelas_id'   => $kelasId,
    //                             'mapel_id'   => $req['mapel_id'],
    //                             'guru_id'    => $req['guru_options'][array_rand($req['guru_options'])],
    //                             'waktu_id'   => $jamValidIds[$slotIdx],
    //                             'ruangan_id' => $this->assignRuangan($req, $kelasId)
    //                         ];
    //                         $slotIdx++;
    //                     }
    //                 }
    //             }
    //         }

    //         $pop[] = $chrom;
    //     }

    //     return $pop;
    // }

    // protected function initialPopulation()
    // {
    //     $pop = [];
    //     $groupedWaktu = $this->waktuList->groupBy('hari');
    //     $totalGen = 0;

    //     for ($i = 0; $i < $this->populationSize; $i++) {
    //         $chrom = [];
    //         $kelasList = collect($this->requirements)->pluck('kelas_id')->unique();

    //         foreach ($kelasList as $kelasId) {
    //             $kelasReqs = collect($this->requirements)->where('kelas_id', $kelasId);
    //             $availableReqs = $kelasReqs->shuffle();

    //             foreach ($groupedWaktu as $hari => $waktus) {
    //                 $slotCount = 0;

    //                 if ($hari === 'Senin') {
    //                     $targetSlot = 6;
    //                     $mapelMin = 2;
    //                     $mapelMax = 3;
    //                     $jamValid = $waktus->whereBetween('jam_ke', [3, 8]);
    //                 } elseif (in_array($hari, ['Selasa', 'Rabu', 'Kamis'])) {
    //                     $targetSlot = 8;
    //                     $mapelMin = 2;
    //                     $mapelMax = 4;
    //                     $jamValid = $waktus->whereBetween('jam_ke', [1, 8]);
    //                 } elseif ($hari === 'Jumat') {
    //                     $targetSlot = 5;
    //                     $mapelMin = $mapelMax = 2;
    //                     $jamValid = $waktus->whereBetween('jam_ke', [1, 5]);
    //                 } elseif ($hari === 'Sabtu') {
    //                     $targetSlot = 5;
    //                     $mapelMin = $mapelMax = 1;
    //                     $jamValid = $waktus->whereBetween('jam_ke', [1, 5]);
    //                 } else {
    //                     continue;
    //                 }

    //                 $jamValidIds = $jamValid->pluck('id')->shuffle()->take($targetSlot);
    //                 $mapelDipakai = $availableReqs->take($mapelMax)->shuffle()->take(rand($mapelMin, $mapelMax));
    //                 $mapelList = $hari === 'Sabtu' ? $mapelDipakai->take(1) : $mapelDipakai;

    //                 $slotIdx = 0;
    //                 foreach ($mapelList as $req) {
    //                     $jamPerMapel = floor($targetSlot / $mapelList->count());
    //                     for ($j = 0; $j < $jamPerMapel && $slotIdx < count($jamValidIds); $j++) {
    //                         $chrom[] = [
    //                             'kelas_id'   => $kelasId,
    //                             'mapel_id'   => $req['mapel_id'],
    //                             'guru_id'    => $req['guru_options'][array_rand($req['guru_options'])],
    //                             'waktu_id'   => $jamValidIds[$slotIdx],
    //                             'ruangan_id' => $this->assignRuangan($req, $kelasId)
    //                         ];
    //                         $slotIdx++;
    //                     }
    //                 }
    //             }
    //         }

    //         $totalGen += count($chrom);
    //         Log::info("🧬 Kromosom ke-$i jumlah gen: " . count($chrom));
    //         $pop[] = $chrom;
    //     }

    //     $rataRata = $this->populationSize > 0 ? ($totalGen / $this->populationSize) : 0;
    //     Log::info("📊 Rata-rata gen per kromosom: $rataRata");

    //     return $pop;
    // }

    protected function initialPopulation()
    {
        $pop = [];
        $totalGen = 0;

        for ($i = 0; $i < $this->populationSize; $i++) {
            $chrom = [];
            $kelasList = collect($this->requirements)->pluck('kelas_id')->unique();

            foreach ($kelasList as $kelasId) {
                $kelasReqs = collect($this->requirements)->where('kelas_id', $kelasId);

                // Acak semua waktu yang tersedia untuk kelas ini
                $availableWaktu = $this->waktuList->pluck('id')->shuffle();

                foreach ($kelasReqs as $req) {
                    $mapelId = $req['mapel_id'];
                    $jumlahJam = $req['jumlah_jam'] ?? 2;

                    for ($j = 0; $j < $jumlahJam && !$availableWaktu->isEmpty(); $j++) {
                        $waktuId = $availableWaktu->pop();

                        $chrom[] = [
                            'kelas_id'   => $kelasId,
                            'mapel_id'   => $mapelId,
                            'guru_id'    => $req['guru_options'][array_rand($req['guru_options'])],
                            'waktu_id'   => $waktuId,
                            'ruangan_id' => $this->assignRuangan($req, $kelasId),
                        ];
                    }
                }
            }

            $totalGen += count($chrom);
            Log::info("🧬 Kromosom ke-$i jumlah gen: " . count($chrom));
            $pop[] = $chrom;
        }

        Log::info("✅ Jumlah kromosom yang terbentuk: " . count($pop));
        Log::info("📊 Total gen: $totalGen | Rata-rata gen per kromosom: " . ($totalGen / max(count($pop), 1)));

        return $pop;
    }




    // Mutasi kromosom
    protected function mutate(array $chrom): array
    {
        foreach ($chrom as &$g) {
            if (mt_rand() / mt_getrandmax() > $this->mutationRate) continue;

            $req = collect($this->requirements)->first(
                fn($r) =>
                $r['kelas_id'] == $g['kelas_id'] && $r['mapel_id'] == $g['mapel_id']
            );

            if (!$req || empty($req['guru_options'])) continue;

            $g['guru_id'] = $req['guru_options'][array_rand($req['guru_options'])];
            $newWaktu = $this->getRandomAvailableWaktu($chrom, $g['kelas_id']);
            if ($newWaktu) $g['waktu_id'] = $newWaktu;
            $g['ruangan_id'] = $this->assignRuangan($req, $g['kelas_id']);
        }

        return $chrom;
    }

    // Fungsi evaluasi kualitas solusi
    // protected function fitness($chrom)
    // {
    //     $score = 0;

    //     $byHariKelas = collect($chrom)->groupBy(fn($g) => $this->hariCache[$g['waktu_id']] . '-' . $g['kelas_id']);

    //     foreach ($byHariKelas as $hariKelas => $genes) {
    //         [$hari, $kelasId] = explode('-', $hariKelas);
    //         $jamKeList = $genes->pluck('waktu_id')->map(fn($id) => $this->waktuCache[$id])->unique()->sort()->values();
    //         $mapelList = $genes->pluck('mapel_id')->unique();

    //         $countSlot = $jamKeList->count();
    //         $countMapel = $mapelList->count();

    //         // Mulai dari nilai dasar tiap hari valid
    //         $baseScore = 0;
    //         $valid = false;

    //         if ($hari === 'Senin') {
    //             $valid = $countSlot == 6 && $countMapel >= 2 && $countMapel <= 3 && $jamKeList->min() >= 3 && $jamKeList->max() <= 8;
    //             $baseScore = 1000;
    //         } elseif (in_array($hari, ['Selasa', 'Rabu', 'Kamis'])) {
    //             $valid = $countSlot == 8 && $countMapel <= 4 && $jamKeList->min() >= 1 && $jamKeList->max() <= 8;
    //             $baseScore = 1200;
    //         } elseif ($hari === 'Jumat') {
    //             $valid = $countSlot == 5 && $countMapel == 2 && $jamKeList->min() >= 1 && $jamKeList->max() <= 5;
    //             $baseScore = 800;
    //         } elseif ($hari === 'Sabtu') {
    //             $valid = $countSlot == 5 && $countMapel == 1 && $genes->every(fn($g) => $g['mapel_id'] == $mapelList->first());
    //             $baseScore = 700;
    //         }

    //         if ($valid) {
    //             // Tambahkan skor jika valid
    //             $score += $baseScore;

    //             // Bonus jika jam berurutan (tidak lompat-lompat)
    //             $isSequential = true;
    //             for ($i = 1; $i < $jamKeList->count(); $i++) {
    //                 if ($jamKeList[$i] != $jamKeList[$i - 1] + 1) {
    //                     $isSequential = false;
    //                     break;
    //                 }
    //             }
    //             if ($isSequential) $score += 200;

    //             // Bonus jika mapel tidak diulang-ulang dalam hari itu
    //             if ($countMapel == $genes->count()) {
    //                 $score += 100;
    //             }
    //         } else {
    //             // Penalti berat jika tidak valid
    //             $score -= 1000;
    //         }
    //     }

    //     return max($score, 0); // Pastikan tidak negatif
    // }

    protected function fitness($chrom)
    {
        $KG = 0; // Konflik Guru
        $KR = 0; // Konflik Ruangan
        $KW = 0; // Konflik Waktu (kelas)

        $byWaktu = collect($chrom)->groupBy('waktu_id');

        foreach ($byWaktu as $waktuId => $items) {
            $guruCounter = [];
            $ruangCounter = [];
            $kelasCounter = [];

            foreach ($items as $g) {
                $guruCounter[$g['guru_id']] = ($guruCounter[$g['guru_id']] ?? 0) + 1;
                $ruangCounter[$g['ruangan_id']] = ($ruangCounter[$g['ruangan_id']] ?? 0) + 1;
                $kelasCounter[$g['kelas_id']] = ($kelasCounter[$g['kelas_id']] ?? 0) + 1;
            }

            // Hitung konflik: lebih dari satu entri untuk guru/ruang/kelas di waktu yang sama
            $KG += collect($guruCounter)->filter(fn($c) => $c > 1)->count();
            $KR += collect($ruangCounter)->filter(fn($c) => $c > 1)->count();
            $KW += collect($kelasCounter)->filter(fn($c) => $c > 1)->count();
        }

        $totalPenalty = $KG + $KR + $KW;

        // F = 1 / (1 + total penalty)
        return 1 / (1 + $totalPenalty);
    }



    // Crossover menggunakan beberapa parent
    protected function crossoverMulti(array $pop): array
    {
        $parents = $this->selectMultipleParents($pop, 5);
        $child = [];
        $len = min(array_map('count', $parents));

        for ($i = 0; $i < $len; $i++) {
            $parent = $parents[random_int(0, count($parents) - 1)];
            $child[$i] = $parent[$i];
        }

        return $child;
    }

    // Seleksi beberapa parent menggunakan tournament
    protected function selectMultipleParents(array $pop, int $count): array
    {
        $parents = [];
        for ($i = 0; $i < $count; $i++) {
            $parents[] = $this->selectTournament($pop);
        }
        return $parents;
    }

    // Tournament selection untuk seleksi parent terbaik
    protected function selectTournament($pop)
    {
        $best = null;
        for ($i = 0; $i < 3; $i++) {
            $candidate = $pop[array_rand($pop)];
            if (!$best || $this->fitness($candidate) > $this->fitness($best)) {
                $best = $candidate;
            }
        }
        return $best;
    }

    // Alokasikan ruangan untuk requirement tertentu
    protected function assignRuangan($req, $kelasId)
    {
        if (!empty($req['requires_ruang'])) {
            $filtered = $this->rooms->filter(fn($r) => strtolower($r->tipe) === strtolower($req['requires_ruang']));
            if ($filtered->isNotEmpty()) {
                return $filtered->random()->id;
            }
        }

        return $this->kelasToRuangan[$kelasId] ?? $this->rooms->random()->id;
    }

    // Petakan nama kelas ke ruangan jika tipe ruang = kelas
    protected function mapKelasToRuangan()
    {
        foreach ($this->rooms as $room) {
            if (strtolower($room->tipe) === 'kelas') {
                $kelas = Kelas::where('nama', $room->nama)->first();
                if ($kelas) {
                    $this->kelasToRuangan[$kelas->id] = $room->id;
                }
            }
        }
    }

    // Ambil semua waktu yang valid per hari
    protected function getValidWaktuPerHari()
    {
        return $this->waktuList->groupBy('hari')->map(fn($waktus) => $waktus->pluck('id'));
    }

    // Ambil waktu acak yang belum dipakai untuk kelas tertentu
    protected function getRandomAvailableWaktu($chrom, $kelasId)
    {
        $usedWaktu = collect($chrom)->where('kelas_id', $kelasId)->pluck('waktu_id')->toArray();
        $allIds = $this->waktuList->pluck('id')->toArray();
        $remaining = array_diff($allIds, $usedWaktu);

        return !empty($remaining) ? collect($remaining)->random() : null;
    }
}
