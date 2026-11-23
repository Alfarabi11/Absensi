<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiKaryawanController extends Controller
{
    // Menampilkan halaman Scan QR & GPS Karyawan
    public function index()
    {
        $user = auth()->user();
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $namaHari = $now->locale('id')->isoFormat('dddd');
        
        // Ambil data absensi hari ini
        $absensi = Absensi::where('users_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        // Ambil data jam kerja hari ini untuk mengetahui jam keluar
        $jamKerja = DB::table('jam_kerja')->where('hari', $namaHari)->first();
        $waktuKeluar = $jamKerja && $jamKerja->waktu_keluar ? $jamKerja->waktu_keluar : '17:00:00';
        
        // Cek apakah waktu sekarang sudah melewati atau sama dengan jam keluar
        $isCheckoutTime = $now->gte(Carbon::createFromTimeString($waktuKeluar));

        return view('karyawan.absensi', compact('absensi', 'today', 'waktuKeluar', 'isCheckoutTime'));
    }

    // Memproses Check-In & Check-Out
    public function store(Request $request)
    {
        try {
            $request->validate([
                'qr_token'  => 'required|string',
                'latitude'  => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            $user = auth()->user();
            $now = Carbon::now();
            $today = $now->format('Y-m-d');
            $namaHari = $now->locale('id')->isoFormat('dddd');

            // ----------------------------------------------------
            // 1. VALIDASI GEO-FENCING (Dari Tabel settings)
            // ----------------------------------------------------
            $settings = DB::table('settings')
                ->whereIn('key_name', ['kantor_latitude', 'kantor_longitude', 'kantor_radius'])
                ->pluck('value', 'key_name');

            $officeLat = (float) ($settings['kantor_latitude'] ?? -6.200000);
            $officeLng = (float) ($settings['kantor_longitude'] ?? 106.816666);
            $maxRadius = (float) ($settings['kantor_radius'] ?? 100);

            $distance = $this->calculateDistance($officeLat, $officeLng, $request->latitude, $request->longitude);

            if ($distance > $maxRadius) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Anda berada di luar area kantor. Jarak Anda: ' . round($distance) . ' meter (Maksimal ' . $maxRadius . ' meter).'
                ], 422);
            }

            // ----------------------------------------------------
            // 2. VALIDASI QR CODE (Dari Tabel qr_codes)
            // ----------------------------------------------------
            $qrData = DB::table('qr_code')
                ->where('token_qr', $request->qr_token)
                ->where('status_aktif', 1)
                ->first();

            if (!$qrData) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid atau sudah kadaluarsa untuk hari ini.'
                ], 422);
            }

            // ----------------------------------------------------
            // 3. CEK STATUS ABSENSI HARI INI (Check-In vs Check-Out)
            // ----------------------------------------------------
            $absensi = Absensi::where('users_id', $user->id)
                ->where('tanggal', $today)
                ->first();

            if (!$absensi) {
                // ================= PROSES CHECK-IN =================
                $jamKerja = DB::table('jam_kerja')->where('hari', $namaHari)->first();

                if ($jamKerja && !empty($jamKerja->batas_keterlambatan)) {
                    $batasKeterlambatan = Carbon::createFromTimeString($jamKerja->batas_keterlambatan);
                } else {
                    $batasKeterlambatan = Carbon::createFromTimeString('08:00:00')->addMinutes(15);
                }

                $statusKehadiran = $now->gt($batasKeterlambatan) ? 'Terlambat' : 'Hadir';

                Absensi::create([
                    'tanggal'          => $today,
                    'waktu_check_in'   => $now,
                    'waktu_check_out'  => null,
                    'koordinat_in'     => $request->latitude . ',' . $request->longitude,
                    'koordinat_out'    => null,
                    'status_kehadiran' => $statusKehadiran,
                    'users_id'         => $user->id,
                    'qr_code_id'       => $qrData->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Check-in berhasil dicatat! Status: ' . $statusKehadiran
                ]);

            } else {
                // ================= PROSES CHECK-OUT =================
                if ($absensi->waktu_check_out) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda sudah melakukan Check-Out hari ini.'
                    ], 422);
                }

                // Ambil jam keluar dari tabel jam_kerja
                $jamKerja = DB::table('jam_kerja')->where('hari', $namaHari)->first();
                $waktuKeluarStr = $jamKerja && $jamKerja->waktu_keluar ? $jamKerja->waktu_keluar : '17:00:00';
                $waktuKeluar = Carbon::createFromTimeString($waktuKeluarStr);

                // Validasi: Apakah sudah waktunya pulang?
                if ($now->lt($waktuKeluar)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal! Belum waktunya jam pulang. Jam keluar kantor adalah pukul ' . $waktuKeluarStr
                    ], 422);
                }

                $absensi->update([
                    'waktu_check_out' => $now,
                    'koordinat_out'   => $request->latitude . ',' . $request->longitude,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Check-out berhasil dicatat. Selamat beristirahat!'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}