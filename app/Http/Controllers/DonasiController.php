<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Penitip;
use App\Models\Penitipan;

class DonasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $donasi = Donasi::paginate(12);

        return response()->json([
            'status' => true,
            'message' => 'List Donasi',
            'data' => $donasi
        ]);
    }

    public function getDonasiById($id)
    {
        $donasi = Donasi::where('id_organisasi', $id)->get();

        return response()->json([
            'status' => true,
            'message' => 'List Donasi',
            'data' => $donasi
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
        $request->validate([
            'id_organisasi' => 'required|exists:organisasis,id_organisasi',
            'nama_penerima' => 'required|string|max:255',
            'kode_produk' => 'required|exists:barangs,kode_produk',
        ]);

        $tanggalDonasi = now()->format('Y-m-d');

        $barang = Barang::where('kode_produk', $request->kode_produk)->first();
        if($barang->status_barang != 'tersedia') {
            return response()->json([
                'status' => false,
                'message' => 'Status barang tidak valid',
                'status_barang' => $barang->status_barang
            ]);
        }
        $barang->status_barang = 'donasi';
        
        $donasi = Donasi::create([
            'id_organisasi' => $request->id_organisasi,
            'nama_penerima' => $request->nama_penerima,
            'tanggal_donasi' => $tanggalDonasi,
        ]);
        $barang->id_donasi = $donasi->id_donasi;
        $barang->save();

        $penitipan = Penitipan::where('nota_penitipan', $barang->nota_penitipan)->first();
        $penitip = Penitip::find($penitipan->id_penitip);
        if (!$penitip) {
            return response()->json([
                'status' => false,
                'message' => 'Penitip not found',
            ]);
        }

        $reward = $barang->harga_barang / 10.000;
        $penitip->saldo += $reward;
        $penitip->save();

        return response()->json([
            'status' => true,
            'message' => 'Donasi created successfully',
            'data' => $donasi
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Donasi $donasi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Donasi $donasi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Donasi $donasi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Donasi $donasi)
    {
        //
    }
}
