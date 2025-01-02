<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MetodePembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_metode_pembayaran',
    ];
}
