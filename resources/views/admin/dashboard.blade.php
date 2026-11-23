@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('header_title', 'Ringkasan Sistem Absensi')

@section('content')
<div class="space-y-6">
    
    <!-- Banner Sambutan -->
    <div class="bg-gradient-to-r from-saamGreen to-green-700 text-white p-6 rounded-2xl shadow-md flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div>
            <h1 class="text-xl sm:text-2xl font-black">Selamat Datang, {{ auth()->user()->nama }}! 👋</h1>
            <p class="text-xs sm:text-sm text-green-100 mt-1">Berikut adalah laporan ringkas aktivitas kehadiran karyawan CV Indonesia Raya Merdeka SAAM hari ini.</p>
        </div>
        <div class="bg-saamYellow text-saamDarkGreen px-4 py-2 rounded-xl text-xs sm:text-sm font-bold shadow">
            <i class="fa-solid fa-calendar-day mr-2"></i> {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d M Y') }}
        </div>
    </div>

    <!-- Statistik Cards (Data Dinamis Database) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Karyawan</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">{{ $totalKaryawan }} <span class="text-xs font-normal text-gray-500">Orang</span></h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hadir Hari Ini</p>
                <h3 class="text-2xl font-black text-saamGreen mt-1">{{ $hadirHariIni }} <span class="text-xs font-normal text-gray-500">Pegawai</span></h3>
            </div>
            <div class="w-12 h-12 bg-green-50 text-saamGreen rounded-xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Terlambat</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $terlambatHariIni }} <span class="text-xs font-normal text-gray-500">Pegawai</span></h3>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-user-clock"></i>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Izin / Cuti Pending</p>
                <h3 class="text-2xl font-black text-purple-600 mt-1">{{ $pendingIzin }} <span class="text-xs font-normal text-gray-500">Pengajuan</span></h3>
            </div>
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
        </div>
    </div>

    <!-- Bagian Tabel Aktivitas & Pengajuan Terbaru (Dinamis) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Tabel Absensi Terbaru -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800 text-base">Aktivitas Absensi Masuk Terbaru</h3>
                <span class="text-xs text-gray-400">Hari Ini</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 uppercase font-semibold text-[11px]">
                            <th class="py-3 px-2">Nama Karyawan</th>
                            <th class="py-3 px-2">Waktu Masuk</th>
                            <th class="py-3 px-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($absensiTerbaru as $absen)
                        <tr>
                            <td class="py-3 px-2 font-medium text-gray-800">{{ $absen->user->nama ?? 'Tidak Diketahui' }}</td>
                            <td class="py-3 px-2 text-gray-600">{{ $absen->waktu_check_in ? \Carbon\Carbon::parse($absen->waktu_check_in)->format('H:i') . ' WIB' : '-' }}</td>
                            <td class="py-3 px-2">
                                @if($absen->status_kehadiran == 'Hadir')
                                    <span class="bg-green-100 text-saamGreen px-2.5 py-1 rounded-full text-[11px] font-bold">Hadir</span>
                                @else
                                    <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full text-[11px] font-bold">{{ $absen->status_kehadiran }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-400 text-xs">Belum ada data absensi hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daftar Pengajuan Cuti Perlu Persetujuan -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="font-bold text-gray-800 text-base mb-4">Persetujuan Cuti / Izin</h3>
            <div class="space-y-4">
                @forelse($pengajuanPending as $izin)
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-bold text-gray-800 text-xs sm:text-sm">{{ $izin->user->nama ?? 'Karyawan' }}</p>
                            <p class="text-xs text-gray-500">{{ $izin->jenis_pengajuan }} ({{ $izin->tanggal_mulai }})</p>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded">Pending</span>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-xs text-center py-6">Tidak ada pengajuan cuti yang tertunda.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection