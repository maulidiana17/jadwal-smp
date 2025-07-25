<?php

namespace App\Imports;

use App\Models\Mapel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MapelImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Mapel([
            'kode_mapel' => $row['kode_mapel'],
            'mapel' => $row['mapel'],
            'jam_per_minggu' => $row['jam_per_minggu'],
            'ruang_khusus' => $row['ruang_khusus'],
        ]);
    }
}
