<?php

namespace App\Console\Commands;

use App\Models\Pembeli;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Penjualan;
use App\Models\RincianPenjualan;
use App\Models\Barang;
use App\Models\Alamat;
use Carbon\Carbon;

class AutoBatalkanPenjualan extends Command
{
    protected $signature = 'transaksi:auto-batal';
    protected $description = 'Batalkan transaksi jika belum dibayar dalam 15 menit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batasWaktu = Carbon::now()->subMinutes(2);
        $penjualans = Penjualan::where('status_penjualan', 'belum_lunas')
            ->where('created_at', '<=', $batasWaktu)
            ->get();

        Log::info('Semua penjualan yang dibatalkan:' . $penjualans);

        foreach ($penjualans as $penjualan) {
            if ($penjualan->bukti_pembayaran) {
                // Misalnya Anda punya proses manual validasi oleh admin,
                // maka bisa skip di sini atau beri log
                $this->info("Menunggu admin validasi untuk: {$penjualan->nota_penjualan}");
            } else {
                $penjualan->status_penjualan = 'batal';
                $penjualan->status_pengiriman = 'batal';
                $penjualan->metode_pengiriman = 'batal';
                $penjualan->save();

                $kodeProduks = RincianPenjualan::where('nota_penjualan', $penjualan->nota_penjualan)
                ->pluck('kode_produk');

                $barangList = Barang::whereIn('kode_produk', $kodeProduks)->get();

                foreach ($barangList as $barang) {
                    $barang->status_barang = 'tersedia';
                    $barang->save();
                }

                $id_alamat = $penjualan->id_alamat;
                $alamat = Alamat::where('id_alamat', $id_alamat)->first();
                $id_pembeli = $alamat->id_pembeli;
                $pembeli = Pembeli::find($id_pembeli);
                $pembeli->poin_reward += $penjualan->poin; 
                $pembeli->save();

                Log::info("Pembayaran dibatalkan untuk: {$penjualan->nota_penjualan}");

                $this->warn("Pembayaran gagal untuk: {$penjualan->nota_penjualan} (tidak ada bukti)");
            }
        }

        return 0;
    }
}
