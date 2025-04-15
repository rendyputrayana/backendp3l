<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penitipan;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangs = Barang::all();
        return response()->json([
            'status' => 'success',
            'data' => $barangs,
            'message' => 'List of all barangs'
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
            'id_penitip' => 'required|exists:penitips,id_penitip',
            'nama_barang' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'id_subkategori' => 'required|exists:subkategoris,id_subkategori',
            'id_pegawai' => 'nullable|exists:pegawais,id_pegawai',
            'id_hunter' => 'nullable|exists:hunters,id_hunter',
            'garansi' => 'nullable|date',
        ]);

        if ($request->filled('id_pegawai') && $request->filled('id_hunter')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya boleh memilih id_pegawai atau id_hunter, tidak boleh keduanya.'
            ], 400);
        }

        $penitipanHariIni = Penitipan::where('id_penitip', $request->id_penitip)
                                        ->whereDate('tanggal_penitipan', now()->toDateString())
                                        ->first();

        if(!$penitipanHariIni){
            if($request->filled('id_pegawai')){
                $penitipan = Penitipan::create([
                    'id_penitip' => $request->id_penitip,
                    'id_pegawai' => $request->id_pegawai,
                    'tanggal_penitipan' => now(),
                    'masa_penitipan' => now()->addDays(30),
                ]);
            }else{
                $penitipan = Penitipan::create([
                    'id_penitip' => $request->id_penitip,
                    'id_hunter' => $request->id_hunter,
                    'tanggal_penitipan' => now(),
                    'masa_penitipan' => now()->addDays(30),
                ]);
            }
        }else{
            $penitipan = $penitipanHariIni;
        }

        if($request->filled('id_pegawai')) {
            $komisi_reuse = 0.2 * $request->harga_barang;
            $komisi_hunter = null;
        } else {
            $komisi_reuse = 0.15 * $request->harga_barang;
            $komisi_hunter = $request->filled('id_hunter') ? 0.05 * $request->harga_barang : null;
        }

        $komisi_penitip = 0.8 * $request->harga_barang;

        $barang = Barang::create([
            'id_subkategori' => $request->id_subkategori,
            'id_donasi' => null,
            'nota_penitipan' => $penitipan->nota_penitipan,
            'nama_barang' => $request->nama_barang,
            'harga_barang' => $request->harga_barang,
            'rating_barang' => 0,
            'status_barang' => 'tersedia',
            'komisi_penitip' => $komisi_penitip,
            'komisi_reuseMart' => $komisi_reuse,
            'komisi_hunter' => $komisi_hunter ?? null,
            'perpanjangan' => false,
            'garansi' => $request->garansi,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $barang,
            'message' => 'Barang created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        //
    }

    public function search($keyword)
    {
        $barangs = Barang::where('nama_barang', 'like', '%' . $keyword . '%')
            ->orWhere('harga_barang', 'like', '%' . $keyword . '%')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $barangs,
            'message' => 'List of barangs'
        ]);
    }
}
