<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id('nota_penjualan');
            $table->date('tanggal_transaksi')->default(now());
            $table->date('tanggal_lunas')->nullable();
            $table->bigInteger('total_harga');
            $table->enum('status_penjualan', ['lunas', 'belum_lunas', 'batal'])->default('belum_lunas');
            $table->bigInteger('ongkos_kirim')->default(0);
            $table->date('tanggal_diterima')->nullable();
            $table->enum('status_pengiriman', ['dikirim', 'belum_dikirim', 'diterima'])->default('belum_dikirim');
            $table->enum('metode_pengiriman', ['ambil', 'kirim']);
            $table->string('bukti_pembayaran')->nullable();
            $table->unsignedBigInteger('id_pegawai')->nullable();
            $table->unsignedBigInteger('id_alamat');
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawais')->onDelete('set null');
            $table->foreign('id_alamat')->references('id_alamat')->on('alamats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
