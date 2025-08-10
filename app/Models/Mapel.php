<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;
    protected $table = 'mapel';
    protected $fillable = ['kode_mapel', 'mapel', 'jam_per_minggu', 'ruang_khusus'];

    public function pengampu()
    {
        return $this->hasMany(Pengampu::class);
    }

    public function guru()
    {
     return $this->belongsToMany(Guru::class, 'pengampu');
    }
    
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
