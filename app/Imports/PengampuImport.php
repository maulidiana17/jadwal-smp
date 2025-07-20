<?php
namespace App\Imports;

use App\Models\Pengampu;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PengampuImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cari guru berdasarkan email
        $guru = Guru::where('email', $row['guru_email'])->first();
        $mapel = Mapel::where('kode_mapel', $row['mapel_kode'])->first();
        $kelas = Kelas::where('nama', $row['kelas_nama'])->first();

        if (!$guru || !$mapel || !$kelas) {
            return null; // Lewati jika ada data tidak ditemukan
        }

        // Cek duplikat sebelum simpan
        $exists = Pengampu::where('guru_id', $guru->id)
                          ->where('mapel_id', $mapel->id)
                          ->where('kelas_id', $kelas->id)
                          ->exists();

        if (!$exists) {
            return new Pengampu([
                'guru_id' => $guru->id,
                'mapel_id' => $mapel->id,
                'kelas_id' => $kelas->id,
            ]);
        }

        return null;
    }
}
