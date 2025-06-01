<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penitip;
use App\Models\AkumulasiRating;
use App\Models\Penitipan;
use App\Models\Barang;
use Illuminate\Support\Facades\Log;

class RecalculateRating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rating:recalculate {id_penitip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hitung ulang rata-rata rating berdasarkan barang yang sudah diberi rating oleh pembeli';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id_penitip = $this->argument('id_penitip');

        $penitip = Penitip::find($id_penitip);
        if (!$penitip) {
            $this->error('Penitip tidak ditemukan.');
            return;
        }

        $akumulasi = AkumulasiRating::firstOrNew(['id_penitip' => $penitip->id_penitip]);
        $akumulasi->akumulasi = 0.0;

        $penitipan = Penitipan::where('id_penitip', $penitip->id_penitip)->get();

        if ($penitipan->isEmpty()) {
            $this->warn("Tidak ada penitipan untuk penitip ini.");
            $akumulasi->akumulasi = 0.0;
            $akumulasi->save();
            return;
        }

        $notaList = $penitipan->pluck('nota_penitipan');

        $barangRated = Barang::whereIn('nota_penitipan', $notaList)
            ->where('rating_barang', '>', 0)
            ->get();

        $jumlahRating = $barangRated->count();
        $totalRating = $barangRated->sum('rating_barang');

        Log::info("Penitip ID: $id_penitip | Total Rating: $totalRating | Jumlah Barang Dirating: $jumlahRating");

        $meanRating = $jumlahRating > 0 ? round($totalRating / $jumlahRating, 2) : 0.0;

        $akumulasi->akumulasi = $meanRating;
        $akumulasi->save();

        $this->info("Akumulasi rating berhasil diperbarui menjadi $meanRating");
    }
}
