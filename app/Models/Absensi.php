<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'tanggal',
    'waktu_check_in',
    'waktu_check_out',
    'koordinat_in',
    'koordinat_out',
    'status_kehadiran',
    'users_id',
    'qr_code_id'
])]
class Absensi extends Model
{
    protected $table = 'absensi';

    /**
     * Relasi: Absensi milik satu User (Karyawan)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Relasi: Absensi menggunakan satu QrCode tertentu
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QrCode::class, 'qr_code_id');
    }
}