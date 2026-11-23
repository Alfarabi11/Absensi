<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    // Dashboard Utama Karyawan
    public function dashboard()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

        // Cek status absensi hari ini
        $absensiHariIni = Absensi::where('users_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        // Riwayat absensi 5 hari terakhir
        $riwayatAbsensi = Absensi::where('users_id', $user->id)
            ->latest('tanggal')
            ->take(5)
            ->get();

        // Pengajuan izin/cuti terbaru
        $riwayatIzin = PengajuanIzin::where('users_id', $user->id)
            ->latest('tanggal_mulai')
            ->take(3)
            ->get();

        return view('karyawan.dashboard', compact('user', 'absensiHariIni', 'riwayatAbsensi', 'riwayatIzin', 'today'));
    }
}