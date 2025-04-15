<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Pengguna extends Model
{
    /** @use HasFactory<\Database\Factories\PenggunaFactory> */
    use HasFactory, HasApiTokens;

    protected $table = 'penggunas';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false;
    protected $fillable = [
        'email',
        'username',
        'password',
        'id_pembeli',
        'id_pegawai',
        'id_penitip',
        'id_organisasi',
        'id_hunter',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip', 'id_penitip');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }

    public function hunter()
    {
        return $this->belongsTo(Hunter::class, 'id_hunter', 'id_hunter');
    }
    
    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'id_organisasi', 'id_organisasi');
    }

    public function getRoleAttribute()
    {
        if ($this->id_pembeli) {
            return 'Pembeli';
        } elseif ($this->id_pegawai) {
            return 'Pegawai';
        } elseif ($this->id_penitip) {
            return 'Penitip';
        } elseif ($this->id_organisasi) {
            return 'Organisasi';
        } elseif ($this->id_hunter) {
            return 'Hunter';
        }

        return 'Unknown';
    }
}
