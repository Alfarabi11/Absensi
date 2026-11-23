<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'hari',
    'waktu_masuk',
    'waktu_keluar',
    'batas_keterlambatan'
])]
class JamKerja extends Model
{
    protected $table = 'jam_kerja';
}