<?php

namespace App\Imports;

use App\Models\Waktu;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WaktuImport implements ToModel, WithHeadingRow 
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Waktu([
           'hari' => $row['hari'],
           'jam_ke' => $row['jam_ke'],
           'jam_mulai' => $row['jam_mulai'],
           'jam_selesai' => $row['jam_selesai'],
           'ket' => $row['ket'],
        ]);
    }
}
