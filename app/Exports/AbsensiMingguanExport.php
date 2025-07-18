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
        $kelasGuru = DB::table('jadwal')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->where('jadwal.guru_id', auth()->user()->guru->id)
            ->pluck('kelas.nama')
            ->unique();

        $mapelGuru = DB::table('jadwal')
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

