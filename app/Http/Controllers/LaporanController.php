<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // Menampilkan halaman laporan dengan filter
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $userId = $request->input('user_id');
        $status = $request->input('status');

        $query = Absensi::with('user')
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($userId) {
            $query->where('users_id', $userId); // Menyesuaikan foreign key tabel absensi
        }

        if ($status) {
            $query->where('status_kehadiran', $status); // Menyesuaikan kolom status_kehadiran
        }

        $laporan = $query->latest('tanggal')->paginate(15)->withQueryString();
        $employees = User::where('role', 'Karyawan')->orderBy('nama')->get();

        return view('admin.laporan.index', compact('laporan', 'employees', 'startDate', 'endDate', 'userId', 'status'));
    }

    // Ekspor Data ke CSV/Excel
    public function exportCsv(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $userId = $request->input('user_id');
        $status = $request->input('status');

        $query = Absensi::with('user')
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($userId) $query->where('users_id', $userId);
        if ($status) $query->where('status_kehadiran', $status);

        $data = $query->get();

        $filename = "Laporan_Absensi_SAAM_{$startDate}_hingga_{$endDate}.csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            // Header kolom CSV sesuai kolom database absensi
            fputcsv($file, ['No', 'Nama Karyawan', 'Tanggal', 'Check-In', 'Check-Out', 'Status Kehadiran', 'Koordinat In', 'Koordinat Out']);

            $no = 1;
            foreach ($data as $row) {
                fputcsv($file, [
                    $no++,
                    $row->user->name ?? 'N/A',
                    $row->tanggal,
                    $row->waktu_check_in ? Carbon::parse($row->waktu_check_in)->format('H:i:s') : '-',
                    $row->waktu_check_out ? Carbon::parse($row->waktu_check_out)->format('H:i:s') : '-',
                    $row->status_kehadiran ?? '-',
                    $row->koordinat_in ?? '-',
                    $row->koordinat_out ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Tampilan Cetak / PDF Ramah Printer
    public function printPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $userId = $request->input('user_id');
        $status = $request->input('status');

        $query = Absensi::with('user')
            ->whereBetween('tanggal', [$startDate, $endDate]);

        if ($userId) $query->where('users_id', $userId);
        if ($status) $query->where('status_kehadiran', $status);

        $laporan = $query->orderBy('tanggal', 'asc')->get();

        return view('admin.laporan.print', compact('laporan', 'startDate', 'endDate'));
    }

    public function updateStatus(Request $request, Absensi $absensi)
    {
        $request->validate([
            'status_kehadiran' => 'required|in:Hadir,Terlambat,Izin,Sakit,Alpha',
        ]);

        $absensi->update([
            'status_kehadiran' => $request->status_kehadiran,
        ]);

        return redirect()->route('admin.laporan.index', request()->query())
            ->with('success', 'Status kehadiran ' . ($absensi->user->name ?? 'Karyawan') . ' berhasil diperbarui.');
    }
}