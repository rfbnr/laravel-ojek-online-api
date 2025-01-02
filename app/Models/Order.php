<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_pengemudi',
        'longitute_jemput',
        'latitude_jemput',
        'longitute_tujuan',
        'latitude_tujuan',
        'waktu_order',
        'waktu_terima_order',
        'waktu_jemput',
        'waktu_sampai',
        'total_harga',
        'harga_bersih',
        'status_perjalanan',
        'id_metode_pembayaran',
    ];

    public function pengemudi()
    {
        return $this->belongsTo(Pengemudi::class, 'id_pengemudi');
    }

    public function ulasan()
    {
        return $this->hasOne(Ulasan::class, 'id_order');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
