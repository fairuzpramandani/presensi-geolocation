<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanIzin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_izin';
    protected $primaryKey = 'id';
    protected $fillable = [
        'email',
        'tgl_izin',
        'status_izin',
        'keterangan',
        'status_approved',
    ];
}
