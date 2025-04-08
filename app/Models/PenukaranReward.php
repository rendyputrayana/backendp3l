<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenukaranReward extends Model
{
    /** @use HasFactory<\Database\Factories\PenukaranRewardFactory> */
    use HasFactory;

    protected $table = 'penukaran_rewards';
    protected $primaryKey = 'id_penukaran';

    protected $fillable = [
        'tanggal_penukaran',
        'id_pembeli',
        'id_merchandise',
    ];
    public $timestamps = false;

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }
    public function merchandise()
    {
        return $this->belongsTo(Merchandise::class, 'id_merchandise', 'id_merchandise');
    }
}
