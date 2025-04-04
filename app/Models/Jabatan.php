<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    //

    protected $table = 'jabatans';
    protected $primaryKey = 'id';
    protected $fillable=[
        'nama_jabatan'
    ];
}
