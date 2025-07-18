<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $row = array_change_key_case($row, CASE_LOWER);
        return new Siswa([
            'nis' => (string) $row['nis'],
            'nama_lengkap' => $row['nama'],
            'kelas' => $row['kelas'],
            'no_hp' => (string) $row['no_hp_ortu'],
            'password' => bcrypt($row['nis']),
            // 'password' => bcrypt('123456'),
        ]);

    }
}
