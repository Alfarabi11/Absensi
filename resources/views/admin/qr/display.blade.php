<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Absensi - CV SAAM</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        saamGreen: '#1b5e20',
                        saamDarkGreen: '#0f3812',
                        saamYellow: '#fbc02d',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-saamDarkGreen to-saamGreen h-screen flex flex-col justify-between items-center text-white p-6 select-none overflow-hidden">

    <!-- Header / Branding -->
    <div class="text-center mt-4 space-y-1">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-saamYellow text-saamDarkGreen rounded-2xl shadow-lg mb-2">
            <i class="fa-solid fa-seedling text-2xl"></i>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-wider">CV INDONESIA RAYA MERDEKA SAAM</h1>
        <p class="text-xs sm:text-sm text-green-200 font-medium">Silakan pindai QR Code di bawah ini menggunakan perangkat Anda untuk melakukan absensi.</p>
    </div>

    <!-- Kotak QR Code Besar -->
    <div class="bg-white p-8 rounded-3xl shadow-2xl border-4 border-saamYellow flex flex-col items-center justify-center">
        @if($activeQr)
            <div id="qrcodeDisplay" class="bg-white p-2"></div>
            <div class="mt-4 text-center">
                <span class="inline-block bg-green-50 text-saamGreen font-mono font-bold text-xs sm:text-sm px-4 py-1.5 rounded-xl border border-green-200">
                    {{ $activeQr->token_qr }}
                </span>
            </div>
        @else
            <div class="py-16 px-8 text-gray-400 text-center">
                <i class="fa-solid fa-triangle-exclamation text-4xl text-amber-500 mb-2"></i>
                <p class="text-sm font-semibold">QR Code belum digenerate oleh Admin.</p>
            </div>
        @endif
    </div>

    <!-- Footer / Jam Real-time -->
    <div class="text-center mb-4 space-y-1">
        <div id="realtimeClock" class="text-3xl sm:text-4xl font-black tracking-widest text-saamYellow">00:00:00 WIB</div>
        <p class="text-xs text-green-300">Sistem Absensi Terintegrasi Berbasis QR Code & Geo-fencing</p>
    </div>

    <!-- Pustaka QR Code & Jam Digital -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    @if($activeQr)
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new QRCode(document.getElementById("qrcodeDisplay"), {
                text: "{{ $activeQr->token_qr }}",
                width: 280,
                height: 280,
                colorDark: "#0f3812",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    </script>
    @endif

    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('realtimeClock').innerText = `${hours}:${minutes}:${seconds} WIB`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>