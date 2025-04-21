<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;

class AlamatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alamats = Alamat::all();
        return response()->json([
            'status' => true,
            'message' => 'List Alamat',
            'data' => $alamats
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
            'detail_alamat' => 'required|string',
            'id_pembeli' => 'required|exists:pembelis,id_pembeli'
        ]);
        $alamat = Alamat::create([
            'detail_alamat' => $request->detail_alamat,
            'id_pembeli' => $request->id_pembeli
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Alamat berhasil ditambahkan',
            'data' => $alamat
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Alamat $alamat)
    {
        $alamat = Alamat::find($alamat->id_alamat);
        if ($alamat) {
            return response()->json([
                'status' => true,
                'message' => 'Detail Alamat',
                'data' => $alamat
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Alamat not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Alamat $alamat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Alamat $alamat)
    {
        $request->validate([
           'detail_alamat' => 'required|string',
        ]);

        $alamat->detail_alamat = $request->detail_alamat;
        $alamat->save();
        return response()->json([
            'status' => true,
            'message' => 'Alamat berhasil diperbarui',
            'data' => $alamat
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alamat $alamat)
    {
        $alamat = Alamat::find($alamat->id_alamat);
        if ($alamat) {
            $alamat->delete();
            return response()->json([
                'status' => true,
                'message' => 'Alamat berhasil dihapus'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Alamat not found'
            ], 404);
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $alamat = Alamat::where('detail_alamat', 'like', "%{$query}%")
                        ->orWhere('id_pembeli', 'like', "%{$query}%")
                        ->get();

        if ($alamat->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Alamat tidak ditemukan',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Hasil pencarian alamat',
            'data' => $alamat
        ], 200);
    }

    public function getAlamatByIdPembeli(Request $request, $id_pembeli)
    {
        $alamats = Alamat::where('id_pembeli', $id_pembeli)->get();
        if ($alamats->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Alamat tidak ditemukan untuk id_pembeli ' . $id_pembeli,
                'data' => []
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'List Alamat untuk id_pembeli ' . $id_pembeli,
            'data' => $alamats
        ], 200);
    }

}
