<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDonasi extends Model
{
    /** @use HasFactory<\Database\Factories\RequestDonasiFactory> */
    use HasFactory;

    protected $table = 'request_donasis';
    protected $primaryKey = 'id_request';

    protected $fillable = [
        'detail_request',
        'id_organisasi'
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'id_organisasi', 'id_organisasi');
    }
}
