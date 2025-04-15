<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penitipan;
use Faker\Factory as Faker;

class PenitipanSeeder extends Seeder
{
    public function run()
{
    $faker = Faker::create();

    for ($i = 1; $i <= 50; $i++) {
        if ($i <= 25) {
            $id_hunter = null;
            $id_pegawai = $faker->numberBetween(13, 15);
        } else {
            $id_hunter = $faker->numberBetween(1, 10);
            $id_pegawai = null;
        }

        $tanggalPenitipan = $faker->dateTimeBetween('-2 months', 'now');
        $masaPenitipan = (clone $tanggalPenitipan)->modify('+30 days');

        Penitipan::create([
            'id_penitip' => $i,
            'id_hunter' => $id_hunter,
            'id_pegawai' => $id_pegawai,
            'tanggal_penitipan' => $tanggalPenitipan,
            'masa_penitipan' => $masaPenitipan,
        ]);
    }
}

}
