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

        // Nota penjualan 1-10, kode produk 76-100
        for ($i = 1; $i <= 15; $i++) {
            do {
                $kode_produk = fake()->numberBetween(76, 100);
            } while (in_array($kode_produk, $usedKodeProduk));

            $usedKodeProduk[] = $kode_produk;
            $nota_penjualan = fake()->numberBetween(1, 10);

            $data[] = [
                'nota_penjualan' => $nota_penjualan,
                'kode_produk' => $kode_produk,
            ];
        }

        // Nota penjualan 11-20, kode produk 25-50
        for ($i = 16; $i <= 30; $i++) {
            do {
                $kode_produk = fake()->numberBetween(25, 50);
            } while (in_array($kode_produk, $usedKodeProduk));

            $usedKodeProduk[] = $kode_produk;
            $nota_penjualan = fake()->numberBetween(11, 20);

            $data[] = [
                'nota_penjualan' => $nota_penjualan,
                'kode_produk' => $kode_produk,
            ];
        }

        RincianPenjualan::insert($data);
    }
}

