<?php

namespace Database\Factories;

use App\Models\DetailKeranjang;
use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetailKeranjangFactory extends Factory
{
    public function definition(): array
    {
        $id_pembeli = $this->faker->numberBetween(1, 50);

        $produkTersedia = Barang::whereNotIn('kode_produk', function ($query) use ($id_pembeli) {
            $query->select('kode_produk')->from('detail_keranjangs')->where('id_pembeli', $id_pembeli);
        })->whereBetween('kode_produk', [21, 50]) // Menambahkan filter untuk kode_produk antara 21 dan 50
        ->pluck('kode_produk')->toArray();

        if (empty($produkTersedia)) {
            return [];
        }

        return [
            'id_pembeli' => $id_pembeli,
            'kode_produk' => $this->faker->randomElement($produkTersedia),
        ];
    }
}

