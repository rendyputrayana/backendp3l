<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;
use App\Models\Penitip;
use App\Models\AkumulasiRating;
use Illuminate\Support\Facades\Artisan;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $badges = Badge::all();
        return response()->json([
            'status' => true,
            'message' => 'List Badge',
            'data' => $badges
        ], 200);
    }

    public function TopSeller()
    {
        $badges = Badge::with('penitip.akumulasi')->orderBy('id_badge', 'desc')->first();

        if ($badges) {
            return response()->json([
                'status' => true,
                'message' => 'Top Seller Badge',
                'data' => $badges
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada Top Seller ditemukan.',
                'data' => null
            ], 404);
        }
    }

    public function TopSellerCommand(){
        Artisan::call('penjualan:top-seller');

        $output = Artisan::output();
        
        return response()->json([
            'status' => true,
            'message' => 'Top Seller Command executed successfully',
            'data' => $output
        ]);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_badge' => 'required|string',
            'logo_badge' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_penitip' => 'required|exists:penitips,id_penitip',
        ]);
        $badge = Badge::create([
            'nama_badge' => $request->nama_badge,
            'logo_badge' => $request->file('logo_badge')->store('badges', 'public'),
            'id_penitip' => $request->id_penitip,
         
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Badge berhasil ditambahkan',
            'data' => $badge
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Badge $badge)
    {
        $badge = Badge::find($badge->id_badge);
        return response()->json([
            'status' => true,
            'message' => 'Detail Badge',
            'data' => $badge
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Badge $badge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Badge $badge)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Badge $badge)
    {
        //
    }
}
