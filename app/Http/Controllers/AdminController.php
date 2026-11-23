<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\PengajuanIzin;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Ambil tanggal hari ini (sesuaikan format database)
        $today = Carbon::now()->toDateString();

        // Hitung statistik untuk widget
        $totalKaryawan = User::where('role', 'Karyawan')->count();
        $hadirHariIni = Absensi::whereDate('tanggal', $today)->where('status_kehadiran', 'Hadir')->count();
        $terlambatHariIni = Absensi::whereDate('tanggal', $today)->where('status_kehadiran', 'Terlambat')->count();
        $pendingIzin = PengajuanIzin::where('status_persetujuan', 'Pending')->count();

        // Ambil data aktivitas absensi terbaru hari ini
        $absensiTerbaru = Absensi::with('user')
            ->whereDate('tanggal', $today)
            ->latest('waktu_check_in')
            ->take(5)
            ->get();

        // Ambil daftar pengajuan cuti/izin yang masih pending
        $pengajuanPending = PengajuanIzin::with('user')
            ->where('status_persetujuan', 'Pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalKaryawan',
            'hadirHariIni',
            'terlambatHariIni',
            'pendingIzin',
            'absensiTerbaru',
            'pengajuanPending',
            'today'
        ));
    }
}