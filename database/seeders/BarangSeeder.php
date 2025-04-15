<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $kategoriData = [
            'Elektronik & Gadget' => [
                'Smartphone & Tablet', 'Laptop & Komputer', 'Kamera & Aksesori', 
                'Peralatan Audio/Video', 'Konsol Game & Aksesorinya', 'Printer & Scanner', 
                'Peralatan Dapur Elektronik'
            ],
            'Pakaian & Aksesori' => [
                'Pakaian Pria, Wanita, dan Anak', 'Jaket, Sweater, dan Outerwear', 
                'Sepatu, Sandal, dan Boots', 'Tas, Dompet, dan Ransel', 
                'Perhiasan & Aksesori', 'Topi, Syal, dan Aksesori lainnya'
            ],
            'Perabotan Rumah Tangga' => [
                'Sofa, Meja, Kursi', 'Lemari, Rak Buku, dan Meja TV', 
                'Tempat Tidur & Kasur', 'Peralatan Masak', 'Dekorasi Rumah', 
                'Alat Kebersihan'
            ],
            'Buku, Alat Tulis, & Peralatan Sekolah' => [
                'Buku Pelajaran & Buku Bacaan', 'Buku Koleksi', 'Alat Tulis', 
                'Tas Sekolah & Peralatan Laboratorium', 'Kalkulator & Alat Ukur'
            ],
            'Hobi, Mainan, & Koleksi' => [
                'Mainan Anak', 'Alat Musik', 'Perlengkapan Olahraga', 
                'Barang Koleksi', 'Buku Komik, CD Musik, DVD Film', 
                'Peralatan Memancing atau Camping'
            ],
            'Perlengkapan Bayi & Anak' => [
                'Pakaian Bayi & Anak', 'Perlengkapan Makan Bayi', 'Mainan Edukasi', 
                'Stroller, Car Seat, & Baby Carrier', 'Tempat Tidur & Perlengkapan Bayi'
            ],
            'Otomotif & Aksesori' => [
                'Sepeda Motor & Sepeda Bekas', 'Suku Cadang & Aksesori Mobil/Motor', 
                'Helm, Jaket Riding, dan Sarung Tangan', 'Ban, Velg, dan Aksesori Kendaraan', 
                'Peralatan Perawatan Kendaraan'
            ],
            'Perlengkapan Taman & Outdoor' => [
                'Peralatan Berkebun', 'Meja & Kursi Taman', 'Alat BBQ & Outdoor Cooking', 
                'Tenda, Sleeping Bag, & Peralatan Camping'
            ],
            'Peralatan Kantor & Industri' => [
                'Meja & Kursi Kantor', 'Lemari Arsip', 'Mesin Fotokopi, Printer, dan Scanner', 
                'Alat-alat Teknik & Perkakas', 'Rak Gudang & Peralatan Penyimpanan'
            ],
            'Kosmetik & Perawatan Diri' => [
                'Alat Kecantikan', 'Parfum & Produk Perawatan', 'Aksesori Kecantikan'
            ]
        ];

        $subkategoriList = [];
        foreach ($kategoriData as $kategori => $subkategori) {
            $subkategoriList = array_merge($subkategoriList, $subkategori);
        }

        $statusOrder = array_merge(
            array_fill(0, 20, 'donasi'),
            array_fill(0, 30, 'tersedia'),
            array_fill(0, 25, 'dikembalikan'),
            array_fill(0, 25, 'terjual')
        );

        foreach ($statusOrder as $i => $status_barang) {
            $subkategoriIndex = array_rand($subkategoriList);
            $selectedSubkategori = $subkategoriList[$subkategoriIndex];

            $nama_barang = $selectedSubkategori . " " . strtoupper($faker->lexify('?????')) . "-" . $faker->numberBetween(100, 999);

            $harga_barang = $faker->randomElement([
                50000, 100000, 250000, 500000, 1000000, 2500000, 5000000, 
                7500000, 8000000, 450000, 75000, 125000, 1500000, 200000, 
                300000, 400000, 600000, 700000, 900000, 1200000
            ]);

            $nota_penitipan = $faker->numberBetween(1, 50);

            $id_donasi = ($status_barang === 'donasi') ? $faker->numberBetween(1, 20) : null;

            $komisi_penitip = floor($harga_barang * 0.8);
            $komisi_reuseMart = floor($harga_barang * 0.2);
            $komisi_hunter = ($nota_penitipan > 25) ? floor($harga_barang * 0.05) : 0;

            $rating_barang = ($status_barang === 'terjual') ? $faker->randomFloat(1, 1, 5) : null;

            $id_subkategori = $subkategoriIndex + 1;
            $garansi = null;
            if ($id_subkategori >= 1 && $id_subkategori <= 7) {
                $bulanGaransi = $faker->randomElement([3, 6, 12, 24]);
                $garansi = Carbon::now()->addMonths($bulanGaransi)->format('Y-m-d');
            }

            DB::table('barangs')->insert([
                'id_subkategori' => $id_subkategori,
                'id_donasi' => $id_donasi,
                'nota_penitipan' => $nota_penitipan,
                'nama_barang' => $nama_barang,
                'harga_barang' => $harga_barang,
                'rating_barang' => $rating_barang,
                'status_barang' => $status_barang,
                'komisi_penitip' => $komisi_penitip,
                'komisi_reuseMart' => $komisi_reuseMart,
                'komisi_hunter' => $komisi_hunter,
                'perpanjang' => false,
                'garansi' => $garansi,
            ]);
        }
    }
}
