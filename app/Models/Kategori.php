<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    //

    protected $table = 'kategoris';
    protected $primaryKey = 'id_kategori';
    protected $fillable = [
        'nama_kategori',
    ];
    public $timestamps = false;

    public function subkategoris()
    {
        return $this->hasMany(Subkategori::class, 'id_kategori', 'id_kategori');
    }
}
