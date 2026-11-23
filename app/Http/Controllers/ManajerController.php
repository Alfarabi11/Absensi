<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ManajerController extends Controller
{
    // Dashboard Manajer (Ringkasan Kehadiran Hari Ini)
    public function dashboard()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        $totalKaryawan = User::where('role', 'Karyawan')->count();
        $absensiHariIni = Absensi::where('tanggal', $today)->get();
        
        $hadir = $absensiHariIni->where('status_kehadiran', 'Hadir')->count();
        $terlambat = $absensiHariIni->where('status_kehadiran', 'Terlambat')->count();
        $izin = $absensiHariIni->where('status_kehadiran', 'Izin')->count();
        $sakit = $absensiHariIni->where('status_kehadiran', 'Sakit')->count();
        
        // DIPERBAIKI: Menambahkan tanda minus (-) yang sebelumnya terlewat
        $alpha = $totalKaryawan - ($hadir + $terlambat + $izin + $sakit);
        if ($alpha < 0) $alpha = 0;

        $riwayatTerbaru = Absensi::with('user')
            ->where('tanggal', $today)
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('manajer.dashboard', compact(
            'totalKaryawan', 'hadir', 'terlambat', 'izin', 'sakit', 'alpha', 'riwayatTerbaru', 'today'
        ));
    }

    // Laporan Kehadiran Tim untuk Manajer
    public function laporan(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $userId = $request->input('user_id');
        $status = $request->input('status');

        $query = Absensi::with('user')
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($userId) {
            $query->where('users_id', $userId);
        }

        if ($status) {
            $query->where('status_kehadiran', $status);
        }

        $laporan = $query->latest('tanggal')->paginate(15)->withQueryString();
        $employees = User::where('role', 'Karyawan')->orderBy('nama')->get();

        return view('manajer.laporan', compact('laporan', 'employees', 'startDate', 'endDate', 'userId', 'status'));
    }

    // Menampilkan Daftar Perizinan Berdasarkan Tabel 'pengajuan_izin'
    public function perizinan(Request $request)
    {
        $status = $request->input('status', 'Pending'); 
        
        $query = PengajuanIzin::with('user');
        
        if ($status) {
            $query->where('status_persetujuan', $status);
        }

        $listIzin = $query->latest('tanggal_mulai')->paginate(10)->withQueryString();

        return view('manajer.perizinan', compact('listIzin', 'status'));
    }

    // Memproses Approval Perizinan
    // DIPERBAIKI: Menggunakan $izin agar selaras dengan parameter route {izin}
    public function updateApproval(Request $request, PengajuanIzin $izin)
    {
        $request->validate([
            'status_persetujuan' => 'required|in:Disetujui,Ditolak',
        ]);

        // Jika disetujui dan jenis pengajuannya "Cuti Tahunan", kurangi kuota cuti karyawan
        if ($request->status_persetujuan === 'Disetujui' && $izin->status_persetujuan !== 'Disetujui') {
            $karyawan = $izin->user;
            if ($karyawan && $izin->jenis_pengajuan === 'Cuti Tahunan') {
                $jumlahHari = Carbon::parse($izin->tanggal_mulai)->diffInDays(Carbon::parse($izin->tanggal_selesai)) + 1;
                $sisaKuota = max(0, ($karyawan->kuota_cuti ?? 12) - $jumlahHari);
                $karyawan->update(['kuota_cuti' => $sisaKuota]);
            }
        }

        // Perbarui data status persetujuan dan waktu persetujuan
        $izin->update([
            'status_persetujuan' => $request->status_persetujuan,
            'tanggal_persetujuan' => Carbon::now(),
        ]);

        return redirect()->route('manajer.perizinan')->with('success', 'Status persetujuan pengajuan berhasil diperbarui.');
    }
}