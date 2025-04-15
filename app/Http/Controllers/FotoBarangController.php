<?php

namespace App\Http\Controllers;

use App\Models\FotoBarang;
use Illuminate\Http\Request;

class FotoBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fotoBarangs = FotoBarang::all();
        return response()->json([
            'status' => true,
            'message' => 'List Foto Barang',
            'data' => $fotoBarangs
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
            'kode_produk' => 'required|exists:barangs,kode_produk',
            'foto_barang' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $fotoBarang = new FotoBarang();
        $fotoBarang->kode_produk = $request->kode_produk;

        if ($request->hasFile('foto_barang')) {
            $file = $request->file('foto_barang');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $fotoBarang->foto_barang = $filename;
        }

        $fotoBarang->save();

        return response()->json([
            'status' => true,
            'message' => 'Foto Barang berhasil ditambahkan',
            'data' => $fotoBarang
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(FotoBarang $fotoBarang)
    {
        $fotoBarang = FotoBarang::find($fotoBarang->id_foto);
        if ($fotoBarang) {
            return response()->json([
                'status' => true,
                'message' => 'Detail Foto Barang',
                'data' => $fotoBarang
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Foto Barang not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FotoBarang $fotoBarang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FotoBarang $fotoBarang)
    {
        $request->validate([
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($request->hasFile('foto_barang')) {
            if ($fotoBarang->foto_barang && file_exists(public_path('images/' . $fotoBarang->foto_barang))) {
                unlink(public_path('images/' . $fotoBarang->foto_barang));
            }
    
            $file = $request->file('foto_barang');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
    
            $fotoBarang->foto_barang = $filename;
        }
    
        $fotoBarang->save();
    
        return response()->json([
            'status' => true,
            'message' => 'Foto Barang berhasil diperbarui',
            'data' => $fotoBarang
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FotoBarang $fotoBarang)
    {
        $fotoBarang = FotoBarang::find($fotoBarang->id_foto);
        if ($fotoBarang) {
            $fotoBarang->delete();
            return response()->json([
                'status' => true,
                'message' => 'Foto Barang berhasil dihapus'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Foto Barang not found'
            ], 404);
        }
    }
}
