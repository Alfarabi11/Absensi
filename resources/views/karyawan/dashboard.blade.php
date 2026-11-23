@extends('layouts.karyawan')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="space-y-5">

    <!-- Kartu Status Profil & Sisa Cuti -->
    <div class="bg-gradient-to-r from-saamDarkGreen to-saamGreen rounded-2xl p-5 text-white shadow-md flex items-center justify-between">
        <div class="space-y-1">
            <p class="text-xs text-green-200">Sisa Kuota Cuti Tahunan:</p>
            <h3 class="text-2xl font-black text-saamYellow">{{ auth()->user()->kuota_cuti ?? 12 }} <span class="text-xs font-normal text-white">Hari</span></h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-xl">
            <i class="fa-solid fa-umbrella-beach text-saamYellow"></i>
        </div>
    </div>

    <!-- Status Absensi Hari Ini -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h4 class="font-bold text-gray-800 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-clipboard-user text-saamGreen"></i>
                <span>Status Absensi Hari Ini</span>
            </h4>
            <span class="text-xs text-gray-500">{{ Carbon\Carbon::now()->translatedFormat('d M Y') }}</span>
        </div>

        @if($absensiHariIni)
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="bg-gray-50 p-3 rounded-xl border">
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Check-In</p>
                    <p class="text-base font-extrabold text-saamGreen mt-0.5">
                        {{ $absensiHariIni->waktu_check_in ? \Carbon\Carbon::parse($absensiHariIni->waktu_check_in)->format('H:i') : '-' }}
                    </p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl border">
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Check-Out</p>
                    <p class="text-base font-extrabold text-gray-700 mt-0.5">
                        {{ $absensiHariIni->waktu_check_out ? \Carbon\Carbon::parse($absensiHariIni->waktu_check_out)->format('H:i' ) : 'Belum' }}
                    </p>
                </div>
            </div>
            <div class="text-center pt-1">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-saamGreen border border-green-200">
                    Status: {{ $absensiHariIni->status_kehadiran }}
                </span>
            </div>
        @else
            <div class="text-center py-4 space-y-3">
                <p class="text-xs text-gray-500">Anda belum melakukan absensi hari ini.</p>
                <a href="{{ route('karyawan.absensi.index') }}" class="inline-block bg-saamGreen hover:bg-green-800 text-white font-bold px-6 py-2.5 rounded-xl shadow transition text-xs">
                    <i class="fa-solid fa-qrcode mr-1"></i> Scan QR & Absen Sekarang
                </a>
            </div>
        @endif
    </div>

    <!-- Riwayat Absensi Terakhir -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-200 space-y-3">
        <h4 class="font-bold text-gray-800 text-sm flex items-center space-x-2">
            <i class="fa-solid fa-clock-rotate-left text-saamGreen"></i>
            <span>Riwayat Absensi Terakhir</span>
        </h4>
        <div class="space-y-2">
            @forelse($riwayatAbsensi as $item)
                <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50 border text-xs">
                    <div>
                        <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</p>
                        <p class="text-[10px] text-gray-500">In: {{ $item->waktu_check_in ?? '-' }} | Out: {{ $item->waktu_check_out ?? '-' }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold {{ $item->status_kehadiran == 'Hadir' ? 'bg-green-100 text-saamGreen' : 'bg-amber-100 text-amber-800' }}">
                        {{ $item->status_kehadiran }}
                    </span>
                </div>
            @empty
                <p class="text-center text-xs text-gray-400 py-3">Belum ada riwayat absensi.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection