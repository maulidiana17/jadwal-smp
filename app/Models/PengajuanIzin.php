<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanIzin extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_izin';

    protected $fillable = [
        'nis', 'nama_lengkap', 'kelas', 'tanggal_izin', 'tanggal_izin_akhir',
        'status', 'keterangan', 'file_surat', 'status_approved', 'catatan_penolakan'
    ];
}
