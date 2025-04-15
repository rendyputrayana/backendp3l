<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jabatans = Jabatan::all();
        return response()->json([
            'status' => true,
            'message' => 'List Jabatan',
            'data' => $jabatans
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
            'nama_jabatan' => 'required|string|max:255',
        ]);

        $jabatan = Jabatan::create([
            'nama_jabatan' => $request->nama_jabatan,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Jabatan berhasil ditambahkan',
            'data' => $jabatan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Jabatan $jabatan)
    {
        $jabatan = Jabatan::find($jabatan->id_jabatan);
        return response()->json([
            'status' => true,
            'message' => 'Detail Jabatan',
            'data' => $jabatan
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jabatan $jabatan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jabatan $jabatan)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
        ]);

        $jabatan->update([
            'nama_jabatan' => $request->nama_jabatan,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Jabatan berhasil diperbarui',
            'data' => $jabatan
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jabatan $jabatan)
    {
        $jabatan = Jabatan::find($jabatan->id_jabatan);
        $jabatan->delete();
        return response()->json([
            'status' => true,
            'message' => 'Jabatan berhasil dihapus',
        ], 200);
    }
}
