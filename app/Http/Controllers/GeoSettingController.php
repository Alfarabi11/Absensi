<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class GeoSettingController extends Controller
{
    // Menampilkan halaman pengaturan geo-fencing dari tabel settings
    public function index()
    {
        // Ambil semua setting dan ubah menjadi bentuk key => value agar mudah dipanggil di view
        $settings = Setting::pluck('value', 'key_name');

        // Petakan ke array dengan nilai default jika belum ada di database
        $setting = [
            'nama_kantor'  => $settings['kantor_nama'] ?? 'Kantor Pusat CV Indonesia Raya Merdeka SAAM',
            'latitude'     => $settings['kantor_latitude'] ?? -7.25750000,
            'longitude'    => $settings['kantor_longitude'] ?? 112.75210000,
            'radius_meter' => $settings['kantor_radius'] ?? 50,
            'alamat'       => $settings['kantor_alamat'] ?? 'Surabaya, Jawa Timur',
        ];

        return view('admin.settings.index', compact('setting'));
    }

    // Memperbarui pengaturan menggunakan updateOrCreate berdasarkan key_name
    public function update(Request $request)
    {
        $request->validate([
            'nama_kantor'  => 'required|string|max:255',
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'radius_meter' => 'required|integer|min:5',
            'alamat'       => 'nullable|string',
        ]);

        // Simpan atau perbarui per key
        Setting::updateOrCreate(
            ['key_name' => 'kantor_nama'],
            ['value' => $request->nama_kantor, 'keterangan' => 'Nama Kantor / Cabang Utama']
        );

        Setting::updateOrCreate(
            ['key_name' => 'kantor_latitude'],
            ['value' => $request->latitude, 'keterangan' => 'Titik Latitude Kantor untuk Geo-fencing']
        );

        Setting::updateOrCreate(
            ['key_name' => 'kantor_longitude'],
            ['value' => $request->longitude, 'keterangan' => 'Titik Longitude Kantor untuk Geo-fencing']
        );

        Setting::updateOrCreate(
            ['key_name' => 'kantor_radius'],
            ['value' => $request->radius_meter, 'keterangan' => 'Batas Maksimal Radius Absensi (Meter)']
        );

        Setting::updateOrCreate(
            ['key_name' => 'kantor_alamat'],
            ['value' => $request->alamat, 'keterangan' => 'Alamat Lengkap Kantor']
        );

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan Geo-fencing & Lokasi kantor berhasil diperbarui.');
    }
}