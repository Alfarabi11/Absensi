<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Admin') - CV SAAM</title>
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
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR (Responsif: tersembunyi di mobile, muncul dengan toggle) -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-saamDarkGreen text-white transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-auto flex flex-col shadow-xl">
            <!-- Brand Logo -->
            <div class="flex items-center justify-center h-16 bg-saamGreen border-b border-green-800 px-4">
                <div class="flex items-center space-x-2">
                    <div class="w-9 h-9 bg-saamYellow text-saamDarkGreen rounded-full flex items-center justify-center font-bold shadow">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <span class="font-extrabold tracking-wider text-base">SAAM ABSENSI</span>
                </div>
            </div>

            <!-- Menu Navigasi Sidebar dengan Pengecekan Active Route -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto text-sm">
                <!-- Menu Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-saamGreen text-white font-semibold shadow' : 'text-gray-300 hover:bg-green-900 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Menu Kelola Pengguna -->
                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.users*') ? 'bg-saamGreen text-white font-semibold shadow' : 'text-gray-300 hover:bg-green-900 hover:text-white' }}">
                    <i class="fa-solid fa-users w-5"></i>
                    <span>Kelola Pengguna</span>
                </a>

                <!-- Menu Jam Kerja -->
                <a href="{{ route('admin.jam-kerja.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.jam-kerja*') ? 'bg-saamGreen text-white font-semibold shadow' : 'text-gray-300 hover:bg-green-900 hover:text-white' }}">
                    <i class="fa-solid fa-clock w-5"></i>
                    <span>Jam Kerja</span>
                </a>

                <!-- Menu Generator QR Code -->
                <a href="{{ route('admin.qr.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.qr*') ? 'bg-saamGreen text-white font-semibold shadow' : 'text-gray-300 hover:bg-green-900 hover:text-white' }}">
                    <i class="fa-solid fa-qrcode w-5"></i>
                    <span>Generator QR Code</span>
                </a>

                <!-- Menu Geo-fencing & Setting -->
                <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.settings*') ? 'bg-saamGreen text-white font-semibold shadow' : 'text-gray-300 hover:bg-green-900 hover:text-white' }}">
                    <i class="fa-solid fa-map-location-dot w-5"></i>
                    <span>Geo-fencing & Setting</span>
                </a>

                <!-- Menu Laporan & Ekspor -->
                <a href="{{ route('admin.laporan.index') }}" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.laporan*') ? 'bg-saamGreen text-white font-semibold shadow' : 'text-gray-300 hover:bg-green-900 hover:text-white' }}">
                    <i class="fa-solid fa-file-lines w-5"></i>
                    <span>Laporan Kehadiran</span>
                </a>
            </nav>

            <!-- Footer Sidebar / Info User Lengkap dengan Foto Profil -->
            <a href="{{ route('profile.edit') }}" class="p-4 bg-green-950 hover:bg-green-900 border-t border-green-900 flex items-center space-x-3 transition group" title="Kelola Profil & Akun">
                @if(auth()->user()->foto)
                    <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="Foto {{ auth()->user()->nama }}" class="w-10 h-10 rounded-full object-cover border border-green-800 flex-shrink-0 shadow group-hover:border-saamYellow transition">
                @else
                    <div class="w-10 h-10 rounded-full bg-green-900 text-green-300 flex items-center justify-center border border-green-800 flex-shrink-0 shadow group-hover:border-saamYellow transition">
                        <i class="fa-solid fa-user text-sm"></i>
                    </div>
                @endif
                <div class="overflow-hidden flex-1">
                    <p class="font-bold text-white text-xs truncate group-hover:text-saamYellow transition flex items-center justify-between">
                        <span>{{ auth()->user()->nama }}</span>
                    </p>
                    <p class="text-gray-400 capitalize text-[11px] font-medium">Role: {{ auth()->user()->role }}</p>
                </div>
            </a>
        </aside>

        <!-- KONTROL UTAMA & KONTEN -->
        <div class="flex flex-col flex-1 h-full overflow-hidden">
            
            <!-- NAVBAR ATAS -->
            <header class="flex items-center justify-between h-16 bg-white border-b border-gray-200 px-4 sm:px-6 shadow-sm z-10">
                <div class="flex items-center space-x-3">
                    <button onclick="toggleSidebar()" class="text-gray-600 hover:text-saamGreen focus:outline-none md:hidden">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-base sm:text-lg font-bold text-gray-800">@yield('header_title', 'Dashboard Admin')</h2>
                </div>

                <!-- Bagian Tombol Aksi Kanan -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    
                    <!-- Tombol Pintasan ke Dashboard Karyawan -->
                    <a href="{{ route('karyawan.dashboard') }}" class="flex items-center space-x-1.5 bg-green-50 hover:bg-green-100 text-saamGreen px-3 py-2 rounded-lg text-xs sm:text-sm font-semibold transition border border-green-200">
                        <i class="fa-solid fa-user-gear"></i>
                        <span class="hidden sm:inline">Panel Karyawan</span>
                    </a>

                    <!-- Tombol Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="inline-flex">
                        @csrf
                        <button type="submit" class="flex items-center space-x-1.5 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg text-xs sm:text-sm font-semibold transition border border-red-200">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>

                </div>
            </header>

            <!-- KONTEN UTAMA HALAMAN -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 sm:p-6 max-w-[1440px] w-full mx-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Script Toggle Sidebar Mobile -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }
    </script>
</body>
</html>