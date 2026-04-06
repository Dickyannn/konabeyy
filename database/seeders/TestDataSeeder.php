<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\EmployeeKaryawan;
use App\Models\MasterGolongan;
use App\Models\MasterStatusKaryawan;
use App\Models\MasterStatusKawin;
use App\Models\MasterUnitPt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create test user
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'nama' => 'Test Admin',
                'password' => bcrypt('password123'),
                'id_role' => 1,
            ]
        );

        // Get or create first available master data (or create if none exist)
        if (MasterGolongan::count() === 0) {
            MasterGolongan::create(['nama_golongan' => 'Pelaksana Teknis I', 'kode_golongan' => 'PLT1', 'is_active' => true]);
        }
        $golongan = MasterGolongan::first();

        if (MasterStatusKaryawan::count() === 0) {
            MasterStatusKaryawan::create(['nama_status' => 'Pegawai Tetap']);
        }
        $statusKaryawan = MasterStatusKaryawan::first();

        if (MasterStatusKawin::count() === 0) {
            MasterStatusKawin::create(['nama_status_kawin' => 'Belum Kawin']);
        }
        $statusKawin = MasterStatusKawin::first();

        if (MasterUnitPt::count() === 0) {
            MasterUnitPt::create(['nama_pt' => 'Head Quarter', 'kode_pt' => 'HQ', 'is_active' => true]);
        }
        $unit = MasterUnitPt::first();

        // Create test employee 1
        EmployeeKaryawan::firstOrCreate(
            ['nip' => 'TEST0001'],
            [
                'nama_karyawan' => 'Test Employee',
                'nik' => '3373020105900001',
                'tanggal_lahir' => '1990-05-01',
                'jenis_kelamin' => 'L',
                'nomor_telepon' => '081234567890',
                'email' => 'employee@test.com',
                'alamat' => 'Jalan Test No. 1',
                'jabatan' => 'Staff',
                'id_golongan' => $golongan?->id ?? 1,
                'id_unit' => $unit?->id ?? 1,
                'id_status_karyawan' => $statusKaryawan?->id ?? 1,
                'id_status_kawin' => $statusKawin?->id ?? 1,
                'tanggal_masuk' => now()->subMonths(6),
                'is_active' => true,
            ]
        );

        // Create test employee 2
        EmployeeKaryawan::firstOrCreate(
            ['nip' => 'TEST0002'],
            [
                'nama_karyawan' => 'Another Employee',
                'nik' => '3373020205800002',
                'tanggal_lahir' => '1988-03-15',
                'jenis_kelamin' => 'P',
                'nomor_telepon' => '081298765432',
                'email' => 'another@test.com',
                'alamat' => 'Jalan Lain No. 2',
                'jabatan' => 'Supervisor',
                'id_golongan' => $golongan?->id ?? 1,
                'id_unit' => $unit?->id ?? 1,
                'id_status_karyawan' => $statusKaryawan?->id ?? 1,
                'id_status_kawin' => $statusKawin?->id ?? 1,
                'tanggal_masuk' => now()->subYear(),
                'is_active' => true,
            ]
        );

        echo "Test data created successfully!\n";
    }
}
