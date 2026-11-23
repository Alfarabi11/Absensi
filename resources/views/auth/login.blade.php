<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi CV Indonesia Raya Merdeka SAAM</title>
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
<body class="bg-gradient-to-br from-saamGreen to-saamDarkGreen flex items-center justify-center min-h-screen p-4">

    <!-- Container Responsif: HP maks 360px, Tablet maks 800px, PC maks 1440px -->
    <div class="w-full max-w-[360px] sm:max-w-[450px] md:max-w-[650px] lg:max-w-[1440px] flex justify-center items-center">
        
        <div class="w-full max-w-[420px] bg-white rounded-2xl shadow-2xl overflow-hidden border-t-4 border-saamYellow p-6 sm:p-8">
            
            <!-- Logo / Header Brand -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-saamLightYellow text-saamGreen rounded-full mb-3 shadow-inner border border-saamYellow">
                    <i class="fa-solid fa-seedling text-2xl"></i>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-800 tracking-wide">CV. SAAM</h1>
                <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">Sistem Absensi Berbasis QR Code</p>
            </div>
            
            <!-- Notifikasi Error -->
            @if($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-md text-xs sm:text-sm flex items-center space-x-2" role="alert">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form Login -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Input Email -->
                <div>
                    <label class="block text-gray-700 text-xs sm:text-sm font-bold mb-1.5" for="email">Email Perusahaan</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input class="w-full pl-10 pr-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-saamGreen focus:border-transparent transition" 
                               id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@saam.com">
                    </div>
                </div>

                <!-- Input Password dengan Fitur Mata (Show/Hide) -->
                <div>
                    <label class="block text-gray-700 text-xs sm:text-sm font-bold mb-1.5" for="password">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input class="w-full pl-10 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-saamGreen focus:border-transparent transition" 
                               id="password" type="password" name="password" required placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-saamGreen focus:outline-none">
                            <i id="eyeIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button class="w-full bg-saamYellow hover:bg-yellow-500 text-saamDarkGreen font-extrabold py-2.5 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-200 text-sm tracking-wide flex items-center justify-center space-x-2" type="submit">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>MASUK SISTEM</span>
                    </button>
                </div>
            </form>

            <!-- Footer / Hak Cipta -->
            <div class="mt-6 text-center border-t border-gray-100 pt-4">
                <p class="text-[11px] text-gray-400">&copy; 2026 CV Indonesia Raya Merdeka SAAM</p>
            </div>
        </div>
    </div>

    <!-- Skrip Toggle Show/Hide Password -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>