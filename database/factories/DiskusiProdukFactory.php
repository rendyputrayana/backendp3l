<?php

namespace Database\Factories;

use App\Models\Penitipan;
use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiskusiProduk>
 */
class DiskusiProdukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ambil penitipan secara acak
        $penitipan = Penitipan::inRandomOrder()->first();

        // Pastikan penitipan ditemukan
        if (!$penitipan) {
            return [];
        }

        // Ambil barang yang memiliki nota_penitipan yang sama dengan penitipan yang dipilih
        $barang = Barang::where('nota_penitipan', $penitipan->nota_penitipan)->inRandomOrder()->first();

        // Pastikan barang ditemukan
        if (!$barang) {
            return [];
        }

        return [
            'id_pembeli' => $this->faker->numberBetween(1, 50),
            'kode_produk' => $barang->kode_produk, // Produk sesuai dengan penitipan
            'id_penitip' => $penitipan->id_penitip, // Id penitip diambil dari penitipans
            'isi_diskusi' => $this->faker->sentence(),
            'tanggal_diskusi' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
