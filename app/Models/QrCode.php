<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'token_qr',
    'waktu_dibuat',
    'waktu_kadaluwarsa',
    'status_aktif'
])]
class QrCode extends Model
{
    protected $table = 'qr_code';

    /**
     * Relasi: 1 QrCode dapat dipindai oleh banyak data Absensi
     */
    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'qr_code_id');
    }
}