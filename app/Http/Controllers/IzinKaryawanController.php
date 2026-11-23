<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinKaryawanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $riwayatIzin = PengajuanIzin::where('users_id',$user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('karyawan.izin.index', compact('riwayatIzin'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'jenis_pengajuan'  => 'required|string',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'       => 'required|string|max:500',
            'berkas_lampiran'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('berkas_lampiran')) {
            $filePath =$request->file('berkas_lampiran')->store('berkas_izin', 'public');
        }

        PengajuanIzin::create([
            'users_id'           => $user->id,
            'jenis_pengajuan'    => $request->jenis_pengajuan,
            'tanggal_mulai'      => $request->tanggal_mulai,
            'tanggal_selesai'    => $request->tanggal_selesai,
            'keterangan'         => $request->keterangan,
            'berkas_lampiran'    => $filePath,
            'status_persetujuan' => 'Pending',
        ]);

        return redirect()->route('karyawan.izin.index')
            ->with('success', 'Pengajuan izin berhasil dikirim.');
    }
}