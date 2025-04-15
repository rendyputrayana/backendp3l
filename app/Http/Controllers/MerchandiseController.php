<?php

namespace App\Http\Controllers;

use App\Models\Merchandise;
use Illuminate\Http\Request;

class MerchandiseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $merchandises = Merchandise::all(); 
        return response()->json([
            'status' => true,
            'message' => 'List Merchandise',
            'data' => $merchandises
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
            'nama_merchandise' => 'required|string|max:255',
            'poin' => 'required|integer',
        ]);

        $merchandise = Merchandise::create([
            'nama_merchandise' => $request->nama_merchandise,
            'poin' => $request->point,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Merchandise berhasil ditambahkan',
            'data' => $merchandise
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Merchandise $merchandise)
    {
        $merchandise = Merchandise::find($merchandise->id_merchandise);
        return response()->json([
            'status' => true,
            'message' => 'Detail Merchandise',
            'data' => $merchandise
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Merchandise $merchandise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Merchandise $merchandise)
    {
        $request->validate([
            'nama_merchandise' => 'required|string|max:255',
            'poin' => 'required|integer',
        ]);
        $merchandise->update([
            'nama_merchandise' => $request->nama_merchandise,
            'poin' => $request->point,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Merchandise berhasil diupdate',
            'data' => $merchandise
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Merchandise $merchandise)
    {
        $merchandise = Merchandise::find($merchandise->id_merchandise);
        $merchandise->delete();
        return response()->json([
            'status' => true,
            'message' => 'Merchandise berhasil dihapus',
        ], 200);
    }
}
