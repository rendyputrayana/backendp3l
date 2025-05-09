<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriData = [
            'Elektronik & Gadget' => [
                'Smartphone & Tablet',
                'Laptop & Komputer',
                'Kamera & Aksesori',
                'Peralatan Audio/Video',
                'Konsol Game & Aksesorinya',
                'Printer & Scanner',
                'Peralatan Dapur Elektronik',
            ],
            'Pakaian & Aksesori' => [
                'Pakaian Pria, Wanita, dan Anak',
                'Jaket, Sweater, dan Outerwear',
                'Sepatu, Sandal, dan Boots',
                'Tas, Dompet, dan Ransel',
                'Perhiasan & Aksesori',
                'Topi, Syal, dan Aksesori lainnya',
            ],
            'Perabotan Rumah Tangga' => [
                'Sofa, Meja, Kursi',
                'Lemari, Rak Buku, dan Meja TV',
                'Tempat Tidur & Kasur',
                'Peralatan Masak',
                'Dekorasi Rumah',
                'Alat Kebersihan',
            ],
            'Buku, Alat Tulis, & Peralatan Sekolah' => [
                'Buku Pelajaran & Buku Bacaan',
                'Buku Koleksi',
                'Alat Tulis',
                'Tas Sekolah & Peralatan Laboratorium',
                'Kalkulator & Alat Ukur',
            ],
            'Hobi, Mainan, & Koleksi' => [
                'Mainan Anak',
                'Alat Musik',
                'Perlengkapan Olahraga',
                'Barang Koleksi',
                'Buku Komik, CD Musik, DVD Film',
                'Peralatan Memancing atau Camping',
            ],
            'Perlengkapan Bayi & Anak' => [
                'Pakaian Bayi & Anak',
                'Perlengkapan Makan Bayi',
                'Mainan Edukasi',
                'Stroller, Car Seat, & Baby Carrier',
                'Tempat Tidur & Perlengkapan Bayi',
            ],
            'Otomotif & Aksesori' => [
                'Sepeda Motor & Sepeda Bekas',
                'Suku Cadang & Aksesori Mobil/Motor',
                'Helm, Jaket Riding, dan Sarung Tangan',
                'Ban, Velg, dan Aksesori Kendaraan',
                'Peralatan Perawatan Kendaraan',
            ],
            'Perlengkapan Taman & Outdoor' => [
                'Peralatan Berkebun',
                'Meja & Kursi Taman',
                'Alat BBQ & Outdoor Cooking',
                'Tenda, Sleeping Bag, & Peralatan Camping',
            ],
            'Peralatan Kantor & Industri' => [
                'Meja & Kursi Kantor',
                'Lemari Arsip',
                'Mesin Fotokopi, Printer, dan Scanner',
                'Alat-alat Teknik & Perkakas',
                'Rak Gudang & Peralatan Penyimpanan',
            ],
            'Kosmetik & Perawatan Diri' => [
                'Alat Kecantikan',
                'Parfum & Produk Perawatan',
                'Aksesori Kecantikan',
            ],
        ];
        foreach ($kategoriData as $kategori => $subKategoriList) {
            $kategoriId = DB::table('kategoris')->insertGetId([
                'nama_kategori' => $kategori,
            ], 'id_kategori');

            foreach ($subKategoriList as $subKategori) {
                DB::table('subkategoris')->insert([
                    'id_kategori' => $kategoriId,
                    'nama_subkategori' => $subKategori,
                ]);
            }
        }
    }
}
