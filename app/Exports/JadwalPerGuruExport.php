<?php

namespace App\Exports;

use App\Models\Jadwal;
use App\Models\Guru;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class JadwalPerGuruExport implements FromArray, WithHeadings, WithTitle
{
    protected $guru;

    public function __construct($guru)
    {
        $this->guru = $guru;
    }

    public function array(): array
    {
        $jadwals = Jadwal::with(['kelas', 'waktu', 'mapel', 'ruangan'])
            ->where('guru_id', $this->guru->id)
            ->get();

        // Susun data seperti: [Kelas, Hari, Jam ke, Mapel, Ruangan]
        $data = [];

        foreach ($jadwals as $item) {
            $data[] = [
                'kelas'    => $item->kelas->nama,
                'hari'     => $item->waktu->hari,
                'jam_ke'   => $item->waktu->jam_ke,
                'mapel'    => $item->mapel->kode_mapel ?? $item->mapel->mapel,
                'ruangan'  => $item->ruangan->nama,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return ['Kelas', 'Hari', 'Jam ke-', 'Mapel', 'Ruangan'];
    }

    public function title(): string
    {
        return 'Jadwal Guru ' . $this->guru->nama;
    }

}
