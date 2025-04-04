<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKeranjang extends Model
{
    /** @use HasFactory<\Database\Factories\DetailKeranjangFactory> */
    use HasFactory;

    protected $table = 'detail_keranjangs';
    protected $primaryKey = 'id_keranjang';

    protected $fillable = [
        'kode_produk',
        'id_pembeli',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_produk', 'kode_produk');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }
}
