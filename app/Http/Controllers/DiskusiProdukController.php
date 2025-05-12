<?php

namespace App\Http\Controllers;

use App\Models\DiskusiProduk;
use Illuminate\Http\Request;
use App\Models\Barang;

class DiskusiProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $diskusiProduk = DiskusiProduk::with(['barang'])  
            ->orderBy('kode_produk')
            ->orderByDesc('tanggal_diskusi')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'List Diskusi Produk',
            'data' => $diskusiProduk
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
    public function show(Barang $barangs)
    {
        $diskusiProduk = DiskusiProduk::with(['pembeli', 'pegawai'])
            ->where('kode_produk', $barangs->kode_produk)
            ->get();

        if ($diskusiProduk->isEmpty()) {
            return response()->json([
                'message' => 'Diskusi Produk not found',
                'status' => false,
            ]);
        } else {
            return response()->json([
                'message' => 'Detail Diskusi Produk',
                'status' => true,
                'data' => $diskusiProduk
            ]);
        }
    }
    

    public function addByPembeli(Request $request, Barang $barang)
    {
        $request->validate([
            'isi_diskusi' => 'required|string',
            'id_pembeli' => 'required|exists:pembelis,id_pembeli',
        ]);
    
        $diskusiProduk = DiskusiProduk::create([
            'isi_diskusi' => $request->isi_diskusi,
            'tanggal_diskusi' => now(),
            'id_pembeli' => $request->id_pembeli,
            'kode_produk' => $barang->kode_produk,
            'id_pegawai' => null
        ]);

        return response()->json([
            'message' => 'Diskusi Produk created successfully',
            'status' => true,
            'data' => $diskusiProduk
        ]);
    }

    public function addByPegawai(Request $request, Barang $barang)
    {
        $request->validate([
            'isi_diskusi' => 'required',
            'id_pegawai' => 'required|exists:pegawais,id_pegawai',
        ]);
    
        $diskusiProduk = DiskusiProduk::create([
            'isi_diskusi' => $request->isi_diskusi,
            'tanggal_diskusi' => now(),
            'id_pembeli' => null,
            'id_pegawai' => $request->id_pegawai,
            'kode_produk' => $barang->kode_produk,
        ]);
    
        return response()->json([
            'message' => 'Diskusi Produk oleh pegawai berhasil ditambahkan.',
            'status' => true,
            'data' => $diskusiProduk
        ], 201);
    }
    
    public function edit(DiskusiProduk $diskusiProduk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiskusiProduk $diskusiProduk)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiskusiProduk $diskusiProduk)
    {
        //
    }
}
