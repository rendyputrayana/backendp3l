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

    protected $fillable = [
        'isi_diskusi',
        'tanggal_diskusi',
        'id_pembeli',
        'kode_produk',
        'id_penitip'
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_produk', 'kode_produk');
    }
    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip', 'id_penitip');
    }
}
