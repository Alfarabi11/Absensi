@extends('layouts.manager')

@section('title', 'Dashboard Manajer')
@section('header_title', 'Ringkasan Kehadiran Karyawan Hari Ini')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Informasi Tanggal Hari Ini -->
    <div class="bg-gradient-to-r from-saamDarkGreen to-saamGreen p-6 rounded-2xl text-white shadow-md flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-bold">Halo, {{ auth()->user()->nama }}! 👋</h3>
            <p class="text-xs text-green-200 mt-1">Berikut adalah pemantauan real-time status kehadiran karyawan CV SAAM.</p>
        </div>
        <div class="bg-white/10 px-4 py-2 rounded-xl backdrop-blur border border-white/20 text-xs font-semibold">
            <i class="fa-solid fa-calendar-days mr-1.5"></i> {{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- Kartu Statistik Hari Ini -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 space-y-1">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Karyawan</p>
            <h4 class="text-2xl font-black text-gray-800">{{ $totalKaryawan }}</h4>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 space-y-1">
            <p class="text-[11px] font-bold text-green-600 uppercase tracking-wider">Hadir</p>
            <h4 class="text-2xl font-black text-saamGreen">{{ $hadir }}</h4>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 space-y-1">
            <p class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Terlambat</p>
            <h4 class="text-2xl font-black text-amber-600">{{ $terlambat }}</h4>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 space-y-1">
            <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Izin / Sakit</p>
            <h4 class="text-2xl font-black text-blue-600">{{ $izin + $sakit }}</h4>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 space-y-1 col-span-2 lg:col-span-1">
            <p class="text-[11px] font-bold text-red-600 uppercase tracking-wider">Alpha / Belum Absen</p>
            <h4 class="text-2xl font-black text-red-600">{{ $alpha }}</h4>
        </div>
    </div>

    <!-- Tabel Aktivitas Kehadiran Terbaru Hari Ini -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm sm:text-base flex items-center space-x-2">
                <i class="fa-solid fa-clock-rotate-left text-saamGreen"></i>
                <span>Aktivitas Check-In Hari Ini</span>
            </h3>
            <a href="{{ route('manajer.laporan') }}" class="text-xs font-bold text-saamGreen hover:underline">Lihat Semua Laporan &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase font-bold text-[11px]">
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Nama Karyawan</th>
                        <th class="py-3 px-4 text-center">Check-In</th>
                        <th class="py-3 px-4 text-center">Check-Out</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayatTerbaru as $index => $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 text-center text-gray-400">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 font-bold text-gray-800 flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-full bg-green-100 text-saamGreen flex items-center justify-center font-bold text-xs flex-shrink-0">
                                {{ substr($item->user->nama ?? 'U', 0, 1) }}
                            </div>
                            <span>{{ $item->user->nama ?? 'N/A' }}</span>
                        </td>
                        <td class="py-3 px-4 text-center font-semibold text-gray-700">
                            {{ $item->waktu_check_in ? \Carbon\Carbon::parse($item->waktu_check_in)->format('H:i:s') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-center font-semibold text-gray-700">
                            {{ $item->waktu_check_out ? \Carbon\Carbon::parse($item->waktu_check_out)->format('H:i:s') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($item->status_kehadiran == 'Hadir')
                                <span class="bg-green-100 text-saamGreen px-2.5 py-1 rounded-lg text-[11px] font-bold border border-green-200">Hadir</span>
                            @elseif($item->status_kehadiran == 'Terlambat')
                                <span class="bg-amber-100 text-amber-800 px-2.5 py-1 rounded-lg text-[11px] font-bold border border-amber-200">Terlambat</span>
                            @else
                                <span class="bg-blue-100 text-blue-800 px-2.5 py-1 rounded-lg text-[11px] font-bold border border-blue-200">{{ $item->status_kehadiran }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400 text-xs">
                            Belum ada karyawan yang melakukan check-in hari ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection