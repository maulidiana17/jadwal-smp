<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class LaporanPresensiExport implements FromView, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $siswa;
    protected $tanggalData;
    protected $bulan;
    protected $tahun;
    protected $namabulan;

    public function __construct($siswa, $tanggalData, $bulan, $tahun, $namabulan)
    {
        $this->siswa = $siswa;
        $this->tanggalData = $tanggalData;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->namabulan = $namabulan;
    }

    public function view(): View
    {
        return view('absensi.cetaklaporanexcel', [
            'siswa' => $this->siswa,
            'tanggalData' => $this->tanggalData,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'namabulan' => $this->namabulan
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 13,   // No.
            'B' => 15,   // Tanggal
            'C' => 15,   // NIS
            'D' => 20,   // Jam Masuk
            'E' => 20,   // Jam Pulang
            'F' => 35,   // Keterangan
        ];
    }



    // protected $nis;
    // protected $bulan;
    // protected $tahun;
    // protected $namabulan = ["", "Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];

    // public function __construct($nis, $bulan, $tahun)
    // {
    //     $this->nis = $nis;
    //     $this->bulan = $bulan;
    //     $this->tahun = $tahun;
    // }

    // public function view(): View
    // {
    //     $siswa = DB::table('siswa')->where('nis', $this->nis)->first();

    //     $absensi = DB::table('absensi')
    //         ->where('nis', $this->nis)
    //         ->whereYear('tgl_absen', $this->tahun)
    //         ->whereMonth('tgl_absen', $this->bulan)
    //         ->orderBy('tgl_absen')
    //         ->get();

    //     return view('absensi.cetaklaporanexcel', [
    //         'siswa' => $siswa,
    //         'absensi' => $absensi,
    //         'namabulan' => $this->namabulan,
    //         'bulan' => $this->bulan,
    //         'tahun' => $this->tahun,
    //     ]);
    // }
    // public function columnWidths(): array
    // {
    //     return [
    //         // Ini untuk kolom data absensi (tabel kedua)
    //         'A' => 13,     // No.
    //         'B' => 12,    // Tanggal
    //         'C' => 15,    // NIS
    //         'D' => 20,    // Jam Masuk
    //         'E' => 20,    // Jam Pulang
    //         'F' => 35,    // Keterangan


    //     ];
    // }
}
