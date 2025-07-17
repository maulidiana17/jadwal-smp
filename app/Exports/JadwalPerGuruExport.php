<?php

namespace App\Exports;

use App\Models\Jadwal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JadwalPerGuruExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $guruId;

    public function __construct($guruId)
    {
        $this->guruId = $guruId;
    }

    public function collection()
    {
        return Jadwal::with(['kelas', 'guru', 'mapel', 'ruangan', 'waktu'])
            ->where('guru_id', $this->guruId)->get();
    }

    public function headings(): array
    {
        return ['Hari', 'Jam ke', 'Kelas', 'Mapel', 'Ruangan'];
    }

    public function map($row): array
    {
        return [
            $row->waktu->hari,
            $row->waktu->jam_ke,
            $row->kelas->nama,
            $row->mapel->mapel,
            $row->ruangan->nama,
        ];
    }
}
