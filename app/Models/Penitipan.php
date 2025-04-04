<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penitipan extends Model
{
    /** @use HasFactory<\Database\Factories\PenitipanFactory> */
    use HasFactory;

    protected $table = 'penitipans';
    protected $primaryKey = 'nota_penitipan';
    protected $fillable = [
        'tanggal_penitipan',
        'masa_penitipan',
        'id_penitip',
        'id_pegawai',
        'id_hunter'
    ];

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip', 'id_penitip');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function hunter()
    {
        return $this->belongsTo(Hunter::class, 'id_hunter', 'id_hunter');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'nota_penitipan', 'nota_penitipan');
    }
}
