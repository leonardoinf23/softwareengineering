<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    public $timestamps = false; // Menggunakan format timestamp bawaan SQL

    protected $fillable = [
        'id_booking', 'metode_pembayaran', 'jumlah_bayar', 'bukti_transfer', 'status_pembayaran'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking', 'id_booking');
    }
}