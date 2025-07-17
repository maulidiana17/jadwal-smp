<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QRValidasi extends Model
{
    use HasFactory;
    protected $table = 'qr_validasi';

    protected $fillable = [
        'kode_qr', 'tanggal', 'created_at', 'expired_at'
    ];
}
