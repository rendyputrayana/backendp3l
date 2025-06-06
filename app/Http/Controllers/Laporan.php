<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Alamat;
use App\Models\Penjualan;
use App\Models\RincianPenjualan;
use App\Models\Organisasi;
use App\Models\RequestDonasi;
use App\Models\Subkategori;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class Laporan extends Controller
{
    public function LaporanRequestDonasi()
    {

        $tanggalHariIni = Carbon::now()->toDateString();

        $requestDonasi = RequestDonasi::where('terpenuhi', false)
            ->with('organisasi')
            ->get();

        $data =[
            'tanggal_hari_ini' => $tanggalHariIni,
            'status' => 'success',
            'data' => $requestDonasi
        ];

        return response()->json($data);
    }

    public function LaporanBulanan()
    {
        $tahunIni = Carbon::now()->year;

        $tanggalHariIni = Carbon::now()->toDateString();

        $penjualanBulanJanuari = Penjualan::whereMonth('tanggal_lunas', 1)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataJanuari = [
            'total_penjualan' => $penjualanBulanJanuari->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanJanuari->pluck('nota_penjualan')
            )->count(),
        ];
        
        $penjualanBulanFebruari = Penjualan::whereMonth('tanggal_lunas', 2)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataFebruari = [
            'total_penjualan' => $penjualanBulanFebruari->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanFebruari->pluck('nota_penjualan')
            )->count(),
        ];

        $penjualanBulanMaret = Penjualan::whereMonth('tanggal_lunas', 3)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataMaret = [
            'total_penjualan' => $penjualanBulanMaret->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanMaret->pluck('nota_penjualan')
            )->count(),
        ];

        $penjualanBulanApril = Penjualan::whereMonth('tanggal_lunas', 4)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataApril = [
            'total_penjualan' => $penjualanBulanApril->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanApril->pluck('nota_penjualan')
            )->count(),
        ];
        
        $penjualanBulanMei = Penjualan::whereMonth('tanggal_lunas', 5)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataMei = [
            'total_penjualan' => $penjualanBulanMei->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanMei->pluck('nota_penjualan')
            )->count(),
        ];

        $penjualanBulanJuni = Penjualan::whereMonth('tanggal_lunas', 6)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataJuni = [
            'total_penjualan' => $penjualanBulanJuni->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanJuni->pluck('nota_penjualan')
            )->count(),
        ];
        
        $penjualanBulanJuli = Penjualan::whereMonth('tanggal_lunas', 7)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataJuli = [
            'total_penjualan' => $penjualanBulanJuli->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanJuli->pluck('nota_penjualan')
            )->count(),
        ];

        $penjualanBulanAgustus = Penjualan::whereMonth('tanggal_lunas', 8)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataAgustus = [
            'total_penjualan' => $penjualanBulanAgustus->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanAgustus->pluck('nota_penjualan')
            )->count(),
        ];

        $penjualanBulanSeptember = Penjualan::whereMonth('tanggal_lunas', 9)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataSeptember = [
            'total_penjualan' => $penjualanBulanSeptember->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanSeptember->pluck('nota_penjualan')
            )->count(),
        ];

        $penjualanBulanOktober = Penjualan::whereMonth('tanggal_lunas', 10)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataOktober = [
            'total_penjualan' => $penjualanBulanOktober->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanOktober->pluck('nota_penjualan')
            )->count(),
        ];

        $penjualanBulanNovember = Penjualan::whereMonth('tanggal_lunas', 11)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataNovember = [
            'total_penjualan' => $penjualanBulanNovember->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanNovember->pluck('nota_penjualan')
            )->count(),
        ];
        

        $penjualanBulanDesember = Penjualan::whereMonth('tanggal_lunas', 12)
            ->whereYear('tanggal_lunas', $tahunIni)
            ->where('status_penjualan', 'lunas')
            ->get();

        $dataDesember = [
            'total_penjualan' => $penjualanBulanDesember->sum('total_harga'),
            'jumlah_rincian' => RincianPenjualan::whereIn(
                'nota_penjualan',
                $penjualanBulanDesember->pluck('nota_penjualan')
            )->count(),
        ];

        return response()->json([
            'status' => 'success',
            'tanggal_hari_ini' => $tanggalHariIni,
            'data' => [
                'januari' => $dataJanuari,
                'februari' => $dataFebruari,
                'maret' => $dataMaret,
                'april' => $dataApril,
                'mei' => $dataMei,
                'juni' => $dataJuni,
                'juli' => $dataJuli,
                'agustus' => $dataAgustus,
                'september' => $dataSeptember,
                'oktober' => $dataOktober,
                'november' => $dataNovember,
                'desember' => $dataDesember,
            ]
        ]);
    }

    public function laporanStokGudang()
    {
        $barangs = Barang::with(['penitipan.penitip', 'penitipan.hunter'])
        ->whereIn('status_barang', ['tersedia', 'barang_untuk_donasi'])
        ->get()
        ->map(function ($barang) {
            return [
                'kode_produk' => $barang->kode_produk,
                'nama_produk' => $barang->nama_barang,
                'id_penitip' => $barang->penitipan->id_penitip ?? null,
                'nama_penitip' => $barang->penitipan->penitip->nama_penitip ?? null,
                'tanggal_masuk' => $barang->penitipan->tanggal_penitipan ?? null,
                'perpanjangan' => $barang->perpanjang == 1 ? 'Ya' : 'Tidak',
                'id_hunter' => $barang->penitipan->id_hunter ?? null,
                'nama_hunter' => $barang->penitipan->hunter->nama_hunter ?? null,
                'harga' => $barang->harga_barang,
            ];
        });

        return response()->json([
            'status' => 'success',
            'tanggal_cetak' => Carbon::now()->format('Y-m-d'),
            'data' => $barangs
        ]);
    }

    public function laporanKomisiBulanan(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

         Log::info("API laporanKomisiBulanan dipanggil. Bulan: {$month}, Tahun: {$year}");

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        Log::info("Rentang tanggal: {$startOfMonth->toDateString()} s/d {$endOfMonth->toDateString()}");

        $rincianPenjualans = RincianPenjualan::with([
            'barang' => function ($query) {
                $query->select(
                    'kode_produk', 'nama_barang', 'harga_barang',
                    'komisi_penitip', 'komisi_hunter', 'komisi_reuseMart', 'perpanjang', 'nota_penitipan'
                );
            },
            'barang.penitipan' => function ($query) {
                $query->select('nota_penitipan', 'tanggal_penitipan');
            },
            'penjualan' => function ($query) {
                $query->select('nota_penjualan', 'tanggal_lunas');
            }
        ])
        ->whereHas('penjualan', function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('tanggal_lunas', [$startOfMonth, $endOfMonth]);
        })
        ->get();

        $laporanData = $rincianPenjualans->groupBy('kode_produk')->map(function ($items, $kode_produk) {
            $firstItem = $items->first();
            $barang = $firstItem->barang;

            if (!$barang || !$barang->penitipan || !$barang->penitipan->tanggal_penitipan || !$firstItem->penjualan || !$firstItem->penjualan->tanggal_lunas) {
                 return null; 
            }

            $tanggalMasuk = Carbon::parse($barang->penitipan->tanggal_penitipan);
            $tanggalLaku = Carbon::parse($firstItem->penjualan->tanggal_lunas);
            $diffInDays = $tanggalMasuk->diffInDays($tanggalLaku);

            $komisiHunter = (float) $barang->komisi_hunter;
            $komisiReuseMartDasar = (float) $barang->komisi_reuseMart; 

            $bonusPenitip = 0.0;
            $komisiReuseMartAktual = $komisiReuseMartDasar; 

            // --- Logika Perhitungan Komisi ---

            // 1. Logika Perpanjangan: Jika ada perpanjangan
            if ($barang->perpanjang == 1) {
                $komisiReuseMartAktual = (float) $barang->harga_barang * 0.30;
                $komisiHunter = 0.0; 
            }
            // 2. Logika Bonus Penitip (jika TIDAK ada perpanjangan DAN laku < 7 hari)
            elseif ($diffInDays < 7 && $barang->perpanjang == 0) {

                $bonusPenitip = 0.10 * $komisiReuseMartDasar;
             
                $komisiReuseMartAktual = $komisiReuseMartDasar - $bonusPenitip;
            }

            return [
                'kode_produk' => $barang->kode_produk,
                'nama_produk' => $barang->nama_barang,
                'harga_jual' => (float) $barang->harga_barang,
                'tanggal_masuk' => $tanggalMasuk->format('d/n/Y'),
                'tanggal_laku' => $tanggalLaku->format('d/n/Y'),
                'komisi_hunter' => $komisiHunter,
                'komisi_reuse_mart' => $komisiReuseMartAktual,
                'bonus_penitip' => $bonusPenitip,
                'perpanjangan' => $barang->perpanjang == 1 ? 'Ya' : 'Tidak',
            ];
        })->filter()->values()->all();

        $totalKomisiHunter = array_sum(array_column($laporanData, 'komisi_hunter'));
        $totalKomisiReuseMart = array_sum(array_column($laporanData, 'komisi_reuse_mart'));
        $totalBonusPenitip = array_sum(array_column($laporanData, 'bonus_penitip'));
        $totalHargaJual = array_sum(array_column($laporanData, 'harga_jual'));

        return response()->json([
            'meta' => [
                'bulan' => Carbon::create(null, $month)->translatedFormat('F'),
                'tahun' => $year,
                'tanggal_cetak' => Carbon::now()->translatedFormat('d F Y'),
                'keterangan_komisi' => [
                    'Komisi Hunter: Nilai nominal terinput. Jika ada perpanjangan penitipan, menjadi 0%.',
                    'Komisi ReuseMart Aktual:',
                    '  - Jika ada perpanjangan: 30% dari Harga Jual.',
                    '  - Jika laku < 7 hari (tanpa perpanjangan): Nilai nominal dari DB dikurangi Bonus Penitip.',
                    '  - Kondisi lain (normal): Nilai nominal diambil langsung dari DB.',
                    'Bonus Penitip: 10% dari Komisi ReuseMart dasar (nilai nominal dari DB), berlaku jika produk laku < 7 hari dan tidak ada perpanjangan.'
                ]
            ],
            'data' => $laporanData,
            'totals' => [
                'total_harga_jual' => $totalHargaJual,
                'total_komisi_hunter' => $totalKomisiHunter,
                'total_komisi_reuse_mart' => $totalKomisiReuseMart,
                'total_bonus_penitip' => $totalBonusPenitip,
            ]
        ]);
    }
}
