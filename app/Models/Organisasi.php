<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisasi extends Model
{
    /** @use HasFactory<\Database\Factories\OrganisasiFactory> */
    use HasFactory;

    protected $table = 'organisasis';
    protected $primaryKey = 'id_organisasi';
    protected $fillable =[
        'nama_organisasi',
        'alamat_organisasi',
    ];
    public $timestamps = false;

    public function request_donasis()
    {
        return $this->hasMany(RequestDonasi::class, 'id_organisasi', 'id_organisasi');
    }

    public function donasis()
    {
        return $this->hasMany(Donasi::class, 'id_organisasi', 'id_organisasi');
    }

    public function pengguna()
    {
        return $this->hasOne(Pengguna::class, 'id_organisasi', 'id_organisasi');
    }
}
