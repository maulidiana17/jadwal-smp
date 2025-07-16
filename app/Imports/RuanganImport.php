<?php

namespace App\Imports;

use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuanganImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Ruangan([
            'kode_ruangan' => $row['kode_ruang'],
            'nama' => $row['nama'],
            'kapasitas' => $row['kapasitas'],
            'fasilitas' => $row['fasilitas'],
        ]);
    }
}
