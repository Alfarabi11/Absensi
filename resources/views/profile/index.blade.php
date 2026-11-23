@php
    $user = auth()->user();
    // Deteksi layout otomatis berdasarkan role
    $layout = 'layouts.karyawan';
    if ($user->role == 'Admin') {
        $layout = 'layouts.admin';
    } elseif ($user->role == 'Manajer') {
        $layout = 'layouts.manager';
    }
@endphp

@extends($layout)

@section('title', 'Profil Saya')
@section('header_title', 'Informasi Akun & Data Pribadi')

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

    <!-- Tampilkan Error Validasi Jika Ada -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl text-xs sm:text-sm shadow-sm">
            <p class="font-bold mb-1">Terjadi kesalahan pengisian:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Kartu Ringkasan Akun (Foto & Informasi Status) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 text-center space-y-4 md:col-span-1 flex flex-col items-center justify-center">
            <div class="relative">
                @if($user->foto)
                    <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil" class="w-24 h-24 rounded-full object-cover shadow-md border-2 border-saamGreen">
                @else
                    <div class="w-24 h-24 rounded-full bg-green-100 text-saamGreen flex items-center justify-center text-3xl font-bold shadow-inner border-2 border-saamGreen">
                        {{ substr($user->nama, 0, 1) }}
                    </div>
                @endif
            </div>

            <div class="space-y-1">
                <h3 class="font-bold text-gray-800 text-base">{{ $user->nama }}</h3>
                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                
                <div class="pt-2 flex flex-wrap justify-center gap-1.5">
                    <span class="px-2.5 py-0.5 bg-saamGreen text-white text-[10px] font-bold uppercase rounded-full">
                        {{ $user->role }}
                    </span>
                    <span class="px-2.5 py-0.5 {{ $user->status_aktif == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-[10px] font-bold uppercase rounded-full">
                        {{ $user->status_aktif ?? 'Aktif' }}
                    </span>
                </div>
            </div>

            <!-- Informasi Kuota Cuti (Read-Only) -->
            <div class="w-full bg-gray-50 border border-gray-200 p-3 rounded-xl text-left space-y-1 mt-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Kuota Cuti Tahunan</span>
                <p class="text-sm font-extrabold text-saamDarkGreen flex items-center justify-between">
                    <span>Sisa Cuti:</span>
                    <span class="bg-saamYellow text-saamDarkGreen px-2 py-0.5 rounded-lg text-xs">{{ $user->kuota_cuti ?? 12 }} Hari</span>
                </p>
            </div>
        </div>

        <!-- Form Ubah Data Lengkap -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 md:col-span-2 space-y-4">
            <h3 class="font-bold text-gray-800 text-sm sm:text-base border-b pb-3 flex items-center space-x-2">
                <i class="fa-solid fa-user-pen text-saamGreen"></i>
                <span>Ubah Data Pribadi & Alamat</span>
            </h3>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs sm:text-sm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nomor Telepon / HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen" placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Desa / Kelurahan</label>
                        <input type="text" name="desa" value="{{ old('desa', $user->desa) }}" class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen" placeholder="Nama Desa">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Kecamatan</label>
                        <input type="text" name="kecamatan" value="{{ old('kecamatan', $user->kecamatan) }}" class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen" placeholder="Nama Kecamatan">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen" placeholder="Jalan, RT/RW, Dusun">{{ old('alamat', $user->alamat) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Ganti Foto Profil <span class="text-xs font-normal text-gray-400">(Maks. 2MB: JPG, PNG)</span></label>
                    <input type="file" name="foto" accept="image/*" class="w-full border rounded-xl px-3 py-2 text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-saamGreen hover:file:bg-green-100">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="bg-saamGreen hover:bg-green-800 text-white font-bold px-6 py-2.5 rounded-xl shadow transition text-xs sm:text-sm flex items-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Ubah Password -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 space-y-4">
        <h3 class="font-bold text-gray-800 text-sm sm:text-base border-b pb-3 flex items-center space-x-2">
            <i class="fa-solid fa-key text-saamGreen"></i>
            <span>Ubah Kata Sandi (Password)</span>
        </h3>

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4 text-xs sm:text-sm">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-gray-700 mb-1">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen" placeholder="••••••••">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen" placeholder="Minimal 8 karakter">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" required class="w-full border rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-saamGreen" placeholder="Ulangi kata sandi baru">
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="bg-saamDarkGreen hover:bg-black text-white font-bold px-6 py-2.5 rounded-xl shadow transition text-xs sm:text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Perbarui Kata Sandi</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection