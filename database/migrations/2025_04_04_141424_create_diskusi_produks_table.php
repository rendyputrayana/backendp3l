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
        Schema::create('diskusi_produks', function (Blueprint $table) {
            $table->id('id_diskusi');
            $table->text('isi_diskusi');
            $table->date('tanggal_diskusi');
            $table->unsignedBigInteger('id_pembeli')->nullable();
            $table->unsignedBigInteger('kode_produk');
            $table->unsignedBigInteger('id_pegawai')->nullable();
            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembelis')->onDelete('cascade');
            $table->foreign('kode_produk')->references('kode_produk')->on('barangs')->onDelete('cascade');
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawais')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diskusi_produks');
    }
};
