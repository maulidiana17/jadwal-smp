<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;
     protected $table = 'guru';
    protected $fillable = ['kode_guru', 'nama', 'nip', 'email', 'alamat'];

    public function pengampu()
    {
        return $this->hasMany(Pengampu::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
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
