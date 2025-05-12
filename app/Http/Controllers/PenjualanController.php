<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use App\Models\DetailKeranjang;
use App\Models\Pembeli;
use App\Models\RincianPenjualan;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Penitipan;
use App\Models\Penitip;
use Illuminate\Support\Facades\Storage;

class PenjualanController extends Controller
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
         $request->validate([
             'id_pembeli' => 'required|exists:pembelis,id_pembeli',
             'id_alamat' => 'exists:alamats,id_alamat',
             'metode_pengiriman' => 'required|in:ambil,kirim,batal',
             'keranjang_ids' => 'required|array',
             'keranjang_ids.*' => 'exists:detail_keranjangs,id_keranjang',
             'poin' => 'nullable|integer',
         ]);
     
         DB::beginTransaction();
     
         try {
             $keranjangs = DetailKeranjang::with('barang')
                             ->whereIn('id_keranjang', $request->keranjang_ids)
                             ->get();
     
             $diskon = $request->poin ? ($request->poin * 100) : 0;
     
             $totalHarga = $keranjangs->sum(function($item) {
                 return $item->barang->harga_barang ?? 0;
             });
     
             $totalHarga -= $diskon;

             $ongkosKirim = 0;
                if ($request->metode_pengiriman == 'ambil') {
                    $ongkosKirim = 0;
                } elseif ($request->metode_pengiriman == 'kirim') {
                    if ($totalHarga > 1500000) {
                        $ongkosKirim = 0;
                    } else {
                        $ongkosKirim = 100000;
                    }
                }
     
             if ($request->metode_pengiriman == 'kirim') {
                 $totalHarga += $totalHarga > 1500000 ? 0 : 100000;
             }
             $idAlamat = $request->id_alamat;

            if (!$idAlamat) {
                $alamatDefault = Alamat::where('id_pembeli', $request->id_pembeli)
                                    ->where('is_default', true)
                                    ->first();

                if (!$alamatDefault) {
                    return response()->json(['message' => 'Alamat default tidak ditemukan.'], 400);
                }

                $idAlamat = $alamatDefault->id_alamat;
            }
             
            if($request->metode_pengiriman == 'ambil'){
                $status = 'belum diambil';
            }else{
                $status = 'belum dikirm';
            }
     
             $penjualan = Penjualan::create([
                 'tanggal_transaksi' => now(),
                 'total_harga' => $totalHarga,
                 'id_alamat' => $idAlamat,
                 'metode_pengiriman' => $request->metode_pengiriman,
                 'ongkos_kirim' => $ongkosKirim,
                 'status_pengiriman' => $status,
             ]);
     
             $pembeli = Pembeli::findOrFail($request->id_pembeli);
     
             if ($request->has('poin')) {
                 $poinDipakai = $request->poin;
                 if ($poinDipakai > $pembeli->poin_reward) {
                     return response()->json(['message' => 'Poin tidak mencukupi.'], 400);
                 }
                 $pembeli->poin_reward -= $poinDipakai;
             }
     
             
             $pembeli->save();
     
             foreach ($keranjangs as $item) {
                 if (!$item->barang || !$item->barang->kode_produk) {
                     throw new \Exception('Produk dari keranjang tidak ditemukan.');
                 }
     
                 RincianPenjualan::create([
                     'nota_penjualan' => $penjualan->nota_penjualan,
                     'kode_produk' => $item->barang->kode_produk,
                 ]);
             }
     
             DetailKeranjang::whereIn('id_keranjang', $request->keranjang_ids)->delete();
     
             DB::commit();
     
             return response()->json([
                 'message' => 'Penjualan berhasil dibuat.',
                 'data' => $penjualan
             ], 201);
     
         } catch (\Exception $e) {
             DB::rollBack();
             return response()->json([
                 'message' => 'Terjadi kesalahan saat menyimpan penjualan.',
                 'error' => $e->getMessage()
             ], 500);
         }
     }

     public function verifikasiPenjualan(Request $request)
     {
       $request->validate([
              'nota_penjualan' => 'required|exists:penjualans,nota_penjualan',
              'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $penjualan = Penjualan::findOrFail($request->nota_penjualan);

        if (Carbon::now()->greaterThan(Carbon::parse($penjualan->tanggal_transaksi)->addMinutes(15))) {
            $penjualan->status_penjualan = 'batal';
            $penjualan->save();
        
            return response()->json([
                'message' => 'Batas waktu verifikasi adalah 15 menit setelah transaksi.',
            ], 400);
        }

        $rincian = RincianPenjualan::where('nota_penjualan', $penjualan->nota_penjualan)->get();

        foreach ($rincian as $item) {
            $barang = Barang::where('kode_produk', $item->kode_produk)->first();
            if ($barang) {
                $penitipan = Penitipan::where('nota_penitipan', $barang->nota_penitipan)->first();
                $penitip = Penitip::find($penitipan->id_penitip);
                if (now()->diffInDays($penitipan->tanggal_penitipan) < 7) {
                    $bonus = $barang->komisi_reuseMart * 0.1;
                    $barang->komisi_penitip += $bonus;
                }
                $penitip->saldo += $barang->komisi_penitip;
                $penitip->save();
                $barang->status_barang = 'terjual';
                $barang->save();
            }
        }

        $totalHarga = $penjualan->total_harga;

        $pendapatanPoin = floor($totalHarga / 10000);
             if ($totalHarga > 500000) {
                 $bonusPoin = floor($pendapatanPoin * 0.2);
                 $pendapatanPoin += $bonusPoin;
             }
        

        $idPembeli = Alamat::where('id_alamat', $penjualan->id_alamat)->value('id_pembeli');
        $pembeli = Pembeli::findOrFail($idPembeli);
        $pembeli->poin_reward += $pendapatanPoin;
        $pembeli->save();
      
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        $penjualan->bukti_pembayaran = Storage::url($path);
        $penjualan->status_penjualan = 'lunas';
        $penjualan->status_pengiriman = 'disiapkan';
        $penjualan->tanggal_lunas = now();
        $penjualan->jadwal_pengiriman = now()->addDays(2);
        $idJabatanKurir = Jabatan::where('nama_jabatan', 'Kurir')->value('id_jabatan');

        $pegawaiKurir = Pegawai::where('id_jabatan', $idJabatanKurir)->get();

        $randomPegawai = $pegawaiKurir->random();

        $idPegawai = $randomPegawai->id_pegawai;

        $penjualan->id_pegawai = $idPegawai;

        $penjualan->save();

        return response()->json([
            'message' => 'Verifikasi penjualan berhasil.',
            'data' => $penjualan
        ], 200);
     }
    
     
    

    public function getJadwalHariini(Request $request)
    {

        $PengirimanHariIni = Penjualan::where('jadwal_pengiriman', now())
            ->get();

        $PenjualanBatal = Penjualan::where('status_pengiriman', 'belum_diambil')
            ->whereDate('jadwal_pengiriman', '<=', Carbon::now()->subDays(2)->toDateString())
            ->get();
        
        foreach ($PenjualanBatal as $penjualan) {
            $penjualan->status_penjualan = 'batal';
            $penjualan->status_pengiriman = 'batal';
            $penjualan->metode_pengiriman = 'batal';
        
            $rincian = RincianPenjualan::where('nota_penjualan', $penjualan->nota_penjualan)->get();
        
            foreach ($rincian as $item) {
                $barang = Barang::where('kode_produk', $item->kode_produk)->first();
                if ($barang) {
                    $barang->status_barang = 'donasi';
                    $barang->save();
                }
            }
            $penjualan->save();
        }
        
        

        if ($PengirimanHariIni->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada pengiriman hari ini.',
            ], 404);
        }else{
            return response()->json([
                'message' => 'Berhasil mendapatkan pengiriman hari ini.',
                'data' => $PengirimanHariIni,
            ], 200);
        }
    }

    public function selesaikanTransaksiCS(Request $request)
    {
        $request->validate([
            'nota_penjualan' => 'required|exists:penjualans,nota_penjualan',
            'id_pegawai' => 'required|exists:pegawais,id_pegawai',
        ]);

        $pegawai = Pegawai::find($request->id_pegawai);
        $jabatan = Jabatan::find($pegawai->id_jabatan);

        if($jabatan->nama_jabatan != 'CS') {
            return response()->json([
                'message' => 'Pegawai bukan CS.',
            ], 400);
        }

        $penjualan = Penjualan::findOrFail($request->nota_penjualan);
        $penjualan->status_pengiriman = 'diterima';
        $penjualan->tanggal_diterima = now();
        $penjualan->id_pegawai = $request->id_pegawai;
        $penjualan->save();

        return response()->json([
            'message' => 'Transaksi berhasil diselesaikan.',
            'data' => $penjualan
        ], 200);
    }

    public function selesaikanTransaksiKurir(Request $request)
    {
        $request->validate([
            'nota_penjualan' => 'required|exists:penjualans,nota_penjualan',
            'id_pegawai' => 'required|exists:pegawais,id_pegawai',
        ]);

        $pegawai = Pegawai::find($request->id_pegawai);
        $jabatan = Jabatan::find($pegawai->id_jabatan);

        if($jabatan->nama_jabatan != 'Kurir') {
            return response()->json([
                'message' => 'Pegawai bukan Kurir.',
            ], 400);
        }

        $penjualan = Penjualan::findOrFail($request->nota_penjualan);
        $penjualan->status_pengiriman = 'diterima';
        $penjualan->tanggal_diterima = now();
        $penjualan->id_pegawai = $request->id_pegawai;
        $penjualan->save();

        return response()->json([
            'message' => 'Transaksi berhasil diselesaikan.',
            'data' => $penjualan
        ], 200);
    }

    public function getPenjualanByIdPembeli($id_pembeli)
    {
        // 1. Ambil semua id_alamat milik pembeli
        $alamatIds = Alamat::where('id_pembeli', $id_pembeli)->pluck('id_alamat');

        if ($alamatIds->isEmpty()) {
            return response()->json([
                'message' => 'Pembeli tidak memiliki alamat.',
                'data' => [],
            ], 200);
        }

        // 2. Ambil semua penjualan berdasarkan id_alamat
        $penjualan = Penjualan::whereIn('id_alamat', $alamatIds)->get();

        if ($penjualan->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data penjualan untuk pembeli ini.',
                'data' => [],
            ], 200);
        }

        // 3. Ambil rincian penjualan berdasarkan nota_penjualan dari penjualan
        $notaPenjualanIds = $penjualan->pluck('nota_penjualan');
        $rincianPenjualan = RincianPenjualan::whereIn('nota_penjualan', $notaPenjualanIds)->get();

        // 4. Ambil semua barang yang terlibat dari kode_produk pada rincian penjualan
        $kodeProduks = $rincianPenjualan->pluck('kode_produk')->unique();
        $barang = Barang::whereIn('kode_produk', $kodeProduks)->get();

        // 5. Kaitkan rincian dan barang ke masing-masing penjualan
        foreach ($penjualan as $item) {
            $item->rincian_penjualan = $rincianPenjualan
                ->where('nota_penjualan', $item->nota_penjualan)
                ->values();

            foreach ($item->rincian_penjualan as $rincian) {
                // Menambahkan data barang ke dalam tiap rincian
                $rincian->barang = $barang->firstWhere('kode_produk', $rincian->kode_produk);
            }
        }

        // 6. Return semua data penjualan lengkap dengan rincian dan barang
        return response()->json([
            'message' => 'Data penjualan berhasil diambil.',
            'data' => $penjualan->values(), // Mengembalikan array terstruktur dengan baik
        ], 200);
    }



    
     /**
     * Display the specified resource.
     */
    public function show(Penjualan $penjualan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penjualan $penjualan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penjualan $penjualan)
    {
        //
    }
}
