<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    /** @use HasFactory<\Database\Factories\DonasiFactory> */
    use HasFactory;

    protected $table = 'donasis';
    protected $primaryKey = 'id_donasi';
    protected $fillable = [
        'tanggal_donasi',
        'id_organisasi',
        'nama_penerima'
    ];
    public $timestamps = false;

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'id_organisasi', 'id_organisasi');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'id_donasi', 'id_donasi');
    }
}
