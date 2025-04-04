<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    /** @use HasFactory<\Database\Factories\BarangFactory> */
    use HasFactory;

    protected $table = 'barangs';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'id_subkategori',
        'id_donasi',
        'nota_penitipan',
        'nama_barang',
        'harga_barang',
        'rating_barang',
        'status_barang',
        'komisi_penitip',
        'komisi_hunter',
        'komisi_reuseMart',
        'perpanjang'
    ];

    public function subkategori()
    {
        return $this->belongsTo(Subkategori::class, 'id_subkategori', 'id_subkategori');
    }

    public function donasi()
    {
        return $this->belongsTo(Donasi::class, 'id_donasi', 'id_donasi');
    }

    public function penitipan()
    {
        return $this->belongsTo(Penitipan::class, 'nota_penitipan', 'nota_penitipan');
    }

    public function rincianPenjualans()
    {
        return $this->hasMany(RincianPenjualan::class, 'kode_barang', 'kode_barang');
    }

    public function detailKeranjangs()
    {
        return $this->hasMany(DetailKeranjang::class, 'kode_produk', 'kode_produk');
    }

    public function diskusiProduks()
    {
        return $this->hasMany(DiskusiProduk::class, 'kode_produk', 'kode_produk');
    }
}
