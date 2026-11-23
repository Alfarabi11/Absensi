@extends('layouts.admin')

@section('title', 'Geo-fencing & Pengaturan')
@section('header_title', 'Pengaturan Lokasi & Geo-fencing Kantor')

@section('content')
<!-- Leaflet CSS CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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

    <!-- Card Informasi -->
    <div class="bg-gradient-to-r from-saamGreen to-green-800 text-white p-5 rounded-2xl shadow-md flex items-center space-x-4">
        <div class="w-12 h-12 bg-saamYellow text-saamDarkGreen rounded-xl flex items-center justify-center text-xl shadow flex-shrink-0">
            <i class="fa-solid fa-map-location-dot font-bold"></i>
        </div>
        <div>
            <h3 class="font-bold text-sm sm:text-base">Validasi Kehadiran Berbasis Radius (Geo-fencing)</h3>
            <p class="text-xs text-green-100 mt-0.5">Titik koordinat kantor dikunci dari klik acak. Geser ikon penanda (*marker*) pada peta secara manual untuk mengubah lokasi kantor.</p>
        </div>
    </div>

    <!-- Form & Peta Interaktif -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4 text-xs sm:text-sm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Nama Kantor / Cabang</label>
                    <input type="text" name="nama_kantor" value="{{ old('nama_kantor', $setting['nama_kantor']) }}" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Batas Radius Absensi (Meter)</label>
                    <input type="number" name="radius_meter" id="radiusInput" value="{{ old('radius_meter', $setting['radius_meter']) }}" min="5" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
                    <p class="text-[11px] text-gray-400 mt-1">*Contoh: 50 meter dari titik koordinat kantor.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Latitude (Garis Lintang)</label>
                    <input type="text" name="latitude" id="latInput" value="{{ old('latitude', $setting['latitude']) }}" required class="w-full border rounded-xl px-3 py-2.5 bg-gray-50 font-mono" readonly>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Longitude (Garis Bujur)</label>
                    <input type="text" name="longitude" id="lngInput" value="{{ old('longitude', $setting['longitude']) }}" required class="w-full border rounded-xl px-3 py-2.5 bg-gray-50 font-mono" readonly>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Alamat Lengkap Kantor</label>
                <textarea name="alamat" rows="2" class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">{{ old('alamat', $setting['alamat']) }}</textarea>
            </div>

            <!-- Petunjuk Peta & Tombol Reset -->
            <div class="pt-2 space-y-2">
                <div class="flex justify-between items-center">
                    <label class="block font-bold text-gray-700">
                        Peta Lokasi Kantor 
                        <span class="text-xs font-normal text-amber-600 block sm:inline">*(Tahan dan geser ikon penanda hijau untuk memindahkan lokasi)*</span>
                    </label>
                    <button type="button" id="resetBtn" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition border border-gray-300 flex items-center space-x-1.5">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Reset ke Posisi Tersimpan</span>
                    </button>
                </div>
                <div id="map" class="w-full h-80 rounded-xl border border-gray-300 z-10 shadow-inner"></div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-saamGreen hover:bg-green-800 text-white font-bold px-6 py-2.5 rounded-xl shadow transition text-xs sm:text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Pengaturan Geo-fencing</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Leaflet JS CDN -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const originalLat = parseFloat("{{ $setting['latitude'] }}");
        const originalLng = parseFloat("{{ $setting['longitude'] }}");
        const initialRadius = parseInt("{{ $setting['radius_meter'] }}");

        // Inisialisasi Peta
        const map = L.map('map').setView([originalLat, originalLng], 17);

        // Tambah Layer Peta OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Tambah Marker (draggable: true agar bisa digeser) & Lingkaran Radius
        let marker = L.marker([originalLat, originalLng], { draggable: true }).addTo(map);
        let circle = L.circle([originalLat, originalLng], {
            color: '#1b5e20',
            fillColor: '#1b5e20',
            fillOpacity: 0.2,
            radius: initialRadius
        }).addTo(map);

        // Fungsi update nilai form, marker, dan lingkaran
        function updatePosition(lat, lng) {
            document.getElementById('latInput').value = lat.toFixed(8);
            document.getElementById('lngInput').value = lng.toFixed(8);
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);
        }

        // CATATAN KEAMANAN: Perintah map.on('click') SENGAJA DIHAPUS 
        // agar admin tidak tidak sengaja mengubah koordinat lewat klik sembarangan.

        // Event drag pada marker (satu-satunya cara mengubah posisi via peta)
        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            updatePosition(pos.lat, pos.lng);
        });

        // Tombol Reset ke Posisi Awal
        document.getElementById('resetBtn').addEventListener('click', function() {
            updatePosition(originalLat, originalLng);
            map.setView([originalLat, originalLng], 17);
        });

        // Update radius lingkaran ketika input radius diubah
        document.getElementById('radiusInput').addEventListener('input', function(e) {
            const val = parseInt(e.target.value);
            if (!isNaN(val) && val > 0) {
                circle.setRadius(val);
            }
        });
    });
</script>
@endsection