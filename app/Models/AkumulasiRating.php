<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkumulasiRating extends Model
{
    /** @use HasFactory<\Database\Factories\AkumulasiRatingFactory> */
    use HasFactory;

    protected $table = 'akumulasi_ratings';
    protected $primaryKey = 'id_akumulasi';
    protected $fillable = [
        'akumulasi',
        'id_penitip',
    ];

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip', 'id_penitip');
    }
}
