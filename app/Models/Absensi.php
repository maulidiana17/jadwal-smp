<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'absensi';

    protected $fillable = [
        'nis', 'nama_lengkap', 'kelas', 'tgl_absen', 'jam_masuk', 'jam_keluar',
        'foto_masuk', 'foto_keluar', 'location_masuk', 'location_keluar'
    ];

}
