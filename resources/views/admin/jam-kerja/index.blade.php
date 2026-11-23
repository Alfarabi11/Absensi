@extends('layouts.admin')

@section('title', 'Kelola Jam Kerja')
@section('header_title', 'Pengaturan Jam Kerja Operasional')

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

    <!-- Bar Aksi (Tombol Tambah) & Informasi -->
    <div class="bg-gradient-to-r from-saamGreen to-green-800 text-white p-5 rounded-2xl shadow-md flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-saamYellow text-saamDarkGreen rounded-xl flex items-center justify-center text-xl shadow flex-shrink-0">
                <i class="fa-solid fa-clock font-bold"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm sm:text-base">Aturan Jam Kerja CV SAAM</h3>
                <p class="text-xs text-green-100 mt-0.5">Kelola jam masuk, pulang, dan toleransi keterlambatan operasional.</p>
            </div>
        </div>
        <button onclick="openModal('addJamModal')" class="w-full sm:w-auto bg-saamYellow hover:bg-yellow-500 text-saamDarkGreen font-bold px-4 py-2.5 rounded-xl text-xs sm:text-sm shadow transition flex items-center justify-center space-x-2 flex-shrink-0">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Jam Kerja</span>
        </button>
    </div>

    <!-- Tabel / Daftar Hari Kerja -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase font-bold text-[11px]">
                        <th class="py-3.5 px-6">Hari Operasional</th>
                        <th class="py-3.5 px-4 text-center">Jam Masuk</th>
                        <th class="py-3.5 px-4 text-center">Jam Keluar</th>
                        <th class="py-3.5 px-4 text-center">Toleransi Terlambat</th>
                        <th class="py-3.5 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jamKerja as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6 font-bold text-gray-800">
                            <i class="fa-solid fa-calendar-day text-saamGreen mr-2"></i>
                            {{ $item->hari }}
                        </td>
                        <td class="py-4 px-4 text-center text-gray-700 font-semibold">
                            <span class="bg-green-50 text-saamGreen px-3 py-1 rounded-lg border border-green-200">
                                {{ \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i') }} WIB
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center text-gray-700 font-semibold">
                            <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg border border-blue-200">
                                {{ \Carbon\Carbon::parse($item->waktu_keluar)->format('H:i') }} WIB
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center font-medium text-amber-700">
                            <span class="bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 text-xs">
                                {{ $item->batas_keterlambatan }} Menit
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center space-x-1">
                            <!-- Tombol Edit -->
                            <button type="button" 
                                    data-item="{{ json_encode($item) }}" 
                                    onclick="editJamKerja(this)" 
                                    class="bg-amber-50 hover:bg-amber-100 text-amber-700 px-3 py-1.5 rounded-xl font-semibold transition text-xs shadow-sm border border-amber-200 inline-flex items-center space-x-1">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Edit</span>
                            </button>

                            <!-- Tombol Hapus -->
                            <form action="{{ route('admin.jam-kerja.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jam kerja hari {{ $item->hari }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-xl font-semibold transition text-xs shadow-sm border border-red-200 inline-flex items-center space-x-1" title="Hapus Jam Kerja">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400 text-xs">Belum ada data jam kerja. Silakan tambahkan jadwal baru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH JAM KERJA -->
<div id="addJamModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base flex items-center space-x-2">
                <i class="fa-solid fa-clock text-saamGreen"></i>
                <span>Tambah Jam Kerja Baru</span>
            </h3>
            <button onclick="closeModal('addJamModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form action="{{ route('admin.jam-kerja.store') }}" method="POST" class="space-y-4 text-xs sm:text-sm">
            @csrf
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Hari / Keterangan Shift</label>
                <input type="text" name="hari" required placeholder="Contoh: Sabtu atau Shift Khusus" class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Waktu Masuk Kantor</label>
                <input type="time" name="waktu_masuk" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Waktu Pulang / Keluar Kantor</label>
                <input type="time" name="waktu_keluar" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
            </div>
            
            <div>
                <label class="block font-bold text-gray-700 mb-1">Toleransi Keterlambatan (Menit)</label>
                <input type="time" name="batas_keterlambatan" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t">
                <button type="button" onclick="closeModal('addJamModal')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-semibold">Batal</button>
                <button type="submit" class="bg-saamGreen hover:bg-green-800 text-white px-4 py-2 rounded-xl font-semibold shadow">Simpan Jam Kerja</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT JAM KERJA -->
<div id="editJamModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base flex items-center space-x-2">
                <i class="fa-solid fa-clock text-saamGreen"></i>
                <span>Edit Jam Kerja Hari <span id="modalHariName" class="text-saamGreen"></span></span>
            </h3>
            <button onclick="closeModal('editJamModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form id="editJamForm" method="POST" class="space-y-4 text-xs sm:text-sm">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-gray-700 mb-1">Waktu Masuk Kantor</label>
                <input type="time" id="edit_waktu_masuk" name="waktu_masuk" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Waktu Pulang / Keluar Kantor</label>
                <input type="time" id="edit_waktu_keluar" name="waktu_keluar" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Toleransi Keterlambatan (Menit)</label>
                <input type="time" id="edit_batas_keterlambatan" name="batas_keterlambatan" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
            </div>

            <div class="pt-3 flex justify-end space-x-2 border-t">
                <button type="button" onclick="closeModal('editJamModal')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-semibold">Batal</button>
                <button type="submit" class="bg-saamGreen hover:bg-green-800 text-white px-4 py-2 rounded-xl font-semibold shadow">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function editJamKerja(button) {
        const item = JSON.parse(button.getAttribute('data-item'));
        const form = document.getElementById('editJamForm');
        
        form.action = `/admin/jam-kerja/${item.id}`;
        document.getElementById('modalHariName').innerText = item.hari;
        document.getElementById('edit_waktu_masuk').value = item.waktu_masuk;
        document.getElementById('edit_waktu_keluar').value = item.waktu_keluar;
        document.getElementById('edit_batas_keterlambatan').value = item.batas_keterlambatan;
        
        openModal('editJamModal');
    }
</script>
@endsection