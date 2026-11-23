<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Absensi Karyawan') - CV SAAM</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        saamGreen: '#1b5e20',
                        saamDarkGreen: '#0f3812',
                        saamYellow: '#fbc02d',
                        saamLightYellow: '#fffde7',
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800 flex flex-col min-h-screen">

    <!-- CONTAINER UTAMA (Tampilan mobile-frame di desktop, full-width di HP) -->
    <div class="w-full max-w-md sm:max-w-lg md:max-w-xl mx-auto bg-white min-h-screen shadow-2xl flex flex-col relative pb-24">
        
        <!-- HEADER ATAS -->
        <header class="bg-saamDarkGreen text-white px-5 py-4 flex items-center justify-between sticky top-0 z-40 shadow-md">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-saamYellow text-saamDarkGreen rounded-full flex items-center justify-center font-bold shadow">
                    <i class="fa-solid fa-seedling"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-sm tracking-wide">SAAM ABSENSI</h1>
                    <p class="text-[10px] text-green-200">Halo, {{ auth()->user()->nama }}</p>
                </div>
            </div>

            <!-- Tombol Navigasi Cepat untuk Admin / Manajer yang sedang login sebagai pegawai -->
            <div class="flex items-center space-x-2">
                @if(auth()->user()->role === 'Admin')
                    <a href="{{ route('admin.dashboard') }}" class="bg-white/10 hover:bg-white/25 text-white px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition" title="Kembali ke Panel Admin">
                        <i class="fa-solid fa-gauge mr-1"></i> Admin
                    </a>
                @elseif(auth()->user()->role === 'Manajer')
                    <a href="{{ route('manajer.dashboard') }}" class="bg-white/10 hover:bg-white/25 text-white px-2.5 py-1.5 rounded-lg text-[11px] font-semibold transition" title="Kembali ke Panel Manajer">
                        <i class="fa-solid fa-user-tie mr-1"></i> Manajer
                    </a>
                @endif
                
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-500/20 hover:bg-red-500/40 text-red-200 p-2 rounded-lg text-xs transition" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- KONTEN UTAMA HALAMAN -->
        <main class="flex-1 p-4 sm:p-5 overflow-y-auto">
            @yield('content')
        </main>

        <!-- BOTTOM NAVIGATION BAR (Navigasi Bawah Khas Aplikasi Mobile) -->
        <nav class="fixed bottom-0 left-0 right-0 max-w-md sm:max-w-lg md:max-w-xl mx-auto bg-white border-t border-gray-200 flex justify-around items-center h-16 px-2 z-50 shadow-lg">
            <!-- Beranda -->
            <a href="{{ route('karyawan.dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition {{ request()->routeIs('karyawan.dashboard') ? 'text-saamGreen font-bold' : 'text-gray-400 hover:text-gray-600' }}">
                <i class="fa-solid fa-house text-lg mb-0.5"></i>
                <span class="text-[10px]">Beranda</span>
            </a>

            <!-- Tombol Utama: Absen / Scan QR -->
            <a href="{{ route('karyawan.absensi.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition {{ request()->routeIs('karyawan.absensi*') ? 'text-saamGreen font-bold' : 'text-gray-400 hover:text-gray-600' }}">
                <div class="{{ request()->routeIs('karyawan.absensi*') ? 'bg-saamGreen text-white' : 'bg-green-100 text-saamGreen' }} w-11 h-11 rounded-full flex items-center justify-center shadow-md -mt-4 border-2 border-white transition transform hover:scale-105">
                    <i class="fa-solid fa-qrcode text-lg"></i>
                </div>
                <span class="text-[10px] mt-0.5 font-semibold">Absen</span>
            </a>

            <!-- Izin / Cuti -->
            <a href="{{ route('karyawan.izin.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition {{ request()->routeIs('karyawan.izin*') ? 'text-saamGreen font-bold' : 'text-gray-400 hover:text-gray-600' }}">
                <i class="fa-solid fa-file-lines text-lg mb-0.5"></i>
                <span class="text-[10px]">Izin/Cuti</span>
            </a>

            <!-- Profil (Menggunakan Universal Profile Controller yang sudah dibuat sebelumnya) -->
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center flex-1 py-1 transition {{ request()->routeIs('profile*') ? 'text-saamGreen font-bold' : 'text-gray-400 hover:text-gray-600' }}">
                <i class="fa-solid fa-user text-lg mb-0.5"></i>
                <span class="text-[10px]">Profil</span>
            </a>
        </nav>

    </div>

    @stack('scripts')
</body>
</html>