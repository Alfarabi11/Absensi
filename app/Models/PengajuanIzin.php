<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanIzin extends Model
{
    protected $table = 'pengajuan_izin';

    // Gunakan format standar fillable Laravel
    protected $fillable = [
        'jenis_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'berkas_lampiran',
        'keterangan',
        'status_persetujuan',
        'tanggal_persetujuan',
        'users_id' // Sesuaikan dengan database (user_id, bukan users_id)
    ];

    /**
     * Relasi: Pengajuan izin diajukan oleh satu User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id'); // Foreign key disesuaikan ke user_id
    }
}