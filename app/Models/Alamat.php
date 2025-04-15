<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    /** @use HasFactory<\Database\Factories\AlamatFactory> */
    use HasFactory;

    protected $table = 'alamats';
    protected $primaryKey = 'id_alamat';
    public $timestamps = false;

    protected $fillable =[
        'detail_alamat'
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'id_alamat', 'id_alamat');
    }
}
