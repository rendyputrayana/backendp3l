<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, AlamatController, PegawaiController, PenitipController,
    OrganisasiController, HunterController, PembeliController,
    PenukaranRewardController, RequestDonasiController, FotoBarangController,
    BarangController, DetailKeranjangController, KategoriController,
    SubkategoriController, PenjualanController, DiskusiProdukController, DonasiController
};

// ======================= AUTH =======================
Route::prefix('auth')->group(function () {
    Route::post('/register/pembeli', [AuthController::class, 'registerPembeli']);
    Route::post('/register/pegawai', [AuthController::class, 'registerPegawai']);
    Route::post('/register/penitip', [AuthController::class, 'registerPenitip']);
    Route::post('/register/organisasi', [AuthController::class, 'registerOrganisasi']);
    Route::post('/register/hunter', [AuthController::class, 'registerHunter']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::put('/ubahPasswordPegawai',[AuthController::class, 'changePasswordPegawai']);
    Route::post('/forgot-password', [AuthController::class, 'sendOTP']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

// ======================= DATA MASTER =======================
Route::get('/alamat/search', [AlamatController::class, 'search']);
Route::get('/alamat/{id_pembeli}', [AlamatController::class, 'getAlamatByIdPembeli']);

Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/subkategori/{kategori}', [SubkategoriController::class, 'show']);

// ======================= PUBLIC BARANG =======================
Route::get('/barang', [BarangController::class, 'index']);
Route::get('/barang/search/{keyword}', [BarangController::class, 'search']);
Route::get('/barang/{barang}', [BarangController::class, 'show']);
Route::get('/listBarang/{id_penitip}', [BarangController::class, 'listBarangByIdPenitip']);
Route::get('/barangTersedia', [BarangController::class, 'getBarangTersedia']);
Route::get('/barangByCategory/{id_kategori}', [BarangController::class, 'getBarangByIdKategori']);

Route::get('/foto-barang', [FotoBarangController::class, 'index']);
Route::get('/foto-barang/{fotoBarang}', [FotoBarangController::class, 'show']);
Route::get('/foto-barang/kode_produk/{kode_produk}', [FotoBarangController::class, 'getByBarangId']);





Route::get('/diskusiProduk/{barangs}', [DiskusiProdukController::class, 'show']);
Route::get('/tampilRating/{barang}', [BarangController::class, 'tampilRating']);

// ======================= AUTHENTICATED ROUTES =======================
Route::middleware('auth:sanctum')->group(function () {

    // Donasi
    Route::get('/donasi', [DonasiController::class, 'index']);
    Route::post('/donasi', [DonasiController::class, 'store']);
    Route::get('/donasi/{id}', [DonasiController::class, 'getDonasiById']);

    Route::post('/fcm-token', [AuthController::class, 'postFCMToken']);

    // --------- ALAMAT ---------
    Route::post('/addAlamat/{id_pembeli}', [AlamatController::class, 'store']);

    // --------- PEGAWAI ---------
    Route::get('/pegawai', [PegawaiController::class, 'index']);
    Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show']);
    Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update']);
    Route::delete('/pegawai/{pegawai}', [PegawaiController::class, 'destroy']);
    Route::get('/pegawai/search/{keyword}', [PegawaiController::class, 'search']);

    //---------- KURIR ---------
    Route::get('/kurir/{id_pagawai}', [PegawaiController::class, 'getPegawaiKurir']);

    // --------- PENITIP ---------
    Route::get('/penitip', [PenitipController::class, 'index']);
    Route::get('/penitip/{penitip}', [PenitipController::class, 'show']);
    Route::put('/penitip/{penitip}', [PenitipController::class, 'update']);
    Route::delete('/penitip/{penitip}', [PenitipController::class, 'destroy']);
    Route::get('/penitip/search/{keyword}', [PenitipController::class, 'search']);

    // --------- ORGANISASI ---------
    Route::get('/organisasi', [OrganisasiController::class, 'index']);
    Route::get('/organisasi/{organisasi}', [OrganisasiController::class, 'show']);
    Route::put('/organisasi/{organisasi}', [OrganisasiController::class, 'update']);
    Route::delete('/organisasi/{organisasi}', [OrganisasiController::class, 'destroy']);
    Route::get('/organisasi/search/{keyword}', [OrganisasiController::class, 'search']);

    // --------- HUNTER & PEMBELI ---------
    Route::get('/hunter/{hunter}', [HunterController::class, 'show']);
    Route::get('/pembeli/{pembeli}', [PembeliController::class, 'show']);

    // --------- PENUKARAN REWARD ---------
    Route::get('/penukaran', [PenukaranRewardController::class, 'index']);
    Route::get('/penukaran/{penukaranReward}', [PenukaranRewardController::class, 'show']);
    Route::post('/penukaran', [PenukaranRewardController::class, 'store']);

    // --------- REQUEST DONASI ---------
    Route::get('/request-donasi', [RequestDonasiController::class, 'index']);
    Route::get('/request-donasi/{requestDonasi}', [RequestDonasiController::class, 'show']);
    Route::get('/request-donasi/search/{keyword}', [RequestDonasiController::class, 'search']);
    Route::post('/request-donasi', [RequestDonasiController::class, 'store']);
    Route::put('/request-donasi/{requestDonasi}', [RequestDonasiController::class, 'update']);
    Route::delete('/request-donasi/{requestDonasi}', [RequestDonasiController::class, 'destroy']);
    Route::get('/request-donasi/organisasi/{id_organisasi}', [RequestDonasiController::class, 'filterByOrganisasi']);

    // --------- FOTO BARANG ---------
    Route::post('/foto-barang', [FotoBarangController::class, 'store']);
    Route::put('/foto-barang/{fotoBarang}', [FotoBarangController::class, 'update']);
    Route::delete('/foto-barang/{fotoBarang}', [FotoBarangController::class, 'destroy']);

    // --------- BARANG ---------
    Route::post('/barang', [BarangController::class, 'store']);
    Route::put('/barang/{barang}/perpanjang', [BarangController::class, 'updateStatusPerpanjang']);
    Route::put('/barang/{barang}/ambil', [BarangController::class, 'ambilByPenitip']);
    Route::get('/barang/pembeli/{id_pembeli}', [BarangController::class, 'getByIdPembeli']);

    // --------- KERANJANG ---------
    Route::get('/detail-keranjang/{id_pembeli}', [DetailKeranjangController::class, 'showByIdPembeli']);
    Route::post('/detail-keranjang', [DetailKeranjangController::class, 'addToKeranjang']);
    Route::post('/remove', [DetailKeranjangController::class, 'removeFromKeranjang']);

    // --------- PENJUALAN ---------
    Route::post('/addPenjualan', [PenjualanController::class, 'store']);
    Route::post('/verifPembayaran', [PenjualanController::class, 'verifikasiPenjualan']);
    Route::get('/pengirimanHariIni', [PenjualanController::class, 'getJadwalHariini']);
    Route::put('/selesaikanPenjualanCS', [PenjualanController::class, 'selesaikanTransaksiCS']);
    Route::put('/selesaikanPenjualanKurir', [PenjualanController::class, 'selesaikanTransaksiKurir']);

    // --------- DISKUSI PRODUK ---------
    Route::post('/addByPembeli/{barang}', [DiskusiProdukController::class, 'addByPembeli']);
    Route::post('/addByPegawai/{barang}', [DiskusiProdukController::class, 'addByPegawai']);

    // --------- RATING ---------
    Route::put('/addRating/{barang}', [BarangController::class, 'addRating']);
});
