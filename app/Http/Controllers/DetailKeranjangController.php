<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailKeranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailKeranjangController extends Controller
{
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
    public function show(DetailKeranjang $detailKeranjang)
    {
        //
    }

    public function showByIdPembeli($id_pembeli)
    {
        // Subquery untuk ambil 1 foto per barang
        $subquery = DB::table('foto_barangs')
            ->select('kode_produk', DB::raw('MIN(id_foto) as min_id_foto'))
            ->groupBy('kode_produk');

        $data = DetailKeranjang::where('detail_keranjangs.id_pembeli', $id_pembeli)
            ->join('barangs', 'detail_keranjangs.kode_produk', '=', 'barangs.kode_produk')
            ->joinSub($subquery, 'first_foto', function ($join) {
                $join->on('barangs.kode_produk', '=', 'first_foto.kode_produk');
            })
            ->join('foto_barangs', 'foto_barangs.id_foto', '=', 'first_foto.min_id_foto')
            ->join('penitipans', 'barangs.nota_penitipan', '=', 'penitipans.nota_penitipan')
            ->join('penitips', 'penitipans.id_penitip', '=', 'penitips.id_penitip')
            ->join('subkategoris', 'barangs.id_subkategori', '=', 'subkategoris.id_subkategori')
            ->select(
                'detail_keranjangs.id_keranjang',
                'detail_keranjangs.kode_produk',
                'detail_keranjangs.id_pembeli',
                'barangs.nama_barang',
                'barangs.harga_barang',
                'penitips.nama_penitip',
                'foto_barangs.foto_barang',
                'subkategoris.nama_subkategori',
            )
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'List of all detail keranjangs by id pembeli'
        ]);
    }



    public function addToKeranjang(Request $request)
    {
        $request->validate([
            'id_pembeli' => 'required|exists:pembelis,id_pembeli',
            'kode_produk' => 'required|exists:barangs,kode_produk',
        ]);

        $existingDetail = DetailKeranjang::where('id_pembeli', $request->id_pembeli)
            ->where('kode_produk', $request->kode_produk)
            ->first();

        if($existingDetail){
            return response()->json([
                'status' => false,
                'message' => 'Barang sudah ada di keranjang'
            ], 400);
        }

        $barang = Barang::where('kode_produk', $request->kode_produk)->first();

        if($barang->status_barang != 'tersedia')
        {
            return response()->json([
                'status' => false,
                'message' => 'Barang tidak tersedia'
            ], 400);
        }

        $detailKeranjang = DetailKeranjang::create([
            'id_pembeli' => $request->id_pembeli,
            'kode_produk' => $request->kode_produk
        ]);

        return response()->json([
            'status' => true,
            'data' => $detailKeranjang,
            'message' => 'Barang berhasil ditambahkan ke keranjang'
        ]);
    }

    public function removeFromKeranjang(Request $request)
    {
        $request->validate([
            'id_pembeli' => 'required|exists:pembelis,id_pembeli',
            'kode_produk' => 'required|exists:barangs,kode_produk',
        ]);

        $detailKeranjang = DetailKeranjang::where('id_pembeli', $request->id_pembeli)
            ->where('kode_produk', $request->kode_produk)
            ->first();

        if(!$detailKeranjang){
            return response()->json([
                'status' => false,
                'message' => 'Barang tidak ditemukan di keranjang'
            ], 404);
        }

        $detailKeranjang->delete();

        return response()->json([
            'status' => true,
            'message' => 'Barang berhasil dihapus dari keranjang'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DetailKeranjang $detailKeranjang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DetailKeranjang $detailKeranjang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DetailKeranjang $detailKeranjang)
    {
        //
    }
}
