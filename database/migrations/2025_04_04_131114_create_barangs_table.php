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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id('kode_produk');
            $table->unsignedBigInteger('id_subkategori');
            $table->foreign('id_subkategori')->references('id_subkategori')->on('subkategoris')->onDelete('cascade');
            $table->unsignedBigInteger('id_donasi')->nullable();
            $table->foreign('id_donasi')->references('id_donasi')->on('donasis')->onDelete('cascade');
            $table->unsignedBigInteger('nota_penitipan');
            $table->integer('berat_barang');
            $table->string('nama_barang');
            $table->bigInteger('harga_barang');
            $table->date('masa_penitipan');
            $table->decimal('rating_barang', 2, 1)->nullable();
            $table->enum('status_barang', ['tersedia', 'terjual', 'donasi', 'dikembalikan'])->default('tersedia');
            $table->bigInteger('komisi_penitip');
            $table->bigInteger('komisi_reuseMart');
            $table->bigInteger('komisi_hunter')->nullable();
            $table->boolean('perpanjang')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
