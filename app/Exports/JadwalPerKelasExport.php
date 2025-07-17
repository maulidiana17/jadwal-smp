<?php

namespace App\Exports;

use App\Models\Jadwal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JadwalPerKelasExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $kelasId;

    public function __construct($kelasId)
    {
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        return Jadwal::with(['kelas', 'guru', 'mapel', 'ruangan', 'waktu'])
            ->where('kelas_id', $this->kelasId)->get();
    }

    public function headings(): array
    {
        return ['Hari', 'Jam ke', 'Mapel', 'Guru', 'Ruangan'];
    }

    public function map($row): array
    {
        return [
            $row->waktu->hari,
            $row->waktu->jam_ke,
            $row->mapel->mapel,
            $row->guru->nama,
            $row->ruangan->nama,
        ];
    }
}
