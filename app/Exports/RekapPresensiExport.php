<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapPresensiExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $rekap;
    protected $jumlahHari;
    protected $izinPerTanggal;
    protected $tanggalMerah;
    protected $tahun;
    protected $bulan;
    protected $izinRentangPerSiswa;

    public function __construct($rekap, $jumlahHari, $izinPerTanggal, $tanggalMerah, $tahun, $bulan, $izinRentangPerSiswa)
    {
        $this->rekap = $rekap;
        $this->jumlahHari = $jumlahHari;
        $this->izinPerTanggal = $izinPerTanggal;
        $this->tanggalMerah = $tanggalMerah;
        $this->tahun = $tahun;
        $this->bulan = $bulan;
        $this->izinRentangPerSiswa = $izinRentangPerSiswa;
    }


    public function collection()
    {
        return collect($this->rekap)->map(function ($item) {
            $row = [
                $item->nis,
                $item->nama_lengkap,
                $item->kelas,
            ];

            // Tanggal-tanggal
          for ($i = 1; $i <= $this->jumlahHari; $i++) {
    $field = "tgl_$i";
    $tglLengkap = sprintf('%04d-%02d-%02d', $this->tahun, $this->bulan, $i);

    $isi = '';

    // Isi jam hadir
    if (!empty($item->$field)) {
        $isi = $item->$field;
    }

    // Cek izin per tanggal
    if (isset($this->izinPerTanggal[$item->nis][$tglLengkap])) {
        $status = $this->izinPerTanggal[$item->nis][$tglLengkap]->status;
        $isi = $status === 'i' ? 'IZIN' : 'SAKIT';
    }

    // Cek rentang izin/sakit
    if (isset($this->izinRentangPerSiswa[$item->nis])) {
        foreach ($this->izinRentangPerSiswa[$item->nis] as $izin) {
            $awal = $izin->tanggal_awal;
            $akhir = $izin->tanggal_izin_akhir;

            if ($tglLengkap >= $awal && $tglLengkap <= $akhir) {
                $status = $izin->status === 'i' ? 'IZIN' : 'SAKIT';
                $isi = $status;
            }
        }
    }

    // Cek tanggal merah
    if (isset($this->tanggalMerah[$tglLengkap])) {
        $isi .= ($isi ? ' - ' : '') . $this->tanggalMerah[$tglLengkap];
    }

    $row[] = $isi;
}


            // Data tambahan
            $row[] = $item->jumlah_hadir;
            $row[] = $item->jumlah_terlambat;
            $row[] = $item->izin_sakit_detail ?? '-';
            $row[] = $item->total_izin_sakit_6bulan_detail ?? '-';

            return $row;
        });
    }

    public function headings(): array
    {
        $headings = [
            'NIS', 'Nama', 'Kelas'
        ];

        // Heading tanggal
        for ($i = 1; $i <= $this->jumlahHari; $i++) {
            $headings[] = (string) $i;
        }

        // Heading tambahan
        $headings[] = 'Jumlah Hadir';
        $headings[] = 'Jumlah Terlambat';
        $headings[] = 'Izin/Sakit';
        $headings[] = 'Total Izin/Sakit 6 Bulan';

        return $headings;
    }

}


