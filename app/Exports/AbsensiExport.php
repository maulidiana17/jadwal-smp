<?php

namespace App\Exports;

use App\Models\QRAbsen;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsensiExport implements FromCollection, WithHeadings

{
    protected $awal;
    protected $akhir;
    protected $namaGuru;

    public function __construct($awal, $akhir)
    {
        $this->awal = $awal;
        $this->akhir = $akhir;

        $user = auth()->user();
        $this->namaGuru = $user->name ?? '-';
    }

    public function collection()
    {
        $jadwal = DB::connection('mysql_spensa')
            ->table('jadwal')
            ->join('kelas', 'jadwal.kelas_id', '=', 'kelas.id')
            ->join('mapel', 'jadwal.mapel_id', '=', 'mapel.id')
            ->where('jadwal.guru_id', auth()->user()->guru->id)
            ->select('kelas.nama as kelas', 'mapel.mapel')
            ->get();

        $kelasGuru = $jadwal->pluck('kelas')->unique();
        $mapelGuru = $jadwal->pluck('mapel')->unique();

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


        $awal = $this->awal;
        $akhir = $this->akhir;

        $izin = DB::table('pengajuan_izin')
            ->where('status_approved', 1)
            ->where(function ($query) use ($awal, $akhir) {
                $query->whereBetween('tanggal_izin', [$awal, $akhir])
                    ->orWhereBetween('tanggal_izin_akhir', [$awal, $akhir])
                    ->orWhere(function ($query) use ($awal, $akhir) {
                        $query->where('tanggal_izin', '<=', $awal)
                                ->where('tanggal_izin_akhir', '>=', $akhir);
                    });
            })
            ->select('nis', 'status', 'tanggal_izin', 'tanggal_izin_akhir')
            ->get()
            ->groupBy('nis');

        $hariAktif = collect(CarbonPeriod::create($this->awal, $this->akhir))
            ->filter(fn($date) => in_array($date->dayOfWeek, [1, 2, 3, 4, 5, 6])) // Senin–Sabtu
            ->count();

        $results = collect();

        foreach ($siswa as $s) {
            $mapels = $jadwal->where('kelas', $s->kelas)->pluck('mapel');

            foreach ($mapels as $mapel) {
                $key = $s->nis . '|' . $mapel;
                $hadir = $absensi->has($key) ? $absensi[$key]->first()->total : 0;

                $izinTotal = 0;
                $sakitTotal = 0;

                if ($izin->has($s->nis)) {
                    foreach ($izin[$s->nis] as $izinItem) {
                        $start = Carbon::parse($izinItem->tanggal_izin);
                        $end = Carbon::parse($izinItem->tanggal_izin_akhir);
                        $period = CarbonPeriod::create($start, $end);

                        foreach ($period as $date) {
                            if ($date->between($this->awal, $this->akhir) && in_array($date->dayOfWeek, [1, 2, 3, 4, 5, 6]))
                            {
                                if ($izinItem->status === 'i') {
                                    $izinTotal++;
                                } elseif ($izinItem->status === 's') {
                                    $sakitTotal++;
                                }
                            }
                        }
                    }
                }

                $tidakHadir = max(0, $hariAktif - ($hadir + $izinTotal + $sakitTotal));

                if ($hadir > 0 || $izinTotal > 0 || $sakitTotal > 0 || $tidakHadir > 0) {
                    $results->push([
                        'nis' => $s->nis,
                        'nama_lengkap' => $s->nama_lengkap,
                        'kelas' => $s->kelas,
                        'mapel' => $mapel,
                        'total_hadir' => $hadir,
                        'total_sakit' => $sakitTotal,
                        'total_izin' => $izinTotal,
                        'total_tidak_hadir' => $tidakHadir,
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
            'Total Sakit',
            'Total Izin',
            'Total Tidak Hadir',
            'Nama Guru',
            'Periode',
        ];
    }
}

