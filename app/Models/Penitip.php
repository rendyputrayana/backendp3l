<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penitip extends Model
{
    /** @use HasFactory<\Database\Factories\PenitipFactory> */
    use HasFactory;

    protected $table = 'penitips';
    protected $primaryKey = 'id_penitip';
    protected $fillable = [
        'nama_penitip',
        'no_telepon',
        'alamat_penitip',
        'saldo',
        'foto_ktp',
        'no_ktp',
    ];
    public $timestamps = false;

    public function badges()
    {
        return $this->hasMany(Badge::class, 'id_penitip', 'id_penitip');
    }

    public function akumulasi()
    {
        return $this->hasOne(AkumulasiRating::class, 'id_penitip', 'id_penitip');
    }

    public function penitipans()
    {
        return $this->hasMany(Penitipan::class, 'id_penitip', 'id_penitip');
    }
    public function diskusiProduks()
    {
        return $this->hasMany(DiskusiProduk::class, 'id_penitip', 'id_penitip');
    }
    public function pengguna()
    {
        return $this->hasOne(Pengguna::class, 'id_penitip', 'id_penitip');
    }
}
