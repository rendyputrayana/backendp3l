<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    /** @use HasFactory<\Database\Factories\AlamatFactory> */
    use HasFactory;

    protected $table = 'alamats';
    protected $primaryKey = 'id_alamat';

    protected $fillable =[
        'detail_alamat'
    ];
}
