<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [];
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        foreach (array_rand(array_flip(range(1, 50)), 15) as $id_penitip) {
            $bulan = $months[array_rand($months)]; // Pilih bulan secara acak
        
            $badges[] = [
                'id_penitip'   => $id_penitip,
                'nama_badge'   => "Penjualan Terbaik Bulan $bulan",
                'logo_badge'   => "https://via.placeholder.com/100"
            ];
        }

        DB::table('badges')->insert($badges);
    }
}
