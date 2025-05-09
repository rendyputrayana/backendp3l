<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoBarang extends Model
{
    /** @use HasFactory<\Database\Factories\FotoBarangFactory> */
    use HasFactory;

    protected $table = 'foto_barangs';
    protected $primaryKey = 'id_foto';
    public $timestamps = false;
    protected $fillable = [
        'kode_produk',
        'foto_barang',
    ];
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_produk', 'kode_produk');
    }
}
