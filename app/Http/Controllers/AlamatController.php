<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Pembeli;
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
    public function store(Request $request, $id_pembeli)
{
    $request->validate([
        'detail_alamat' => 'required|string'
    ]);

    $hasDefault = Alamat::where('id_pembeli', $id_pembeli)
                        ->where('is_default', true)
                        ->exists();

    $data = [
        'detail_alamat' => $request->detail_alamat,
        'id_pembeli' => $id_pembeli,
    ];

    if (!$hasDefault) {
        $data['is_default'] = true;
    }else{
        $data['is_default'] = false;
    }

    $alamat = Alamat::create($data);

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
    public function update(Request $request, $id_alamat)
    {
        $request->validate([
           'detail_alamat' => 'required|string',
           'is_default' => 'boolean'
        ]);

        $alamat = Alamat::find($id_alamat);

        if (!$alamat) {
            return response()->json([
                'status' => false,
                'message' => 'Alamat not found'
            ], 404);
        }

        // Jika is_default diubah menjadi true, set alamat lain menjadi false
        if ($request->is_default) {
            Alamat::where('id_pembeli', $alamat->id_pembeli)
                ->where('id_alamat', '!=', $id_alamat)
                ->update(['is_default' => false]);
        }

        $alamat->is_default = $request->is_default ?? $alamat->is_default;
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
    public function destroy($id_alamat)
    {
        $alamat = Alamat::find($id_alamat);
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