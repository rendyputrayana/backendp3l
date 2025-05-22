<?php

namespace App\Http\Controllers;

use App\Models\RincianPenjualan;
use Illuminate\Http\Request;

class RincianPenjualanController extends Controller
{

    public function getAllPengirimanBarang()
    {
        $rincian = RincianPenjualan::with([
            'barang',
            'penjualan.alamat.pembeli'
        ])
        ->whereHas('penjualan', function ($query) {
            $query->where('status_pengiriman', 'disiapkan');
        })
        ->orderByDesc('id_rincian_penjualan')
        ->get();

        return response()->json([
            'message' => 'Data pengiriman berhasil diambil.',
            'data' => $rincian
        ]);
    }

    public function getAllBarangBelumDiambil()
    {
        $rincian = RincianPenjualan::with([
            'barang',
            'penjualan.alamat.pembeli'
        ])
        ->whereHas('penjualan', function ($query) {
            $query->where('status_pengiriman', 'belum_diambil');
        })
        ->orderByDesc('id_rincian_penjualan')
        ->get();

        return response()->json([
            'message' => 'Data barang belum diambil berhasil diambil.',
            'data' => $rincian
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(RincianPenjualan $rincianPenjualan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RincianPenjualan $rincianPenjualan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RincianPenjualan $rincianPenjualan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RincianPenjualan $rincianPenjualan)
    {
        //
    }
}
