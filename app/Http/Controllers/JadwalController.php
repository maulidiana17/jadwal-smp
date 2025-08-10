<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Waktu;
use App\Models\Jadwal;
use App\Models\Ruangan;
use App\Models\Pengampu;
use Illuminate\Http\Request;
use App\Jobs\GenerateJadwalJob;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\JadwalGuruExport;
use App\Helpers\GeneticScheduler;
use App\Exports\JadwalKelasExport;
use App\Services\SchedulerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\JadwalPerGuruExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JadwalPerKelasExport;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::all();
        $guruList = Guru::all();
        $ruanganList = Ruangan::all();
        $jadwals = Jadwal::with(['waktu', 'mapel', 'guru', 'ruangan'])
            ->when($request->kelas_id, fn($q) => $q->where('kelas_id', $request->kelas_id))
            ->when($request->hari, fn($q) => $q->whereHas('waktu', fn($w) => $w->where('hari', $request->hari)))
            ->when($request->guru_id, fn($q) => $q->where('guru_id', $request->guru_id))
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->orderByRaw("FIELD(waktu.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('waktu.jam_mulai')
            ->select('jadwal.*')
            ->get();



        $selectedKelasId = $request->kelas_id ?? null;
        $selectedGuruId = $request->guru_id ?? null;

        $kelas_aktif = $selectedKelasId ? Kelas::find($selectedKelasId) : null;
        $guru_aktif = $selectedGuruId ? Guru::find($selectedGuruId) : null;

        return view('jadwal.index', compact('jadwals', 'kelasList', 'guruList', 'ruanganList', 'selectedKelasId', 'kelas_aktif', 'selectedGuruId', 'guru_aktif'));
    }


    function isWaktuBentrok($kelas_id, $guru_id, $ruangan_id, $waktu_id)
    {
        // Cek bentrok jadwal berdasarkan waktu_id
        $conflict = Jadwal::where('waktu_id', $waktu_id)
            ->where(function ($query) use ($kelas_id, $guru_id, $ruangan_id) {
                $query->where('kelas_id', $kelas_id)
                    ->orWhere('guru_id', $guru_id)
                    ->orWhere('ruangan_id', $ruangan_id);
            })->exists();

        return $conflict; // true = bentrok
    }
    
    public function generateProcess(Request $request, SchedulerService $svc)
    {
        // Validasi input
        $validated = $request->validate([
            'popSize' => 'required|integer|min:2',
            'crossRate' => 'required|numeric|between:0.6,1',
            'mutRate' => 'required|numeric|between:0.1,1',
            'generations' => 'required|integer|min:1',
            'tries' => 'nullable|integer|min:1|max:10',
        ]);
    
        // Proses generate jadwal
        [$bestSchedule, $conflicts, $skipped, $fitness] = $svc->generate($validated);
        $savedCount = $svc->save($bestSchedule);
    
        // Simpan skor fitness
        file_put_contents(storage_path('logs/fitness.txt'), $fitness);

        // Simpan hasil generate untuk evaluasi
        file_put_contents(storage_path('logs/hasil_generate.json'), json_encode([
        'berhasil' => $savedCount,
        'skipped'  => $skipped
        ]));
        
        return redirect()->route('jadwal.index');
    }



    // public function evaluasi()
    // {
    //     // Ambil semua jadwal dari DB untuk deteksi konflik & cek coverage
    //     $jadwals = \App\Models\Jadwal::with(['guru', 'kelas', 'mapel', 'ruangan', 'waktu'])->get();

    //     // Hitung expected total dari semua pengampu
    //     $pengampus = \App\Models\Pengampu::with('mapel')->get();
    //     $expectedTotal = $pengampus
    //         ->filter(fn($p) => $p->mapel && $p->mapel->jam_per_minggu)
    //         ->sum(fn($p) => $p->mapel->jam_per_minggu);

    //     // Baca hasil generate terakhir (skipped teknis & total jadwal tersimpan)
    //     $hasilGenerateFile = storage_path('logs/hasil_generate.json');
    //     if (file_exists($hasilGenerateFile)) {
    //         $hasilGenerate = json_decode(file_get_contents($hasilGenerateFile), true);
    //         $total = $hasilGenerate['berhasil'];
    //         $skippedTeknis = $hasilGenerate['skipped'];
    //     } else {
    //         $total = $jadwals->count();
    //         $skippedTeknis = max(0, $expectedTotal - $total);
    //     }

    //     // Skipped akademik = jam seharusnya - jam terjadwal
    //     $skippedAkademik = max(0, $expectedTotal - $total);

    //     // Cari pengampu/mapel yang tidak punya jadwal
    //     $pengampusTidakTerjadwal = $pengampus->filter(function($p) use ($jadwals) {
    //         return !$jadwals->contains('pengampu_id', $p->id);
    //     });

    //     // ====== Deteksi konflik ======
    //     $conflicts = [];
    //     $conflictGuru = $conflictKelas = $conflictRuangan = 0;

    //     foreach ($jadwals as $i => $a) {
    //         foreach ($jadwals as $j => $b) {
    //             if ($i >= $j) continue;
    //             if ($a->waktu_id !== $b->waktu_id) continue;

    //             $waktuText = $a->waktu
    //                 ? $a->waktu->hari . ' ' . $a->waktu->jam_mulai . ' - ' . $a->waktu->jam_selesai
    //                 : 'Waktu ID: ' . $a->waktu_id;

    //             if ($a->guru_id && $a->guru_id === $b->guru_id) {
    //                 $conflicts[] = ['type' => 'guru', 'waktu' => $waktuText];
    //                 $conflictGuru++;
    //             }
    //             if ($a->kelas_id && $a->kelas_id === $b->kelas_id) {
    //                 $conflicts[] = ['type' => 'kelas', 'waktu' => $waktuText];
    //                 $conflictKelas++;
    //             }
    //             if ($a->ruangan_id && $a->ruangan_id === $b->ruangan_id) {
    //                 $conflicts[] = ['type' => 'ruangan', 'waktu' => $waktuText];
    //                 $conflictRuangan++;
    //             }
    //         }
    //     }

    //     $conflictCount = count($conflicts);
    //     $nonConflictCount = $total - $conflictCount;

    //     // Ambil fitness
    //     $fitness = null;
    //     $fitnessFile = storage_path('logs/fitness.txt');
    //     if (file_exists($fitnessFile)) {
    //         $fitness = file_get_contents($fitnessFile);
    //     }

    //     return view('jadwal.evaluasi', [
    //         'total' => $total,
    //         'expected' => $expectedTotal,
    //         'skippedTeknis' => $skippedTeknis,
    //         'skippedAkademik' => $skippedAkademik,
    //         'pengampusTidakTerjadwal' => $pengampusTidakTerjadwal,
    //         'fitness' => $fitness,
    //         'conflicts' => $conflicts,
    //         'conflictCount' => $conflictCount,
    //         'nonConflictCount' => $nonConflictCount,
    //         'conflictGuru' => $conflictGuru,
    //         'conflictKelas' => $conflictKelas,
    //         'conflictRuangan' => $conflictRuangan,
    //     ]);
    // }
    // public function evaluasi()
    // {
    //     // Ambil semua jadwal dari DB untuk deteksi konflik & cek coverage
    //     $jadwals = \App\Models\Jadwal::with(['guru', 'kelas', 'mapel', 'ruangan', 'waktu'])->get(); 
    //     // Ambil semua data pengampu + relasi
    //     $pengampus = \App\Models\Pengampu::with(['guru', 'kelas', 'mapel'])->get();


    //     // ==============================
    //     // Hitung expected total jam mengajar dari tabel mapel
    //     // ==============================
    //     // Total ideal berdasarkan semua jam_per_minggu
    //     $pengampus = \App\Models\Pengampu::with('mapel')->get();
    //     $expectedTotal = $pengampus
    //         ->filter(fn($p) => $p->mapel && $p->mapel->jam_per_minggu)
    //         ->sum(fn($p) => $p->mapel->jam_per_minggu);

    //     // Hitung skipped dengan aman (tidak negatif)
    //     $skipped = max(0, $expectedTotal - $total);

    //     // ==============================
    //     // Baca hasil generate terakhir (skipped teknis & total jadwal tersimpan)
    //     // ==============================
    //     // $hasilGenerateFile = storage_path('logs/hasil_generate.json');
    //     // if (file_exists($hasilGenerateFile)) {
    //     //     $hasilGenerate = json_decode(file_get_contents($hasilGenerateFile), true);
    //     //     $total = $hasilGenerate['berhasil'];
    //     //     $skippedTeknis = $hasilGenerate['skipped'];
    //     // } else {
    //     //     $total = $jadwals->count();
    //     //     $skippedTeknis = max(0, $expectedTotal - $total);
    //     // }

    //     // ==============================
    //     // Skipped akademik = slot tersedia - slot terjadwal
    //     // ==============================
    //     // $skippedAkademik = max(0, $expectedTotal - $total);

    //     // ==============================
    //     // Cari pengampu yang kurang jam
    //     // ==============================
    //     // $pengampusTidakTerjadwal = $pengampus->map(function($p) use ($jadwals) {
    //     //     $jamSeharusnya = $p->mapel->jam_per_minggu ?? 0;
    //     //     $jamTerjadwal = $jadwals->where('pengampu_id', $p->id)->count();
    //     //     $jamKurang = max(0, $jamSeharusnya - $jamTerjadwal);

    //     //     return (object) [
    //     //         'id' => $p->id,
    //     //         'guru' => $p->guru,
    //     //         'kelas' => $p->kelas,
    //     //         'mapel' => $p->mapel,
    //     //         'jam_seharusnya' => $jamSeharusnya,
    //     //         'jam_terjadwal' => $jamTerjadwal,
    //     //         'jam_kurang' => $jamKurang
    //     //     ];
    //     // })->filter(function($p) {
    //     //     return $p->jam_kurang > 0; // hanya tampilkan yang masih kurang jam
    //     // });

    //     // ==============================
    //     // ====== Deteksi konflik =======
    //     // ==============================
    //     $conflicts = [];
    //     $conflictGuru = $conflictKelas = $conflictRuangan = 0;

    //     foreach ($jadwals as $i => $a) {
    //         foreach ($jadwals as $j => $b) {
    //             if ($i >= $j) continue;
    //             if ($a->waktu_id !== $b->waktu_id) continue;

    //             $waktuText = $a->waktu
    //                 ? $a->waktu->hari . ' ' . $a->waktu->jam_mulai . ' - ' . $a->waktu->jam_selesai
    //                 : 'Waktu ID: ' . $a->waktu_id;

    //             if ($a->guru_id && $a->guru_id === $b->guru_id) {
    //                 $conflicts[] = ['type' => 'guru', 'waktu' => $waktuText];
    //                 $conflictGuru++;
    //             }
    //             if ($a->kelas_id && $a->kelas_id === $b->kelas_id) {
    //                 $conflicts[] = ['type' => 'kelas', 'waktu' => $waktuText];
    //                 $conflictKelas++;
    //             }
    //             if ($a->ruangan_id && $a->ruangan_id === $b->ruangan_id) {
    //                 $conflicts[] = ['type' => 'ruangan', 'waktu' => $waktuText];
    //                 $conflictRuangan++;
    //             }
    //         }
    //     }

    //     $conflictCount = count($conflicts);
    //     $nonConflictCount = $total - $conflictCount;

    //     // ==============================
    //     // Ambil fitness terakhir
    //     // ==============================
    //     $fitness = null;
    //     $fitnessFile = storage_path('logs/fitness.txt');
    //     if (file_exists($fitnessFile)) {
    //         $fitness = file_get_contents($fitnessFile);
    //     }

    //     return view('jadwal.evaluasi', [
    //         'total' => $total,
    //         'expected' => $expectedTotal,
    //         'skipped'=> $skipped,
    //         // 'skippedTeknis' => $skippedTeknis,
    //         // 'skippedAkademik' => $skippedAkademik,
    //         // 'pengampusTidakTerjadwal' => $pengampusTidakTerjadwal,
    //         'fitness' => $fitness,
    //         'conflicts' => $conflicts,
    //         'conflictCount' => $conflictCount,
    //         'nonConflictCount' => $nonConflictCount,
    //         'conflictGuru' => $conflictGuru,
    //         'conflictKelas' => $conflictKelas,
    //         'conflictRuangan' => $conflictRuangan,
    //     ]);
    // }
    // public function evaluasi()
    // {
    //     $jadwals = \App\Models\Jadwal::with(['guru', 'kelas', 'mapel', 'ruangan', 'waktu'])->get(); 
    //     $total = $jadwals->count();

    //     $pengampus = \App\Models\Pengampu::with('mapel')->get();
    //     $expectedTotal = $pengampus
    //         ->filter(fn($p) => $p->mapel && $p->mapel->jam_per_minggu)
    //         ->sum(fn($p) => $p->mapel->jam_per_minggu);

    //     $skipped = max(0, $expectedTotal - $total);

    //     // Kumpulkan konflik detail
    //     $conflicts = [];

    //     $conflictGuru = $jadwals
    //         ->groupBy(fn($j) => $j->guru_id . '-' . $j->waktu_id)
    //         ->filter(function ($group) use (&$conflicts) {
    //             if ($group->count() > 1) {
    //                 $conflicts[] = $group; // simpan detail konflik guru
    //                 return true;
    //             }
    //             return false;
    //         })->count();

    //     $conflictKelas = $jadwals
    //         ->groupBy(fn($j) => $j->kelas_id . '-' . $j->waktu_id)
    //         ->filter(function ($group) use (&$conflicts) {
    //             if ($group->count() > 1) {
    //                 $conflicts[] = $group; // simpan detail konflik kelas
    //                 return true;
    //             }
    //             return false;
    //         })->count();

    //     $conflictRuangan = $jadwals
    //         ->groupBy(fn($j) => $j->ruangan_id . '-' . $j->waktu_id)
    //         ->filter(function ($group) use (&$conflicts) {
    //             if ($group->count() > 1) {
    //                 $conflicts[] = $group; // simpan detail konflik ruangan
    //                 return true;
    //             }
    //             return false;
    //         })->count();

    //     $totalConflicts = $conflictGuru + $conflictKelas + $conflictRuangan;
    //     $nonConflictCount = max(0, $total - $totalConflicts);

    //     $fitness = $expectedTotal > 0 ? $nonConflictCount / $expectedTotal : 0;

    //     return view('jadwal.evaluasi', [
    //         'total' => $total,
    //         'expected' => $expectedTotal,
    //         'skipped'=> $skipped,
    //         'fitness' => round($fitness, 4),
    //         'totalConflicts' => $totalConflicts,
    //         'conflictGuru' => $conflictGuru,
    //         'conflictKelas' => $conflictKelas,
    //         'conflictRuangan' => $conflictRuangan,
    //         'nonConflictCount' => $nonConflictCount,
    //         'conflicts' => $conflicts, // ini aman karena pasti array
    //     ]);
    // }

public function evaluasi()
{
    // Baca fitness dari file
    $fitness = 0;
    $fitnessFile = storage_path('logs/fitness.txt');
    if (file_exists($fitnessFile)) {
        $fitness = (float) trim(file_get_contents($fitnessFile));
    }

    // Baca hasil generate (jumlah berhasil dan skipped)
    $hasilGenerateFile = storage_path('logs/hasil_generate.json');
    $hasilGenerate = ['berhasil' => 0, 'skipped' => 0];
    if (file_exists($hasilGenerateFile)) {
        $hasilGenerate = json_decode(file_get_contents($hasilGenerateFile), true) ?: $hasilGenerate;
    }

    $total = $hasilGenerate['berhasil'] ?? 0;
    $skipped = $hasilGenerate['skipped'] ?? 0;

    // Ambil semua jadwal dari DB
    $jadwals = \App\Models\Jadwal::with(['guru', 'kelas', 'mapel', 'ruangan', 'waktu'])->get();

    // Hitung total konflik guru
    $conflictGuru = $jadwals
        ->groupBy(fn($j) => $j->guru_id . '-' . $j->waktu_id)
        ->filter(fn($group) => $group->count() > 1)
        ->count();

    // Hitung total konflik kelas
    $conflictKelas = $jadwals
        ->groupBy(fn($j) => $j->kelas_id . '-' . $j->waktu_id)
        ->filter(fn($group) => $group->count() > 1)
        ->count();

    // Hitung total konflik ruangan
    $conflictRuangan = $jadwals
        ->groupBy(fn($j) => $j->ruangan_id . '-' . $j->waktu_id)
        ->filter(fn($group) => $group->count() > 1)
        ->count();

    $totalConflicts = $conflictGuru + $conflictKelas + $conflictRuangan;

    $nonConflictCount = max(0, $total - $totalConflicts);

    return view('jadwal.evaluasi', [
        'total' => $total,
        'skipped' => $skipped,
        'fitness' => round($fitness, 4),
        'nonConflictCount' => $nonConflictCount,
        'totalConflicts' => $totalConflicts,
        'conflictGuru' => $conflictGuru,
        'conflictKelas' => $conflictKelas,
        'conflictRuangan' => $conflictRuangan,
        'conflicts' => [], // kalau mau detail konflik, bisa tambahkan logika tambahan
    ]);
}





    public function showGenerateForm()
    {
        return view('jadwal.generate');
    }

    public function reset()
    {
        Jadwal::truncate();

        return redirect()->route('jadwal.index')->with('success', 'Semua jadwal berhasil direset.');
    }


    public function exportPDFKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        $jadwals = Jadwal::with(['mapel', 'guru', 'waktu', 'ruangan'])
            ->where('kelas_id', $id)
            ->get();

        $pdf = PDF::loadView('jadwal.print_kelas', compact('kelas', 'jadwals'));
        return $pdf->stream("jadwal-{$kelas->nama}.pdf");
    }

    public function exportPDFGuru($id)
    {
        $guru = Guru::findOrFail($id);

        // Ambil semua jadwal guru ini dan relasi lengkap
        $jadwalItems = Jadwal::with(['kelas', 'waktu', 'mapel', 'ruangan'])
            ->where('guru_id', $id)
            ->get();

        // Susun data jadi struktur: $data[$kelas][$hari][$jam_ke] = ['teks' => ..., 'color' => ...]
        $jadwals = [];

        $colors = ['#d1e7dd', '#fde2e2', '#e0d4fd', '#fdf6b2', '#caf0f8', '#ffc8dd'];
        $colorIndex = 0;

        foreach ($jadwalItems as $item) {
            $kelasNama = $item->kelas->nama;
            $hari = $item->waktu->hari;
            $jam = $item->waktu->jam_ke;

            $mapelKode = $item->mapel->kode_mapel ?? $item->mapel->mapel;
            $ruangan = $item->ruangan->nama;
            $teks = "<strong>$mapelKode</strong><br><small>R.$ruangan</small>";

            if (!isset($jadwals[$kelasNama][$hari][$jam])) {
                $jadwals[$kelasNama][$hari][$jam] = [
                    'teks' => $teks,
                    'color' => $colors[$colorIndex % count($colors)],
                ];
                $colorIndex++;
            }
        }

        $pdf = Pdf::loadView('jadwal.print_guru', [
            'guru' => $guru,
            'jadwals' => $jadwals,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Jadwal-Guru-' . $guru->nama . '.pdf');
    }


    public function exportExcelKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        return Excel::download(new JadwalPerKelasExport($id), "jadwal-{$kelas->nama}.xlsx");
    }

    public function exportExcelGuru($id)
    {
        $guru = Guru::findOrFail($id);
        return Excel::download(new JadwalPerGuruExport($guru), 'Jadwal-Guru-' . $guru->nama . '.xlsx');
    }

    public function exportAllPDF()
    {
        $jadwals = Jadwal::with(['kelas', 'mapel', 'guru', 'ruangan', 'waktu'])
                        ->orderBy('kelas_id')
                        ->orderBy('waktu_id')
                        ->get();

        $pdf = Pdf::loadView('jadwal.print', compact('jadwals'))
                    ->setPaper('a4', 'landscape');

        return $pdf->stream('jadwal-pelajaran-semua.pdf');
    }


}
