<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class cekBarang7Hari extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'penjualan:cek-barang-7-hari';

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
        $tanggalHariIniMin7 = Carbon::now()->subDays(7)->format('Y-m-d');

        $barangs = Barang::where('masa_penitipan', '<=', $tanggalHariIniMin7)
                        ->where('status_barang', '=', 'tersedia')
                        ->get();

        foreach ($barangs as $barang) {
            $barang->status_barang = 'barang_untuk_donasi';
            $barang->save();
        }
    }
}