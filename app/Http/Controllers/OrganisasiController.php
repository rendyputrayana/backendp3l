<?php

namespace App\Http\Controllers;

use App\Models\Organisasi;
use Illuminate\Http\Request;

class OrganisasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organisasi = Organisasi::all();
        return response()->json([
            'status' => true,
            'message' => 'List Organisasi',
            'data' => $organisasi
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisasi $organisasi)
    {
        $organisasi = Organisasi::find($organisasi->id_organisasi);
        if ($organisasi) {
            return response()->json([
                'status' => true,
                'message' => 'Detail Organisasi',
                'data' => $organisasi
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Organisasi not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisasi $organisasi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisasi $organisasi)
    {
        $request->validate([
            'nama_organisasi' => 'required|string|max:255',
            'alamat_organisasi' => 'required|string|max:255',
        ]);

        $organisasi->update([
            'nama_organisasi' => $request->nama_organisasi,
            'alamat_organisasi' => $request->alamat_organisasi,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Organisasi updated successfully',
            'data' => $organisasi
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisasi $organisasi)
    {
        $organisasi->find($organisasi->id);
        if (!$organisasi) {
            return response()->json([
                'status' => false,
                'message' => 'Organisasi not found'
            ], 404);
        }
        $organisasi->delete();
        return response()->json([
            'status' => true,
            'message' => 'Organisasi deleted successfully'
        ]);
    }

    public function search($query)
    {
        $organisasi = Organisasi::where('nama_organisasi', 'LIKE', "%$query%")
                                ->orWhere('alamat_organisasi', 'LIKE', "%$query%")
                                ->get();

        if ($organisasi->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Organisasi not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'List Organisasi',
            'data' => $organisasi
        ]);
    }

}
