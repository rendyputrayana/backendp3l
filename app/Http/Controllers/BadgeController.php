<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;

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
