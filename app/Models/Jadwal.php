<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal'; // atau 'jadwals'

    protected $fillable = [
        'waktu_id',
        'kelas_id',
        'mapel_id',
        'guru_id',
        'ruangan_id',
    ];

    // Relasi
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function waktu()
    {
        return $this->belongsTo(Waktu::class);
    }

}
