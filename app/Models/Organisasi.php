<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisasi extends Model
{
    /** @use HasFactory<\Database\Factories\OrganisasiFactory> */
    use HasFactory;

    protected $table = 'organisasi';
    protected $primaryKey = 'id_organisasi';
    protected $fillable =[
        'nama_organisasi',
        'alamat_organisasi',
        'password_organisasi'
    ];
}
