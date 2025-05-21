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

            if ($status_penjualan === 'batal') {
                $status_pengiriman = 'batal';
                $metode_pengiriman = 'batal';
                $id_pegawai = null;
            } elseif ($status_penjualan === 'belum_lunas') {
                $status_pengiriman = 'belum_dikirim';
                $id_pegawai = null;
            } elseif ($metode_pengiriman === 'ambil') {
                $status_pengiriman = fake()->boolean(70) ? 'diterima' : 'belum_diambil';
                $id_pegawai = fake()->numberBetween(10, 12);
            } else {
                $status_pengiriman = fake()->randomElement(['dikirim', 'diterima']);
                $id_pegawai = fake()->numberBetween(4, 6);
            }

            $tanggal_transaksi = now()->subDays(fake()->numberBetween(1, 30));

            $tanggal_pengiriman = ($status_penjualan === 'lunas')
                ? $tanggal_transaksi->clone()->addDays(2)
                : null;

            $total_harga = RincianPenjualan::where('nota_penjualan', $i)
                ->join('barangs', 'rincian_penjualans.kode_produk', '=', 'barangs.kode_produk')
                ->sum('barangs.harga_barang');

            $ongkos_kirim = ($total_harga >= 1500000 || $metode_pengiriman === 'ambil') ? 0 : 100000;

            $data[] = [
                'nota_penjualan' => $i,
                'tanggal_transaksi' => $tanggal_transaksi,
                'tanggal_lunas' => $status_penjualan === 'lunas'
                    ? $tanggal_transaksi->clone()->addMinutes(fake()->numberBetween(1, 15))
                    : null,
                'jadwal_pengiriman' => $tanggal_pengiriman,
                'total_harga' => $total_harga,
                'status_penjualan' => $status_penjualan,
                'ongkos_kirim' => $ongkos_kirim,
                'tanggal_diterima' => ($status_pengiriman === 'diterima' && $status_penjualan === 'lunas')
                    ? $tanggal_pengiriman
                    : null,
                'status_pengiriman' => $status_pengiriman,
                'metode_pengiriman' => $metode_pengiriman,
                'bukti_pembayaran' => $status_penjualan === 'lunas'
                    ? fake()->imageUrl(200, 200, 'business')
                    : null,
                'id_pegawai' => $id_pegawai,
                'id_alamat' => $id_pembeli,
                'poin' => null,
            ];
        }

        Penjualan::insert($data);

        $faker = Faker::create();
        $data = [];

        $penitipan = DB::table('penitipans')
            ->join('barangs', 'penitipans.nota_penitipan', '=', 'barangs.nota_penitipan')
            ->select('penitipans.id_penitip', 'barangs.kode_produk')
            ->get();

        $pembeli = DB::table('pembelis')->pluck('id_pembeli')->toArray();

        if ($penitipan->isEmpty() || empty($pembeli)) return;

        $produkSample = $penitipan->take(5); // ambil 5 produk pertama
        $data = [];

        foreach ($produkSample as $produk) {
            // Pertanyaan dari pembeli
            $id_pembeli = $faker->numberBetween(1, 20);
            $data[] = [
                'id_pembeli' => $id_pembeli,
                'id_pegawai' => null,
                'kode_produk' => $produk->kode_produk,
                'isi_diskusi' => $faker->sentence(10),
                'tanggal_diskusi' => $faker->dateTimeBetween('-1 year', 'now'),
            ];

            // Jawaban dari pegawai
            $id_pegawai = $faker->numberBetween(7, 9);
            $data[] = [
                'id_pembeli' => null,
                'id_pegawai' => $id_pegawai,
                'kode_produk' => $produk->kode_produk,
                'isi_diskusi' => $faker->sentence(10),
                'tanggal_diskusi' => $faker->dateTimeBetween('-1 year', 'now')
            ];
}


        DB::table('diskusi_produks')->insert($data);
    }
}
