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
    public $timestamps = false;

    public function penukaranRewards()
    {
        return $this->hasMany(PenukaranReward::class, 'id_merchandise', 'id_merchandise');
    }
}
