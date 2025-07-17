<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\GenerateJadwalJob;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Ruangan;
use App\Models\Pengampu;
use App\Models\Waktu;
use App\Models\Jadwal;
use App\Helpers\GeneticScheduler;
use App\Exports\JadwalKelasExport;
use App\Exports\JadwalGuruExport;
use Illuminate\Support\Facades\Log;
use App\Services\SchedulerService;
use App\Exports\JadwalPerKelasExport;
use App\Exports\JadwalPerGuruExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
            ->when($request->ruangan_id, fn($q) => $q->where('ruangan_id', $request->ruangan_id))
            ->get();

        //Default kelas_id jika belum dipilih user → ambil kelas pertama
        // $selectedKelasId = $request->kelas_id ?? $kelasList->first()?->id;
        // $kelas_aktif = Kelas::find($selectedKelasId);
        // $selectedGuruId = $request->guru_id ?? $guruList->first()?->id;
        // $guru_aktif = Guru::find($selectedGuruId);
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
        $validated = $request->validate([
            'popSize'=>'required|integer|min:2',
            'crossRate'=>'required|numeric|between:0.6,1',
            'mutRate'=>'required|numeric|between:0.1,1',
            'generations'=>'required|integer|min:1',
            'tries'=>'nullable|integer|min:1|max:10',
        ]);

        [$bestSchedule, $conflicts, $skipped, $fitness] = $svc->generate($validated);
        $count = $svc->save($bestSchedule);
        \Log::info('Jumlah Jadwal yang akan disimpan: ' . count($bestSchedule));

        return view('jadwal.evaluasi', [
            'total' => count($bestSchedule),
            'skipped' => $skipped,  // Karena semua berhasil disimpan
            // Kalau mau tampilkan analisis konflik:
            'conflict_estimation' => $skipped,
            'conflict_details' => $conflicts,
            'fitness' => $fitness,
            
        ]);
    }

    public function perKelas()
    {
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $maxJam = 6;
        $kelasList = Kelas::all();
        $jadwal = Jadwal::with(['guru', 'mapel', 'ruangan'])->get();

        return view('jadwal.per_kelas', compact('hariList', 'maxJam', 'kelasList', 'jadwal'));
    }

    public function perGuru()
    {
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $guruList = Guru::all();
        $jadwal = Jadwal::with(['mapel', 'kelas', 'ruangan'])->get();

        return view('jadwal.per_guru', compact('hariList', 'guruList', 'jadwal'));
    }

    public function perMapel()
    {
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $mapelList = Mapel::all();
        $jadwal = Jadwal::with(['guru', 'kelas', 'ruangan'])->get();

        return view('jadwal.per_mapel', compact('hariList', 'mapelList', 'jadwal'));
    }

    public function evaluasi()
    {
        $total = Jadwal::count();
        $requirements = \App\Models\Pengampu::count();
        $skipped = $requirements - $total;

        return view('jadwal.evaluasi', compact('total', 'skipped'));
    }


    public function filter(Request $request)
    {
        $jadwal = Jadwal::with(['waktu', 'guru', 'mapel', 'kelas', 'ruangan']);

        if ($request->filled('kelas_id')) {
            $jadwal->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('hari')) {
            $jadwal->whereHas('waktu', fn($q) => $q->where('hari', $request->hari));
        }
        if ($request->filled('guru_id')) {
            $jadwal->where('guru_id', $request->guru_id);
        }
        if ($request->filled('ruangan_id')) {
            $jadwal->where('ruangan_id', $request->ruangan_id);
        }

        $jadwals = $jadwal->get();
        $kelas = Kelas::all();
        $guru = Guru::all();
        $ruang = Ruangan::all();

        return view('jadwal.index', compact('jadwals', 'kelas', 'guru', 'ruang'));
    }
    public function showGenerateForm()
    {
        return view('jadwal.generate');
    }

    public function reset()
    {
        // Menghapus semua data jadwal
        Jadwal::truncate(); // kosongkan semua entri jadwal

        return redirect()->route('jadwal.index')->with('success', 'Semua jadwal berhasil direset.');
    }
 


//taruh bawah karena package PDF belum terinstall
   public function exportPDFKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        $jadwals = Jadwal::with(['mapel', 'guru', 'waktu', 'ruangan'])
                    ->where('kelas_id', $id)
                    ->get();

        $pdf = PDF::loadView('jadwal.pdf_kelas', compact('kelas', 'jadwals'));
        return $pdf->download("jadwal-{$kelas->nama}.pdf");
    }

    public function exportPDFGuru($id)
    {
        $guru = Guru::findOrFail($id);
        $jadwals = Jadwal::with(['mapel', 'kelas', 'waktu', 'ruangan'])
                    ->where('guru_id', $id)
                    ->get();

        $pdf = PDF::loadView('jadwal.pdf_guru', compact('guru', 'jadwals'));
        return $pdf->download("jadwal-{$guru->nama}.pdf");
    }


    public function exportExcelKelas($id)
    {
        $kelas = Kelas::findOrFail($id);
        return Excel::download(new JadwalPerKelasExport($id), "jadwal-{$kelas->nama}.xlsx");
    }

    public function exportExcelGuru($id)
    {
        $guru = Guru::findOrFail($id);
        return Excel::download(new JadwalPerGuruExport($id), "jadwal-{$guru->nama}.xlsx");
    }// JadwalController.php
}