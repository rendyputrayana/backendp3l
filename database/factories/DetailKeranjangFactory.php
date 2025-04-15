<?php

namespace Database\Factories;

use App\Models\DetailKeranjang;
use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailKeranjangFactory extends Factory
{
    public function definition(): array
    {
        // Ambil ID pembeli secara acak
        $id_pembeli = $this->faker->numberBetween(1, 50);

        // Ambil semua kode produk yang belum dibeli oleh pembeli ini
        $produkTersedia = Barang::whereNotIn('kode_produk', function ($query) use ($id_pembeli) {
            $query->select('kode_produk')->from('detail_keranjangs')->where('id_pembeli', $id_pembeli);
        })->pluck('kode_produk')->toArray();

        // Jika tidak ada barang yang tersedia, kembalikan data kosong untuk menghindari error
        if (empty($produkTersedia)) {
            return [];
        }

        return [
            'id_pembeli' => $id_pembeli,
            'kode_produk' => $this->faker->randomElement($produkTersedia),
        ];
    }
}

