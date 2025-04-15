<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RincianPenjualan;

class RincianPenjualanSeeder extends Seeder
{
    public function run()
    {
        $data = [];
        $usedKodeProduk = [];
        $usedKodeProdukUnik = [];

        $nota_tersedia = range(1, 20);
        shuffle($nota_tersedia);

        for ($id_rincian = 1; $id_rincian <= 30; $id_rincian++) {
            $nota_penjualan = $nota_tersedia[($id_rincian - 1) % 20];

            if ($nota_penjualan <= 10) {
                do {
                    $kode_produk = fake()->unique()->numberBetween(76, 100);
                } while (in_array($kode_produk, $usedKodeProdukUnik));

                $usedKodeProdukUnik[] = $kode_produk;
            } else {
                do {
                    $kode_produk = fake()->numberBetween(21, 50);
                } while (in_array($kode_produk, $usedKodeProduk));
            }

            $usedKodeProduk[] = $kode_produk;

            $data[] = [
                'nota_penjualan' => $nota_penjualan,
                'kode_produk' => $kode_produk,
            ];
        }

        RincianPenjualan::insert($data);
    }
}
