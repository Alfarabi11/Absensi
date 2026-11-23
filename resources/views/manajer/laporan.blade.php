@extends('layouts.manager')

@section('title', 'Laporan Kehadiran Tim')
@section('header_title', 'Rekapitulasi Kehadiran Karyawan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Form Filter Laporan -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
        <form action="{{ route('manajer.laporan') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs sm:text-sm items-end">
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
                <a href="{{ route('manajer.laporan') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2.5 px-3 rounded-xl transition flex items-center justify-center" title="Reset Filter">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>
        </form>
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
                            <span>{{ $item->user->nama ?? 'N/A' }}</span>
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
                            @else
                                <span class="bg-blue-100 text-blue-800 px-2.5 py-1 rounded-lg text-[11px] font-bold border border-blue-200">{{ $item->status_kehadiran }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono text-[11px] text-gray-500">
                            <div>In: {{ $item->koordinat_in ?? '-' }}</div>
                            <div>Out: {{ $item->koordinat_out ?? '-' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-gray-400 text-xs">
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
@endsection