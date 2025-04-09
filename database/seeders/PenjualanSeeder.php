<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penjualan;
use App\Models\RincianPenjualan;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Barang;

class PenjualanSeeder extends Seeder
{
    public function run()
    {
        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            if ($i <= 10) {
                $status_penjualan = 'lunas';
            } elseif ($i <= 15) {
                $status_penjualan = 'belum_lunas';
            } else {
                $status_penjualan = 'batal';
            }

            $id_pembeli = fake()->numberBetween(1, 150);
            $metode_pengiriman = $i % 2 === 0 ? 'kirim' : 'ambil';

            // Menentukan status pengiriman dan metode pengiriman
            if ($status_penjualan === 'batal') {
                $status_pengiriman = 'batal';
                $metode_pengiriman = 'batal';
                $id_pegawai = null;
            } elseif ($status_penjualan === 'belum_lunas') {
                $status_pengiriman = 'belum_dikirim';
                $id_pegawai = null; // Belum lunas, pegawai tidak boleh ada
            } elseif ($metode_pengiriman === 'ambil') {
                $status_pengiriman = 'diterima';
                $id_pegawai = fake()->numberBetween(10, 12); // Pegawai khusus pengambilan
            } else {
                $status_pengiriman = fake()->randomElement(['dikirim', 'diterima']);
                $id_pegawai = fake()->numberBetween(4, 6);
            }

            // Menghitung total harga berdasarkan barang yang dibeli
            $total_harga = RincianPenjualan::where('nota_penjualan', $i)
                ->join('barangs', 'rincian_penjualans.kode_produk', '=', 'barangs.kode_produk')
                ->sum('barangs.harga_barang');

            // Menentukan ongkos kirim
            if ($total_harga >= 1500000 || $metode_pengiriman === 'ambil') {
                $ongkos_kirim = 0;
            } else {
                $ongkos_kirim = 100000;
            }

            $data[] = [
                'nota_penjualan' => $i, // Pastikan ID terurut sesuai kategori
                'tanggal_transaksi' => $tanggal_transaksi = now()->subDays(fake()->numberBetween(1, 30)),
                'tanggal_lunas' => $status_penjualan === 'lunas' 
                    ? $tanggal_transaksi->clone()->addMinutes(fake()->numberBetween(1, 15)) 
                    : null,
                'total_harga' => $total_harga,
                'status_penjualan' => $status_penjualan,
                'ongkos_kirim' => $ongkos_kirim,
                'tanggal_diterima' => $status_pengiriman === 'diterima' ? now()->subDays(fake()->numberBetween(1, 5)) : null,
                'status_pengiriman' => $status_pengiriman,
                'metode_pengiriman' => $metode_pengiriman,
                'bukti_pembayaran' => $status_penjualan === 'lunas' ? fake()->imageUrl(200, 200, 'business') : null,
                'id_pegawai' => $id_pegawai,
                'id_alamat' => $id_pembeli,
            ];
        }

        Penjualan::insert($data);


        $faker = Faker::create();
        $data = [];

        // Ambil daftar penitipan yang valid (hanya penitip yang benar-benar memiliki barang)
        $penitipan = DB::table('penitipans')
            ->join('barangs', 'penitipans.nota_penitipan', '=', 'barangs.nota_penitipan')
            ->select('penitipans.id_penitip', 'barangs.kode_produk')
            ->get();

        // Ambil daftar pembeli
        $pembeli = DB::table('pembelis')->pluck('id_pembeli')->toArray();

        // Pastikan ada cukup data penitipan dan pembeli sebelum insert
        if ($penitipan->isEmpty() || empty($pembeli)) {
            return;
        }

        // Loop untuk membuat 10 diskusi
        foreach ($penitipan->random(min(10, $penitipan->count())) as $pen) {
            $data[] = [
                'id_pembeli'   => $faker->randomElement($pembeli),
                'id_penitip'   => $pen->id_penitip,
                'kode_produk'    => $pen->kode_produk,
                'isi_diskusi'  => $faker->sentence(10),
                'tanggal_diskusi' => $faker->dateTimeBetween('-1 year', 'now'),
            ];
        }

        // Masukkan data ke tabel DiskusiProduk
        DB::table('diskusi_produks')->insert($data);
    }
}
