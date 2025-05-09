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
        Schema::create('penggunas', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->string('email')->unique()->nullable();
            $table->string('fcm_token', 512)->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->unsignedBigInteger('id_organisasi')->nullable();
            $table->unsignedBigInteger('id_hunter')->nullable();
            $table->unsignedBigInteger('id_pembeli')->nullable();
            $table->unsignedBigInteger('id_pegawai')->nullable();
            $table->unsignedBigInteger('id_penitip')->nullable();
            $table->foreign('id_organisasi')->references('id_organisasi')->on('organisasis')->onDelete('cascade');
            $table->foreign('id_hunter')->references('id_hunter')->on('hunters')->onDelete('cascade');
            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembelis')->onDelete('cascade');
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawais')->onDelete('cascade');
            $table->foreign('id_penitip')->references('id_penitip')->on('penitips')->onDelete('cascade');
            $table->string('otp')->nullable();
            $table->timestamp('otp_expired_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggunas');
    }
};
