<?php

namespace App\Http\Controllers;

use App\Models\Merchandise;
use App\Models\Pembeli;
use App\Models\PenukaranReward;
use Illuminate\Http\Request;

class PenukaranRewardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penukaranRewards = PenukaranReward::all();
        return response()->json([
            'status' => 'success',
            'data' => $penukaranRewards,
            'message' => 'List of Penukaran Rewards'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pembeli' => 'required|exists:pembelis,id_pembeli',
            'id_merchandise' => 'required|exists:merchandises,id_merchandise',
        ]);
        $pembeli = Pembeli::findOrFail($request->id_pembeli);
        $merch = Merchandise::findOrFail($request->id_merchandise);

        if($pembeli->poin_reward < $merch->poin)
        {
            return response()->json([
                'status' => false,
                'message' => 'Poin tidak cukup'
            ], 400);
        } else {
            $penukaran = PenukaranReward::create([
                'id_pembeli' => $request->id_pembeli,
                'id_merchandise' => $request->id_merchandise,
                'tanggal_penukaran' => now(),
            ]);

            $pembeli->decrement('poin_reward', $merch->poin);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil melakukan penukaran',
                'data' => $penukaran
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PenukaranReward $penukaranReward)
    {
        $penukaran = PenukaranReward::find($penukaranReward->id_penukaran);

        if($penukaran) {
            return response()->json([
                'status' => true,
                'message' => 'Detail Penukaran Reward',
                'data' => $penukaran
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Penukaran Reward not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PenukaranReward $penukaranReward)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PenukaranReward $penukaranReward)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PenukaranReward $penukaranReward)
    {
        //
    }
}
