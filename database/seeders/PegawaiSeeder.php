<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatanIds = range(1, 10);
        $jabatanIds = array_diff($jabatanIds, [5]); // Menghapus id_jabatan 5 (Owner)

        foreach ($jabatanIds as $jabatanId) {
            Pegawai::factory(3)->create([
                'id_jabatan' => $jabatanId
            ]);
        }

        Pegawai::factory(1)->create([
            'id_jabatan' => 5, // Owner
        ]);
    }
}
