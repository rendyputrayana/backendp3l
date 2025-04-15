<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    /** @use HasFactory<\Database\Factories\PenjualanFactory> */
    use HasFactory;

    protected $table = 'penjualans';
    protected $primaryKey = 'nota_penjualan';

    protected $fillable = [
        'tanggal_transaksi',
        'tanggal_lunas',
        'total_harga',
        'status_penjualan',
        'ongkos_kirim',
        'tanggal_diterima',
        'status_pengiriman',
        'metode_pengiriman',
        'bukti_pembayaran',
        'id_alamat'
    ];
    public $timestamps = false;
    
    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'id_alamat', 'id_alamats');
    }

    public function rincianPenjualans()
    {
        return $this->hasMany(RincianPenjualan::class, 'nota_penjualan', 'nota_penjualan');
    }
    
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}
