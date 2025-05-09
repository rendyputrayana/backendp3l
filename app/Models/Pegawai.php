<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    /** @use HasFactory<\Database\Factories\PegawaiFactory> */
    use HasFactory;

    protected $table = 'pegawais';
    protected $primaryKey = 'id_pegawai';
    protected $fillable = [
        'nama_pegawai',
        'tanggal_lahir',
        'id_jabatan',
    ];
    public $timestamps = false;

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    public function penitipans()
    {
        return $this->hasMany(Penitipan::class, 'id_pegawai', 'id_pegawai');
    }

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'id_pegawai', 'id_pegawai');
    }

    public function pengguna()
    {
        return $this->hasOne(Pengguna::class, 'id_pegawai', 'id_pegawai');
    }

    public function diskusiProduks()
    {
        return $this->hasMany(DiskusiProduk::class, 'id_pegawai', 'id_pegawai');
    }
}
