<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;

class UpdatePenjualanSeeder extends Seeder
{
    public function run()
    {
        // Untuk setiap penjualan, hitung total_harga berdasarkan rincian penjualan
        foreach (Penjualan::all() as $penjualan) {
            $total_harga = DB::table('rincian_penjualans')
                ->join('barangs', 'rincian_penjualans.kode_produk', '=', 'barangs.kode_produk')
                ->where('rincian_penjualans.nota_penjualan', $penjualan->nota_penjualan)
                ->sum('barangs.harga_barang');

            // Menghitung ongkos kirim sesuai aturan (flat 0 jika harga total >= 1,5 juta atau metode pengiriman ambil)
            if ($total_harga >= 1500000 || $penjualan->metode_pengiriman === 'ambil') {
                $ongkos_kirim = 0;
            } else {
                $ongkos_kirim = 100000;
            }

            // Update penjualan dengan total_harga dan ongkos_kirim yang telah dihitung
            $penjualan->update([
                'total_harga' => $total_harga,
                'ongkos_kirim' => $ongkos_kirim,
            ]);
        }
    }
}
