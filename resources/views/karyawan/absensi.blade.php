@extends('layouts.karyawan')

@section('title', 'Scan Absensi')

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="space-y-5">

    <!-- Header Info -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 text-center space-y-1">
        <h3 class="font-bold text-gray-800 text-base">Absensi Kehadiran</h3>
        <p class="text-xs text-gray-500">
            @if(!$absensi)
                Silakan lakukan <span class="font-bold text-saamGreen">Check-In</span> hari ini.
            @elseif($absensi && !$absensi->waktu_check_out)
                @if($isCheckoutTime)
                    Waktu kerja selesai. Silakan lakukan <span class="font-bold text-amber-600">Check-Out</span>.
                @else
                    Anda sudah Check-In. <span class="font-bold text-blue-600">Check-Out aktif pukul {{ $waktuKeluar }}</span>.
                @endif
            @else
                Absensi hari ini telah <span class="font-bold text-blue-600">Selesai</span>.
            @endif
        </p>
    </div>

    <!-- Kotak Scanner Kamera -->
    @if(!$absensi)
        <!-- Mode Check-In -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 space-y-3">
            <div class="text-center font-bold text-xs text-gray-700">
                <span class="bg-green-100 text-saamGreen px-3 py-1 rounded-full">Mode: Check-In Masuk</span>
            </div>
            <div id="reader" class="overflow-hidden rounded-xl border border-gray-200"></div>
            <div id="scanner-status" class="text-center text-xs font-semibold text-gray-600">
                Mengaktifkan kamera & GPS...
            </div>
        </div>
    @elseif($absensi && !$absensi->waktu_check_out)
        @if($isCheckoutTime)
            <!-- Mode Check-Out (Aktif jika sudah jam pulang) -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 space-y-3">
                <div class="text-center font-bold text-xs text-gray-700">
                    <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full">Mode: Check-Out Pulang</span>
                </div>
                <div id="reader" class="overflow-hidden rounded-xl border border-gray-200"></div>
                <div id="scanner-status" class="text-center text-xs font-semibold text-gray-600">
                    Mengaktifkan kamera & GPS...
                </div>
            </div>
        @else
            <!-- Belum Waktunya Pulang (Scanner Dikunci) -->
            <div class="bg-amber-50 p-6 rounded-2xl border border-amber-200 text-center space-y-3">
                <div class="w-12 h-12 bg-amber-500 text-white rounded-full flex items-center justify-center mx-auto text-xl shadow">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <h4 class="font-bold text-amber-800 text-sm">Belum Waktunya Jam Pulang</h4>
                <p class="text-xs text-gray-600">
                    Anda sudah melakukan Check-In pada pukul <strong>{{ $absensi->waktu_check_in }}</strong>.<br>
                    Scanner Check-Out baru akan aktif otomatis pada pukul <strong class="text-amber-700">{{ $waktuKeluar }}</strong>.
                </p>
            </div>
        @endif
    @else
        <!-- Selesai -->
        <div class="bg-green-50 p-6 rounded-2xl border border-green-200 text-center space-y-2">
            <div class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center mx-auto text-xl shadow">
                <i class="fa-solid fa-check"></i>
            </div>
            <h4 class="font-bold text-saamGreen text-sm">Absensi Selesai Hari Ini</h4>
            <p class="text-xs text-gray-600">Anda telah melakukan Check-In dan Check-Out dengan sukses.</p>
        </div>
    @endif

    <!-- Informasi GPS & Peta Leaflet -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 space-y-3">
        <div class="flex justify-between items-center">
            <h4 class="font-bold text-gray-800 text-xs flex items-center space-x-1.5">
                <i class="fa-solid fa-location-dot text-saamGreen"></i>
                <span>Status Lokasi GPS Anda</span>
            </h4>
            <span id="gps-status" class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">Mencari Lokasi...</span>
        </div>
        
        <!-- Peta Leaflet -->
        <div id="map" class="w-full h-40 rounded-xl z-0 border"></div>
        
        <div class="text-[11px] text-gray-500 font-mono space-y-0.5">
            <div>Lat: <span id="lat-val">-</span></div>
            <div>Lng: <span id="lng-val">-</span></div>
        </div>
    </div>

    <!-- Ringkasan Absensi Hari Ini -->
    <div class="bg-gray-50 p-4 rounded-2xl border text-center space-y-1">
        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Rekap Hari Ini</p>
        <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
            <div class="bg-white p-2.5 rounded-xl border">
                <p class="text-gray-400 text-[10px]">Check-In</p>
                <p class="font-bold text-saamGreen text-sm">{{ $absensi->waktu_check_in ?? '-' }}</p>
            </div>
            <div class="bg-white p-2.5 rounded-xl border">
                <p class="text-gray-400 text-[10px]">Check-Out</p>
                <p class="font-bold text-gray-700 text-sm">{{ $absensi->waktu_check_out ?? 'Belum' }}</p>
            </div>
        </div>
        @if($absensi)
            <p class="text-xs text-saamGreen font-bold mt-2">Status: {{ $absensi->status_kehadiran }}</p>
        @endif
    </div>

</div>
@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Html5-Qrcode Library -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        let currentLat = null;
        let currentLng = null;
        let map, marker;
        let html5QrcodeScanner = null;
        let isProcessing = false; // Mencegah scan berulang ganda

        // Inisialisasi Peta Leaflet
        function initMap(lat, lng) {
            if (!map) {
                map = L.map('map').setView([lat, lng], 17);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                }).addTo(map);
                marker = L.marker([lat, lng]).addTo(map);
            } else {
                map.setView([lat, lng], 17);
                marker.setLatLng([lat, lng]);
            }
        }

        // Ambil Lokasi GPS Perangkat
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (position) => {
                    currentLat = position.coords.latitude;
                    currentLng = position.coords.longitude;

                    document.getElementById('lat-val').innerText = currentLat;
                    document.getElementById('lng-val').innerText = currentLng;
                    document.getElementById('gps-status').innerText = 'GPS Terkunci (Aman)';
                    document.getElementById('gps-status').className = 'text-[10px] font-bold text-saamGreen bg-green-50 px-2 py-0.5 rounded-full border border-green-200';

                    initMap(currentLat, currentLng);
                },
                (error) => {
                    document.getElementById('gps-status').innerText = 'GPS Gagal / Izin Ditolak';
                    document.getElementById('gps-status').className = 'text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full border border-red-200';
                    console.error('GPS Error:', error);
                },
                { enableHighAccuracy: true }
            );
        } else {
            alert('Browser Anda tidak mendukung Geolocation.');
        }

        // Handler Scan QR Code yang lebih aman
        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return; // Cegah proses dobel
            
            if (!currentLat || !currentLng) {
                alert('Menunggu sinyal GPS terkunci... Mohon tunggu sebentar.');
                return;
            }

            isProcessing = true;
            console.log("QR Terdeteksi:", decodedText);

            document.getElementById('scanner-status').innerText = 'Memproses absensi, mohon tunggu...';

            // Hentikan scanner dengan aman
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().catch(err => console.error("Gagal clear scanner:", err));
            }

            // Kirim data via Fetch API
            fetch("{{ route('karyawan.absensi.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    qr_token: decodedText,
                    latitude: currentLat,
                    longitude: currentLng
                })
            })
            .then(async response => {
                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error("Respon server bukan JSON valid: " + text);
                }

                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan pada server (HTTP ' + response.status + ')');
                }
                return data;
            })
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert("Gagal: " + error.message);
                window.location.reload();
            });
        }

        // Jalankan Scanner hanya jika elemen 'reader' ada
        document.addEventListener("DOMContentLoaded", function () {
            if (document.getElementById('reader')) {
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", { fps: 10, qrbox: { width: 250, height: 250 } }, false
                );
                html5QrcodeScanner.render(onScanSuccess, (errorMessage) => {});
            }
        });
    </script>
@endpush