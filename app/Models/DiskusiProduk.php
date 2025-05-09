<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiskusiProduk extends Model
{
    /** @use HasFactory<\Database\Factories\DiskusiProdukFactory> */
    use HasFactory;

    protected $table = 'diskusi_produks';
    protected $primaryKey = 'id_diskusi';
    public $timestamps = false;
    protected $fillable = [
        'isi_diskusi',
        'tanggal_diskusi',
        'id_pembeli',
        'kode_produk',
        'id_pegawai'
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_produk', 'kode_produk');
    }
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}
