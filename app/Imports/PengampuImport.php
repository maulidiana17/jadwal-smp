<?php

namespace App\Imports;

use App\Models\Pengampu;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PengampuImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    
    */
    
    public function model(array $row)
    {
        return new Pengampu([
            'nama' => $row['nama'],
            'mapel' => $row['mapel'],
        ]);
    }
}
