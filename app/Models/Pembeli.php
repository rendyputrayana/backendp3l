<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembeli extends Model
{
    /** @use HasFactory<\Database\Factories\PembeliFactory> */
    use HasFactory;

    protected $table = 'pembelis';
    protected $primaryKey = 'id_pembeli';
    protected $fillable = [
        'nama_pembeli',
        'poin_reward'
    ];
    public $timestamps = false;

    public function alamats()
    {
        return $this->hasMany(Alamat::class, 'id_pembeli', 'id_pembeli');
    }

    public function penukaranRewards()
    {
        return $this->hasMany(PenukaranReward::class, 'id_pembeli', 'id_pembeli');
    }

    public function detailKeranjangs()
    {
        return $this->hasMany(DetailKeranjang::class, 'id_pembeli', 'id_pembeli');
    }

    public function diskusiProduks()
    {
        return $this->hasMany(DiskusiProduk::class, 'id_pembeli', 'id_pembeli');
    }

    public function pengguna()
    {
        return $this->hasOne(Pengguna::class, 'id_pembeli', 'id_pembeli');
    }
}
