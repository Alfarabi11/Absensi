@extends('layouts.admin')

@section('title', 'Kelola Pengguna')
@section('header_title', 'Manajemen Akun & Pengguna')

@section('content')
<div class="space-y-6">
    
    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-saamGreen text-saamGreen p-4 rounded-xl text-xs sm:text-sm shadow-sm flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Bar Aksi (Tombol Tambah & Filter) -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
        <button onclick="openModal('addModal')" class="w-full sm:w-auto bg-saamGreen hover:bg-green-800 text-white font-bold px-4 py-2.5 rounded-xl text-xs sm:text-sm shadow transition flex items-center justify-center space-x-2">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah Pengguna Baru</span>
        </button>

        <!-- Filter Role -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="w-full sm:w-auto flex items-center space-x-2">
            <select name="role" onchange="this.form.submit()" class="w-full sm:w-auto text-xs sm:text-sm border border-gray-300 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-saamGreen">
                <option value="">Semua Role</option>
                <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Manajer" {{ request('role') == 'Manajer' ? 'selected' : '' }}>Manajer</option>
                <option value="Karyawan" {{ request('role') == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
            </select>
        </form>
    </div>

    <!-- Tabel Daftar Pengguna -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase font-bold text-[11px]">
                        <th class="py-3 px-4">Nama & Email</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">No. HP / Wilayah</th>
                        <th class="py-3 px-4 text-center">Kuota Cuti</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 flex items-center space-x-3">
                            <!-- Kondisi Tampilkan Foto atau Avatar Icon -->
                            @if($user->foto)
                                <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto {{ $user->nama }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm flex-shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center border border-gray-200 shadow-sm flex-shrink-0">
                                    <i class="fa-solid fa-user text-sm"></i>
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-gray-800">{{ $user->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold 
                                {{ $user->role == 'Admin' ? 'bg-blue-100 text-blue-700' : ($user->role == 'Manajer' ? 'bg-green-100 text-saamGreen' : 'bg-indigo-100 text-indigo-700') }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-600">
                            <p>{{ $user->no_hp ?? '-' }}</p>
                            <p class="text-[11px] text-gray-400">{{ $user->desa ? $user->desa . ', ' .$user->kecamatan : '-' }}</p>
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-gray-700">
                            {{ $user->kuota_cuti }} Hari
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($user->status_aktif == '1')
                                <span class="bg-green-50 text-saamGreen border border-green-200 px-2 py-0.5 rounded-full text-[10px] font-bold">Aktif</span>
                            @else
                                <span class="bg-red-50 text-red-600 border border-red-200 px-2 py-0.5 rounded-full text-[10px] font-bold">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center space-x-1">
                            <!-- Tombol Edit -->
                            <button type="button" 
                                    data-user="{{ json_encode($user) }}" 
                                    onclick="editUser(this)" 
                                    class="bg-amber-50 hover:bg-amber-100 text-amber-700 p-2 rounded-lg transition border border-amber-200" 
                                    title="Edit Pengguna">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            <!-- Tombol Toggle Status -->
                            <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="p-2 rounded-lg transition border {{ $user->status_aktif == '1' ? 'bg-red-50 hover:bg-red-100 text-red-600 border-red-200' : 'bg-green-50 hover:bg-green-100 text-saamGreen border-green-200' }}" title="{{ $user->status_aktif == '1' ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                    <i class="fa-solid {{ $user->status_aktif == '1' ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-400 text-xs">Belum ada data pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Paginasi -->
        <div class="p-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- MODAL TAMBAH PENGGUNA -->
<div id="addModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base">Tambah Pengguna Baru</h3>
            <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs sm:text-sm">
            @csrf
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Role / Hak Akses</label>
                    <select name="role" required class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                        <option value="Karyawan">Karyawan</option>
                        <option value="Manajer">Manajer</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Desa</label>
                    <input type="text" name="desa" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Kecamatan</label>
                    <input type="text" name="kecamatan" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Kuota Cuti (Hari)</label>
                    <input type="number" name="kuota_cuti" value="12" required class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Foto Profil <span class="text-[10px] text-gray-400">(Opsional)</span></label>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <i id="addPreviewIcon" class="fa-solid fa-user text-gray-400 text-xs"></i>
                            <img id="addPreviewImg" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                        </div>
                        <input type="file" name="foto" accept="image/*" onchange="previewAddImage(event)" class="w-full border rounded-xl px-2 py-1.5 text-[11px] focus:ring-2 focus:ring-saamGreen">
                    </div>
                </div>
            </div>
            <div class="pt-3 flex justify-end space-x-2">
                <button type="button" onclick="closeModal('addModal')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-semibold">Batal</button>
                <button type="submit" class="bg-saamGreen hover:bg-green-800 text-white px-4 py-2 rounded-xl font-semibold shadow">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PENGGUNA -->
<div id="editModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base">Edit Data Pengguna</h3>
            <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs sm:text-sm">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" id="edit_nama" name="nama" required class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Email</label>
                    <input type="email" id="edit_email" name="email" required class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Password Baru <span class="text-[10px] text-gray-400">(Opsional)</span></label>
                    <input type="password" name="password" placeholder="Kosongkan jika tetap" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Role / Hak Akses</label>
                    <select id="edit_role" name="role" required class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                        <option value="Karyawan">Karyawan</option>
                        <option value="Manajer">Manajer</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">No. HP / WhatsApp</label>
                    <input type="text" id="edit_no_hp" name="no_hp" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Desa</label>
                    <input type="text" id="edit_desa" name="desa" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Kecamatan</label>
                    <input type="text" id="edit_kecamatan" name="kecamatan" class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Kuota Cuti (Hari)</label>
                    <input type="number" id="edit_kuota_cuti" name="kuota_cuti" required class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-saamGreen">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Ganti Foto <span class="text-[10px] text-gray-400">(Opsional)</span></label>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <div id="editCurrentAvatar" class="w-full h-full flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-user text-xs"></i>
                            </div>
                            <img id="editCurrentImg" src="#" alt="Foto Profil" class="w-full h-full object-cover hidden">
                        </div>
                        <input type="file" name="foto" accept="image/*" onchange="previewEditImage(event)" class="w-full border rounded-xl px-2 py-1.5 text-[11px] focus:ring-2 focus:ring-saamGreen">
                    </div>
                </div>
            </div>
            <div class="pt-3 flex justify-end space-x-2">
                <button type="button" onclick="closeModal('editModal')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-semibold">Batal</button>
                <button type="submit" class="bg-saamGreen hover:bg-green-800 text-white px-4 py-2 rounded-xl font-semibold shadow">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<!-- Skrip JavaScript Modal & Preview Gambar -->
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    // Preview untuk Modal Tambah
    function previewAddImage(event) {
        const reader = new FileReader();
        const img = document.getElementById('addPreviewImg');
        const icon = document.getElementById('addPreviewIcon');
        
        reader.onload = function(){
            img.src = reader.result;
            img.classList.remove('hidden');
            icon.classList.add('hidden');
        }
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Load data dan preview foto eksisting untuk Modal Edit
    function editUser(button) {
        const user = JSON.parse(button.getAttribute('data-user'));
        const form = document.getElementById('editForm');
        form.action = `/admin/users/${user.id}`;
        document.getElementById('edit_nama').value = user.nama;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
        document.getElementById('edit_no_hp').value = user.no_hp || '';
        document.getElementById('edit_desa').value = user.desa || '';
        document.getElementById('edit_kecamatan').value = user.kecamatan || '';
        document.getElementById('edit_kuota_cuti').value = user.kuota_cuti;

        // Atur tampilan foto profil saat ini di modal edit
        const currentImg = document.getElementById('editCurrentImg');
        const currentAvatar = document.getElementById('editCurrentAvatar');
        
        if (user.foto) {
            currentImg.src = `/storage/${user.foto}`;
            currentImg.classList.remove('hidden');
            currentAvatar.classList.add('hidden');
        } else {
            currentImg.classList.add('hidden');
            currentAvatar.classList.remove('hidden');
        }

        openModal('editModal');
    }

    // Preview foto baru saat diganti di Modal Edit
    function previewEditImage(event) {
        const reader = new FileReader();
        const currentImg = document.getElementById('editCurrentImg');
        const currentAvatar = document.getElementById('editCurrentAvatar');
        
        reader.onload = function(){
            currentImg.src = reader.result;
            currentImg.classList.remove('hidden');
            currentAvatar.classList.add('hidden');
        }
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endsection