<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MerchandiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchandises = [
            ['nama_merchandise' => 'Ballpoint', 'poin' => 100, 'stok' => 10],
            ['nama_merchandise' => 'Sticker', 'poin' => 100, 'stok' => 10],
            ['nama_merchandise' => 'Mug', 'poin' => 250, 'stok' => 10],
            ['nama_merchandise' => 'Topi', 'poin' => 250, 'stok' => 10],
            ['nama_merchandise' => 'Tumbler', 'poin' => 500, 'stok' => 10],
            ['nama_merchandise' => 'Jam Dinding', 'poin' => 500, 'stok' => 10],
            ['nama_merchandise' => 'T-Shirt', 'poin' => 500, 'stok' => 10],
            ['nama_merchandise' => 'Tas Travel', 'poin' => 1000, 'stok' => 10],
            ['nama_merchandise' => 'Payung', 'poin' => 1000, 'stok' => 10],
            ['nama_merchandise' => 'Jaket Hoodie', 'poin' => 200, 'stok' => 10],
        ];

        DB::table('merchandises')->insert($merchandises);
    }
}
