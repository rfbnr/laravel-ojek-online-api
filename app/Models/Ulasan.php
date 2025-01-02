<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ulasan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_order',
        'id_user',
        'rating',
        'komentar',
        'waktu_ulasan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order', 'id');
    }
}
