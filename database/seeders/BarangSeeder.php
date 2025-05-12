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

        $penitipans = DB::table('penitipans')->get()->keyBy('nota_penitipan');

        $barangId = 1;

        // Buat array id_donasi dari 1-20 untuk memastikan muncul semua
        $donasiIds = range(1, 20);
        shuffle($donasiIds);
        $donasiIndex = 0;

        foreach ($statusOrder as $status_barang) {
            $subkategoriIndex = array_rand($subkategoriList);
            $selectedSubkategori = $subkategoriList[$subkategoriIndex];

            $nama_barang = $selectedSubkategori . " " . strtoupper($faker->lexify('?????')) . "-" . $barangId++;

            $harga_barang = $faker->randomElement([
                50000, 100000, 250000, 500000, 1000000, 2500000, 5000000,
                7500000, 8000000, 450000, 75000, 125000, 1500000, 200000,
                300000, 400000, 600000, 700000, 900000, 1200000
            ]);

            $nota_penitipan_keys = $penitipans->keys()->toArray();
            $nota_penitipan = $nota_penitipan_keys[$barangId % count($nota_penitipan_keys)];
            $tanggal_penitipan = $penitipans[$nota_penitipan]->tanggal_penitipan;

            $masa_penitipan = Carbon::parse($tanggal_penitipan)->addDays(30)->format('Y-m-d');

            // Tentukan id_donasi
            $id_donasi = null;
            if ($status_barang === 'donasi') {
                if ($donasiIndex < count($donasiIds)) {
                    $id_donasi = $donasiIds[$donasiIndex];
                    $donasiIndex++;
                } else {
                    $id_donasi = $faker->numberBetween(1, 20);
                }
            }

            $hasHunter = $nota_penitipan > 25;

            $komisi_hunter = $hasHunter ? floor($harga_barang * 0.05) : 0;
            $komisi_reuseMart = $hasHunter ? floor($harga_barang * 0.15) : floor($harga_barang * 0.20);
            $komisi_penitip = $harga_barang - $komisi_reuseMart - $komisi_hunter;

            $rating_barang = ($status_barang === 'terjual') ? $faker->randomFloat(1, 1, 5) : null;

            $id_subkategori = $subkategoriIndex + 1;

            $garansi = null;
            if ($id_subkategori >= 1 && $id_subkategori <= 7) {
                $bulanGaransi = $faker->randomElement([3, 6, 12, 24]);
                $garansi = Carbon::now()->addMonths($bulanGaransi)->format('Y-m-d');
            }

            $berat_barang = $faker->numberBetween(1, 20);

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
                'masa_penitipan' => $masa_penitipan,
                'berat_barang' => $berat_barang,
            ]);
        }
    }
}