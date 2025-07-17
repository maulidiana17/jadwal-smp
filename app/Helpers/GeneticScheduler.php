<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneticScheduler
{
    protected $requirements, $rooms, $waktuList;
    protected $populationSize, $crossoverRate, $mutationRate, $maxGenerations;
    protected $elitism = 2, $stagnationLimit = 30, $noChange = 0;
    protected $mapelJamPerMinggu;
    protected $maxGuruJam;
    protected $kelasToRuangan = [];
    protected $waktuPerSlot = [];
    protected $waktuByHari = [];
    protected $mapelButuhJamBerurutan = [ /* mapel_id yang butuh jam berurutan */ ];


    public function __construct($requirements, $rooms, $waktuList, $popSize=100, $crossRate=0.8, $mutRate=0.2, $gens=200,$mapelJamPerMinggu = [], $maxGuruJam = 42)
    {
        [$this->requirements, $this->waktuList] = [$requirements, $waktuList];
        [$this->populationSize, $this->crossoverRate, $this->mutationRate, $this->maxGenerations] =
        [$popSize, $crossRate, $mutRate, $gens];
        $this->rooms = collect($rooms); // Ubah rooms jadi Collection
        //$this ->mapelJamPerMinggu = $mapelJamPerMinggu;
        if (empty($mapelJamPerMinggu)) {
            $this->mapelJamPerMinggu = DB::table('mapel')->pluck('jam_per_minggu', 'id')->toArray();
        } else {
            $this->mapelJamPerMinggu = $mapelJamPerMinggu;
        }

        $this->maxGuruJam = $maxGuruJam;
        $this->groupWaktuPerSlot();
        // $this->waktuList = $this->waktuList
        //     ->sortBy(fn($w) => [$w->hari, $w->jam_ke])
        //     ->values();
        // $this->waktuList = $this->waktuList
        //     ->filter(fn($w) => !str_contains(strtolower($w->ket), 'istirahat'))
        //     ->sortBy(fn($w) => [$w->hari, $w->jam_ke])
        //     ->values();
            $this->waktuList = $this->waktuList
                ->filter(fn($w) =>
                    !str_contains(strtolower($w->ket), 'istirahat') &&
                    $w->jam_ke >= 1 && $w->jam_ke <= 9
                )
                ->sortBy(fn($w) => [$w->hari, $w->jam_ke])
                ->values();



        $this->waktuByHari = $this->waktuList
            ->groupBy('hari')
            ->map(function ($grouped) {
                return $grouped->sortBy('jam_ke')->pluck('id')->values();
            })->toArray();

    }

    public function run()
    {
        // ✅ Logging awal proses
        Log::info("Generasi dimulai...");

        $pop = $this->initialPopulation();

        usort($pop, fn($a, $b) => $this->fitness($b) <=> $this->fitness($a));
        $bestFit = $this->fitness($pop[0]);

        for ($gen = 1; $gen <= $this->maxGenerations; $gen++) {
            $newPop = array_slice($pop, 0, $this->elitism);
            while (count($newPop) < $this->populationSize) {
                $child = $this->crossoverMulti($pop);
                $child = $this->mutate($child);
                $newPop[] = $child;
            }
            $pop = $newPop;
            usort($pop, fn($a, $b) => $this->fitness($b) <=> $this->fitness($a));
            $curr = $this->fitness($pop[0]);

            if ($curr <= $bestFit) {
                $this->noChange++;
                if ($this->noChange >= $this->stagnationLimit) break;
            } else {
                $bestFit = $curr;
                $this->noChange = 0;
            }
        }

    // ✅ Logging akhir proses
    Log::info("Generasi selesai. Best fitness: $bestFit");

        return [
            'jadwal' => $pop[0],
            'fitness' => $bestFit
        ];
    }

    //bismillah udah bisa
    protected function initialPopulation()
    {
        $pop = [];

        // mapping kelas ke ruangan (khusus tipe "kelas")
        $this->kelasToRuangan = [];

        foreach ($this->rooms as $room) {
            if (strtolower($room->tipe) === 'kelas') {
                $kelas = \App\Models\Kelas::where('nama', $room->nama)->first();
                if ($kelas) {
                    $this->kelasToRuangan[$kelas->id] = $room->id;
                }
            }
        }

        for ($i = 0; $i < $this->populationSize; $i++) {
            $chrom = [];
            $mapelCounter = []; // [kelas_id][mapel_id] => jumlah terpakai

            \Log::info(">> Mulai initialPopulation...");
            $kelasList = collect($this->requirements)->pluck('kelas_id')->unique();
            \Log::info(">> Jumlah kelas yang akan diisi: " . count($kelasList));
            \Log::info(">> Total requirement: " . count($this->requirements));

            if (empty($this->mapelJamPerMinggu)) {
                \Log::error(">> mapelJamPerMinggu kosong!");
            }
            foreach ($kelasList as $kelasId) {
                \Log::info(">> Menjadwalkan kelas_id: $kelasId");
                $reqKelas = collect($this->requirements)->where('kelas_id', $kelasId)->values();

                foreach ($this->waktuByHari as $hari => $waktuIdList) {
                    \Log::info(">> Hari $hari - jumlah waktu tersedia: " . count($waktuIdList));
                    $usedWaktu = [];

                    for ($j = 0; $j < 9; $j++) {
                        // Pilih mapel yang belum melampaui jam_per_minggu
                        // ✅ Filter hanya mapel yang masih memiliki jam sisa
                    $filteredReq = $reqKelas->filter(function ($req) use (
                        $kelasId, $mapelCounter
                    ) {
                        $mapelId = $req['mapel_id'];
                        $max = $this->mapelJamPerMinggu[$mapelId] ?? 0;
                        $used = $mapelCounter[$kelasId][$mapelId] ?? 0;

                        // Mapel 1 atau 2 jam hanya boleh muncul sekali
                        if ($max <= 2 && $used >= $max) {
                            return false;
                        }
                        

                        // Mapel ≥ 3 jam masih boleh lanjut jika belum penuh
                        return $used < $max;
                    })->values();

                        if ($filteredReq->isEmpty()) break;

                        $req = $filteredReq->random();
                        $mapelId = $req['mapel_id'];
                        $jamTersisa = ($this->mapelJamPerMinggu[$mapelId] ?? 0) - ($mapelCounter[$kelasId][$mapelId] ?? 0);
                        $butuh2Jam = in_array($mapelId, $this->mapelButuhJamBerurutan) || $jamTersisa >= 2;

                        // ✅ Cek guru tersedia
                        if (empty($req['guru_options'])) continue;
                        $guru = $req['guru_options'][array_rand($req['guru_options'])];

                        if (($mapelCounter[$kelasId][$mapelId] ?? 0) >= ($this->mapelJamPerMinggu[$mapelId] ?? 0)) {
                            continue;
                        }

                        // Tentukan ruangan
                        $filtered = !empty($req['requires_ruang'])
                            ? $this->rooms->filter(fn($room) => strtolower($room->tipe) === strtolower($req['requires_ruang']))
                            : $this->rooms->filter(fn($room) => strtolower($room->tipe) === 'kelas');

                        $ruangan = $filtered->isNotEmpty()
                            ? $filtered->random()->id
                            : ($this->kelasToRuangan[$kelasId] ?? $this->rooms->random()->id);

                        // ✅ Penjadwalan 2 jam berurutan
                        if ($butuh2Jam) {
                            for ($k = 0; $k < count($waktuIdList) - 1; $k++) {
                                $waktu1 = $waktuIdList[$k];
                                $waktu2 = $waktuIdList[$k + 1];

                                $w1 = $this->waktuList->firstWhere('id', $waktu1);
                                $w2 = $this->waktuList->firstWhere('id', $waktu2);

                                if (
                                    in_array($waktu1, $usedWaktu) ||
                                    in_array($waktu2, $usedWaktu) ||
                                    !$w1 || !$w2 ||
                                    $w1->hari !== $w2->hari || abs($w1->jam_ke - $w2->jam_ke) !== 1
                                ) continue;

                                // Jika valid, simpan
                                $usedWaktu[] = $waktu1;
                                $usedWaktu[] = $waktu2;

                                for ($slot = 0; $slot < 2; $slot++) {
                                    $chrom[] = [
                                        'kelas_id'    => $kelasId,
                                        'mapel_id'    => $mapelId,
                                        'guru_id'     => $guru,
                                        'waktu_id'    => $slot === 0 ? $waktu1 : $waktu2,
                                        'ruangan_id'  => $ruangan
                                    ];
                                    $mapelCounter[$kelasId][$mapelId] = ($mapelCounter[$kelasId][$mapelId] ?? 0) + 1;
                                }
                                continue 2; // Lanjut ke iterasi berikutnya
                            }
                            // ❗ Tidak ditemukan slot berurutan, log saja
                            \Log::info("Tidak ditemukan slot berurutan untuk mapel_id {$mapelId} di kelas_id {$kelasId} hari {$hari}");
                            continue; // jika tidak ketemu slot 2 jam, skip mapel ini
                        }

                        // ✳️ Jika tidak butuh 2 jam
                        $availableWaktu = array_values(array_diff($waktuIdList, $usedWaktu));
                        if (empty($availableWaktu)) break;

                        $waktuId = $availableWaktu[0];
                        $usedWaktu[] = $waktuId;

                        $chrom[] = [
                            'kelas_id'    => $kelasId,
                            'mapel_id'    => $mapelId,
                            'guru_id'     => $guru,
                            'waktu_id'    => $waktuId,
                            'ruangan_id'  => $ruangan
                        ];
                        $mapelCounter[$kelasId][$mapelId] = ($mapelCounter[$kelasId][$mapelId] ?? 0) + 1;
                    }

                }
            }

            $pop[] = $chrom;
            
            // Validasi mapel ≤2 jam tidak muncul di hari berbeda
                foreach ($chrom as $item) {
                $mapelId = $item['mapel_id'];
                $kelasId = $item['kelas_id'];
                $jamPerminggu = $this->mapelJamPerMinggu[$mapelId] ?? 0;

                $grouped = collect($chrom)
                    ->where('kelas_id', $kelasId)
                    ->where('mapel_id', $mapelId)
                    ->groupBy(function ($x) {
                        $waktu = $this->waktuList->firstWhere('id', $x['waktu_id']);
                        return $waktu->hari ?? '??';
                    });

                // 1. Mapel ≤ 2 jam per minggu tidak boleh menyebar di lebih dari satu hari
                if ($jamPerminggu <= 2 && count($grouped) > 1) {
                    $hariList = implode(', ', array_keys($grouped->toArray()));
                    Log::warning("⚠️ Mapel ID {$mapelId} (≤2 jam) di kelas {$kelasId} muncul di beberapa hari: $hariList");
                }

                // 2. Mapel 3–4 jam → maksimal 2 jam per hari
                if ($jamPerminggu >= 3 && $jamPerminggu <= 4) {
                    foreach ($grouped as $hari => $list) {
                        if (count($list) > 2) {
                            Log::warning("⚠️ Mapel ID {$mapelId} ({$jamPerminggu} jam) di kelas {$kelasId} terlalu banyak di hari $hari (" . count($list) . " jam)");
                        }
                    }
                }
            }

        }
    \Log::info(">> Jumlah kromosom populasi pertama: " . count($pop[0] ?? []));

        return $pop;
    }



    protected function groupWaktuPerSlot()
    {
        $this->waktuPerSlot = [];

        foreach ($this->waktuList as $waktu) {
            $slotKey = $waktu->hari . '-' . $waktu->jam_ke;
            $this->waktuPerSlot[$slotKey][] = $waktu->id;
        }
    }

    protected function getAvailableTimeSlot($chrom, $guru_id, $kelas_id)
    {
        $used = collect($chrom)->groupBy('waktu_id')->map(function ($slots) {
            return [
                'guru' => $slots->pluck('guru_id')->all(),
                'kelas' => $slots->pluck('kelas_id')->all(),
            ];
            });

        foreach ($this->waktuList->shuffle() as $w) {
            $waktuId = $w->id;
            if (!isset($used[$waktuId])) return $w;

            $wdata = $used[$waktuId];
            if (!in_array($guru_id, $wdata['guru']) && !in_array($kelas_id, $wdata['kelas'])) {
                return $w;
            }
        }

        return $this->waktuList->random(); // fallback
    }

    protected function fitness($chrom)
    {
        $score = 0;
        $conflict = [];
        $load = [];
        
        // ✅ Cache waktu_id => hari & jam_ke
        $waktuCache = [];
        foreach ($this->waktuList as $w) {
            $waktuCache[$w->id] = ['hari' => $w->hari, 'jam_ke' => $w->jam_ke];
        }

        foreach ($chrom as $g) {
            $w = $g['waktu_id'];

            foreach (['kelas_id', 'guru_id', 'ruangan_id'] as $key) {
                $val = $g[$key] ?? null;
                if ($val && isset($conflict[$w][$key][$val])) {
                    $score -= 10000; // ❗ Penalti besar
                } else {
                    $conflict[$w][$key][$val] = true;
                    $score += 10;
                }
            }

            $load[$g['guru_id']][] = $w;
        }

        // Penalti jika beban guru melebihi batas
        // foreach ($load as $slots) {
        //     $overload = max(0, count($slots) - 6);
        //     $score -= $overload * 50;
        // }
            foreach ($load as $guruId => $slots) {
            $totalJam = count(array_unique($slots));
            $maxJam = 24; // Misalnya maksimal 24 jam per minggu

            if ($totalJam > $maxJam) {
                $score -= ($totalJam - $maxJam) * 100; // Penalti besar
            }
        }


        // Penalti jika alokasi jam mapel tidak sesuai
        $kelasMapelJam = [];
        foreach ($chrom as $g){
            $key = $g['kelas_id'] . '-' . $g['mapel_id'];
            if (!isset($kelasMapelJam[$key])) $kelasMapelJam[$key] = 0;
            $kelasMapelJam[$key]++;
        }

        foreach ($kelasMapelJam as $key => $jumlahSlot) {
            [$kelasId, $mapelId] = explode('-', $key);
            $mapelId = (int) $mapelId;

            $maksSlot = $this->mapelJamPerMinggu[$mapelId] ?? 0;

            if ($jumlahSlot > $maksSlot) {
                $score -= ($jumlahSlot - $maksSlot) * 300;
            } elseif ($jumlahSlot < $maksSlot) {
                $score -= ($maksSlot - $jumlahSlot) * 200;
            }
        }

        // Bonus jika mapel berurutan benar
        foreach ($chrom as $g) {
            if (in_array($g['mapel_id'], $this->mapelButuhJamBerurutan)) {
                $sameDay = collect($chrom)->filter(fn($x) =>
                    $x['kelas_id'] === $g['kelas_id'] &&
                    $x['mapel_id'] === $g['mapel_id'] &&
                    $waktuCache[$x['waktu_id']]['hari'] ?? null === $waktuCache[$g['waktu_id']]['hari'] ?? null
                )->pluck('waktu_id')->sort()->values();

                for ($i = 0; $i < count($sameDay) - 1; $i++) {
                    $a = $waktuCache[$sameDay[$i]]['jam_ke'] ?? null;
                    $b = $waktuCache[$sameDay[$i+1]]['jam_ke'] ?? null;
                    if ($b - $a === 1) {
                        $score += 100; // Bonus skor jika benar
                        break;
                    }
                }
            }
        }


        return $score;
    }

    // protected function getHari($waktu_id)
    // {
    //     $w = $this->waktuList->firstWhere('id', $waktu_id);
    //     return $w ? $w->hari : null;
    // }

    // protected function getJamKe($waktu_id)
    // {
    //     $w = $this->waktuList->firstWhere('id', $waktu_id);
    //     return $w ? $w->jam_ke : null;
    // }

    protected function isMapelBerurutan($chrom, $mapel_id, $kelas_id)
    {
        $slots = collect($chrom)
            ->where('kelas_id', $kelas_id)
            ->where('mapel_id', $mapel_id)
            ->pluck('waktu_id')
            ->map(function ($waktu_id) {
                $w = $this->waktuList->firstWhere('id', $waktu_id);
                return $w ? [$w->hari, $w->jam_ke] : null;
            })
            ->filter()
            ->values()
            ->sortBy(fn($x) => $x[0] . $x[1]);

        // Cek apakah ada dua jam ke berurutan di hari yang sama
        for ($i = 0; $i < $slots->count() - 1; $i++) {
            [$hari1, $jam1] = $slots[$i];
            [$hari2, $jam2] = $slots[$i + 1];
            if ($hari1 === $hari2 && $jam2 === $jam1 + 1) {
                return true; // ✅ valid
            }
        }

        return false;
    }



    protected function selectTournament($pop)
    {
        $best = null;
        for ($i = 0; $i < 3; $i++) {
            $cand = $pop[array_rand($pop)];
            if (!$best || $this->fitness($cand) > $this->fitness($best)) {
                $best = $cand;
            }
        }
        return $best;
    }

    protected function crossoverMulti($pop)
    {
        $parents = [];
        for ($i = 0; $i < 5; $i++) {
            $parents[] = $this->selectTournament($pop);
        }
        $len = count($parents[0]);
        $child = [];

        for ($i = 0; $i < $len; $i++) {
            $p = $parents[random_int(0, 4)];
            $child[$i] = $p[$i];
        }

        return $child;
    }

    //benar
    protected function mutate($chrom)
    {
        foreach ($chrom as &$g) {
            $rate = $this->mutationRate * ($this->noChange ? 1.5 : 1);
            if (mt_rand() / mt_getrandmax() < $rate) {
                $req = collect($this->requirements)->first(fn($r) =>
                    $r['kelas_id'] == $g['kelas_id'] && $r['mapel_id'] == $g['mapel_id']
                );

                // Mutasi guru
                if (random_int(0, 1)) {
                    $g['guru_id'] = $req['guru_options'][array_rand($req['guru_options'])];
                }


                // Mutasi waktu
                $slotKe = array_rand($this->waktuPerSlot);
                $waktuIdList = $this->waktuPerSlot[$slotKe];
                shuffle($waktuIdList);
                foreach ($waktuIdList as $waktuId) {
                    $conflict = false;
                    foreach ($chrom as $other) {
                        if ($other === $g) continue;
                        if ($other['waktu_id'] == $waktuId &&
                            ($other['guru_id'] == $g['guru_id'] ||
                            $other['kelas_id'] == $g['kelas_id'] ||
                            $other['ruangan_id'] == $g['ruangan_id'])) {
                            $conflict = true;
                            break;
                        }
                    }
                    if (!$conflict) {
                        $g['waktu_id'] = $waktuId;
                        break;
                    }
                }


                if (!empty($req['requires_ruang'])) {
                    $filteredRooms = $this->rooms->filter(fn($room) =>
                        strtolower($room->tipe) === $req['requires_ruang']
                    );
                    if ($filteredRooms->isNotEmpty()) {
                        $g['ruangan_id'] = $filteredRooms->random()->id;
                    }
                } else {
                    // Tetapkan ruangan ke ruangan sesuai kelas (kelas_id => ruangan_id)
                    if (isset($this->kelasToRuangan[$g['kelas_id']])) {
                        $g['ruangan_id'] = $this->kelasToRuangan[$g['kelas_id']];
                    }
                }
            }
        }

        return $this->localRepair($chrom);
    }

     protected function localRepair($chrom)
    {
        $n = count($chrom);
        if ($n < 2) return $chrom; // tidak cukup data untuk swap

        for ($i = 0; $i < 30; $i++) {
            $a = rand(0, $n - 1);
            $b = rand(0, $n - 1);
            if ($a === $b) continue;

            $old = [$chrom[$a], $chrom[$b]];
            [$chrom[$a]['waktu_id'], $chrom[$b]['waktu_id']] = [$chrom[$b]['waktu_id'], $chrom[$a]['waktu_id']];
            $newFitness = $this->fitness([$chrom[$a], $chrom[$b]]);

            // Jika memburuk, kembalikan
            if ($newFitness < $this->fitness($old)) {
                [$chrom[$a]['waktu_id'], $chrom[$b]['waktu_id']] = [$chrom[$b]['waktu_id'], $chrom[$a]['waktu_id']];
            }
        }
        return $chrom;
    }

}
