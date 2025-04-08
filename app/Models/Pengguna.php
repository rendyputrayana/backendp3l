<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    /** @use HasFactory<\Database\Factories\PenggunaFactory> */
    use HasFactory;

    protected $table = 'penggunas';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false;
    protected $fillable = [
        'username',
        'password',
        'role'
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
}
