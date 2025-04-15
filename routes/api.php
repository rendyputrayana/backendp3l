<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\PenukaranRewardController;
use App\Http\Controllers\RequestDonasiController;
use App\Http\Controllers\FotoBarangController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DetailKeranjangController;

Route::prefix('auth')->group(function () {
    Route::post('/register/pembeli', [AuthController::class, 'registerPembeli']);
    Route::post('/register/pegawai', [AuthController::class, 'registerPegawai']);
    Route::post('/register/penitip', [AuthController::class, 'registerPenitip']);
    Route::post('/register/organisasi', [AuthController::class, 'registerOrganisasi']);
    Route::post('/register/hunter', [AuthController::class, 'registerHunter']);

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

Route::get('/alamat/search', [AlamatController::class, 'search']);

// ================= PEGAWAI =================
Route::get('/pegawai', [PegawaiController::class, 'index']);
Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show']);
Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update']);
Route::delete('/pegawai/{pegawai}', [PegawaiController::class, 'destroy']);
Route::get('/pegawai/search/{keyword}', [PegawaiController::class, 'search']);

// ================= PENITIP =================
Route::get('/penitip', [PenitipController::class, 'index']);
Route::get('/penitip/{penitip}', [PenitipController::class, 'show']);
Route::put('/penitip/{penitip}', [PenitipController::class, 'update']);
Route::delete('/penitip/{penitip}', [PenitipController::class, 'destroy']);
Route::get('/penitip/search/{keyword}', [PenitipController::class, 'search']);

// ================= ORGANISASI =================
Route::get('/organisasi', [OrganisasiController::class, 'index']);
Route::get('/organisasi/{organisasi}', [OrganisasiController::class, 'show']);
Route::put('/organisasi/{organisasi}', [OrganisasiController::class, 'update']);
Route::delete('/organisasi/{organisasi}', [OrganisasiController::class, 'destroy']);
Route::get('/organisasi/search/{keyword}', [OrganisasiController::class, 'search']);

//Penukaran
Route::get('/penukaran', [PenukaranRewardController::class, 'index']);
Route::get('/penukaran/{penukaranReward}', [PenukaranRewardController::class, 'show']);
Route::post('/penukaran', [PenukaranRewardController::class, 'store']);

//Request Donasi
Route::get('/request-donasi', [RequestDonasiController::class, 'index']);
Route::get('/request-donasi/{requestDonasi}', [RequestDonasiController::class, 'show']);
Route::post('/request-donasi', [RequestDonasiController::class, 'store']);
Route::put('/request-donasi/{requestDonasi}', [RequestDonasiController::class, 'update']);
Route::delete('/request-donasi/{requestDonasi}', [RequestDonasiController::class, 'destroy']);
Route::get('/request-donasi/search/{keyword}', [RequestDonasiController::class, 'search']);

//Foto Barang
Route::get('/foto-barang', [FotoBarangController::class, 'index']);
Route::get('/foto-barang/{fotoBarang}', [FotoBarangController::class, 'show']);
Route::post('/foto-barang', [FotoBarangController::class, 'store']);
Route::put('/foto-barang/{fotoBarang}', [FotoBarangController::class, 'update']);
Route::delete('/foto-barang/{fotoBarang}', [FotoBarangController::class, 'destroy']);

// ================= Barang =================
Route::get('/barang', [BarangController::class, 'index']);
Route::post('/barang', [BarangController::class, 'store']);
Route::get('/barang/search/{keyword}', [BarangController::class, 'search']);

//detail keranjang
Route::get('/detail-keranjang/{id_pembeli}', [DetailKeranjangController::class, 'showByIdPembeli']);
