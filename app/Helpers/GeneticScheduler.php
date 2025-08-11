<?php

namespace App\Helpers;

use App\Models\Kelas;
use App\Models\Waktu;
use Illuminate\Support\Facades\Log;

class GeneticScheduler
{
    protected $requirements, $rooms;
    protected $waktuList;
    protected $populationSize, $crossoverRate, $mutationRate, $maxGenerations;
    protected $elitism = 2, $stagnationLimit = 30, $noChange = 0;
    protected $kelasToRuangan = [];
    protected $hariCache = [];
    protected $hariRules = [];

    public function __construct(
        $requirements, $rooms, $waktus, $popSize = 100, $crossRate = 0.8, $mutRate = 0.2, $gens = 200)
    {
        $this->requirements = $requirements;
        $this->rooms = collect($rooms);
        $this->populationSize = (int)$popSize;
        $this->crossoverRate = $crossRate;
        $this->mutationRate = $mutRate;
        $this->maxGenerations = $gens;

        $this->waktuList = Waktu::where('ket', 'pelajaran')
            ->orderBy('hari')->orderBy('jam_ke')->get()
            ->map(fn($w) => [
                'id' => $w->id,
                'hari' => $w->hari,
                'jam_ke' => $w->jam_ke,
            ]);

        // buat cache hari per waktu_id untuk scoring/distribusi
        $this->hariCache = $this->waktuList->pluck('hari', 'id')->toArray();

        $this->mapKelasToRuangan();

    }

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

    public function run()
    {
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
                $child = $this->repairAllConflicts($child);
                $this->fillSlotsOptimized($child);
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

        // ✅ Perbaiki kekosongan sebelum hasil akhir dikembalikan
        $chrom = $pop[0];
        $this->repairAllConflicts($chrom);
        $this->fillSlotsOptimized($chrom);

        return [
            'jadwal' => $chrom,
            'fitness' => $this->fitness($chrom)
        ];
    }

    protected function initialPopulation()
    {
        $pop = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $chrom = [];
            $guruTerpakai = [];

            $kelasList = collect($this->requirements)->pluck('kelas_id')->unique();

            foreach ($kelasList as $kelasId) {
                $kelasReqs = collect($this->requirements)->where('kelas_id', $kelasId);
                $availableWaktu = $this->waktuList->pluck('id')->shuffle();

                foreach ($kelasReqs as $req) {
                    $mapelId = $req['mapel_id'];
                    $jumlahJam = $req['jumlah_jam'] ?? 2;
                    $guruOptions = $req['guru_options'] ?? [];

             for ($j = 0; $j < $jumlahJam; $j++) {
            if ($availableWaktu->isEmpty()) {
                // Cari slot kosong di seluruh jadwal
                $waktuId = $this->findEmptyWaktu($chrom, $kelasId);
                if (!$waktuId) continue; // Kalau tetap gak ketemu, skip (rare case)
            } else {
                $waktuId = $availableWaktu->pop();
            }

            $availableGuru = array_filter($guruOptions, fn($gid) =>
                empty($guruTerpakai[$waktuId][$gid])
            );

            if (empty($availableGuru)) continue;

            $guruId = $availableGuru[array_rand($availableGuru)];

            $chrom[] = [
                'kelas_id' => $kelasId,
                'mapel_id' => $mapelId,
                'guru_id' => $guruId,
                'waktu_id' => $waktuId,
                'ruangan_id' => $this->assignRuangan($req, $kelasId),
            ];

            $guruTerpakai[$waktuId][$guruId] = true;
        }

                }
            }

            $this->fillSlotsOptimized($chrom);
            // $this->repairDailySlots($chrom);

            $pop[] = $chrom;
            Log::info("🧬 Kromosom ke-$i jumlah gen: " . count($chrom));
        }

        Log::info("✅ Total kromosom: " . count($pop));

        return $pop;
    }

    public function fillSlotsOptimized(&$chrom)
    {
        foreach ($this->requirements as $req) {
                // Hitung sudah terjadwal berapa jam untuk kelas & mapel ini
                $count = collect($chrom)
                    ->where('kelas_id', $req['kelas_id'])
                    ->where('mapel_id', $req['mapel_id'])
                    ->count();

                $needed = ($req['jumlah_jam'] ?? 2) - $count;
                if ($needed <= 0) continue;

                // Ambil semua waktu dan acak urutannya
                $availableWaktu = $this->waktuList->pluck('id')->shuffle()->toArray();

                // Ambil waktu yang sudah terpakai oleh kelas tsb, supaya tidak double booking
                $usedWaktuByKelas = collect($chrom)
                    ->where('kelas_id', $req['kelas_id'])
                    ->pluck('waktu_id')
                    ->toArray();

                // Filter waktu yang belum dipakai kelas tsb
                $remainingWaktu = array_diff($availableWaktu, $usedWaktuByKelas);

            foreach ($remainingWaktu as $waktuId) {
                    if ($needed <= 0) break;

                    // Pilih guru random yang tidak bentrok di waktu ini
                    $availableGuru = array_filter($req['guru_options'] ?? [], function($gid) use ($chrom, $waktuId) {
                        return !collect($chrom)->contains(function($gene) use ($gid, $waktuId) {
                            return $gene['guru_id'] == $gid && $gene['waktu_id'] == $waktuId;
                        });
                    });

                    if (empty($availableGuru)) continue;

                    // Pilih guru acak dari yang available
                    $guruId = $availableGuru[array_rand($availableGuru)];

                    $ruanganId = $this->assignRuangan($req, $req['kelas_id']);

                    // Cek conflict keseluruhan (kelas, guru, ruangan)
                    if ($this->isConflictFree($chrom, $waktuId, $req['kelas_id'], $guruId, $ruanganId)) {
                        $chrom[] = [
                            'kelas_id' => $req['kelas_id'],
                            'mapel_id' => $req['mapel_id'],
                            'guru_id' => $guruId,
                            'waktu_id' => $waktuId,
                            'ruangan_id' => $ruanganId,
                        ];
                        $needed--;
                    }
            }
        }
    }
 
    protected function findEmptyWaktu($chrom, $kelasId)
    {
        $occupied = array_column(
            array_filter($chrom, fn($g) => $g['kelas_id'] === $kelasId),
            'waktu_id'
        );

        $possible = $this->waktuList->pluck('id')
            ->diff($occupied)
            ->values();

        return $possible->isNotEmpty() ? $possible->random() : null;
    }

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

    protected function isConflictFree($chrom, $waktuId, $kelasId, $guruId, $ruanganId)
    {
        foreach ($chrom as $g) {
            if ($g['waktu_id'] == $waktuId) {
                if ($g['guru_id'] == $guruId || $g['kelas_id'] == $kelasId || $g['ruangan_id'] == $ruanganId) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function repairAllConflicts(array $chrom): array
    {
        $grouped = collect($chrom)->groupBy('waktu_id');
        $newChrom = [];

        foreach ($grouped as $waktuId => $entries) {
            $guruUsed = [];
            $kelasUsed = [];
            $ruangUsed = [];

            foreach ($entries as $g) {
                $kelasId = $g['kelas_id'];
                $mapelId = $g['mapel_id'];
                $guruId  = $g['guru_id'];
                $ruangId = $g['ruangan_id'];

                // Cek konflik guru
                if (in_array($guruId, $guruUsed)) {
                    $req = collect($this->requirements)->first(fn($r) =>
                        $r['kelas_id'] == $kelasId && $r['mapel_id'] == $mapelId
                    );

                    $availableGuru = array_diff($req['guru_options'], $guruUsed);
                    if (!empty($availableGuru)) {
                        $guruId = array_rand(array_flip($availableGuru));
                    } else {
                        $altWaktu = $this->getRandomAvailableWaktu($newChrom, $kelasId, $guruId);
                        if ($altWaktu) {
                            $g['waktu_id'] = $altWaktu;
                            $newChrom[] = $g;
                            continue;
                        }
                    }
                }

                // Cek konflik kelas
                if (in_array($kelasId, $kelasUsed)) {
                    $altWaktu = $this->getRandomAvailableWaktu($newChrom, $kelasId, $guruId);
                    if ($altWaktu) {
                        $g['waktu_id'] = $altWaktu;
                        $newChrom[] = $g;
                        continue;
                    }
                }

                // Cek konflik ruangan
                if (in_array($ruangId, $ruangUsed)) {
                    $req = collect($this->requirements)->first(fn($r) =>
                        $r['kelas_id'] == $kelasId && $r['mapel_id'] == $mapelId
                    );

                    if (!empty($req['requires_ruang'])) {
                        $filtered = $this->rooms->filter(fn($r) => strtolower($r->tipe) === strtolower($req['requires_ruang']));
                        $availableRuang = array_diff($filtered->pluck('id')->toArray(), $ruangUsed);
                    } else {
                        $availableRuang = array_diff($this->rooms->pluck('id')->toArray(), $ruangUsed);
                    }

                    if (!empty($availableRuang)) {
                        $ruangId = $availableRuang[array_rand($availableRuang)];
                    } else {
                        $altWaktu = $this->getRandomAvailableWaktu($newChrom, $kelasId, $guruId);
                        if ($altWaktu) {
                            $g['waktu_id'] = $altWaktu;
                            $newChrom[] = $g;
                            continue;
                        }
                    }
                }

                $guruUsed[]  = $guruId;
                $kelasUsed[] = $kelasId;
                $ruangUsed[] = $ruangId;

                $g['guru_id']    = $guruId;
                $g['ruangan_id'] = $ruangId;
                $newChrom[] = $g;
            }
        }

        return $newChrom;
    }

    protected function crossoverMapelWise(array $pop): array
    {
        // Ambil beberapa parent, pilih gen mapel acak dari mereka
        $parents = $this->selectMultipleParents($pop, 5);
        $child = [];
        $mapelGroups = collect($parents)->flatten(1)->groupBy('mapel_id');

        foreach ($mapelGroups as $mapelId => $genes) {
            $selected = $genes->random();
            $child[] = $selected;
        }
        return $child;
    }

    protected function selectMultipleParents(array $pop, int $count): array
    {
        $parents = [];
        for ($i = 0; $i < $count; $i++) {
            $parents[] = $this->selectTournament($pop);
        }
        return $parents;
    }

    protected function selectTournament($pop)
    {
        $best = null;
        for ($i = 0; $i < 5; $i++) {
            $candidate = $pop[array_rand($pop)];
            if (!$best || $this->fitness($candidate) > $this->fitness($best)) {
                $best = $candidate;
            }
        }
        return $best;
    }

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
            if ($newWaktu) {
                if ($this->isConflictFree($chrom, $newWaktu, $g['kelas_id'], $g['guru_id'], $g['ruangan_id'])) {
                    $g['waktu_id'] = $newWaktu;
                }
            }
            $g['ruangan_id'] = $this->assignRuangan($req, $g['kelas_id']);
        }
        return $chrom;
    }

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

    protected function fitness($chrom)
    {
        $KG = $KR = $KW = 0;

        $byWaktu = [];
        foreach ($chrom as $g) {
            $byWaktu[$g['waktu_id']][] = $g;
        }

        foreach ($byWaktu as $items) {
            $guruCounter  = [];
            $ruangCounter = [];
            $kelasCounter = [];

            foreach ($items as $g) {
                $guruCounter[$g['guru_id']]   = ($guruCounter[$g['guru_id']] ?? 0) + 1;
                $ruangCounter[$g['ruangan_id']] = ($ruangCounter[$g['ruangan_id']] ?? 0) + 1;
                $kelasCounter[$g['kelas_id']] = ($kelasCounter[$g['kelas_id']] ?? 0) + 1;
            }

            foreach ($guruCounter as $c)  if ($c > 1) $KG++;
            foreach ($ruangCounter as $c) if ($c > 1) $KR++;
            foreach ($kelasCounter as $c) if ($c > 1) $KW++;
        }

        $totalPenalty = $KG + $KR + $KW;
        $hardScore = 1 / (1 + $totalPenalty);

// ✅ Tambah penalti jika jam_per_minggu tidak terpenuhi
        $missingPenalty = 0;
        foreach ($this->requirements as $req) {
            $countPlaced = 0;
            foreach ($chrom as $gene) {
                if ($gene['kelas_id'] == $req['kelas_id'] && $gene['mapel_id'] == $req['mapel_id']) {
                    $countPlaced++;
                }
            }
            $diff = abs($countPlaced - $req['jumlah_jam']);
            if ($diff > 0) {
                $missingPenalty += $diff * 1; // penalti 1 poin per jam yang kurang/lebih
            }
        }

        $missingScore = 1 / (1 + $missingPenalty);

        Log::info("Fitness computed: totalPenalty=$totalPenalty, hardScore=$hardScore");

        // ✅ Bobot disesuaikan: hard rule 90%, missing jam 10%
        return (0.9 * $hardScore) + (0.1 * $missingScore);
    }


}