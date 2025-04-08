<?php

namespace Database\Seeders;

use App\Models\AkumulasiRating;
use App\Models\Alamat;
use App\Models\Hunter;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\PenukaranReward;
use App\Models\User;
use Database\Factories\HunterFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
    }
}
