<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Penjualan;
use App\Models\RincianPenjualan;
use App\Models\Barang;
use App\Models\Penitipan;
use App\Models\Penitip;
use Illuminate\Support\Facades\Log;

class hangus2hari extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaksi:hangus-2-hari';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hariini = Carbon::now();
        Log::info('Hari ini: ' . $hariini);

        $penjualans = Penjualan::where('status_pengiriman', 'belum_diambil')
            ->get();

        Log::info('Semua penjualan yang belum diambil: ' . $penjualans->count());

        foreach ($penjualans as $penjualan)
        {
            if($penjualan->jadwal_pengiriman <= $hariini->subDays(2))
            {
                Log::info('Penjualan yang sudah lebih dari 2 hari: ' . $penjualan->nota_penjualan);

                $penjualan->status_pengiriman = 'hangus';
                $penjualan->save();

                Log::info('Status pengiriman diubah menjadi hangus untuk: ' . $penjualan->nota_penjualan);

                // Update barang terkait
                $kodeProduks = RincianPenjualan::where('nota_penjualan', $penjualan->nota_penjualan)
                    ->pluck('kode_produk');

                $barangList = Barang::whereIn('kode_produk', $kodeProduks)->get();

                foreach ($barangList as $barang) {
                    $barang->status_barang = 'barang_untuk_donasi';
                    $barang->save();
                    Log::info('Status barang diubah menjadi donasi untuk: ' . $barang->kode_produk);
                }
            }
        }
    }
}
