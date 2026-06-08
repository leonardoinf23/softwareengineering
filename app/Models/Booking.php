<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    
    protected $table = 'booking'; 

    // primary key-nya
    protected $primaryKey = 'id_booking';

    // Karena di SQL kita cuma pake created_at (tanpa updated_at), matikan timestamps bawaan
    public $timestamps = false; 

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'id_pemain', 'id_lapangan', 'tanggal', 'jam_mulai', 'jam_selesai', 'status'
    ];

    // Relasi ke tabel Lapangan
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class, 'id_lapangan', 'id_lapangan');
    }

    // Relasi ke tabel Pemain
    public function pemain()
    {
        return $this->belongsTo(Pemain::class, 'id_pemain', 'id_pemain');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_booking', 'id_booking');
    }
}