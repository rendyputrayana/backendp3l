<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hunter extends Model
{
    /** @use HasFactory<\Database\Factories\HunterFactory> */
    use HasFactory;

    protected $table = 'hunters';
    protected $primaryKey = 'id_hunter';
    protected $fillable = [
        'nama_hunter',
        'saldo',
        'no_telepon'
    ];
    public $timestamps = false;

    public function penitipans()
    {
        return $this->hasMany(Penitipan::class, 'id_hunter', 'id_hunter');
    }

    public function pengguna()
    {
        return $this->hasOne(Pengguna::class, 'id_hunter', 'id_hunter');
    }
}
