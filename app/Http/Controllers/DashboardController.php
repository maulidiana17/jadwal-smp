<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index()
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m") * 1;
        $tahunini = date("Y");
        $nis = Auth::guard('siswa')->user()->nis;
        $kelas_nama = Auth::guard('siswa')->user()->kelas;

        // Mapping hari ke Bahasa Indonesia
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
        $jamSekarang = strtotime(date('H:i')); // <-- ubah jadi detik

        // Data Presensi Hari Ini
        $presensihariini = DB::table('absensi')
            ->where('nis', $nis)
            ->where('tgl_absen', $hariini)
            ->first();

        $statusIzinHariIni = DB::table('pengajuan_izin')
            ->where('nis', $nis)
            ->where('tanggal_izin', $hariini)
            ->where('status_approved', 1)
            ->select('status') // 'i' untuk izin, 's' untuk sakit
            ->first();

        // Histori Bulan Ini
        $historibulanini = DB::table('absensi')
            ->where('nis', $nis)
            ->whereMonth('tgl_absen', $bulanini)
            ->whereYear('tgl_absen', $tahunini)
            ->orderBy('tgl_absen')
            ->paginate(5);
        // ->get();

        // Rekap Presensi
        $rekappresensi = DB::table('absensi')
            ->selectRaw('COUNT(nis) as jmlhadir, SUM(IF(jam_masuk > "10:00",1,0)) as jmlterlambat')
            ->where('nis', $nis)
            ->whereMonth('tgl_absen', $bulanini)
            ->whereYear('tgl_absen', $tahunini)
            ->first();

        // Nama Bulan
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // Rekap Izin & Sakit
        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jumlahizin, SUM(IF(status="s",1,0)) as jumlahsakit')
            ->where('nis', $nis)
            ->whereMonth('tanggal_izin', $bulanini)
            ->whereYear('tanggal_izin', $tahunini)
            ->where('status_approved', 1)
            ->first();

        $jadwalHariIni = [];
        $jadwalSedangBerlangsung = null;



        // Batas jam sekolah
        $jamAwalSekolah = strtotime('05:00');
        $jamAkhirSekolah = strtotime('22:00');

        // Ambil ID kelas berdasarkan nama kelas
        $kelas = DB::table('kelas')
            ->where('nama', $kelas_nama)
            ->first();


        if ($kelas && $jamSekarang >= $jamAwalSekolah && $jamSekarang <= $jamAkhirSekolah) {

            $jadwalHariIni = DB::table('jadwal')
                // ->table('jadwal')
                ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
                ->join('guru', 'jadwal.guru_id', '=', 'guru.id')
                ->join('waktu', 'jadwal.waktu_id', '=', 'waktu.id')
                ->where('jadwal.kelas_id', $kelas->id)
                ->whereRaw('LOWER(waktu.hari) = ?', [strtolower($hariIndo)])

                // ->where('waktu.hari', $hariIndo)
                ->select('mapel.mapel', 'guru.nama as nama_guru', 'waktu.jam_mulai', 'waktu.jam_selesai')
                ->orderBy('waktu.jam_mulai')
                ->get();

            foreach ($jadwalHariIni as $jadwal) {
                $mulai = strtotime(str_replace('.', ':', $jadwal->jam_mulai));
                $selesai = strtotime(str_replace('.', ':', $jadwal->jam_selesai));

                if ($jamSekarang >= $mulai && $jamSekarang <= $selesai) {
                    $jadwalSedangBerlangsung = $jadwal;
                    break;
                }
            }
        }

        return view('dashboard.dashboard', compact(
            'presensihariini',
            'historibulanini',
            'namabulan',
            'bulanini',
            'tahunini',
            'rekappresensi',
            'rekapizin',
            'jadwalHariIni',
            'jadwalSedangBerlangsung',
            'statusIzinHariIni'
        ));
    }

    public function dashboardadmin()
    {
        if (auth()->user()->roles[0]->pivot['role_id'] != '1') {
            return Auth::user()->role;
        }
        $hariini = date("Y-m-d");

        // Rekap Hadir dan Terlambat
        $rekappresensi = DB::table('absensi')
            ->selectRaw('COUNT(nis) as jmlhadir, SUM(IF(jam_masuk > "10:00",1,0)) as jmlterlambat')
            ->where('tgl_absen', $hariini)
            ->first();

        // Rekap Izin dan Sakit rentang tanggal
        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jumlahizin, SUM(IF(status="s",1,0)) as jumlahsakit')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->first();

        // Data siswa hadir
        $siswaHadir = DB::table('absensi')
            ->join('siswa', 'absensi.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'absensi.jam_masuk')
            ->where('tgl_absen', $hariini)
            ->get();

        // Siswa terlambat
        $siswaTerlambat = DB::table('absensi')
            ->join('siswa', 'absensi.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'absensi.jam_masuk')
            ->where('tgl_absen', $hariini)
            ->where('jam_masuk', '>', '10:00')
            ->get();


        $siswaIzin = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'pengajuan_izin.status', 'pengajuan_izin.tanggal_izin', 'pengajuan_izin.tanggal_izin_akhir')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('pengajuan_izin.status', 'i')
            ->get();


        $siswaSakit = DB::table('pengajuan_izin')
            ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
            ->select('siswa.nis', 'siswa.nama_lengkap', 'siswa.kelas', 'pengajuan_izin.status', 'pengajuan_izin.tanggal_izin', 'pengajuan_izin.tanggal_izin_akhir')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->where('pengajuan_izin.status', 's')
            ->get();



        // Semua Siswa
        $semuaSiswa = DB::table('siswa')->select('nis', 'nama_lengkap')->get();

        // Hitung siswa belum hadir
        $siswaHadirNIS = $siswaHadir->pluck('nis')->toArray();
        $siswaIzinSakitNIS = DB::table('pengajuan_izin')
            ->whereDate('tanggal_izin', '<=', $hariini)
            ->whereDate('tanggal_izin_akhir', '>=', $hariini)
            ->where('status_approved', 1)
            ->pluck('nis')
            ->toArray();

        $siswaBelumHadir = $semuaSiswa->filter(function ($siswa) use ($siswaHadirNIS, $siswaIzinSakitNIS) {
            return !in_array($siswa->nis, $siswaHadirNIS) && !in_array($siswa->nis, $siswaIzinSakitNIS);
        });

        // Query data tabel detail dengan urutan sesuai permintaan
        $orderQuery = "
            FIELD(
                CASE
                    WHEN absensi.jam_masuk IS NOT NULL AND absensi.jam_masuk > '10:00' THEN 'Terlambat'
                    WHEN pengajuan_izin.status = 'i' THEN 'Izin'
                    WHEN pengajuan_izin.status = 's' THEN 'Sakit'
                    WHEN absensi.jam_masuk IS NOT NULL AND absensi.jam_masuk <= '10:00' THEN 'Hadir'
                    ELSE 'Belum Hadir'
                END,
                'Terlambat', 'Izin', 'Sakit', 'Hadir', 'Belum Hadir'
            )
        ";

        $query = DB::table('siswa')
            ->select(
                'siswa.nis',
                'siswa.nama_lengkap',
                'siswa.kelas',
                DB::raw(
                    '
                CASE
                    WHEN absensi.jam_masuk IS NOT NULL AND absensi.jam_masuk <= "10:00" THEN "Hadir"
                    WHEN absensi.jam_masuk IS NOT NULL AND absensi.jam_masuk > "10:00" THEN "Terlambat"
                    WHEN pengajuan_izin.status = "i" THEN "Izin"
                    WHEN pengajuan_izin.status = "s" THEN "Sakit"
                    ELSE "Belum Hadir"
                END as keterangan'
                ),
                'absensi.jam_masuk'
            )
            ->leftJoin('absensi', function ($join) use ($hariini) {
                $join->on('absensi.nis', '=', 'siswa.nis')
                    ->where('absensi.tgl_absen', $hariini);
            })
            ->leftJoin('pengajuan_izin', function ($join) use ($hariini) {
                $join->on('pengajuan_izin.nis', '=', 'siswa.nis')
                    ->where('pengajuan_izin.status_approved', 1)
                    ->whereDate('pengajuan_izin.tanggal_izin', '<=', $hariini)
                    ->whereDate('pengajuan_izin.tanggal_izin_akhir', '>=', $hariini);
            })
            ->orderByRaw($orderQuery)
            ->orderBy('siswa.nama_lengkap', 'asc');


        $datasiswa = $query->paginate(10);


        return view('dashboard.dashboardadmin', compact(
            'rekappresensi',
            'rekapizin',
            'siswaHadir',
            'siswaTerlambat',
            'siswaIzin',
            'siswaSakit',
            'siswaBelumHadir',
            'datasiswa'
        ));
    }

    public function dashboardguru()
    {

            return app(GuruController::class)->qrIndex();
    }
}
