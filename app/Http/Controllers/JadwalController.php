<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Waktu;
use App\Models\Jadwal;
use App\Models\JadwalConflict;
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
        
        return redirect()->route('jadwal.index');
    }



    public function evaluasi()
    {
        // Ambil semua jadwal lengkap dengan relasi
        $jadwals = \App\Models\Jadwal::with(['guru', 'kelas', 'mapel', 'ruangan', 'waktu'])->get();
    
        // Total jadwal yang berhasil disimpan
        $total = $jadwals->count();
    
        // Total ideal berdasarkan semua jam_per_minggu
        $pengampus = \App\Models\Pengampu::with('mapel')->get();
        $expectedTotal = $pengampus
            ->filter(fn($p) => $p->mapel && $p->mapel->jam_per_minggu)
            ->sum(fn($p) => $p->mapel->jam_per_minggu);

        // Hitung skipped dengan aman (tidak negatif)
        $skipped = max(0, $expectedTotal - $total);

        // Deteksi konflik (guru, kelas, ruangan bentrok di waktu yang sama)
        $conflicts = [];
        $conflictGuru = 0;
        $conflictKelas = 0;
        $conflictRuangan = 0;
            
        foreach ($jadwals as $i => $a) {
            foreach ($jadwals as $j => $b) {
                if ($i >= $j) continue; // Hindari duplikat dan diri sendiri
    
                $sameTime = $a->waktu_id === $b->waktu_id;
    
                if (!$sameTime) continue;
    
                $waktuText = $a->waktu
                    ? $a->waktu->hari . ' ' . $a->waktu->jam_mulai . ' - ' . $a->waktu->jam_selesai
                    : 'Waktu ID: ' . $a->waktu_id;
    
                // Cek konflik guru
                if ($a->guru_id && $a->guru_id === $b->guru_id) {
                    $conflicts[] = [
                        'type' => 'guru',
                        'waktu' => $waktuText,
                        'guru' => $a->guru->nama ?? 'Guru ID: ' . $a->guru_id,
                        'kelas_a' => $a->kelas->nama ?? 'Kelas A ID: ' . $a->kelas_id,
                        'kelas_b' => $b->kelas->nama ?? 'Kelas B ID: ' . $b->kelas_id,
                    ];
                    $conflictGuru++;
                }
    
                // Cek konflik kelas
                if ($a->kelas_id && $a->kelas_id === $b->kelas_id) {
                    $conflicts[] = [
                        'type' => 'kelas',
                        'waktu' => $waktuText,
                        'kelas' => $a->kelas->nama ?? 'Kelas ID: ' . $a->kelas_id,
                        'mapel_a' => $a->mapel->nama ?? 'Mapel A ID: ' . $a->mapel_id,
                        'mapel_b' => $b->mapel->nama ?? 'Mapel B ID: ' . $b->mapel_id,
                    ];
                    $conflictKelas++;
                }
    
                // Cek konflik ruangan
                if ($a->ruangan_id && $a->ruangan_id === $b->ruangan_id) {
                    $conflicts[] = [
                        'type' => 'ruangan',
                        'waktu' => $waktuText,
                        'ruangan' => $a->ruangan->nama ?? 'Ruangan ID: ' . $a->ruangan_id,
                        'kelas_a' => $a->kelas->nama ?? 'Kelas A ID: ' . $a->kelas_id,
                        'kelas_b' => $b->kelas->nama ?? 'Kelas B ID: ' . $b->kelas_id,
                    ];
                    $conflictRuangan++;
                }
            }
        }
    
        // Hitung jumlah konflik dan tidak konflik
        $conflictCount = count($conflicts);
        $nonConflictCount = $total - $conflictCount;
    
        // Ambil nilai fitness dari file jika ada
        $fitness = null;
        $fitnessFile = storage_path('logs/fitness.txt');
        if (file_exists($fitnessFile)) {
            $fitness = file_get_contents($fitnessFile);
        }
    
        return view('jadwal.evaluasi', [
            'total' => $total,
            'expected' => $expectedTotal,
            'skipped' => $skipped,
            'fitness' => $fitness,
            'conflicts' => $conflicts,
            'conflictCount' => $conflictCount,
            'nonConflictCount' => $nonConflictCount,
            'conflictGuru' => $conflictGuru,
            'conflictKelas' => $conflictKelas,
            'conflictRuangan' => $conflictRuangan,
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
