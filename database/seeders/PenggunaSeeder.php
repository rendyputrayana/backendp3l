<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use App\Models\Organisasi;
use App\Models\Hunter;
use App\Models\Penitip;
use App\Models\Pembeli;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class PenggunaSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Loop untuk 50 pengguna dengan id_pembeli
        for ($i = 1; $i <= 50; $i++) {
            Pengguna::create([
                'email' => $faker->unique()->safeEmail(),
                'username' => $faker->unique()->userName(),
                'password' => Hash::make('password123'),
                'id_pembeli' => $i,
                'id_penitip' => null,
                'id_hunter' => null,
                'id_pegawai' => null,
                'id_organisasi' => null,
            ]);
        }

        // Loop untuk 50 pengguna dengan id_penitip
        for ($i = 1; $i <= 50; $i++) {
            Pengguna::create([
                'email' => $faker->unique()->safeEmail(),
                'username' => $faker->unique()->userName(),
                'password' => Hash::make('password123'),
                'id_pembeli' => null,
                'id_penitip' => $i,
                'id_hunter' => null,
                'id_pegawai' => null,
                'id_organisasi' => null,
            ]);
        }

        // Loop untuk 10 pengguna dengan id_hunter
        for ($i = 1; $i <= 10; $i++) {
            Pengguna::create([
                'email' => $faker->unique()->safeEmail(),
                'username' => $faker->unique()->userName(),
                'password' => Hash::make('password123'),
                'id_pembeli' => null,
                'id_penitip' => null,
                'id_hunter' => $i,
                'id_pegawai' => null,
                'id_organisasi' => null,
            ]);
        }

        // Loop untuk 28 pengguna dengan id_pegawai
        for ($i = 1; $i <= 28; $i++) {
            Pengguna::create([
                'email' => $faker->unique()->safeEmail(),
                'username' => $faker->unique()->userName(),
                'password' => Hash::make('password123'),
                'id_pembeli' => null,
                'id_penitip' => null,
                'id_hunter' => null,
                'id_pegawai' => $i,
                'id_organisasi' => null,
            ]);
        }

        // Loop untuk 20 pengguna dengan id_organisasi
        for ($i = 1; $i <= 20; $i++) {
            Pengguna::create([
                'email' => $faker->unique()->safeEmail(),
                'username' => $faker->unique()->userName(),
                'password' => Hash::make('password123'),
                'id_pembeli' => null,
                'id_penitip' => null,
                'id_hunter' => null,
                'id_pegawai' => null,
                'id_organisasi' => $i,
            ]);
        }
    }
}
