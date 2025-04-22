<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Subkategori;
use Illuminate\Http\Request;

class SubkategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subkategoris = Subkategori::all();
        return response()->json([
            'status' => true,
            'message' => 'List Subkategori',
            'data' => $subkategoris
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
    public function show(Kategori $kategori)
    {
        $subkategoris = Subkategori::where('id_kategori', $kategori->id_kategori)->get();
        return response()->json([
            'status' => true,
            'message' => 'List Subkategori',
            'data' => $subkategoris
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subkategori $subkategori)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subkategori $subkategori)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subkategori $subkategori)
    {
        //
    }
}
