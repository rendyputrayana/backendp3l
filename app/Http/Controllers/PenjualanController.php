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
     
             //DetailKeranjang::whereIn('id_keranjang', $request->keranjang_ids)->delete();
     
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
        $detailKeranjang = DetailKeranjang::where('id_pembeli', $id_pembeli)->get();

        if ($detailKeranjang->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada keranjang untuk pembeli ini.',
                'data' => [],
            ], 200);
        }

        $kodeProduks = $detailKeranjang->pluck('kode_produk');

        $barang = Barang::whereIn('kode_produk', $kodeProduks)->get();

        if ($barang->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada barang terkait dengan keranjang.',
                'data' => [],
            ], 200);
        }

        $kodeProduksBarang = $barang->pluck('kode_produk');

        $rincianPenjualan = RincianPenjualan::whereIn('kode_produk', $kodeProduksBarang)->get();

        if ($rincianPenjualan->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada rincian penjualan terkait barang.',
                'data' => [],
            ], 200);
        }

        $notaPenjualan = $rincianPenjualan->pluck('nota_penjualan');

        $penjualan = Penjualan::whereIn('nota_penjualan', $notaPenjualan)->get();

        if ($penjualan->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada pembelian untuk pembeli ini.',
                'data' => [],
            ], 200);
        }

        $result = $penjualan->map(function ($p) use ($rincianPenjualan, $barang) {
            $barangInPenjualan = $rincianPenjualan->where('nota_penjualan', $p->nota_penjualan)
                ->map(function ($rincian) use ($barang) {
                    $b = $barang->firstWhere('kode_produk', $rincian->kode_produk);
                    return [
                        'kode_produk' => $b->kode_produk ?? null,
                        'nama_barang' => $b->nama_barang ?? null,
                        'harga' => $b->harga_barang ?? null,
                    ];
                })->values();

            return [
                ...$p->attributesToArray(),
                'barang' => $barangInPenjualan,
            ];
        });

        return response()->json([
            'message' => 'Berhasil mendapatkan pembelian dan barang terkait.',
            'data' => $result,
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
