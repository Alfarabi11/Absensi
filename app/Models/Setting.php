<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'key_name',
    'value',
    'keterangan'
])]
class Setting extends Model
{
    protected $table = 'settings';
}