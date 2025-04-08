<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RincianPenjualan extends Model
{
    /** @use HasFactory<\Database\Factories\RincianPenjualanFactory> */
    use HasFactory;

    protected $table = 'rincian_penjualans';
    protected $primaryKey = 'id_rincian_penjualan';

    protected $fillable = [
        'id_rincian_penjualan',
        'nota_penjualan',
        'kode_barang'
    ];
    public $timestamps = false;

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'nota_penjualan', 'nota_penjualan');
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
