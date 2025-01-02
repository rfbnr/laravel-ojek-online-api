<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengemudi extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'nama_pengemudi',
        'email_pengemudi',
        'phone_pengemudi',
        'merek_tipe_kendaraan',
        'plat_nomor',
        'status_pengemudi',
        'longitude',
        'latitude',
        'tgl_registrasi',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}
