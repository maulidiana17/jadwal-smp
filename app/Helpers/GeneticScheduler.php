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

            $pop = $this->initialPopulation();
            usort($pop, fn($a, $b) => $this->fitness($b) <=> $this->fitness($a));
            $bestFit = $this->fitness($pop[0]);

            for ($gen = 1; $gen <= $this->maxGenerations; $gen++) {
                if ((microtime(true) - $startTime) > $maxSeconds) break;

                $newPop = array_slice($pop, 0, $this->elitism);

                while (count($newPop) < $this->populationSize) {
                    $child = $this->crossoverMapelWise($pop);
                    $child = $this->mutateAdvanced($child);
                    $child = $this->resolveGuruConflicts($child);
                    $child = $this->autoRepairConflicts($child);
                    $newPop[] = $child;
                }

                $pop = $newPop;
                usort($pop, fn($a, $b) => $this->fitness($b) <=> $this->fitness($a));
                $curr = $this->fitness($pop[0]);
                Log::info("Gen-$gen: Fitness={$curr}");

                if ($curr <= $bestFit) {
                    $this->noChange++;
                    if ($this->noChange >= $this->stagnationLimit) break;
                } else {
                    $bestFit = $curr;
                    $this->noChange = 0;
                }
            }
            $this->fillRemainingJadwal($pop[0]);
            $this->repairJadwalKurang($pop[0]);

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
    //     $totalGen = 0;

    //     for ($i = 0; $i < $this->populationSize; $i++) {
    //         $chrom = [];
    //         $kelasList = collect($this->requirements)->pluck('kelas_id')->unique();

    //         foreach ($kelasList as $kelasId) {
    //             $kelasReqs = collect($this->requirements)->where('kelas_id', $kelasId);

    //             // Acak semua waktu yang tersedia untuk kelas ini
    //             $availableWaktu = $this->waktuList->pluck('id')->shuffle();

    //             foreach ($kelasReqs as $req) {
    //                 $mapelId = $req['mapel_id'];
    //                 $jumlahJam = $req['jumlah_jam'] ?? 2;

    //                 for ($j = 0; $j < $jumlahJam && !$availableWaktu->isEmpty(); $j++) {
    //                     $waktuId = $availableWaktu->pop();

    //                     $chrom[] = [
    //                         'kelas_id'   => $kelasId,
    //                         'mapel_id'   => $mapelId,
    //                         'guru_id'    => $req['guru_options'][array_rand($req['guru_options'])],
    //                         'waktu_id'   => $waktuId,
    //                         'ruangan_id' => $this->assignRuangan($req, $kelasId),
    //                     ];
    //                 }
    //             }
    //         }

    //         $totalGen += count($chrom);
    //         Log::info("🧬 Kromosom ke-$i jumlah gen: " . count($chrom));
    //         $pop[] = $chrom;
    //     }

    //     Log::info("✅ Jumlah kromosom yang terbentuk: " . count($pop));
    //     Log::info("📊 Total gen: $totalGen | Rata-rata gen per kromosom: " . ($totalGen / max(count($pop), 1)));

    //     return $pop;
    // }
protected function initialPopulation()
{
    $pop = [];
    $totalGen = 0;

    for ($i = 0; $i < $this->populationSize; $i++) {
        $chrom = [];
        $kelasList = collect($this->requirements)->pluck('kelas_id')->unique();
        $guruTerpakai = []; // [waktu_id => [guru_id]]

        foreach ($kelasList as $kelasId) {
            $kelasReqs = collect($this->requirements)->where('kelas_id', $kelasId);
            $availableWaktu = $this->waktuList->pluck('id')->shuffle();

            foreach ($kelasReqs as $req) {
                $mapelId = $req['mapel_id'];
                $jumlahJam = $req['jumlah_jam'] ?? 2;

                for ($j = 0; $j < $jumlahJam && !$availableWaktu->isEmpty(); $j++) {
                    $waktuId = $availableWaktu->pop();

                    // Pilih guru yang belum terpakai di waktu ini
                    $availableGuru = array_filter($req['guru_options'], function ($gid) use ($guruTerpakai, $waktuId) {
                        return empty($guruTerpakai[$waktuId][$gid]);
                    });

                    if (empty($availableGuru)) continue;

                    $guruId = $availableGuru[array_rand($availableGuru)];

                    $chrom[] = [
                        'kelas_id'   => $kelasId,
                        'mapel_id'   => $mapelId,
                        'guru_id'    => $guruId,
                        'waktu_id'   => $waktuId,
                        'ruangan_id' => $this->assignRuangan($req, $kelasId),
                    ];

                    $guruTerpakai[$waktuId][$guruId] = true;
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
    protected function mutateAdvanced(array $chrom): array
    {
        foreach ($chrom as &$g) {
            if (mt_rand() / mt_getrandmax() > $this->mutationRate) continue;

            $req = collect($this->requirements)->first(
                fn($r) => $r['kelas_id'] == $g['kelas_id'] && $r['mapel_id'] == $g['mapel_id']
            );

            if (!$req || empty($req['guru_options'])) continue;

            $g['guru_id'] = $req['guru_options'][array_rand($req['guru_options'])];
            $newWaktu = $this->getRandomAvailableWaktu($chrom, $g['kelas_id'], $g['guru_id']);
            if ($newWaktu) $g['waktu_id'] = $newWaktu;
            $g['ruangan_id'] = $this->assignRuangan($req, $g['kelas_id']);
        }

        return $chrom;
    }

    // Fungsi evaluasi kualitas solusi
    protected function fitness($chrom)
    {
        $KG = 0; $KR = 0; $KW = 0;
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

            $KG += collect($guruCounter)->filter(fn($c) => $c > 1)->count();
            $KR += collect($ruangCounter)->filter(fn($c) => $c > 1)->count();
            $KW += collect($kelasCounter)->filter(fn($c) => $c > 1)->count();
        }

        $totalPenalty = $KG + $KR + $KW;
        $hardScore = 1 / (1 + $totalPenalty);
        $softScore = $this->scoreDistribusiJamMapel($chrom);

        return 0.7 * $hardScore + 0.3 * $softScore;
    }

    protected function crossoverMapelWise(array $pop): array
    {
        $parents = $this->selectMultipleParents($pop, 5);
        $child = [];
        $mapelGroups = collect($parents)->flatten(1)->groupBy('mapel_id');

        foreach ($mapelGroups as $mapelId => $genes) {
            $selected = $genes->random();
            $child[] = $selected;
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

    protected function scoreDistribusiJamMapel($chrom)
    {
        $score = 0;
        $grouped = collect($chrom)->groupBy(fn($g) => $g['kelas_id'] . '-' . $g['mapel_id']);

        foreach ($grouped as $key => $entries) {
            $days = $entries->map(fn($e) => $this->hariCache[$e['waktu_id']] ?? null)->unique()->count();
            if ($days > 1) $score++;
        }

        $maxScore = $grouped->count();
        return $maxScore > 0 ? $score / $maxScore : 0;
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
    // protected function getRandomAvailableWaktu($chrom, $kelasId)
    // {
    //     $usedWaktu = collect($chrom)->where('kelas_id', $kelasId)->pluck('waktu_id')->toArray();
    //     $allIds = $this->waktuList->pluck('id')->toArray();
    //     $remaining = array_diff($allIds, $usedWaktu);

    //     return !empty($remaining) ? collect($remaining)->random() : null;
    // }
// Tambahkan pengecekan agar waktu tidak dipakai oleh guru lain
protected function getRandomAvailableWaktu($chrom, $kelasId, $guruId = null)
{
    $usedWaktuKelas = collect($chrom)->where('kelas_id', $kelasId)->pluck('waktu_id')->toArray();
    $usedWaktuGuru = $guruId
        ? collect($chrom)->where('guru_id', $guruId)->pluck('waktu_id')->toArray()
        : [];

    $allIds = $this->waktuList->pluck('id')->toArray();
    $remaining = array_diff($allIds, $usedWaktuKelas, $usedWaktuGuru);

    return !empty($remaining) ? collect($remaining)->random() : null;
}


    protected function autoRepairConflicts(array $chrom): array
{
    $byWaktu = collect($chrom)->groupBy('waktu_id');
    $repaired = [];

    foreach ($byWaktu as $waktuId => $entries) {
        $guruUsed = [];

        foreach ($entries as $g) {
            if (!isset($guruUsed[$g['guru_id']])) {
                $guruUsed[$g['guru_id']] = true;
                $repaired[] = $g;
            } else {
                // konflik guru: cari waktu alternatif
                $altWaktu = $this->getRandomAvailableWaktu($chrom, $g['kelas_id']);
                if ($altWaktu) {
                    $g['waktu_id'] = $altWaktu;
                    $repaired[] = $g;
                } else {
                    // tidak ada alternatif, tetap masukkan walau konflik
                    $repaired[] = $g;
                }
            }
        }
    }

    return $repaired;
}

public function fillRemainingJadwal(&$chrom)
{
    foreach ($this->requirements as $req) {
        $count = collect($chrom)->where('kelas_id', $req['kelas_id'])->where('mapel_id', $req['mapel_id'])->count();

        $needed = ($req['jumlah_jam'] ?? 2) - $count;
        if ($needed <= 0) continue;

        $availableWaktu = $this->waktuList->pluck('id')->shuffle();
        $usedWaktu = collect($chrom)->pluck('waktu_id')->toArray();
        $remaining = array_diff($availableWaktu->toArray(), $usedWaktu);

        for ($i = 0; $i < $needed && !empty($remaining); $i++) {
            $waktuId = array_pop($remaining);
            $guruId = $req['guru_options'][array_rand($req['guru_options'])];

            $chrom[] = [
                'kelas_id'   => $req['kelas_id'],
                'mapel_id'   => $req['mapel_id'],
                'guru_id'    => $guruId,
                'waktu_id'   => $waktuId,
                'ruangan_id' => $this->assignRuangan($req, $req['kelas_id']),
            ];
        }
    }
}

protected function repairJadwalKurang(&$chrom)
{
    foreach ($this->requirements as $req) {
        $kelasId = $req['kelas_id'];
        $mapelId = $req['mapel_id'];
        $targetJam = $req['jumlah_jam'] ?? 2;

        // Hitung jam yang sudah terisi untuk mapel ini
        $terisi = collect($chrom)
            ->where('kelas_id', $kelasId)
            ->where('mapel_id', $mapelId)
            ->count();

        $sisa = $targetJam - $terisi;
        if ($sisa <= 0) continue;

        // Ambil waktu yang belum digunakan untuk kelas ini
        $usedWaktu = collect($chrom)
            ->where('kelas_id', $kelasId)
            ->pluck('waktu_id')
            ->toArray();

        $availableWaktu = $this->waktuList
            ->pluck('id')
            ->filter(fn($id) => !in_array($id, $usedWaktu))
            ->shuffle()
            ->values();

        for ($i = 0; $i < $sisa && $i < $availableWaktu->count(); $i++) {
            $chrom[] = [
                'kelas_id'   => $kelasId,
                'mapel_id'   => $mapelId,
                'guru_id'    => $req['guru_options'][array_rand($req['guru_options'])],
                'waktu_id'   => $availableWaktu[$i],
                'ruangan_id' => $this->assignRuangan($req, $kelasId),
            ];
        }
    }
}

protected function resolveGuruConflicts(array $chrom): array
{
    $grouped = collect($chrom)->groupBy('waktu_id');
    $newChrom = [];

    foreach ($grouped as $waktuId => $entries) {
        $guruUsed = [];

        foreach ($entries as $g) {
            $kelasId = $g['kelas_id'];
            $mapelId = $g['mapel_id'];
            $guruId = $g['guru_id'];

            if (!in_array($guruId, $guruUsed)) {
                $guruUsed[] = $guruId;
                $newChrom[] = $g;
                continue;
            }

            // Ada konflik guru di waktu ini
            $req = collect($this->requirements)->first(fn($r) =>
                $r['kelas_id'] == $kelasId && $r['mapel_id'] == $mapelId
            );

            // 1️⃣ Coba cari guru alternatif
            $availableGuru = array_diff($req['guru_options'], [$guruId]);
            $found = false;

            foreach ($availableGuru as $altGuruId) {
                if (!in_array($altGuruId, $guruUsed)) {
                    $g['guru_id'] = $altGuruId;
                    $guruUsed[] = $altGuruId;
                    $newChrom[] = $g;
                    $found = true;
                    break;
                }
            }

            if ($found) continue;

            // 2️⃣ Kalau tidak ada guru alternatif, coba cari waktu lain
            $altWaktu = $this->getRandomAvailableWaktu($newChrom, $kelasId, $guruId);
            if ($altWaktu) {
                $g['waktu_id'] = $altWaktu;
                $newChrom[] = $g;
                continue;
            }

            // 3️⃣ Jika semua gagal → tetap masukkan walau konflik
            $newChrom[] = $g;
        }
    }

    return $newChrom;
}


}
