@extends('layouts.admin')

@section('title', 'Laporan Kehadiran')
@section('header_title', 'Rekapitulasi & Laporan Kehadiran Karyawan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Form Filter Laporan -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
        <form action="{{ route('admin.laporan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs sm:text-sm items-end">
            <div>
                <label class="block font-bold text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Pilih Karyawan</label>
                <select name="user_id" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $userId == $emp->id ? 'selected' : '' }}>{{ $emp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Status Kehadiran</label>
                <select name="status" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                    <option value="">Semua Status</option>
                    <option value="Hadir" {{ $status == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Terlambat" {{ $status == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="Izin" {{ $status == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit" {{ $status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="Alpha" {{ $status == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-saamGreen hover:bg-green-800 text-white font-bold py-2.5 px-3 rounded-xl shadow transition flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.laporan.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2.5 px-3 rounded-xl transition flex items-center justify-center" title="Reset Filter">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tombol Ekspor & Cetak -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="text-xs text-gray-500 font-medium">
            Menampilkan data dari <span class="font-bold text-gray-800">{{ $startDate }}</span> sampai <span class="font-bold text-gray-800">{{ $endDate }}</span>
        </div>
        <div class="flex space-x-2 w-full sm:w-auto">
            <a href="{{ route('admin.laporan.export', request()->all()) }}" class="flex-1 sm:flex-none bg-green-700 hover:bg-green-800 text-white font-semibold px-4 py-2 rounded-xl text-xs shadow transition flex items-center justify-center space-x-2">
                <i class="fa-solid fa-file-excel"></i>
                <span>Ekspor Excel/CSV</span>
            </a>
            <a href="{{ route('admin.laporan.print', request()->all()) }}" target="_blank" class="flex-1 sm:flex-none bg-saamDarkGreen hover:bg-black text-white font-semibold px-4 py-2 rounded-xl text-xs shadow transition flex items-center justify-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak / PDF</span>
            </a>
        </div>
    </div>

    <!-- Tabel Data Laporan -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase font-bold text-[11px]">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Nama Karyawan</th>
                        <th class="py-3.5 px-4 text-center">Tanggal</th>
                        <th class="py-3.5 px-4 text-center">Check-In</th>
                        <th class="py-3.5 px-4 text-center">Check-Out</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Koordinat GPS</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th> <!-- Tambahan Kolom Aksi -->
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($laporan as $index => $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4 text-center text-gray-400 font-medium">
                            {{ $laporan->firstItem() + $index }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-gray-800 flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-full bg-green-100 text-saamGreen flex items-center justify-center font-bold text-xs flex-shrink-0">
                                {{ substr($item->user->nama ?? 'U', 0, 1) }}
                            </div>
                            <span class="truncate max-w-[140px] sm:max-w-xs">{{ $item->user->nama ?? 'Karyawan Dihapus' }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center text-gray-600 font-medium">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-semibold text-gray-700">
                            {{ $item->waktu_check_in ? \Carbon\Carbon::parse($item->waktu_check_in)->format('H:i:s') : '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-semibold text-gray-700">
                            {{ $item->waktu_check_out ? \Carbon\Carbon::parse($item->waktu_check_out)->format('H:i:s') : '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($item->status_kehadiran == 'Hadir')
                                <span class="bg-green-100 text-saamGreen px-2.5 py-1 rounded-lg text-[11px] font-bold border border-green-200">Hadir</span>
                            @elseif($item->status_kehadiran == 'Terlambat')
                                <span class="bg-amber-100 text-amber-800 px-2.5 py-1 rounded-lg text-[11px] font-bold border border-amber-200">Terlambat</span>
                            @elseif($item->status_kehadiran == 'Izin')
                                <span class="bg-blue-100 text-blue-800 px-2.5 py-1 rounded-lg text-[11px] font-bold border border-blue-200">Izin</span>
                            @elseif($item->status_kehadiran == 'Sakit')
                                <span class="bg-purple-100 text-purple-800 px-2.5 py-1 rounded-lg text-[11px] font-bold border border-purple-200">Sakit</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-lg text-[11px] font-bold border border-red-200">{{ $item->status_kehadiran ?? 'Alpha' }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono text-[11px] text-gray-500">
                            <div>In: {{ $item->koordinat_in ?? '-' }}</div>
                            <div>Out: {{ $item->koordinat_out ?? '-' }}</div>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <!-- Tombol Trigger Modal Edit Status -->
                            <button onclick="openEditModal('{{ $item->id }}', '{{ $item->user->nama ?? 'Karyawan' }}', '{{ $item->tanggal }}', '{{ $item->status_kehadiran }}')" 
                                    class="bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-300 p-2 rounded-xl transition text-xs font-semibold flex items-center justify-center space-x-1 mx-auto" title="Ubah Status">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span class="hidden sm:inline">Ubah</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-gray-400 text-xs">
                            <i class="fa-solid fa-folder-open text-3xl mb-2 block text-gray-300"></i>
                            Tidak ada data laporan absensi pada rentang tanggal tersebut.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        @if($laporan->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $laporan->links() }}
        </div>
        @endif
    </div>

</div>
<!-- Modal Edit Status Kehadiran -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl border border-gray-100">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base flex items-center space-x-2">
                <i class="fa-solid fa-user-pen text-saamGreen"></i>
                <span>Ubah Status Kehadiran</span>
            </h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editForm" method="POST" class="space-y-4 text-xs sm:text-sm">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Karyawan</label>
                <input type="text" id="modalNama" class="w-full border rounded-xl px-3 py-2 bg-gray-50 text-gray-600 font-semibold" disabled>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Tanggal Absensi</label>
                <input type="text" id="modalTanggal" class="w-full border rounded-xl px-3 py-2 bg-gray-50 text-gray-600 font-semibold" disabled>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Pilih Status Kehadiran Baru <span class="text-red-500">*</span></label>
                <select name="status_kehadiran" id="modalStatus" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
                    <option value="Hadir">Hadir</option>
                    <option value="Terlambat">Terlambat</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpha">Alpha</option>
                </select>
            </div>

            <div class="flex justify-end space-x-2 pt-3">
                <button type="button" onclick="closeEditModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-xl font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="bg-saamGreen hover:bg-green-800 text-white px-5 py-2.5 rounded-xl font-bold shadow transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript untuk Mengatur Modal -->
<script>
    function openEditModal(id, nama, tanggal, status) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');
        
        // Set action form ke rute update spesifik
        form.action = `/admin/laporan/${id}/update-status`;
        
        // Isi data ke dalam field modal
        document.getElementById('modalNama').value = nama;
        document.getElementById('modalTanggal').value = tanggal;
        document.getElementById('modalStatus').value = status;
        
        // Tampilkan modal
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
    }
</script>
@endsection