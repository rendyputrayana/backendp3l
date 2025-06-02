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
use App\Models\FotoBarang;
use App\Models\Hunter;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Pengguna;
use App\Services\FcmService;



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
             'metode_pengiriman' => 'required|in:ambil,kirim',
             'keranjang_ids' => 'required|array',
             'keranjang_ids.*' => 'exists:detail_keranjangs,id_keranjang',
             'poin' => 'nullable|integer',
         ]);
     
         DB::beginTransaction();
     
         try {
             $keranjangs = DetailKeranjang::with('barang')
                             ->whereIn('id_keranjang', $request->keranjang_ids)
                             ->get();
     
             $diskon = $request->poin ? ($request->poin * 1000) : 0;

             foreach ($keranjangs as $item) {
                if ($item->barang) {
                    $item->barang->status_barang = 'terjual';
                    $item->barang->save();
                }
            }

     
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
                $status = 'belum_diambil';
            }else{
                $status = 'belum_dikirim';
            }
     
             $penjualan = Penjualan::create([
                 'tanggal_transaksi' => now(),
                 'total_harga' => $totalHarga,
                 'id_alamat' => $idAlamat,
                 'metode_pengiriman' => $request->metode_pengiriman,
                 'ongkos_kirim' => $ongkosKirim,
                 'status_pengiriman' => $status,
                 'poin' => $request->poin,
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

     public function uploadBuktiPembayaran(Request $request)
     {
         $request->validate([
             'nota_penjualan' => 'required|exists:penjualans,nota_penjualan',
             'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
         ]);

         $penjualan = Penjualan::findOrFail($request->nota_penjualan);

         $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
         $penjualan->bukti_pembayaran = Storage::url($path);
         $penjualan->status_penjualan = 'menunggu_verifikasi';
         $penjualan->save();

         return response()->json([
             'message' => 'Bukti pembayaran berhasil diunggah.',
             'data' => $penjualan,
             'status' => true
         ], 200);
     }

     public function getPenjualanReadyVerifikasi()
     {
         $penjualan = Penjualan::where('status_penjualan', 'menunggu_verifikasi')->get();

         if ($penjualan->isEmpty()) {
             return response()->json([
                 'message' => 'Tidak ada penjualan yang menunggu verifikasi.',
             ], 404);
         }

         return response()->json([
             'message' => 'Berhasil mendapatkan penjualan yang menunggu verifikasi.',
             'data' => $penjualan,
             'status' => true
         ], 200);
     }

     public function tolakVerifikasiPembayaran(Request $request)
     {
        $request->validate([
            'nota_penjualan' => 'required|exists:penjualans,nota_penjualan',
        ]);

        $penjualan = Penjualan::findOrFail($request->nota_penjualan);

        $rincian = RincianPenjualan::where('nota_penjualan', $penjualan->nota_penjualan)->get();
        foreach ($rincian as $item) {
            $barang = Barang::where('kode_produk', $item->kode_produk)->first();
            if ($barang) {
                $barang->status_barang = 'tersedia';
                $barang->save();
            }
        }

        $penjualan->status_penjualan = 'batal';
        $penjualan->status_pengiriman = 'batal';
        $penjualan->metode_pengiriman = 'batal';
        $penjualan->save();

        return response()->json([
            'message' => 'Verifikasi pembayaran ditolak.',
            'data' => $penjualan,
            'status' => true
        ], 200);
     }

     public function verifikasiPenjualan(Request $request)
     {
       $request->validate([
              'nota_penjualan' => 'required|exists:penjualans,nota_penjualan',
        ]);

        $penjualan = Penjualan::findOrFail($request->nota_penjualan);

        $penjualan->status_penjualan = 'lunas';
        $penjualan->status_pengiriman = 'disiapkan';
        $penjualan->tanggal_lunas = now();

        $idPenitips = $penjualan->rincianPenjualans
            ->map(function($rincian) {
                return optional($rincian->barang->penitipan)->id_penitip;
            })
            ->filter()    
            ->unique()
            ->values()    
            ->all();
        if($idPenitips) {
            foreach ($idPenitips as $idPenitip) {
                $penitip = Penitip::find($idPenitip);
                if ($penitip) {
                    $pengguna_penitip = Pengguna::where('id_penitip', $penitip->id_penitip)->first();
                    Log::info('Pengguna Penitip: ' . $pengguna_penitip);
                    $fcmToken = $pengguna_penitip->fcm_token;
                    if($fcmToken){
                        FcmService::sendNotification(
                            $fcmToken,
                            'Barang anda telah laku',
                            'Barang penitipan Anda telah laku terjual. Silahkan cek di aplikasi.'
                        );
                    }
                }
            }
        }

        $penjualan->save();

        return response()->json([
            'message' => 'Verifikasi penjualan berhasil.',
            'data' => $penjualan,
            'status' => true
        ], 200);
     }

    public function KonfirmasiPengirimanByGudang(Request $request)
    {
        $request->validate([
            'nota_penjualan' => 'required|exists:penjualans,nota_penjualan',
            'id_pegawai' => 'required|exists:pegawais,id_pegawai',
            'jadwal_pengiriman' => 'required|date',
        ]);

        $jamHariIni = Carbon::now()->format('H:i:s');

        Log::info('Jam Hari Ini: ' . $jamHariIni);

        if($request->jadwal_pengiriman == Carbon::now()->format('Y-m-d') && $jamHariIni > '16:00:00') {
            return response()->json([
                'message' => 'Jadwal pengiriman tidak boleh pada hari ini.',
            ], 400);
        }

        $penjualan = Penjualan::with('alamat', 'rincianPenjualans.barang.penitipan')
                    ->findOrFail($request->nota_penjualan);

        $penjualan->jadwal_pengiriman = $request->jadwal_pengiriman;
        $penjualan->status_pengiriman = 'dikirim';
        $penjualan->id_pegawai = $request->id_pegawai;
        $penjualan->save();

        $kurir = Pegawai::findOrFail($request->id_pegawai);
        if($kurir){
            $pengguna_kurir = Pengguna::where('id_pegawai', $kurir->id_pegawai)->first();
            Log::info('Pengguna Kurir: ' . $pengguna_kurir);
            $fcmToken = $pengguna_kurir->fcm_token;
            if($fcmToken){
                FcmService::sendNotification(
                    $fcmToken,
                    'Pengiriman Barang dengan nota penjualan: ' . $penjualan->nota_penjualan,
                    'Anda telah ditugaskan untuk mengirimkan barang pada tanggal ' . $penjualan->jadwal_pengiriman . '. Silahkan cek di aplikasi.'
                );
            }
        }

        $alamat = $penjualan->alamat;
        $idPembeli = $alamat ? $alamat->id_pembeli : null;
        Log::info('ID Pembeli: ' . $idPembeli);
        if($idPembeli){
            $pembeli = Pembeli::findOrFail($idPembeli);
            $pengguna_pembeli = Pengguna::where('id_pembeli', $pembeli->id_pembeli)->first();
            Log::info('Pengguna Pembeli: ' . $pengguna_pembeli);
            $fcmToken = $pengguna_pembeli->fcm_token;
            if($fcmToken)
            {
                FcmService::sendNotification(
                    $fcmToken,
                    'Pengiriman Barang dengan nota penjualan: ' . $penjualan->nota_penjualan,
                    'Barang Anda akan dikirim pada tanggal ' . $penjualan->jadwal_pengiriman . '. Silahkan cek di aplikasi.'
                );
            }
        }

        $idPenitips = $penjualan->rincianPenjualans
            ->map(function($rincian) {
                return optional($rincian->barang->penitipan)->id_penitip;
            })
            ->filter()    
            ->unique()
            ->values()    
            ->all();
        if($idPenitips) {
            foreach ($idPenitips as $idPenitip) {
                $penitip = Penitip::find($idPenitip);
                if ($penitip) {
                    $pengguna_penitip = Pengguna::where('id_penitip', $penitip->id_penitip)->first();
                    Log::info('Pengguna Penitip: ' . $pengguna_penitip);
                    $fcmToken = $pengguna_penitip->fcm_token;
                    if($fcmToken)
                    {
                        FcmService::sendNotification(
                            $fcmToken,
                            'Pengiriman Barang dengan nota penjualan: ' . $penjualan->nota_penjualan,
                            'Barang penitipan Anda akan dikirim pada tanggal ' . $penjualan->jadwal_pengiriman . '. Silahkan cek di aplikasi.'
                        );
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Konfirmasi pengiriman berhasil.',
            'data' => $penjualan,
            'alamat' => $alamat,
            'id_pembeli' => $idPembeli,
            'id_penitip' => $idPenitips,
        ]);
    }



    public function KonfirmasiPengambilanByGudang(Request $request)
    {
        $request->validate([
            'nota_penjualan' => 'required|exists:penjualans,nota_penjualan',
            'jadwal_pengiriman' => 'required|date',
        ]);

        $penjualan = Penjualan::with('alamat', 'rincianPenjualans.barang.penitipan')
                    ->findOrFail($request->nota_penjualan);
        $penjualan->status_pengiriman = 'belum_diambil'; 
        $penjualan->jadwal_pengiriman = $request->jadwal_pengiriman;
        $penjualan->save();

        $alamat = $penjualan->alamat;
        $idPembeli = $alamat ? $alamat->id_pembeli : null;

        if($idPembeli) {
            $pembeli = Pembeli::findOrFail($idPembeli);
            $pengguna_pembeli = Pengguna::where('id_pembeli', $pembeli->id_pembeli)->first();
            Log::info('Pengguna Pembeli: ' . $pengguna_pembeli);
            $fcmToken = $pengguna_pembeli->fcm_token;
            if($fcmToken)
            {
                FcmService::sendNotification(
                    $fcmToken,
                    'Pengambilan Barang dengan nota penjualan: ' . $penjualan->nota_penjualan,
                    'Barang Anda dapat diambil pada sampai pada tanggal ' . $penjualan->jadwal_pengiriman . '. Silahkan cek di aplikasi.'
                );
            }
        }

        $idPenitips = $penjualan->rincianPenjualans
            ->map(function($rincian) {
                return optional($rincian->barang->penitipan)->id_penitip;
            })
            ->filter()    
            ->unique()
            ->values()    
            ->all();

        if($idPenitips) {
            foreach ($idPenitips as $idPenitip) {
                $penitip = Penitip::find($idPenitip);
                if ($penitip) {
                    $pengguna_penitip = Pengguna::where('id_penitip', $penitip->id_penitip)->first();
                    Log::info('Pengguna Penitip: ' . $pengguna_penitip);
                    $fcmToken = $pengguna_penitip->fcm_token;
                    if($fcmToken)
                    {
                        FcmService::sendNotification(
                            $fcmToken,
                            'Pengambilan Barang dengan nota penjualan: ' . $penjualan->nota_penjualan,
                            'Barang penitipan Anda dapat diambil pada sampai pada tanggal ' . $penjualan->jadwal_pengiriman . '. Silahkan cek di aplikasi.'
                        );
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Konfirmasi pengambilan berhasil.',
            'data' => $penjualan,
            'alamat' => $alamat,
            'id_pembeli' => $idPembeli,
            'id_penitip' => $idPenitips,
        ]);
    }

    public function verifPengirimanKurir(Request $request)
    {
        $request->validate([
            'nota_penjualan' => 'required|exists:penjualans,nota_penjualan',
            'id_pegawai' => 'required|exists:pegawais,id_pegawai',
        ]);

        $penjualan = Penjualan::findOrFail($request->nota_penjualan);
        $penjualan->id_pegawai = $request->id_pegawai;
        $penjualan->status_pengiriman = 'diterima';
        $penjualan->tanggal_diterima = now();
        $penjualan->save();

        $alamat = $penjualan->alamat;
        $idPembeli = $alamat ? $alamat->id_pembeli : null;

        if($idPembeli) {
            $pembeli = Pembeli::findOrFail($idPembeli);
            $pengguna_pembeli = Pengguna::where('id_pembeli', $pembeli->id_pembeli)->first();
            Log::info('Pengguna Pembeli: ' . $pengguna_pembeli);
            $fcmToken = $pengguna_pembeli->fcm_token;
            if($fcmToken)
            {
                FcmService::sendNotification(
                    $fcmToken,
                    'Barang anda sedang dalam proses pengiriman',
                    'Barang Anda sedang dalam proses pengiriman oleh kurir. Silahkan cek di aplikasi.'
                );
            }
        }

        $idPenitips = $penjualan->rincianPenjualans
            ->map(function($rincian) {
                return optional($rincian->barang->penitipan)->id_penitip;
            })
            ->filter()    
            ->unique()
            ->values()    
            ->all();

        if($idPenitips) {
            foreach ($idPenitips as $idPenitip) {
                $penitip = Penitip::find($idPenitip);
                if ($penitip) {
                    $pengguna_penitip = Pengguna::where('id_penitip', $penitip->id_penitip)->first();
                    Log::info('Pengguna Penitip: ' . $pengguna_penitip);
                    $fcmToken = $pengguna_penitip->fcm_token;
                    if($fcmToken)
                    {
                        FcmService::sendNotification(
                            $fcmToken,
                            'Barang penitipan Anda sedang dalam proses pengiriman',
                            'Barang penitipan Anda sedang dalam proses pengiriman oleh kurir. Silahkan cek di aplikasi.'
                        );
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Verifikasi pengiriman berhasil.',
            'data' => $penjualan,
            'status' => true
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
                    $barang->status_barang = 'barang_untuk_donasi';
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
    // if (Carbon::now()->greaterThan(Carbon::parse($penjualan->tanggal_transaksi)->addMinutes(15))) {
    //     $penjualan->status_penjualan = 'batal';
    //     $penjualan->save();
    
    //     return response()->json([
    //         'message' => 'Batas waktu verifikasi adalah 15 menit setelah transaksi.',
    //     ], 400);
    // }

    public function selesaikanTransaksiCS(Request $request)
    {
        $request->validate([
            'nota_penjualan' => 'required|exists:penjualans,nota_penjualan',
            'id_pegawai' => 'required|exists:pegawais,id_pegawai',
        ]);

        $penjualan = Penjualan::findOrFail($request->nota_penjualan);

        $rincian = RincianPenjualan::where('nota_penjualan', $penjualan->nota_penjualan)->get();

        foreach ($rincian as $item) {
            $barang = Barang::where('kode_produk', $item->kode_produk)->first();
            if ($barang) {
                $penitipan = Penitipan::where('nota_penitipan', $barang->nota_penitipan)->first();
                $id_hunter = $penitipan->id_hunter;
                if($id_hunter)
                {
                    $hunter = Hunter::find($id_hunter);
                    $hunter->saldo += $barang->komisi_hunter;
                    $hunter->save();
                }
                $penitip = Penitip::find($penitipan->id_penitip);
                if (now()->diffInDays($penitipan->tanggal_penitipan) < 7) {
                    $bonus = $barang->komisi_reuseMart * 0.1;
                    $barang->komisi_penitip += $bonus;
                }
                $penitip->saldo += $barang->komisi_penitip;
                if($penitip)
                {
                    $penitip_pengguna = Pengguna::where('id_penitip', $penitip->id_penitip)->first();
                    Log::info('Penitip Pengguna: ' . $penitip_pengguna);
                    $fcmToken = $penitip_pengguna->fcm_token;
                    if($fcmToken)
                    {
                        FcmService::sendNotification(
                            $fcmToken,
                            'Barang Anda telah diterima',
                            'Barang penitipan Anda telah diambil oleh pembeli, silahkan cek di aplikasi'
                        );
                    }
                }
                $penitip->save();
                $barang->status_barang = 'terjual';
                $barang->save();

                Artisan::call('penjualan:push-barang-laku', [
                    'nota_penitipan' => $barang->nota_penitipan,
                    'kode_produk' => $barang->kode_produk,
                ]);
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
        $pengguna_pembeli = Pengguna::where('id_pembeli', $pembeli->id_pembeli)->first();
        Log::info('Pengguna Pembeli: ' . $pengguna_pembeli);
        $fcmToken = $pengguna_pembeli->fcm_token;
        if($fcmToken)
        {
            FcmService::sendNotification(
                $fcmToken,
                'Anda telah menerima barang penitipan',
                'Barang anda telah diambil di CS, terima kasih telah berbelanja di ReuseMart'
            );
        }
        $pembeli->save();

        $pegawai = Pegawai::find($request->id_pegawai);
        $jabatan = Jabatan::find($pegawai->id_jabatan);

        if($jabatan->nama_jabatan != 'Pegawai Gudang') {
            return response()->json([
                'message' => 'Pegawai bukan Pegawai Gudang.',
            ], 400);
        }

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
        $penjualan->status_pengiriman = 'dikirim';
        //$penjualan->tanggal_diterima = now();
        $penjualan->id_pegawai = $request->id_pegawai;
        $penjualan->save();

        $rincian = RincianPenjualan::where('nota_penjualan', $penjualan->nota_penjualan)->get();
        Log::info('Rincian Penjualan: ' . $rincian);

        foreach ($rincian as $item) {
            $barang = Barang::where('kode_produk', $item->kode_produk)->first();
            Log::info('Barang: ' . $barang);
            if ($barang) {
                $penitipan = Penitipan::where('nota_penitipan', $barang->nota_penitipan)->first();
                $id_hunter = $penitipan->id_hunter;
                if($id_hunter)
                {
                    $hunter = Hunter::find($id_hunter);
                    $hunter->saldo += $barang->komisi_hunter;
                    $hunter->save();
                }
                Log::info('Penitipan: ' . $penitipan);
                $penitip = Penitip::find($penitipan->id_penitip);
                Log::info('Penitip: ' . $penitip);
                $penitip_pengguna = Pengguna::where('id_penitip', $penitip->id_penitip)->first();
                Log::info('Penitip Pengguna: ' . $penitip_pengguna);
                $fcmToken = $penitip_pengguna->fcm_token;
                if($fcmToken)
                {
                    FcmService::sendNotification(
                        $fcmToken,
                        'Barang Anda telah dikirim',
                        'Barang penitipan Anda telah dikirim oleh kurir, silahkan cek di aplikasi'
                    );
                }
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
        $pengguna_pembeli = Pengguna::where('id_pembeli', $pembeli->id_pembeli)->first();
        Log::info('Pengguna Pembeli: ' . $pengguna_pembeli);
        $fcmToken = $pengguna_pembeli->fcm_token;
        if($fcmToken)
        {
            FcmService::sendNotification(
                $fcmToken,
                'Barang Anda telah dikirim',
                'Barang anda telah dikirim oleh kurir, terima kasih telah berbelanja di ReuseMart'
            );
        }
        $pembeli->save();

        return response()->json([
            'message' => 'Transaksi berhasil diselesaikan.',
            'data' => $penjualan
        ], 200);
    }

    public function getPenjualanByIdPembeli($id_pembeli)
    {

        $alamatIds = Alamat::where('id_pembeli', $id_pembeli)->pluck('id_alamat');

        if ($alamatIds->isEmpty()) {
            return response()->json([
                'message' => 'Pembeli tidak memiliki alamat.',
                'data' => [],
            ], 200);
        }


        $penjualan = Penjualan::whereIn('id_alamat', $alamatIds)->get();

        if ($penjualan->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data penjualan untuk pembeli ini.',
                'data' => [],
            ], 200);
        }

        $notaPenjualanIds = $penjualan->pluck('nota_penjualan');
        $rincianPenjualan = RincianPenjualan::whereIn('nota_penjualan', $notaPenjualanIds)->get();

        $kodeProduks = $rincianPenjualan->pluck('kode_produk')->unique();
        $barang = Barang::whereIn('kode_produk', $kodeProduks)->get();

        $barangIds = $barang->pluck('kode_produk')->unique();
        $fotoBarang = FotoBarang::whereIn('kode_produk', $barangIds)->get();

        foreach ($penjualan as $item) {
            $item->rincian_penjualan = $rincianPenjualan
                ->where('nota_penjualan', $item->nota_penjualan)
                ->values();

            foreach ($item->rincian_penjualan as $rincian) {
                $rincian->barang = $barang->firstWhere('kode_produk', $rincian->kode_produk);

                if ($rincian->barang) {

                    $rincian->barang->foto_barang = $fotoBarang
                        ->where('kode_produk', $rincian->barang->kode_produk)
                        ->values();
                }
            }
        }

        return response()->json([
            'message' => 'Data penjualan berhasil diambil.',
            'data' => $penjualan->values(), 
        ], 200);
    }

    public function getPengirimanBarang()
    {
        $penjualans = Penjualan::with([
                'rincianPenjualans.barang',
                'alamat.pembeli'             
            ])
            ->where('status_pengiriman', 'disiapkan')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        return response()->json([
            'message' => 'Data pengiriman berhasil diambil.',
            'data' => $penjualans
        ]);
    }

    public function getPengirimanByIdKurir($id_kurir)
    {
        $hariIni = Carbon::now()->format('Y-m-d');

        $penjualans = Penjualan::with([
                'alamat.pembeli'         
            ])
            ->where('id_pegawai', $id_kurir)
            ->where('status_pengiriman', 'dikirim')
            ->where('jadwal_pengiriman', '=', $hariIni)
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        if ($penjualans->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada pengiriman untuk kurir ini.',
                'data' => [],
                'status' => false
            ]);
        }

        return response()->json([
            'message' => 'Data pengiriman berhasil diambil.',
            'data' => $penjualans,
            'status' => true
        ]);
    }

    public function getHistoryPengirimanByIdKurir($id_kurir)
    {
        $penjualans = Penjualan::with([
                'alamat.pembeli'         
            ])
            ->where('id_pegawai', $id_kurir)
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        if ($penjualans->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada riwayat pengiriman untuk kurir ini.',
                'data' => [],
                'status' => false
            ]);
        }

        return response()->json([
            'message' => 'Data riwayat pengiriman berhasil diambil.',
            'data' => $penjualans,
            'status' => true
        ]);
    }

    public function getPenjualanById ($nota_penjualan)
    {
        $penjualan = Penjualan::with([
            'rincianPenjualans.barang.penitipan.penitip',
            'alamat.pembeli.pengguna',
            'pegawai',
            'rincianPenjualans.barang.penitipan.pegawai'             
        ])
        ->where('nota_penjualan', $nota_penjualan)
        ->first();

        if (!$penjualan) {
            return response()->json([
                'message' => 'Data penjualan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Data penjualan berhasil diambil.',
            'data' => $penjualan
        ]);
    }

    public function getAllPenjualan ()
    {
        $penjualan = Penjualan::with([
            'rincianPenjualans.barang',
            'alamat.pembeli.pengguna',
        ])
        ->where('status_penjualan', 'lunas')
        ->orderBy('tanggal_transaksi', 'desc')
        ->get();
        return response()->json([
            'message' => 'Data penjualan berhasil diambil.',
            'data' => $penjualan
        ]);
    }

    public function getAllPenjualanBelumDiambil()
    {
        $penjualan = Penjualan::with([
            'rincianPenjualans.barang',
            'alamat.pembeli.pengguna',
        ])
        ->where('status_pengiriman', 'belum_diambil')
        ->orderBy('tanggal_transaksi', 'desc')
        ->get();

        return response()->json([
            'message' => 'Data penjualan belum diambil berhasil diambil.',
            'data' => $penjualan
        ]);
    }

    public function getHistoryHunterByIdHunter($id_hunter)
    {
        $penjualan = Penjualan::with([
            'rincianPenjualans.barang.penitipan.hunter',
        ])
        ->whereHas('rincianPenjualans.barang.penitipan.hunter', function($query) use ($id_hunter) {
            $query->where('id_hunter', $id_hunter);
        })
        ->where('status_pengiriman', 'diterima')
        ->orderBy('tanggal_transaksi', 'desc')
        ->get();
        if ($penjualan->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data penjualan untuk hunter ini.',
                'data' => [],
            ], 200);
        }
        return response()->json([
            'message' => 'Data penjualan hunter berhasil diambil.',
            'data' => $penjualan
        ]);
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