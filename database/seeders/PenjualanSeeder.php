<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penjualan;
use App\Models\RincianPenjualan;
use App\Models\Barang;

class PenjualanSeeder extends Seeder
{
    public function run()
    {
        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            if ($i <= 10) {
                $status_penjualan = 'lunas';
            } elseif ($i <= 15) {
                $status_penjualan = 'belum_lunas';
            } else {
                $status_penjualan = 'batal';
            }

            $id_pembeli = fake()->numberBetween(1, 50);
            $metode_pengiriman = $i % 2 === 0 ? 'kirim' : 'ambil';

            // Menentukan status pengiriman dan metode pengiriman
            if ($status_penjualan === 'batal') {
                $status_pengiriman = 'batal';
                $metode_pengiriman = 'batal';
                $id_pegawai = null;
            } elseif ($status_penjualan === 'belum_lunas') {
                $status_pengiriman = 'belum_dikirim';
                $id_pegawai = null; // Belum lunas, pegawai tidak boleh ada
            } elseif ($metode_pengiriman === 'ambil') {
                $status_pengiriman = 'diterima';
                $id_pegawai = fake()->numberBetween(10, 12); // Pegawai khusus pengambilan
            } else {
                $status_pengiriman = fake()->randomElement(['dikirim', 'diterima']);
                $id_pegawai = fake()->numberBetween(4, 6);
            }

            // Menghitung total harga berdasarkan barang yang dibeli
            $total_harga = RincianPenjualan::where('nota_penjualan', $i)
                ->join('barangs', 'rincian_penjualans.kode_produk', '=', 'barangs.kode_produk')
                ->sum('barangs.harga_barang');

            // Menentukan ongkos kirim
            if ($total_harga >= 1500000 || $metode_pengiriman === 'ambil') {
                $ongkos_kirim = 0;
            } else {
                $ongkos_kirim = 100000;
            }

            $data[] = [
                'nota_penjualan' => $i, // Pastikan ID terurut sesuai kategori
                'tanggal_transaksi' => now()->subDays(fake()->numberBetween(1, 30)),
                'tanggal_lunas' => $status_penjualan === 'lunas' ? now()->subDays(fake()->numberBetween(1, 10)) : null,
                'total_harga' => $total_harga,
                'status_penjualan' => $status_penjualan,
                'ongkos_kirim' => $ongkos_kirim,
                'tanggal_diterima' => $status_pengiriman === 'diterima' ? now()->subDays(fake()->numberBetween(1, 5)) : null,
                'status_pengiriman' => $status_pengiriman,
                'metode_pengiriman' => $metode_pengiriman,
                'bukti_pembayaran' => $status_penjualan === 'lunas' ? fake()->imageUrl(200, 200, 'business') : null,
                'id_pegawai' => $id_pegawai,
                'id_pembeli' => $id_pembeli,
            ];
        }

        Penjualan::insert($data);
    }
}
