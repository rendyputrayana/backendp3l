<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchandise extends Model
{
    //

    protected $table = 'merchandises';
    protected $primaryKey = 'id_merchandise';
    protected $fillable =[
        'nama_merchandise',
        'poin'
    ];
}
