<?php

namespace App\Imports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    
    */
    
    public function model(array $row)
    {
        return new Guru([
            'kode_guru' => $row['kode'],
            'nama' => $row['nama'],
            'nip' => $row['nip'],
            'email' => $row['email'],
            'alamat' => $row['alamat'],
        ]);
    }
}
