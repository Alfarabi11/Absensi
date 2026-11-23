<?php

namespace App\Http\Controllers;

use App\Models\JamKerja;
use Illuminate\Http\Request;

class JamKerjaController extends Controller
{
    // Menampilkan daftar jam kerja
    public function index()
    {
        $jamKerja = JamKerja::all();
        return view('admin.jam-kerja.index', compact('jamKerja'));
    }

    // Menyimpan jam kerja baru
    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string|max:50',
            'waktu_masuk' => 'required',
            'waktu_keluar' => 'required',
            'batas_keterlambatan' => 'required',
        ]);

        JamKerja::create([
            'hari' => $request->hari,
            'waktu_masuk' => $request->waktu_masuk,
            'waktu_keluar' => $request->waktu_keluar,
            'batas_keterlambatan' => $request->batas_keterlambatan,
        ]);

        return redirect()->route('admin.jam-kerja.index')->with('success', 'Jam kerja baru berhasil ditambahkan.');
    }

    // Memperbarui pengaturan jam kerja per hari
    public function update(Request $request, JamKerja $jamKerja)
    {
        $request->validate([
            'waktu_masuk' => 'required',
            'waktu_keluar' => 'required',
            'batas_keterlambatan' => 'required',
        ]);

        $jamKerja->update([
            'waktu_masuk' => $request->waktu_masuk,
            'waktu_keluar' => $request->waktu_keluar,
            'batas_keterlambatan' => $request->batas_keterlambatan,
        ]);

        return redirect()->route('admin.jam-kerja.index')->with('success', "Jam kerja hari {$jamKerja->hari} berhasil diperbarui.");
    }

    // Menghapus data jam kerja
    public function destroy(JamKerja $jamKerja)
    {
        $hari = $jamKerja->hari;
        $jamKerja->delete();

        return redirect()->route('admin.jam-kerja.index')->with('success', "Jam kerja hari {$hari} berhasil dihapus.");
    }
}