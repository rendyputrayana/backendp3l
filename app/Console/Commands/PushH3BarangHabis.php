<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use App\Services\FcmService;
use Carbon\Carbon;
use App\Models\Penitipan;
use Illuminate\Support\Facades\Log;
use App\Models\Pengguna;


class PushH3BarangHabis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'penjualan:h3-barang-habis';

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
        $waktuHariini = Carbon::now()->addDays(3);

        $barangHabis = Barang::where('masa_penitipan', '<=', $waktuHariini)
            ->where('status_barang', 'tersedia')
            ->get();


        $penitipans = Penitipan::whereIn('nota_penitipan', $barangHabis->pluck('nota_penitipan'))
            ->get();


        foreach ($penitipans as $penitipan) {
            $id_penitip = $penitipan->id_penitip;
            $penitip = Penitipan::where('id_penitip', $id_penitip)->first();
            $pengguna = Pengguna::where('id_penitip', $id_penitip)->first();
            $fcmToken = $pengguna->fcm_token;
            if(!$fcmToken) {
                $this->error('FCM Token tidak ditemukan untuk penitip dengan ID: ' . $id_penitip);
                //Log:;info('FCM Token tidak ditemukan untuk penitip dengan ID: ' . $id_penitip);
                continue;
            }
            Log::info('Token: ' . $fcmToken);
            
            $responseFCM = FcmService::sendNotification(
                $fcmToken,
                'Masa penitipan akan habis dalam waktu 3 hari',
                'Ada barang penitipan yang akan habis dalam waktu 3 hari, silahkan cek di aplikasi',
            );
            Log::info('ID Penitip: ' . $id_penitip);

            if ($responseFCM) {
                $this->info('Notifikasi berhasil dikirim ke penitip');
            } else {
                $this->error('Gagal mengirim notifikasi ke penitip');
            }
        }

    }
}
