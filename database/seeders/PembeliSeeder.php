<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembeli;
use App\Models\Alamat;

class PembeliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pembeli::factory(50)->create()->each(function ($pembeli) {
            // Buat 3 alamat untuk setiap pembeli
            $alamatIds = Alamat::factory(3)->create([
                'id_pembeli' => $pembeli->id_pembeli,
            ]);

            // Pilih salah satu alamat secara acak untuk menjadi default
            $alamatDefault = $alamatIds->random();
            $alamatDefault->update(['is_default' => true]);
        });
    }
}
