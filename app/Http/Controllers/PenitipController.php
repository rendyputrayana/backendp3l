<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use Illuminate\Http\Request;
use App\Models\Badge;
use App\Models\AkumulasiRating;

class PenitipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penitips = Penitip::all();
        return response()->json([
            'status' => true,
            'message' => 'List Penitip',
            'data' => $penitips
        ], 200);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Penitip $penitip)
    {
        $penitip = Penitip::with('akumulasi', 'badges')->find($penitip->id_penitip);
        if ($penitip) {
            return response()->json([
                'status' => true,
                'message' => 'Detail Penitip',
                'data' => $penitip
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Penitip not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penitip $penitip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penitip $penitip)
    {
        $request->validate([
            'nama_penitip' => 'string',
            'no_telepon' => 'string',
            'alamat_penitip' => 'string',
            'saldo' => 'numeric',
        ]);

        $penitip->update([
            'nama_penitip' => $request->nama_penitip,
            'no_telepon' => $request->no_telepon,
            'alamat_penitip' => $request->alamat_penitip,
            'saldo' => $request->saldo,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Penitip updated successfully',
            'data' => $penitip
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penitip $penitip)
    {
        $penitip = Penitip::find($penitip->id_penitip);
        $penitip->delete();
        if (!$penitip) {
            return response()->json([
                'status' => false,
                'message' => 'Penitip not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Penitip deleted successfully'
        ], 200);
    }

    public function search($query)
    {
        $penitips = Penitip::where('nama_penitip', 'LIKE', "%$query%")
            ->get();

        if ($penitips->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Penitip not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Search results',
            'data' => $penitips
        ], 200);
    }
}
