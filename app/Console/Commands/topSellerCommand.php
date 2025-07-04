<?php

namespace App\Console\Commands;

use App\Models\Badge;
use Illuminate\Console\Command;
use App\Models\Penjualan;
use App\Models\RincianPenjualan;
use App\Models\Barang;
use App\Models\Penitipan;
use App\Models\Penitip;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class topSellerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'penjualan:top-seller';

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
        $bulanLalu = Carbon::now()->subMonth()->format('Y-m');

        $komisiTerbanyak = DB::table('penjualans as p')
            ->join('rincian_penjualans as r', 'r.nota_penjualan', '=', 'p.nota_penjualan')
            ->join('barangs as b', 'b.kode_produk', '=', 'r.kode_produk')
            ->join('penitipans as pt', 'pt.nota_penitipan', '=', 'b.nota_penitipan')
            ->where('p.tanggal_lunas', 'like', $bulanLalu . '%')

            ->select('pt.id_penitip', DB::raw('SUM(b.komisi_penitip) as total_komisi'))
            ->groupBy('pt.id_penitip')
            ->orderByDesc('total_komisi')
            ->first();

        if ($komisiTerbanyak) {
            Log::info('ID Penitip dengan komisi tertinggi bulan ini: ' . $komisiTerbanyak->id_penitip);

            $bulanIndo = Carbon::now()->subMonth()->locale('id')->translatedFormat('F');
            $badge = Badge::firstOrNew([
                'nama_badge' => 'Top Seller Bulan ' . $bulanIndo,
                'logo_badge' => 'top_seller.png',
                'id_penitip' => $komisiTerbanyak->id_penitip ?? null
            ]);
            $badge->save();

            // Hitung total bonus untuk penitip
            $totalPenjualan = DB::table('penjualans as p')
                ->join('rincian_penjualans as r', 'r.nota_penjualan', '=', 'p.nota_penjualan')
                ->join('barangs as b', 'b.kode_produk', '=', 'r.kode_produk')
                ->join('penitipans as pt', 'pt.nota_penitipan', '=', 'b.nota_penitipan')
                ->where('p.tanggal_lunas', 'like', $bulanLalu . '%')
                ->where('pt.id_penitip', $komisiTerbanyak->id_penitip)
                ->select(DB::raw('SUM(b.harga_barang) as total_penjualan')) 
                ->value('total_penjualan');

            $bonusPoin = $totalPenjualan * 0.01;

            DB::table('penitips')
                ->where('id_penitip', $komisiTerbanyak->id_penitip)
                ->increment('poin', $bonusPoin);

            Log::info("Bonus poin {$bonusPoin} diberikan kepada penitip ID {$komisiTerbanyak->id_penitip}");
        } else {
            Log::info('Tidak ada data komisi penitip bulan ini.');
        }
    }
}
