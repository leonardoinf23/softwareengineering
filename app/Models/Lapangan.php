<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    // Beritahu Laravel nama tabel aslinya di phpMyAdmin
    protected $table = 'lapangan'; 
    
    // Beritahu nama primary key-nya
    protected $primaryKey = 'id_lapangan';
    
    public $timestamps = false; 

    protected $fillable = ['nama_lapangan', 'harga_per_jam'];
}