<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

// Schedule::call(function () {
//     Artisan::call('transaksi:auto-batal');
// })->everyMinute();

// Schedule::call(function () {
//     Artisan::call('penjualan:h3-barang-habis');
// })->everyTenSeconds();

// Schedule::call(function () {
//     Artisan::call('penjualan:h1-barang-habis');
// })->everyTenSeconds();

// Schedule::call(function () {
//     Artisan::call('transaksi:hangus-2-hari');
// })->everyMinute();

// Schedule::call(function () {
//     Artisan::call('penjualan:cek-barang-7-hari');
// })->everyMinute();

Schedule::call(function () {
    Artisan::call('penjualan:top-seller');
})->everyMinute();
