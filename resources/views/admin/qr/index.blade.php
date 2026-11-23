@extends('layouts.admin')

@section('title', 'Generator QR Code')
@section('header_title', 'Generator QR Code Absensi Harian')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    
    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-saamGreen text-saamGreen p-4 rounded-xl text-xs sm:text-sm shadow-sm flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Grid Utama -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Kartu QR Code Aktif -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col items-center text-center justify-between">
            <div>
                <span class="bg-green-50 text-saamGreen border border-green-200 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    QR Code Aktif Hari Ini
                </span>
                <p class="text-xs text-gray-400 mt-2">Pindai kode ini menggunakan kamera perangkat karyawan untuk absensi masuk.</p>
            </div>

            <!-- Area Render QR Code -->
            <div class="my-6 p-4 bg-gray-50 border border-gray-200 rounded-2xl shadow-inner inline-block">
                @if($activeQr)
                    <div id="qrcode" class="flex justify-center"></div>
                    <p class="text-[11px] font-mono text-gray-500 mt-3 bg-white px-3 py-1 rounded border border-gray-200 select-all">
                        {{ $activeQr->token_qr }}
                    </p>
                @else
                    <div class="py-12 px-6 text-gray-400 text-xs">
                        <i class="fa-solid fa-qrcode text-4xl mb-2 block text-gray-300"></i>
                        Belum ada QR Code aktif.
                    </div>
                @endif
            </div>

            <!-- Tombol Aksi -->
            <div class="w-full space-y-2">
                <form action="{{ route('admin.qr.generate') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-saamYellow hover:bg-yellow-500 text-saamDarkGreen font-bold py-2.5 px-4 rounded-xl shadow transition text-xs sm:text-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-rotate"></i>
                        <span>Generate QR Code Baru</span>
                    </button>
                </form>

                @if($activeQr)
                <a href="{{ route('admin.qr.display') }}" target="_blank" class="w-full bg-saamGreen hover:bg-green-800 text-white font-semibold py-2.5 px-4 rounded-xl shadow transition text-xs sm:text-sm flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-tv"></i>
                    <span>Buka Tampilan Layar Penuh (TV)</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Informasi & Riwayat Token -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col gap-12 space-y-4">
            <div>
                <h3 class="font-bold text-gray-800 text-base mb-1">Informasi Penggunaan</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Token QR Code diperbarui secara berkala demi keamanan absensi. Pastikan untuk menayangkan layar penuh (*Display TV*) pada monitor di lobi atau ruang utama kantor agar karyawan dapat melakukan pemindaian dengan mudah saat tiba di kantor.
                </p>
            </div>

            <!-- Tabel Riwayat Singkat -->
            <div>
                <h4 class="font-bold text-gray-700 text-xs uppercase tracking-wider mb-3">Riwayat Token Terbaru</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 font-semibold">
                                <th class="py-2 px-2">Token</th>
                                <th class="py-2 px-2">Waktu Dibuat</th>
                                <th class="py-2 px-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($historyQr as $hist)
                            <tr>
                                <td class="py-2 px-2 font-mono text-gray-700 truncate max-w-[120px]">{{ $hist->token_qr }}</td>
                                <td class="py-2 px-2 text-gray-500">{{ \Carbon\Carbon::parse($hist->created_at)->format('d/m H:i') }}</td>
                                <td class="py-2 px-2 text-center">
                                    @if($hist->status_aktif == '1')
                                        <span class="bg-green-100 text-saamGreen px-2 py-0.5 rounded text-[10px] font-bold">Aktif</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-[10px]">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-400">Belum ada riwayat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- CDN QRCode.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
@if($activeQr)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $activeQr->token_qr }}",
            width: 192,
            height: 192,
            colorDark: "#0f3812",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    });
</script>
@endif
@endsection