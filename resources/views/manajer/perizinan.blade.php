@extends('layouts.manager')

@section('title', 'Approval Perizinan')
@section('header_title', 'Persetujuan Pengajuan Izin & Cuti Karyawan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-saamGreen text-saamGreen p-4 rounded-xl text-xs sm:text-sm shadow-sm flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Filter Status -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-wrap gap-2 items-center justify-between">
        <div class="text-xs font-bold text-gray-700">Filter Status Persetujuan:</div>
        <div class="flex flex-wrap gap-2 text-xs">
            <a href="{{ route('manajer.perizinan', ['status' => 'Pending']) }}" class="px-4 py-2 rounded-xl font-semibold transition {{ $status == 'Pending' ? 'bg-amber-500 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Pending
            </a>
            <a href="{{ route('manajer.perizinan', ['status' => 'Disetujui']) }}" class="px-4 py-2 rounded-xl font-semibold transition {{ $status == 'Disetujui' ? 'bg-saamGreen text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Disetujui
            </a>
            <a href="{{ route('manajer.perizinan', ['status' => 'Ditolak']) }}" class="px-4 py-2 rounded-xl font-semibold transition {{ $status == 'Ditolak' ? 'bg-red-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Ditolak
            </a>
            <a href="{{ route('manajer.perizinan', ['status' => '']) }}" class="px-4 py-2 rounded-xl font-semibold transition {{ $status == '' ? 'bg-gray-800 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua
            </a>
        </div>
    </div>

    <!-- Tabel Daftar Pengajuan -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase font-bold text-[11px]">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Nama Karyawan</th>
                        <th class="py-3.5 px-4 text-center">Jenis Pengajuan</th>
                        <th class="py-3.5 px-4 text-center">Tanggal Mulai - Selesai</th>
                        <th class="py-3.5 px-4 text-center">Berkas Lampiran</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi / Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($listIzin as $index => $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4 text-center text-gray-400 font-medium">
                            {{ $listIzin->firstItem() + $index }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-gray-800 flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-full bg-green-100 text-saamGreen flex items-center justify-center font-bold text-xs flex-shrink-0">
                                {{ substr($item->user->nama ?? 'U', 0, 1) }}
                            </div>
                            <span>{{ $item->user->nama ?? 'N/A' }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-100 text-gray-700 border">
                                {{ $item->jenis_pengajuan }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center font-semibold text-gray-700">
                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }} s.d. 
                            {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($item->berkas_lampiran)
                                <a href="{{ asset('storage/' . $item->berkas_lampiran) }}" target="_blank" class="text-saamGreen hover:underline font-semibold flex items-center justify-center space-x-1">
                                    <i class="fa-solid fa-file-arrow-down"></i>
                                    <span>Lihat Berkas</span>
                                </a>
                            @else
                                <span class="text-gray-400 italic">Tidak ada</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($item->status_persetujuan == 'Disetujui')
                                <span class="bg-green-100 text-saamGreen px-2.5 py-1 rounded-lg text-[11px] font-bold border border-green-200">Disetujui</span>
                            @elseif($item->status_persetujuan == 'Ditolak')
                                <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-lg text-[11px] font-bold border border-red-200">Ditolak</span>
                            @else
                                <span class="bg-amber-100 text-amber-800 px-2.5 py-1 rounded-lg text-[11px] font-bold border border-amber-200">Pending</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <button onclick="openApprovalModal('{{ $item->id }}', '{{ $item->user->nama ?? 'Karyawan' }}', '{{ $item->jenis_pengajuan }}', '{{ $item->status_persetujuan }}')" 
                                    class="bg-saamGreen hover:bg-green-800 text-white px-3 py-1.5 rounded-xl transition text-xs font-semibold shadow flex items-center justify-center space-x-1 mx-auto">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Tinjau</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-400 text-xs">
                            <i class="fa-solid fa-folder-open text-3xl mb-2 block text-gray-300"></i>
                            Tidak ada pengajuan izin dengan status "{{ $status }}".
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginasi -->
        @if($listIzin->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $listIzin->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal Approval -->
<div id="approvalModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl border border-gray-100">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base flex items-center space-x-2">
                <i class="fa-solid fa-clipboard-check text-saamGreen"></i>
                <span>Tinjau Pengajuan Izin</span>
            </h3>
            <button onclick="closeApprovalModal()" class="text-gray-400 hover:text-gray-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="approvalForm" method="POST" class="space-y-4 text-xs sm:text-sm">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Karyawan</label>
                <input type="text" id="modalNama" class="w-full border rounded-xl px-3 py-2 bg-gray-50 text-gray-600 font-semibold" disabled>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Jenis Pengajuan</label>
                <input type="text" id="modalJenis" class="w-full border rounded-xl px-3 py-2 bg-gray-50 text-gray-600 font-semibold" disabled>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">Keputusan Persetujuan <span class="text-red-500">*</span></label>
                <select name="status_persetujuan" id="modalStatus" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen font-semibold">
                    <option value="Disetujui">Setujui</option>
                    <option value="Ditolak">Tolak</option>
                </select>
            </div>

            <div class="flex justify-end space-x-2 pt-3">
                <button type="button" onclick="closeApprovalModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2.5 rounded-xl font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="bg-saamGreen hover:bg-green-800 text-white px-5 py-2.5 rounded-xl font-bold shadow transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Keputusan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript Modal Approval -->
<script>
    function openApprovalModal(id, nama, jenis, status) {
        const modal = document.getElementById('approvalModal');
        const form = document.getElementById('approvalForm');
        
        form.action = `/manajer/perizinan/${id}`;
        
        document.getElementById('modalNama').value = nama;
        document.getElementById('modalJenis').value = jenis;
        document.getElementById('modalStatus').value = status === 'Pending' ? 'Disetujui' : status;
        
        modal.classList.remove('hidden');
    }

    function closeApprovalModal() {
        document.getElementById('approvalModal').classList.add('hidden');
    }
</script>
@endsection