<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subkategori extends Model
{
    //

    protected $table = 'subkategoris';
    protected $primaryKey = 'id_subkategori';
    protected $fillable = [
        'nama_subkategori',
        'id_kategori'
    ];
    public $timestamps = false;

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'id_subkategori', 'id_subkategori');
    }
}
