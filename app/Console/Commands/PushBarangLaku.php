<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penitipan;
use App\Models\Pengguna;
use App\Services\FcmService;

class PushBarangLaku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'penjualan:push-barang-laku {nota_penitipan} {kode_produk}';

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
        $nota_penitipan = $this->argument('nota_penitipan');
        $kode_produk = $this->argument('kode_produk');

        $penitipan = Penitipan::where('nota_penitipan', $nota_penitipan)->first();
        $id_penitip = $penitipan->id_penitip;
        $pengguna = Pengguna::where('id_penitip', $id_penitip)->first();
        $fcmToken = $pengguna->fcm_token;
        if (!$fcmToken) {
            $this->error('FCM Token tidak ditemukan untuk penitip dengan ID: ' . $id_penitip);
            return;
        }

        $responseFCM = FcmService::sendNotification(
            $fcmToken,
            'Barang anda sudah laku',
            'Barang dengan kode produk ' . $kode_produk . ' sudah laku',
        );

        if ($responseFCM) {
            $this->info('Notifikasi berhasil dikirim ke penitip');
        } else {
            $this->error('Gagal mengirim notifikasi ke penitip');
        }
            
    }
}
