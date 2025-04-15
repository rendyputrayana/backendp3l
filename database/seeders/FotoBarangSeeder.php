<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FotoBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        for ($i = 1; $i <= 100; $i++) {
            $data[] = [
                'kode_produk' => $i,
                'foto_barang' => 'https://example.com/foto_barang_' . $i . '_1.jpg',
            ];
            $data[] = [
                'kode_produk' => $i,
                'foto_barang' => 'https://example.com/foto_barang_' . $i . '_2.jpg',
            ];
        }

        DB::table('foto_barangs')->insert($data);
    }
}
