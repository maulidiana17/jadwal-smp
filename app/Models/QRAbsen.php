<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QRAbsen extends Model
{
    use HasFactory;
    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'mapel',
        'nip',
        'waktu',
    ];
    protected $guarded = ['id'];
    protected $table = 'qr_absens';
// Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis');
    }

    // Relasi ke Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'nip', 'nip');
    }
}



