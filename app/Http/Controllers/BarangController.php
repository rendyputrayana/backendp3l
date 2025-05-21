<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penitipan;
use Illuminate\Http\Request;
use App\Models\RincianPenjualan;
use App\Models\Subkategori;
use App\Models\FotoBarang;
use App\Models\DetailKeranjang;
use App\Models\Penitip;
use App\Models\Penjualan;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangs = Barang::with(['penitipan.penitip', 'fotoBarangs'])
            ->where('status_barang', 'tersedia') // Tambahkan filter di sini
            ->paginate(12);

        return response()->json([
            'status' => 'success',
            'data' => $barangs,
            'message' => 'List of available barangs with penitips'
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
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        //
    }


    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama_barang' => 'sometimes|required|string|max:255',
            'harga_barang' => 'sometimes|required|numeric',
            'id_subkategori' => 'sometimes|required|exists:subkategoris,id_subkategori',
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

        $harga_barang = $request->harga_barang ?? $barang->harga_barang;

        if ($request->filled('id_pegawai')) {
            $komisi_reuse = 0.2 * $harga_barang;
            $komisi_hunter = null;
        } else {
            $komisi_reuse = 0.15 * $harga_barang;
            $komisi_hunter = $request->filled('id_hunter') ? 0.05 * $harga_barang : null;
        }

        $komisi_penitip = 0.8 * $harga_barang;

        $barang->update([
            'nama_barang' => $request->nama_barang ?? $barang->nama_barang,
            'harga_barang' => $harga_barang,
            'id_subkategori' => $request->id_subkategori ?? $barang->id_subkategori,
            'komisi_penitip' => $komisi_penitip,
            'komisi_reuseMart' => $komisi_reuse,
            'komisi_hunter' => $komisi_hunter,
            'garansi' => $request->garansi ?? $barang->garansi,
            'berat_barang' => $request->berat_barang ?? $barang->berat_barang,
        ]);

        if ($request->hasFile('fotos')) {
            FotoBarang::where('kode_produk', $barang->kode_produk)->delete();

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
            'message' => 'Barang updated successfully'
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        $barang->load('penitipan.penitip', 'fotoBarangs', 'subkategori.kategori');
    
        return response()->json([
            'status' => true,
            'data' => $barang,
            'message' => 'Barang details'
        ]);
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
        $barangs = Barang::with(['penitipan.penitip', 'fotoBarangs'])
            ->where('status_barang', 'tersedia')
            ->paginate(12);

        return response()->json([
            'status' => true,
            'data' => $barangs,
            'message' => 'List of available barangs'
        ]);
    }

    public function getBarangByIdKategori($id_kategori)
    {
        $subkategoriIds = Subkategori::where('id_kategori', $id_kategori)
            ->pluck('id_subkategori');

        $barang = Barang::with(['penitipan.penitip', 'fotoBarangs'])
            ->whereIn('id_subkategori', $subkategoriIds)
            ->where('status_barang', 'tersedia')
            ->paginate(9);

        return response()->json([
            'status' => true,
            'data' => $barang,
            'message' => 'List of barangs by kategori'
        ]);
    }

    public function getByIdPembeli($id_pembeli)
    {
        $detailKeranjangs = DetailKeranjang::where('id_pembeli', $id_pembeli)->get();

        if ($detailKeranjangs->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No keranjang found for this pembeli'
            ], 404);
        }

        $barangs = collect();

        foreach ($detailKeranjangs as $keranjang) {
            $items = Barang::where('kode_produk', $keranjang->kode_produk)->get();
            $barangs = $barangs->merge($items);
        }

        return response()->json([
            'status' => true,
            'data' => $barangs,
            'message' => 'List of barangs for pembeli with id ' . $id_pembeli
        ]);
    }

    public function getByIdPenitip($id_penitip)
    {
        // Ambil data penitipan yang terkait dengan id_penitip
        $penitipans = Penitipan::where('id_penitip', $id_penitip)->get();

        if ($penitipans->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Penitip not found'
            ], 404);
        }

        $barangs = collect();
        foreach ($penitipans as $penitipan) {
            $items = Barang::where('nota_penitipan', $penitipan->nota_penitipan)->get();

            foreach ($items as $barang) {
                $foto_barang = FotoBarang::where('kode_produk', $barang->kode_produk)->first();
                $barang->foto_barang = $foto_barang;
            }

            $barangs = $barangs->merge($items);
        }

        if ($barangs->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No barangs found for this penitip'
            ], 404);
        }

        $barangs = $barangs->filter(function ($barang) {
            return $barang->status_barang == 'terjual';
        });

        $kode_produk_list = $barangs->pluck('kode_produk');
        $rincians = RincianPenjualan::whereIn('kode_produk', $kode_produk_list)->get();

        if ($rincians->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No rincians found for this penitip'
            ], 404);
        }

        $nota_penjualan_list = $rincians->pluck('nota_penjualan');
        $penjualans = Penjualan::whereIn('nota_penjualan', $nota_penjualan_list)->get();

        $result = $barangs->map(function ($barang) use ($rincians, $penjualans) {
            $barang->rincian_penjualan = $rincians->where('kode_produk', $barang->kode_produk)->first();
            
            if ($barang->rincian_penjualan) {
                $barang->penjualan = $penjualans->where('nota_penjualan', $barang->rincian_penjualan->nota_penjualan)->first();
            }

            return $barang;
        });

        $result = $result->values();

        return response()->json([
            'status' => true,
            'data' => $result,
            'message' => 'List of penitipan for penitip with id ' . $id_penitip
        ]);
    }

}