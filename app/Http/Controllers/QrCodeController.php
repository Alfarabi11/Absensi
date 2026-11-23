<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class QrCodeController extends Controller
{
    // Menampilkan halaman manajemen QR Code
    public function index()
    {
        // Ambil QR Code yang sedang aktif hari ini
        $activeQr = QrCode::where('status_aktif', '1')->latest()->first();
        
        // Riwayat pembuatan QR Code
        $historyQr = QrCode::latest()->take(5)->get();

        return view('admin.qr.index', compact('activeQr', 'historyQr'));
    }

    // Memproyeksikan QR Code untuk layar penuh (Monitor Kantor / TV)
    public function display()
    {
        $activeQr = QrCode::where('status_aktif', '1')->latest()->first();
        return view('admin.qr.display', compact('activeQr'));
    }

    // Generate token QR Code baru
    public function generate(Request $request)
    {
        // Nonaktifkan semua QR code yang sedang aktif sebelumnya
        QrCode::where('status_aktif', '1')->update(['status_aktif' => '0']);

        // Buat token unik harian
        $token = 'SAAM-QR-' . Carbon::now()->format('Y-m-d') . '-' . Str::upper(Str::random(6));

        QrCode::create([
            'token_qr' => $token,
            'waktu_dibuat' => Carbon::now(),
            'waktu_kadaluwarsa' => Carbon::today()->setHour(17)->setMinute(0)->setSecond(0),
            'status_aktif' => '1',
        ]);

        return redirect()->route('admin.qr.index')->with('success', 'Token QR Code baru berhasil digenerate.');
    }
}