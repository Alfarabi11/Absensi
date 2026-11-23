<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Menampilkan halaman profil lengkap
    public function edit()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    // Memperbarui informasi data lengkap profil & foto
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ]);

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
        ];

        // Tanggah unggah foto profil baru jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada di storage
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            // Simpan foto baru ke folder 'foto_profil'
            $path = $request->file('foto')->store('foto_profil', 'public');
            $data['foto'] = $path;
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Data profil lengkap berhasil diperbarui.');
    }

    // Memperbarui Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Kata sandi berhasil diubah.');
    }
}