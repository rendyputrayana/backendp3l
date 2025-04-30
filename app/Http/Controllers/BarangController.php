<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penitipan;
use Illuminate\Http\Request;
use App\Models\RincianPenjualan;
use App\Models\Subkategori;
use App\Models\FotoBarang;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangs = Barang::with(['penitipan.penitip','fotoBarangs'])->paginate(12);

        return response()->json([
            'status' => 'success',
            'data' => $barangs,
            'message' => 'List of all barangs with penitips'
        ]);
    }


    public function tampilRating(Barang $barang)
    {
        return response()->json([
            'status' => true,
            'data' => $barang->rating_barang,
            'message' => 'Rating barang'
        ]);

    }

    public function addRating(Request $request, Barang $barang)
    {
        $request->validate([
            'id_pembeli' => 'required|exists:pembelis,id_pembeli',
            'rating' => 'required|numeric|min:1|max:5',
        ]);
    
        if ($barang->status_barang !== 'terjual') {
            return response()->json([
                'message' => 'Barang belum terjual, tidak bisa diberi rating.',
                'status' => false,
            ], 400);
        }
    
        $pembelianValid = RincianPenjualan::where('kode_produk', $barang->kode_produk)
            ->whereHas('penjualan', function ($query) use ($request) {
                $query->whereHas('alamat', function ($query) use ($request) {
                    $query->where('id_pembeli', $request->id_pembeli);
                });
            })->exists();


    
        if (!$pembelianValid) {
            return response()->json([
                'message' => 'Pembeli belum pernah membeli barang ini.',
                'status' => false,
            ], 403);
        }
    
        $barang->rating_barang = $request->rating;
        $barang->save();
    
        return response()->json([
            'message' => 'Rating berhasil ditambahkan.',
            'status' => true,
            'data' => $barang
        ]);
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
            'fotos' => 'nullable|array',
            'fotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'berat_barang' => 'nullable|numeric',
        ]);

        if ($request->filled('id_pegawai') && $request->filled('id_hunter')) {
            return response()->json([
                'status' => false,
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
                ]);
            }else{
                $penitipan = Penitipan::create([
                    'id_penitip' => $request->id_penitip,
                    'id_hunter' => $request->id_hunter,
                    'tanggal_penitipan' => now(),
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
            'berat_barang' => $request->berat_barang,
            'masa_penitipan' => now()->addDays(30),
        ]);

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('foto_barangs', 'public');
                FotoBarang::create([
                    'kode_produk' => $barang->kode_produk,
                    'foto_barang' => 'storage/' . $path
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'data' => $barang,
            'message' => 'Barang created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        return response()->json([
            'status' => true,
            'data' => $barang,
            'message' => 'Barang details'
        ]);
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
            'status' => true,
            'data' => $barangs,
            'message' => 'List of barangs'
        ]);
    }

    public function listBarangByIdPenitip($id_penitip)
    {
        $penitipan = Penitipan::where('id_penitip', $id_penitip)->first();
        if (!$penitipan) {
            return response()->json([
                'status' => false,
                'message' => 'Penitip not found'
            ], 404);
        }
        $barangs = Barang::where('nota_penitipan', $penitipan->nota_penitipan)->get();
        return response()->json([
            'status' => true,
            'data' => $barangs,
            'message' => 'List of barangs for penitip with id ' . $id_penitip
        ]);
    }

    public function updateStatusPerpanjang(Request $request, Barang $barang)
    {
        $request->validate([
            'perpanjang' => 'required|boolean',
        ]);

        if($barang->perpanjangan == true){
            return response()->json([
                'status' => false,
                'message' => 'Barang sudah diperpanjang'
            ], 400);
        }
        if($barang->status_barang == 'terjual'){
            return response()->json([
                'status' => false,
                'message' => 'Barang sudah terjual'
            ], 400);
        }else if($barang->status_barang == 'donasi'){
            return response()->json([
                'status' => false,
                'message' => 'Barang sudah didonasikan'
            ], 400);
        }else if($barang->status_barang == 'dikembalikan'){
            return response()->json([
                'status' => false,
                'message' => 'Barang sudah dikembalikan'
            ], 400);
        }

        if($barang->komisi_hunter != null){
            $komisi_reuse = $barang->harga_barang * 0.25;
            $komisi_penitip = $barang->harga_barang * 0.7;
        }else{
            $komisi_reuse = $barang->harga_barang * 0.25;
            $komisi_penitip = $barang->harga_barang * 0.75;
        }

        $barang->update([
            'perpanjang' => $request->perpanjang,
            'masa_penitipan' => \Carbon\Carbon::parse($barang->masa_penitipan)->addDays(30),
            'komisi_penitip' => $komisi_penitip,
            'komisi_reuseMart' => $komisi_reuse,
        ]);        

        return response()->json([
            'status' => true,
            'data' => $barang,
            'message' => 'Status perpanjangan updated successfully'
        ]);
    }

    public function ambilByPenitip(Request $request, Barang $barang)
    {
        $request->validate([
            'id_pegawai' => 'required|exists:pegawais,id_pegawai',
        ]);

        if($barang->status_barang == 'terjual'){
            return response()->json([
                'status' => false,
                'message' => 'Barang sudah terjual'
            ], 400);
        }else if($barang->status_barang == 'donasi'){
            return response()->json([
                'status' => false,
                'message' => 'Barang sudah didonasikan'
            ], 400);
        }
        else if($barang->status_barang == 'dikembalikan'){
            return response()->json([
                'status' => false,
                'message' => 'Barang sudah dikembalikan'
            ], 400);
        }
        $barang->update([
            'status_barang' => 'dikembalikan',
            'id_pegawai' => $request->id_pegawai,
        ]);
        return response()->json([
            'status' => true,
            'data' => $barang,
            'message' => 'Barang berhasil diambil oleh penitip'
        ]);
    }

    public function getBarangTersedia(Request $request)
    {
        $barangs = Barang::where('status_barang', 'tersedia')->get();
        return response()->json([
            'status' => true,
            'data' => $barangs,
            'message' => 'List of available barangs'
        ]);
    }

    public function getBarangByIdKategori($id_kategori)
    {
        $subkategoriIds = Subkategori::where('id_kategori', $id_kategori)->pluck('id_subkategori');

        $barang = Barang::whereIn('id_subkategori', $subkategoriIds)->get();

        return response()->json([
            'status' => true,
            'data' => $barang,
            'message' => 'List of barangs by kategori'
        ]);
    }

}
