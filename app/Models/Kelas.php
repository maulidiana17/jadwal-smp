<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';
    protected $fillable = ['nama', 'tingkat_kelas'];

    public function guru()
    {
     return $this->belongsToMany(Guru::class, 'pengampu');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function pengampu()
    {
        return $this->hasMany(Pengampu::class);
    }
}
