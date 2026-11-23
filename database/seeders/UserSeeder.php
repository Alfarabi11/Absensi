<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin / HR
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'no_hp' => '081234567890',
            'status_aktif' => '1',
            'alamat' => 'Kantor Pusat',
            'desa' => 'Patokan',
            'kecamatan' => 'Situbondo',
            'kuota_cuti' => 12,
            'role' => 'Admin',
        ]);

        // 2. Akun Manajer
        User::create([
            'nama' => 'Bapak Manajer',
            'email' => 'manajer@gmail.com',
            'password' => Hash::make('password123'),
            'no_hp' => '081234567891',
            'status_aktif' => '1',
            'alamat' => 'Jl. Kenanga No. 12',
            'desa' => 'Dawuhan',
            'kecamatan' => 'Situbondo',
            'kuota_cuti' => 12,
            'role' => 'Manajer',
        ]);

        // 3. Akun Karyawan
        User::create([
            'nama' => 'Ahmad Karyawan',
            'email' => 'ahmad@gmail.com',
            'password' => Hash::make('password123'),
            'no_hp' => '081234567892',
            'status_aktif' => '1',
            'alamat' => 'Jl. Mawar No. 45',
            'desa' => 'Mimbaan',
            'kecamatan' => 'Panji',
            'kuota_cuti' => 12,
            'role' => 'Karyawan',
        ]);

        // 4. Konfigurasi Profil, Koordinat & Geo-fencing Kantor
        DB::table('settings')->updateOrInsert(
            ['key_name' => 'kantor_nama'],
            ['value' => 'CV Indonesia Raya Merdeka SAAM']
        );

        DB::table('settings')->updateOrInsert(
            ['key_name' => 'kantor_alamat'],
            ['value' => 'Jl. Raya Kantor Pusat No. 1, Situbondo']
        );

        DB::table('settings')->updateOrInsert(
            ['key_name' => 'kantor_latitude'],
            ['value' => '-7.708123'] // Sesuaikan latitude kantor Anda
        );

        DB::table('settings')->updateOrInsert(
            ['key_name' => 'kantor_longitude'],
            ['value' => '114.001234'] // Sesuaikan longitude kantor Anda
        );

        DB::table('settings')->updateOrInsert(
            ['key_name' => 'kantor_radius'],
            ['value' => '100'] // Radius maksimal dalam meter
        );
    }
}