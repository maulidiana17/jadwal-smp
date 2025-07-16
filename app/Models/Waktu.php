<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waktu extends Model
{
    use HasFactory;
    protected $table = 'waktu';
    protected $fillable = ['hari', 'jam_ke','jam_mulai', 'jam_selesai','ket'];

    /**
     * Relasi: waktu digunakan dalam banyak jadwal.
     */
    // public function jadwals()
    // {
    //     return $this->hasMany(Jadwal::class);
    // }

    /**
     * Optional: Format tampilan waktu
     */
    public function getLabelAttribute()
    {
        return "{$this->hari} ({$this->jam} - {$this->ket})";
    }

     public function guru()
    {
        return $this->belongsTo(Guru::class);
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
