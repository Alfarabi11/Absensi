<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\QrCode;
use Carbon\Carbon;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal Otomatis Generate QR Code (Hanya Hari Kerja Senin - Jumat pukul 07:00 Pagi)
Schedule::call(function () {
    // 1. Nonaktifkan semua QR Code yang masih aktif sebelumnya
    QrCode::where('status_aktif', '1')->update(['status_aktif' => '0']);

    // 2. Buat token QR baru khusus hari kerja ini
    $token = 'SAAM-QR-' . Carbon::now()->format('Y-m-d') . '-' . Str::upper(Str::random(6));

    QrCode::create([
        'token_qr' => $token,
        'waktu_dibuat' => Carbon::now(),
        // Kedaluwarsa otomatis pukul 17:00 WIB di hari yang sama
        'waktu_kadaluwarsa' => Carbon::today()->setHour(17)->setMinute(0)->setSecond(0),
        'status_aktif' => '1',
    ]);
})->dailyAt('07:00')->weekdays();

Schedule::call(function () {
    QrCode::where('status_aktif', '1')->update(['status_aktif' => '0']);
})->dailyAt('17:00')->weekdays();