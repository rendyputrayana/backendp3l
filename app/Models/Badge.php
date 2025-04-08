<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    //

    protected $table = 'badges';
    protected $primaryKey = 'id_badge';
    protected $fillable = [
        'nama_badge',
        'logo_badge'
    ];
    public $timestamps = false;

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip', 'id_penitip');
    }
}
