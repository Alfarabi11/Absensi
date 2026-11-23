@extends('layouts.karyawan')

@section('title', 'Pengajuan Izin & Cuti')

@section('content')
<div class="space-y-5">

    <!-- Header / Tombol Buka Form -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex justify-between items-center">
        <div>
            <h3 class="font-bold text-gray-800 text-sm">Permohonan Izin / Cuti</h3>
            <p class="text-xs text-gray-500">Ajukan ketidakhadiran dengan melampirkan keterangan.</p>
        </div>
        <button onclick="toggleModal(true)" class="bg-saamGreen text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow hover:bg-green-700 transition">
            <i class="fa-solid fa-plus mr-1"></i> Buat Pengajuan
        </button>
    </div>

    <!-- Flash Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <!-- Daftar Riwayat Pengajuan -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 space-y-3">
        <h4 class="font-bold text-xs text-gray-700 uppercase tracking-wider">Riwayat Pengajuan Anda</h4>

        @forelse($riwayatIzin as $izin)
            <div class="p-3 rounded-xl border border-gray-100 bg-gray-50 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg 
                        @if($izin->jenis_pengajuan == 'Sakit') bg-blue-100 text-blue-700 
                        @elseif($izin->jenis_pengajuan == 'Cuti') bg-purple-100 text-purple-700 
                        @else bg-amber-100 text-amber-700 @endif">
                        {{ $izin->jenis_pengajuan }}
                    </span>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full 
                        @if($izin->status_persetujuan == 'Disetujui') bg-green-100 text-green-700 
                        @elseif($izin->status_persetujuan == 'Ditolak') bg-red-100 text-red-700 
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ $izin->status_persetujuan }}
                    </span>
                </div>

                <div class="text-xs text-gray-600 space-y-0.5">
                    <div><i class="fa-regular fa-calendar mr-1 text-gray-400"></i> {{ $izin->tanggal_mulai }} s/d {{ $izin->tanggal_selesai }}</div>
                    <div><i class="fa-regular fa-comment mr-1 text-gray-400"></i> {{ $izin->keterangan }}</div>
                </div>

                @if($izin->berkas_lampiran)
                    <div>
                        <a href="{{ asset('storage/' . $izin->berkas_lampiran) }}" target="_blank" class="text-[11px] text-saamGreen font-bold underline">
                            <i class="fa-solid fa-image mr-1"></i> Lihat Berkas Lampiran
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-6 text-gray-400 text-xs">
                Belum ada riwayat pengajuan izin atau cuti.
            </div>
        @endforelse
    </div>

</div>

<!-- Modal Form Pengajuan -->
<div id="izinModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white w-full max-w-md rounded-2xl p-5 space-y-4 shadow-xl">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-gray-800 text-sm">Form Pengajuan Izin / Cuti</h3>
            <button onclick="toggleModal(false)" class="text-gray-400 hover:text-gray-600 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('karyawan.izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Jenis Pengajuan</label>
                <select name="jenis_pengajuan" class="w-full text-xs border rounded-xl p-2.5 bg-gray-50 focus:ring-saamGreen focus:border-saamGreen" required>
                    <option value="Sakit">Sakit</option>
                    <option value="Cuti">Cuti</option>
                    <option value="Izin">Izin Keperluan Lain</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="w-full text-xs border rounded-xl p-2.5 bg-gray-50" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="w-full text-xs border rounded-xl p-2.5 bg-gray-50" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Keterangan / Alasan</label>
                <textarea name="keterangan" rows="3" class="w-full text-xs border rounded-xl p-2.5 bg-gray-50" placeholder="Tuliskan alasan lengkap..." required></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Berkas Lampiran (Opsional)</label>
                <input type="file" name="berkas_lampiran" accept="image/*" class="w-full text-xs border rounded-xl p-2 bg-gray-50 text-gray-500">
            </div>

            <div class="flex space-x-2 pt-2">
                <button type="button" onclick="toggleModal(false)" class="w-1/2 bg-gray-100 text-gray-700 text-xs font-bold py-2.5 rounded-xl hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="w-1/2 bg-saamGreen text-white text-xs font-bold py-2.5 rounded-xl hover:bg-green-700 transition">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(show) {
        const modal = document.getElementById('izinModal');
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection