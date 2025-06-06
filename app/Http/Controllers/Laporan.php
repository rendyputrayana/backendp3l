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
use App\Models\Donasi;
use App\Models\Penitip;
use App\Models\Penitipan;
use Carbon\Carbon;


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

    public function LaporanPenitip($id_penitip)
    {
        $penitip = Penitip::find($id_penitip);

        $tanggalHariIni = Carbon::now()->toDateString();

        $rincian = RincianPenjualan::with('barang.penitipan','penjualan')
            ->whereHas('barang.penitipan', function($query) use ($id_penitip) {
                $query->where('id_penitip', $id_penitip);
            })
            ->get();

        $penitipans = Penitipan::where('id_penitip', $id_penitip)
            ->with(['barangs' => function($query) {
            $query->where('status_barang', 'terjual');
            }, 'barangs.rincianPenjualans.penjualan'])
            ->get();

        return response()->json([
            'status' => 'success',
            'tanggal_hari_ini' => $tanggalHariIni,
            'data' => [
                'penitip' => $penitip,
                'rincian_penjualan' => $rincian,
            ]
        ]);
    }

    public function LaporanDonasi($bulan)
    {
        $tahunIni = Carbon::now()->year;

        $tanggalHariIni = Carbon::now()->toDateString();

        $donasiBulanINi = Donasi::whereMonth('tanggal_donasi', $bulan)
            ->whereYear('tanggal_donasi', $tahunIni)
            ->with('barang.penitipan.penitip',
                'organisasi')
            ->get();

        return response()->json([
            'status' => 'success',
            'tanggal_hari_ini' => $tanggalHariIni,
            'data' => $donasiBulanINi
        ]);
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
}
