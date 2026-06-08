<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemain extends Model
{
    protected $table = 'pemain'; 
    
    protected $primaryKey = 'id_pemain';
    
    public $timestamps = false; 

    protected $fillable = ['nama', 'email', 'password'];
}