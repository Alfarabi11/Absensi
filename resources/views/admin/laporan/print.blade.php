<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Kehadiran - CV SAAM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800 p-8">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Kop Surat / Header Laporan -->
        <div class="text-center border-b-2 border-gray-800 pb-4 space-y-1">
            <h1 class="text-xl font-bold uppercase tracking-wide">CV INDONESIA RAYA MERDEKA SAAM</h1>
            <p class="text-xs text-gray-600">Laporan Resmi Rekapitulasi Kehadiran Karyawan</p>
            <p class="text-xs font-semibold text-gray-700 pt-1">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            </p>
        </div>

        <!-- Tabel Data -->
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-400 text-gray-700 uppercase font-bold">
                    <th class="py-2.5 px-3 border border-gray-300 text-center w-10">No</th>
                    <th class="py-2.5 px-3 border border-gray-300">Nama Karyawan</th>
                    <th class="py-2.5 px-3 border border-gray-300 text-center">Tanggal</th>
                    <th class="py-2.5 px-3 border border-gray-300 text-center">Check-In</th>
                    <th class="py-2.5 px-3 border border-gray-300 text-center">Check-Out</th>
                    <th class="py-2.5 px-3 border border-gray-300 text-center">Status</th>
                    <th class="py-2.5 px-3 border border-gray-300 text-center">Koordinat GPS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporan as $index => $item)
                <tr class="border-b border-gray-200">
                    <th class="py-2 px-3 border border-gray-300 text-center font-normal">{{ $index + 1 }}</th>
                    <td class="py-2 px-3 border border-gray-300 font-semibold">{{ $item->user->nama ?? 'N/A' }}</td>
                    <td class="py-2 px-3 border border-gray-300 text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td class="py-2 px-3 border border-gray-300 text-center">{{ $item->waktu_check_in ? \Carbon\Carbon::parse($item->waktu_check_in)->format('H:i:s') : '-' }}</td>
                    <td class="py-2 px-3 border border-gray-300 text-center">{{ $item->waktu_check_out ? \Carbon\Carbon::parse($item->waktu_check_out)->format('H:i:s') : '-' }}</td>
                    <td class="py-2 px-3 border border-gray-300 text-center font-bold">{{ $item->status_kehadiran ?? '-' }}</td>
                    <td class="py-2 px-3 border border-gray-300 text-center font-mono text-[10px]">
                        <div>In: {{ $item->koordinat_in ?? '-' }}</div>
                        <div>Out: {{ $item->koordinat_out ?? '-' }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-6 text-center border border-gray-300 text-gray-500">Tidak ada data kehadiran untuk periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan / Footer -->
        <div class="pt-8 flex justify-between items-end text-xs">
            <div>
                <p>Mengetahui,</p>
                <p class="font-bold mt-12">Pimpinan / HRD CV SAAM</p>
            </div>
            <div class="text-right">
                <p>Surabaya, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                <p class="font-bold mt-12">Administrator Sistem</p>
            </div>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>