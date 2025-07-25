<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiMingguanExport;
use Illuminate\Http\Request;
use App\Models\Guru;
use App\Imports\GuruImport;
use App\Models\QRAbsen;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GuruController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|kurikulum']);
    }

   public function index()
{
    $gurus = Guru::orderBy('nama')->paginate(5);

    switch (true) {
        case auth()->user()->hasRole('admin'):
            return view('layouts.admin.guru.index', compact('gurus'));
        case auth()->user()->hasRole('kurikulum'):
            return view('guru.index', compact('gurus'));
        default:
            abort(403, 'Tidak diizinkan.');
    }
}


    public function create()
    {
        return view('guru.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_guru' => 'required|unique:guru,kode_guru|max:10',
            'nama' => 'required|string|max:100',
            'nip' => 'required|unique:guru,nip',
            'email' => 'required|email|unique:guru,email',
            'alamat' => 'nullable|string|max:255',
        ]);

        Guru::create($validated);
        return redirect()->route('guru.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $validated = $request->validate([
            'kode_guru' => 'required|max:10|unique:guru,kode_guru,' . $guru->id,
            'nama' => 'required|string|max:100',
            'nip' => 'required|unique:guru,nip,' . $guru->id,
            'email' => 'required|email|unique:guru,email,' . $guru->id,
            'alamat' => 'nullable|string|max:255',
        ]);

        $guru->update($validated);
        return redirect()->route('guru.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function delete($id)
    {
        $guru = Guru::findOrFail($id);
        $guru->delete();

        return redirect()->route('guru.index')->with('success', 'Guru berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new GuruImport, $request->file('file'));

    return redirect()->route('guru.index')->with('success', 'Data guru berhasil diimpor.');
    }


   public function qr()
    {
        $hariini = Carbon::today()->toDateString();


        $user = Auth::user();
         if (!Auth::user()->hasRole('guru')) {
            abort(403, 'Akses ditolak.');
        }
        $guru = Guru::where('email', Auth::user()->email)->first();


        if (!$guru) {
            abort(404, 'Data guru tidak ditemukan.');
        }

        $timestamp = Carbon::now()->timestamp;
        $intervalKey = floor($timestamp / (30 * 60));

        $data = json_encode([
            'nama' => $user->name,
            'nip' => $guru->nip,
            'mapel' => $guru->mapel,
            'token' => $intervalKey,
        ]);

        $qrCode = QrCode::size(250)->generate($data);

        $siswaAbsenHariIni = QRAbsen::whereDate('waktu', Carbon::today())
            ->where('nip', $guru->nip)
            ->get();

          $siswaIzin = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'pengajuan_izin.tanggal_izin', 'pengajuan_izin.tanggal_izin_akhir')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('pengajuan_izin.status', 'i')
            ->get();

        $siswaSakit = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'pengajuan_izin.tanggal_izin', 'pengajuan_izin.tanggal_izin_akhir')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('pengajuan_izin.status', 's')
            ->get();

        $hariMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $hariIndo = $hariMap[date('l')];

        $jadwalMengajarMinggu = DB::table('jadwal')
            // ->table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereIn(DB::raw('LOWER(waktu.hari)'), ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'])
            ->select('mapel.mapel', 'kelas.nama as nama_kelas', 'waktu.jam_mulai', 'waktu.jam_selesai', 'waktu.hari')
            ->orderByRaw("FIELD(LOWER(waktu.hari), 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')")
            ->orderBy('waktu.jam_mulai')
            ->get()
            ->groupBy('hari');

        $jadwalHariIni = DB::table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereRaw("LOWER(waktu.hari) REGEXP ?", [$hariIndo])
            // ->whereRaw('LOWER(waktu.hari) = ?', [strtolower($hariIndo)])
            ->select('kelas.nama as nama_kelas', 'kelas.id as kelas_id')
            ->get();
        $daftarSiswaKelasHariIni = collect();

        foreach ($jadwalHariIni as $jadwal) {
            $siswaKelas = DB::table('siswa')
                ->where('kelas', $jadwal->nama_kelas)
                ->select('nis', 'nama_lengkap', 'kelas')
                ->get();

            $daftarSiswaKelasHariIni = $daftarSiswaKelasHariIni->merge($siswaKelas);
        }
        $jadwalHariIni = DB::table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereRaw("LOWER(waktu.hari) REGEXP ?", [$hariIndo])
            // ->whereRaw('LOWER(waktu.hari) = ?', [strtolower($hariIndo)])
            ->select('kelas.nama as nama_kelas', 'mapel.mapel')
            ->get();

        $absensiHariIni = DB::table('qr_absens')
                ->whereDate('waktu', $hariini)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->nis . '|' . $item->mapel => true];
                });

        $izinHariIni = DB::table('pengajuan_izin')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->get()
            ->keyBy('nis');

        $daftarSiswaKelasHariIni = collect();

        foreach ($jadwalHariIni as $jadwal) {
            $siswaKelas = DB::table('siswa')
                ->where('kelas', $jadwal->nama_kelas)
                ->select('nis', 'nama_lengkap', 'kelas')
                ->get();

        foreach ($siswaKelas as $siswa) {
            $key = $siswa->nis . '|' . $jadwal->mapel;
            $status = 'Alfa';

            if ($absensiHariIni->has($key)) {
                $status = 'Hadir';
            } elseif (isset($izinHariIni[$siswa->nis])) {
                $izin = $izinHariIni[$siswa->nis];
                $status = $izin->status === 's' ? 'Sakit' : 'Izin';
            }

            $siswa->status = $status;
            $siswa->mapel = $jadwal->mapel;
            $daftarSiswaKelasHariIni->push($siswa);
        }

        }

        return view('dashboard.qr', compact('qrCode', 'guru', 'siswaAbsenHariIni', 'siswaIzin',
            'siswaSakit',
            'jadwalMengajarMinggu',
            'daftarSiswaKelasHariIni'));
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'semester' => 'required|in:1,2',
            'tahun' => 'required|integer|min:2020|max:2100',
        ]);

        $tahun = $request->tahun;

        if ($request->semester == 1) {
            $awal = \Carbon\Carbon::createFromDate($tahun, 1, 1);
            $akhir = \Carbon\Carbon::createFromDate($tahun, 6, 30)->endOfDay();
        } else {
            $awal = \Carbon\Carbon::createFromDate($tahun, 7, 1);
            $akhir = \Carbon\Carbon::createFromDate($tahun, 12, 31)->endOfDay();
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AbsensiExport($awal, $akhir),
            'rekap-semester-' . $request->semester . '-' . $tahun . '.xlsx'
        );
    }

    public function exportMingguanManual(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $awal = Carbon::parse($request->tanggal_awal)->startOfDay();
        $akhir = Carbon::parse($request->tanggal_akhir)->endOfDay();

        $user = Auth::user();
        $guru = Guru::where('email', $user->email)->firstOrFail();

        $kelasDiajar = DB::table('jadwal')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->where('jadwal.guru_id', $guru->id)
            ->pluck('kelas.nama')
            ->unique()
            ->implode('-');

        return Excel::download(
            new AbsensiMingguanExport($awal, $akhir, $user->name),
            'absensi-manual-' . $awal->format('Ymd') . '-' . $akhir->format('Ymd') . '-' . $kelasDiajar . '-' . $user->name . '.xlsx'
        );
    }

    public function downloadQr()
    {
        $user = Auth::user();
        $guru = Guru::where('email', $user->email)->firstOrFail();

        if (!$guru) {
            abort(404, 'Data guru tidak ditemukan.');
        }

        $timestamp = Carbon::now()->timestamp;
        $intervalKey = floor($timestamp / (30 * 60));

        $data = json_encode([
            'nama' => $user->name,
            'nip' => $guru->nip,
            'mapel' => $guru->mapel,
            'interval_key' => $intervalKey,
        ]);

        $qrCode = QrCode::format('svg')->size(300)->generate($data);

        return response($qrCode)
        ->header('Content-Type', 'image/svg+xml')
        ->header('Content-Disposition', 'attachment; filename="qrcode_' . $guru->mapel . '.svg"');

    }

    //benar fiks
    public function qrIndex()
    {
        $hariini = Carbon::today()->toDateString();
        $user = Auth::user();
        $guru = Guru::where('email', Auth::user()->email)->first();
        if (!$guru) {
            abort(404, 'Data guru tidak ditemukan.');
        }
        $jmlhadir = DB::table('qr_absens')
            ->whereDate('waktu', $hariini)
            ->where('nip', $guru->nip)
            ->count();

        $jumlahizin = DB::table('pengajuan_izin')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('status', 'i')
            ->count();

        $jumlahsakit = DB::table('pengajuan_izin')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('status', 's')
            ->count();

        $jumlahterlambat = DB::table('absensi')
            ->where('tgl_absen', $hariini)
            ->where('jam_masuk', '>', '10:00')
            ->count();

        $siswaIzin = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'pengajuan_izin.tanggal_izin', 'pengajuan_izin.tanggal_izin_akhir')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('pengajuan_izin.status', 'i')
            ->get();

        $siswaSakit = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'pengajuan_izin.tanggal_izin', 'pengajuan_izin.tanggal_izin_akhir')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('pengajuan_izin.status', 's')
            ->get();

        $hariMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $hariIndo = $hariMap[date('l')];

        $jadwalMengajarMinggu = DB::table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereIn(DB::raw('LOWER(waktu.hari)'), ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'])
            ->select('mapel.mapel', 'kelas.nama as nama_kelas', 'waktu.jam_mulai', 'waktu.jam_selesai', 'waktu.hari')
            ->orderByRaw("FIELD(LOWER(waktu.hari), 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')")
            ->orderBy('waktu.jam_mulai')
            ->get()
            ->groupBy('hari');

        $jadwalHariIni = DB::table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereRaw("LOWER(waktu.hari) REGEXP ?", [$hariIndo])
            // ->whereRaw('LOWER(waktu.hari) = ?', [strtolower($hariIndo)])
            ->select('kelas.nama as nama_kelas', 'kelas.id as kelas_id')
            ->get();
        $daftarSiswaKelasHariIni = collect();

        foreach ($jadwalHariIni as $jadwal) {
            $siswaKelas = DB::table('siswa')
                ->where('kelas', $jadwal->nama_kelas)
                ->select('nis', 'nama_lengkap', 'kelas')
                ->get();

            $daftarSiswaKelasHariIni = $daftarSiswaKelasHariIni->merge($siswaKelas);
        }
        $jadwalHariIni = DB::table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereRaw("LOWER(waktu.hari) REGEXP ?", [$hariIndo])
            // ->whereRaw('LOWER(waktu.hari) = ?', [strtolower($hariIndo)])
            ->select('kelas.nama as nama_kelas')
            ->get();

        $absensiHariIni = DB::table('qr_absens')
            ->whereDate('waktu', $hariini)
            ->pluck('nis');

        $izinHariIni = DB::table('pengajuan_izin')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->get()
            ->keyBy('nis');

        $daftarSiswaKelasHariIni = collect();

            foreach ($jadwalHariIni as $jadwal) {
                $siswaKelas = DB::table('siswa')
                    ->where('kelas', $jadwal->nama_kelas)
                    ->select('nis', 'nama_lengkap', 'kelas')
                    ->get();

                foreach ($siswaKelas as $siswa) {
                    $status = 'Alfa';
                    if ($absensiHariIni->contains($siswa->nis)) {
                        $status = 'Hadir';
                    } elseif (isset($izinHariIni[$siswa->nis])) {
                        $izin = $izinHariIni[$siswa->nis];
                        $status = $izin->status === 's' ? 'Sakit' : 'Izin';
                    }
                    $siswa->status = $status;
                    $daftarSiswaKelasHariIni->push($siswa);
                }
            }


        return view('dashboard.dashboardguru', compact(
            'jmlhadir',
            'jumlahizin',
            'jumlahsakit',
            'jumlahterlambat',
            'siswaIzin',
            'siswaSakit',
            'jadwalMengajarMinggu',
            'daftarSiswaKelasHariIni'
        ));
    }

    public function ubahKeAlfa(Request $request)
    {
        DB::table('qr_absens')
            ->where('nis', $request->nis)
            ->where('mapel', $request->mapel)
            ->whereDate('waktu', $request->tanggal)
            ->delete();

        return back()->with('success', 'Status diubah menjadi Alfa.');
    }

    public function ubahKeHadir(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->toDateString();
        $nis = $request->nis;

        $siswa = DB::table('siswa')->where('nis', $nis)->first();
        if (!$siswa) {
            return back()->with('error', 'Siswa tidak ditemukan.');
        }

        $user = Auth::user();
        $guru = Guru::where('email', Auth::user()->email)->first();
        if (!$guru) {
            return back()->with('error', 'Guru tidak ditemukan.');
        }

        $hari = strtolower(Carbon::parse($tanggal)->format('l'));
        $hariMap = [
            'sunday' => 'minggu', 'monday' => 'senin', 'tuesday' => 'selasa',
            'wednesday' => 'rabu', 'thursday' => 'kamis',
            'friday' => 'jumat', 'saturday' => 'sabtu',
        ];
        $hariIndo = $hariMap[$hari];

        $jadwal = DB::table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
            ->where('jadwal.guru_id', $guru->id)
            ->whereRaw('LOWER(waktu.hari) = ?', [$hariIndo])
            ->where('kelas.nama', $siswa->kelas)
            ->select('mapel.mapel')
            ->first();

        $mapel = $jadwal->mapel ?? 'Tidak diketahui';
        // Simpan ke qr_absens
        DB::table('qr_absens')->insert([
            'nis' => $nis,
            'nama' => $siswa->nama_lengkap,
            'kelas' => $siswa->kelas,
            'nip' => $guru->nip,
            // 'mapel' => $mapel,
            'mapel' => $request->mapel,
            'waktu' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Status siswa berhasil diubah menjadi Hadir.');
    }

}
