<?php

namespace Database\Seeders;

use App\Models\AkumulasiRating;
use App\Models\Alamat;
use App\Models\Barang;
use App\Models\DetailKeranjang;
use App\Models\DiskusiProduk;
use App\Models\Hunter;
use App\Models\Organisasi;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\PenukaranReward;
use App\Models\RequestDonasi;
use App\Models\User;
use Database\Factories\HunterFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Donasi;
use App\Models\Pengguna;
use App\Models\Penjualan;
use App\Models\RincianPenjualan;
use Illuminate\Bus\Dispatcher;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        // \App\Models\Pembeli::factory(10)->create();
        $this->call([
            JabatanSeeder::class,
            MerchandiseSeeder::class,
            KategoriSeeder::class,
            PegawaiSeeder::class,
        ]);
        Hunter::factory(10)->create();
        Penitip::factory(50)->create();
        $this->call([
            BadgeSeeder::class,  
            PembeliSeeder::class,
        ]);
        AkumulasiRating::factory(50)->create();
        PenukaranReward::factory(15)->create();
        Organisasi::factory(20)->create();
        RequestDonasi::factory(20)->create();
        Donasi::factory(20)->create();
        $this->call([
            PenggunaSeeder::class,
            PenitipanSeeder::class,
            BarangSeeder::class,
        ]);

        for ($i = 0; $i < 30; $i++) {
            try {
                DetailKeranjang::factory()->create();
            } catch (\Illuminate\Database\QueryException $e) {
                continue; 
            }
        }
        $this->call([
            PenjualanSeeder::class,
            RincianPenjualanSeeder::class,
            UpdatePenjualanSeeder::class,
            FotoBarangSeeder::class,
        ]);
    }
}
