<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            ['id_jabatan' => 1, 'nama_jabatan' => 'Admin'],
            ['id_jabatan' => 2, 'nama_jabatan' => 'Kurir'],
            ['id_jabatan' => 3, 'nama_jabatan' => 'CS'],
            ['id_jabatan' => 4, 'nama_jabatan' => 'Pegawai Gudang'],
            ['id_jabatan' => 5, 'nama_jabatan' => 'Owner'],
            ['id_jabatan' => 6, 'nama_jabatan' => 'QC'],
            ['id_jabatan' => 7, 'nama_jabatan' => 'Keuangan'],
            ['id_jabatan' => 8, 'nama_jabatan' => 'Teknisi'],
            ['id_jabatan' => 9, 'nama_jabatan' => 'Manajer Operasional'],
            ['id_jabatan' => 10, 'nama_jabatan' => 'Supervisor'],
        ];

        DB::table('jabatans')->insert($jabatans);
    }
}
