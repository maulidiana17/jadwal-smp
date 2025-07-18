<?php

namespace App\Exports;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsensiMingguanExport implements FromCollection, WithHeadings

{
    protected $awal;
    protected $akhir;
    protected $namaGuru;

    public function __construct($awal, $akhir, $namaGuru)
    {
        $this->awal = $awal;
        $this->akhir = $akhir;
        $this->namaGuru = $namaGuru;
    }

    public function collection()
    {
        $kelasGuru = DB::connection('mysql_spensa')
            ->table('jadwal')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->where('jadwal.guru_id', auth()->user()->guru->id)
            ->pluck('kelas.nama')
            ->unique();

        $mapelGuru = DB::connection('mysql_spensa')
            ->table('jadwal')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->where('jadwal.guru_id', auth()->user()->guru->id)
            ->pluck('mapel.mapel')
            ->unique();

        $siswa = DB::table('siswa')
            ->whereIn('kelas', $kelasGuru)
            ->select('nis', 'nama_lengkap', 'kelas')
            ->get();

        $absensi = DB::table('qr_absens')
            ->whereBetween('waktu', [$this->awal, $this->akhir])
            ->select('nis', 'mapel', DB::raw('count(*) as total'))
            ->groupBy('nis', 'mapel')
            ->get()
            ->groupBy(fn($item) => $item->nis . '|' . $item->mapel);

        $izin = DB::table('pengajuan_izin')
            ->where('status_approved', 1)
            ->where(function ($query) {
                $query->whereBetween('tanggal_izin', [$this->awal, $this->akhir])
                      ->orWhereBetween('tanggal_izin_akhir', [$this->awal, $this->akhir])
                      ->orWhere(function ($query) {
                          $query->where('tanggal_izin', '<=', $this->awal)
                                ->where('tanggal_izin_akhir', '>=', $this->akhir);
                      });
            })
            ->select('nis', 'status', 'tanggal_izin', 'tanggal_izin_akhir')
            ->get()
            ->groupBy('nis');

        $results = collect();

        foreach ($siswa as $row) {
            foreach ($mapelGuru as $mapel) {
                $key = $row->nis . '|' . $mapel;
                $hadir = $absensi->has($key) ? $absensi[$key]->first()->total : 0;

                $izinTotal = 0;
                $sakitTotal = 0;

                if ($izin->has($row->nis)) {
                    foreach ($izin[$row->nis] as $izinItem) {
                        $start = Carbon::parse($izinItem->tanggal_izin);
                        $end = Carbon::parse($izinItem->tanggal_izin_akhir);
                        $period = CarbonPeriod::create($start, $end);

                        foreach ($period as $date) {
                            if ($date->between($this->awal, $this->akhir)) {
                                if ($izinItem->status === 'i') {
                                    $izinTotal++;
                                } elseif ($izinItem->status === 's') {
                                    $sakitTotal++;
                                }
                            }
                        }
                    }
                }

                if ($hadir > 0 || $izinTotal > 0 || $sakitTotal > 0) {
                    $results->push((object)[
                        'nis' => $row->nis,
                        'nama_lengkap' => $row->nama_lengkap,
                        'kelas' => $row->kelas,
                        'mapel' => $mapel,
                        'total_hadir' => $hadir,
                        'total_izin' => $izinTotal,
                        'total_sakit' => $sakitTotal,
                        'nama_guru' => $this->namaGuru,
                        'periode' => $this->awal->format('d-m-Y') . ' s.d. ' . $this->akhir->format('d-m-Y'),
                    ]);
                }
            }
        }

        return $results;
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Nama Lengkap',
            'Kelas',
            'Mapel',
            'Total Hadir',
            'Total Izin',
            'Total Sakit',
            'Nama Guru',
            'Periode'
        ];
    }
}

//benar
// {
//     protected $awal;
//     protected $akhir;
//     protected $namaGuru;

//     public function __construct($awal, $akhir, $namaGuru)
//     {
//         $this->awal = $awal;
//         $this->akhir = $akhir;
//         $this->namaGuru = $namaGuru;
//     }

//     public function collection()
//     {
//         $kelasGuru = DB::connection('mysql_spensa')
//             ->table('jadwal')
//             ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
//             ->where('jadwal.guru_id', auth()->user()->guru->id)
//             ->pluck('kelas.nama')
//             ->unique();

//         $mapelGuru = DB::connection('mysql_spensa')
//             ->table('jadwal')
//             ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
//             ->where('jadwal.guru_id', auth()->user()->guru->id)
//             ->pluck('mapel.mapel')
//             ->unique();

//         $siswa = DB::table('siswa')
//             ->whereIn('kelas', $kelasGuru)
//             ->select('nis', 'nama_lengkap', 'kelas')
//             ->get();

//         $absensi = DB::table('qr_absens')
//             ->whereBetween('waktu', [$this->awal, $this->akhir])
//             ->select('nis', 'mapel', DB::raw('count(*) as total'))
//             ->groupBy('nis', 'mapel')
//             ->get()
//             ->groupBy(fn($item) => $item->nis . '|' . $item->mapel);

//         $izin = DB::table('pengajuan_izin')
//             ->whereBetween('tanggal_izin', [$this->awal, $this->akhir])
//             ->where('status_approved', 1)
//             ->select('nis', 'status', DB::raw('count(*) as total'))
//             ->groupBy('nis', 'status')
//             ->get()
//             ->groupBy('nis');

//         $results = collect();

//         foreach ($siswa as $row) {
//             foreach ($mapelGuru as $mapel) {
//                 $key = $row->nis . '|' . $mapel;
//                 $hadir = $absensi->has($key) ? $absensi[$key]->first()->total : 0;

//                 $izinSiswa = $izin->get($row->nis);
//                 $izinTotal = $izinSiswa?->where('status', 'i')->sum('total') ?? 0;
//                 $sakitTotal = $izinSiswa?->where('status', 's')->sum('total') ?? 0;

//                 if ($hadir > 0 || $izinTotal > 0 || $sakitTotal > 0) {
//                     $results->push((object)[
//                         'nis' => $row->nis,
//                         'nama_lengkap' => $row->nama_lengkap,
//                         'kelas' => $row->kelas,
//                         'mapel' => $mapel,
//                         'total_hadir' => $hadir,
//                         'total_izin' => $izinTotal,
//                         'total_sakit' => $sakitTotal,
//                         'nama_guru' => $this->namaGuru,
//                         'periode' => $this->awal->format('d-m-Y') . ' s.d. ' . $this->akhir->format('d-m-Y'),
//                     ]);
//                 }
//             }
//         }

//         return $results;
//     }

//     public function headings(): array
//     {
//         return [
//         'NIS',
//         'Nama Lengkap',
//         'Kelas',
//         'Mapel',
//         'Total Hadir',
//         'Total Izin',
//         'Total Sakit',
//         'Nama Guru',
//         'Periode'
//     ];
//     }
// }


//benar kurang sakit
//    public function collection()
// {
//     // 1. Ambil total izin/sakit dari tabel pengajuan_izin
//     $izinData = DB::table('pengajuan_izin')
//         ->select('nis',
//             DB::raw("SUM(CASE WHEN status = 'i' THEN 1 ELSE 0 END) as total_izin"),
//             DB::raw("SUM(CASE WHEN status = 's' THEN 1 ELSE 0 END) as total_sakit")
//         )
//         ->where('status_approved', 1)
//         ->whereBetween('tanggal_izin', [$this->awal, $this->akhir])
//         ->groupBy('nis')
//         ->get()
//         ->keyBy('nis');

//     // 2. Ambil semua data absensi harian
//     $data = DB::table('qr_absens')
//         ->join('siswa', 'qr_absens.nis', '=', 'siswa.nis')
//         ->select(
//             DB::raw('DATE(qr_absens.waktu) as tanggal'),
//             'qr_absens.nis',
//             'siswa.nama_lengkap',
//             'siswa.kelas',
//             'qr_absens.mapel',
//             DB::raw("'" . $this->namaGuru . "' as nama_guru")
//         )
//         ->whereBetween('qr_absens.waktu', [$this->awal, $this->akhir])
//         ->orderBy('qr_absens.waktu')
//         ->get();

//     // 3. Hitung total hadir per siswa
//     $totalHadir = $data->groupBy(function ($item) {
//         return $item->nis . '-' . $item->mapel;
//     })->map(function ($items) {
//         return $items->count();
//     });

//     // 4. Tambahkan kolom total hadir, izin, sakit ke setiap baris
//     foreach ($data as $row) {
//         $key = $row->nis . '-' . $row->mapel;
//         $row->total_hadir = $totalHadir[$key] ?? 0;

//         $izin = $izinData[$row->nis] ?? null;
//         $row->total_izin = $izin->total_izin ?? 0;
//         $row->total_sakit = $izin->total_sakit ?? 0;
//     }

//     return $data;
// }
