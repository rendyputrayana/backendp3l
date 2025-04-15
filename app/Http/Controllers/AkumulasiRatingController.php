<?php

namespace App\Http\Controllers;

use App\Models\AkumulasiRating;
use Illuminate\Http\Request;

class AkumulasiRatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $akumulasiRatings = AkumulasiRating::all();
        return response()->json([
            'status' => true,
            'message' => 'List Akumulasi Rating',
            'data' => $akumulasiRatings
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
    public function show(AkumulasiRating $akumulasiRating)
    {
        $akumulasiRating = AkumulasiRating::find($akumulasiRating->id_akumulasi_rating);
        return response()->json([
            'status' => true,
            'message' => 'Detail Akumulasi Rating',
            'data' => $akumulasiRating
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AkumulasiRating $akumulasiRating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AkumulasiRating $akumulasiRating)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AkumulasiRating $akumulasiRating)
    {
        //
    }
}
