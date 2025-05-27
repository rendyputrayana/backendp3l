<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penitip;
use App\Models\AkumulasiRating;
use App\Models\Penitipan;
use App\Models\Barang;
use Illuminate\Support\Facades\Log;

class recalculateRating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rating:recalculate {id_penitip} {rating}';

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

        $id_penitip = $this->argument('id_penitip');
        $rating = $this->argument('rating');


        $penitip = Penitip::find($id_penitip);
        if (!$penitip) {
            $this->error('Penitip not found.');
            return;
        }

        $akumulasi = AkumulasiRating::where('id_penitip', $penitip->id_penitip)->first();

        if (!$akumulasi) {
            $akumulasi = new AkumulasiRating();
            $akumulasi->id_penitip = $penitip->id_penitip;
            $akumulasi->akumulasi = 0.0;
        }


        $penitipan = Penitipan::where('id_penitip', $penitip->id_penitip)->get();
        $barang = Barang::where('nota_penitipan', $penitipan->pluck('nota_penitipan'))->get();
        $barangCount = $barang->count();
        Log::info("Jumlah barang: $barangCount");

        $barangRated = Barang::whereIn('nota_penitipan', $penitipan->pluck('nota_penitipan'))
            ->where('rating_barang', '>', 0)
            ->get();

        Log::info("Jumlah barang yang sudah di-rating: " . $barangRated->count());

        $jumlahRatingLama = $barangRated->count();
        $totalRatingLama = $barangRated->sum('rating_barang');
        Log::info("Jumlah rating lama: $jumlahRatingLama");
        Log::info("Total rating lama: $totalRatingLama");

        $totalRatingBaru = $totalRatingLama + $rating;
        Log::info("Total rating baru: $totalRatingBaru");
        $jumlahRatingBaru = $jumlahRatingLama + 1;
        Log::info("Jumlah rating baru: $jumlahRatingBaru");

        $meanRating = round($totalRatingBaru / $jumlahRatingBaru, 2);
        Log::info("Mean rating baru: $meanRating");

        $akumulasi->akumulasi = $meanRating;
        $akumulasi->save();
    }
}
