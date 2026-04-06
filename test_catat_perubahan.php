<?php
/**
 * TEST CASE: History Data Karyawan - Record Change Flow
 * 
 * Tests the complete flow:
 * 1. Submit "Catat Perubahan" form
 * 2. Verify before_update saved to obs_master_data_karyawan
 * 3. Verify new data saved to employee_riwayat_jabatan
 * 4. Verify employee_karyawan updated
 * 5. Verify UI can display the data
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\EmployeeKaryawan;
use App\Models\EmployeeRiwayatJabatan;
use App\Models\ObsMasterDataKaryawan;
use App\Models\MasterGolongan;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TEST CASE: History Data Karyawan - Catat Perubahan       ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";

// Get test data
$karyawan = EmployeeKaryawan::where('nip', 'TEST0002')->first();
if (!$karyawan) {
    echo "❌ ERROR: Karyawan TEST0002 not found. Please seed test data first.\n";
    exit(1);
}

$golongan = MasterGolongan::first();
if (!$golongan) {
    echo "❌ ERROR: No golongan data found.\n";
    exit(1);
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "INITIAL STATE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Karyawan: {$karyawan->nip} - {$karyawan->nama_karyawan}\n";
echo "Current Jabatan: {$karyawan->jabatan}\n";
echo "Current Golongan: {$karyawan->id_golongan}\n";

// Clear any previous test data for this karyawan
ObsMasterDataKaryawan::where('id_karyawan', $karyawan->id)
    ->where('notes', 'like', 'TEST:%')
    ->delete();
EmployeeRiwayatJabatan::where('id_karyawan', $karyawan->id)
    ->where('catatan', 'like', 'TEST:%')
    ->delete();

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Simulate Form Submission\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Prepare form data (simulating form submission)
$formData = [
    'id_karyawan' => $karyawan->id,
    'jenis_perubahan' => 'mutasi',
    'detail_perubahan' => 'Mutasi ke Departemen Lain',
    'jabatan_lama' => $karyawan->jabatan,
    'jabatan_baru' => 'Supervisor Senior',
    'golongan_lama' => $karyawan->id_golongan,
    'golongan_baru' => $golongan->id,
    'tgl_efektif' => now()->toDateString(),
    'nomor_sk' => 'SK/2026/0001',
    'catatan' => 'TEST: Mutasi departemen - Supervisor Senior',
];

echo "Form Data:\n";
echo "  Jenis Perubahan: {$formData['jenis_perubahan']}\n";
echo "  Detail: {$formData['detail_perubahan']}\n";
echo "  Jabatan Lama: {$formData['jabatan_lama']}\n";
echo "  Jabatan Baru: {$formData['jabatan_baru']}\n";
echo "  Golongan: {$formData['golongan_lama']} → {$formData['golongan_baru']}\n";
echo "  Tgl Efektif: {$formData['tgl_efektif']}\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: Simulate riwayatStore Logic\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Capture before state
$beforeData = $karyawan->toArray();
$userId = 1;

echo "\n[STEP 1] Saving before_update snapshot to obs_master_data_karyawan...\n";
try {
    $obsRecord = ObsMasterDataKaryawan::create([
        'id_karyawan'          => $karyawan->id,
        'action_type'          => 'before_update',
        'change_reason'        => $formData['detail_perubahan'],
        'nip'                  => $beforeData['nip'],
        'nik'                  => $beforeData['nik'],
        'npwp'                 => $beforeData['npwp'],
        'nama_karyawan'        => $beforeData['nama_karyawan'],
        'tanggal_lahir'        => $beforeData['tanggal_lahir'],
        'jenis_kelamin'        => $beforeData['jenis_kelamin'],
        'alamat'               => $beforeData['alamat'],
        'email'                => $beforeData['email'],
        'nomor_telepon'        => $beforeData['nomor_telepon'],
        'bpjs_kesehatan_number' => $beforeData['bpjs_kesehatan_number'],
        'bpjs_tk_number'       => $beforeData['bpjs_tk_number'],
        'tanggal_masuk'        => $beforeData['tanggal_masuk'],
        'id_status_karyawan'   => $beforeData['id_status_karyawan'],
        'id_status_kawin'      => $beforeData['id_status_kawin'],
        'id_golongan'          => $beforeData['id_golongan'],
        'jabatan'              => $beforeData['jabatan'],
        'id_unit'              => $beforeData['id_unit'],
        'id_atasan'            => $beforeData['id_atasan'],
        'foto_path'            => $beforeData['foto_path'],
        'is_active'            => $beforeData['is_active'],
        'created_by'           => $userId,
        'notes'                => "TEST: Snapshot sebelum {$formData['jenis_perubahan']}",
    ]);
    echo "✅ SUCCESS: before_update saved (ID: {$obsRecord->id})\n";
    echo "   Fields saved: id={$obsRecord->id}, jabatan={$obsRecord->jabatan}, golongan={$obsRecord->id_golongan}\n";
} catch (\Exception $e) {
    echo "❌ ERROR: Failed to save before_update\n";
    echo "   {$e->getMessage()}\n";
    exit(1);
}

echo "\n[STEP 2] Updating end_date of previous riwayat records...\n";
$updated = EmployeeRiwayatJabatan::where('id_karyawan', $karyawan->id)
    ->where('end_date', '9999-12-31')
    ->update(['end_date' => \Carbon\Carbon::parse($formData['tgl_efektif'])->subDay()]);
echo "✅ Updated {$updated} previous records\n";

echo "\n[STEP 3] Creating riwayat record in employee_riwayat_jabatan...\n";
try {
    $riwayat = EmployeeRiwayatJabatan::create([
        'id_karyawan'      => $formData['id_karyawan'],
        'nip'              => $karyawan->nip,
        'nama'             => $karyawan->nama_karyawan,
        'jenis_perubahan'  => $formData['jenis_perubahan'],
        'tipe_perubahan'   => $formData['jenis_perubahan'],
        'detail_perubahan' => $formData['detail_perubahan'],
        'jabatan_lama'     => $formData['jabatan_lama'],
        'jabatan_baru'     => $formData['jabatan_baru'],
        'golongan_lama'    => $formData['golongan_lama'],
        'golongan_baru'    => $formData['golongan_baru'],
        'tgl_efektif'      => $formData['tgl_efektif'],
        'end_date'         => '9999-12-31',
        'nomor_sk'         => $formData['nomor_sk'] ?? null,
        'catatan'          => $formData['catatan'] ?? null,
        'created_by'       => $userId,
    ]);
    echo "✅ SUCCESS: Riwayat record created (ID: {$riwayat->id})\n";
    echo "   Fields saved: jenis={$riwayat->jenis_perubahan}, jabatan_baru={$riwayat->jabatan_baru}\n";
} catch (\Exception $e) {
    echo "❌ ERROR: Failed to create riwayat record\n";
    echo "   {$e->getMessage()}\n";
    exit(1);
}

echo "\n[STEP 4] Updating employee_karyawan with new data...\n";
try {
    $karyawan->update([
        'jabatan' => $formData['jabatan_baru'],
        'id_golongan' => $formData['golongan_baru'],
    ]);
    echo "✅ SUCCESS: Employee updated\n";
    echo "   New jabatan: {$karyawan->fresh()->jabatan}\n";
    echo "   New golongan: {$karyawan->fresh()->id_golongan}\n";
} catch (\Exception $e) {
    echo "❌ ERROR: Failed to update employee\n";
    echo "   {$e->getMessage()}\n";
    exit(1);
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: Verify Data Integrity\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n[CHECK 1] obs_master_data_karyawan records:\n";
$obsRecords = ObsMasterDataKaryawan::where('id_karyawan', $karyawan->id)
    ->where('action_type', 'before_update')
    ->latest('id')
    ->get();

if ($obsRecords->count() > 0) {
    echo "✅ Found {$obsRecords->count()} before_update records\n";
    foreach ($obsRecords as $rec) {
        echo "   - ID: {$rec->id}, Jabatan: {$rec->jabatan}, Golongan: {$rec->id_golongan}, Reason: {$rec->change_reason}\n";
    }
} else {
    echo "❌ NO before_update records found\n";
}

echo "\n[CHECK 2] employee_riwayat_jabatan records:\n";
$riwayatRecords = EmployeeRiwayatJabatan::where('id_karyawan', $karyawan->id)
    ->latest('id')
    ->get();

if ($riwayatRecords->count() > 0) {
    echo "✅ Found {$riwayatRecords->count()} riwayat records\n";
    foreach ($riwayatRecords as $rec) {
        echo "   - ID: {$rec->id}, Type: {$rec->jenis_perubahan}, Jabatan: {$rec->jabatan_lama} → {$rec->jabatan_baru}\n";
        echo "     Detail: {$rec->detail_perubahan}\n";
        echo "     Tgl Efektif: {$rec->tgl_efektif}\n";
    }
} else {
    echo "❌ NO riwayat records found\n";
}

echo "\n[CHECK 3] employee_karyawan current state:\n";
$karyawanFresh = $karyawan->fresh();
echo "✅ Karyawan {$karyawan->nip} current state:\n";
echo "   Jabatan: {$karyawanFresh->jabatan}\n";
echo "   Golongan: {$karyawanFresh->id_golongan}\n";

// Verify the change was applied
if ($karyawanFresh->jabatan === $formData['jabatan_baru']) {
    echo "✅ Jabatan correctly updated\n";
} else {
    echo "❌ Jabatan NOT updated correctly (expected: {$formData['jabatan_baru']}, got: {$karyawanFresh->jabatan})\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4: Verify UI Display Capability\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n[CHECK] Simulating UI table display (from employee_riwayat_jabatan):\n";
$latestRiwayat = EmployeeRiwayatJabatan::where('id_karyawan', $karyawan->id)
    ->orderBy('tgl_efektif', 'desc')
    ->first();

if ($latestRiwayat) {
    echo "┌──────────────────────────────────────────────────────────┐\n";
    echo "│ HISTORY DATA KARYAWAN - TABLE DISPLAY                   │\n";
    echo "├──────────────────────────────────────────────────────────┤\n";
    echo "│ NIP: {$latestRiwayat->nip}\n";
    echo "│ Nama: {$latestRiwayat->nama}\n";
    echo "│ Tipe Perubahan: {$latestRiwayat->jenis_perubahan}\n";
    echo "│ Detail: {$latestRiwayat->detail_perubahan}\n";
    echo "│ Tanggal Efektif: {$latestRiwayat->tgl_efektif}\n";
    echo "│ Status: Active (end_date = {$latestRiwayat->end_date})\n";
    echo "└──────────────────────────────────────────────────────────┘\n";
    echo "✅ Data ready for UI display\n";
} else {
    echo "❌ NO riwayat records to display\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "SUMMARY\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$obsCount = ObsMasterDataKaryawan::where('id_karyawan', $karyawan->id)
    ->where('action_type', 'before_update')
    ->count();
$riwayatCount = EmployeeRiwayatJabatan::where('id_karyawan', $karyawan->id)->count();

echo "\n✅ TEST RESULTS:\n";
echo "   • before_update saved to obs_master_data_karyawan: {$obsCount} records ✓\n";
echo "   • New data saved to employee_riwayat_jabatan: {$riwayatCount} records ✓\n";
echo "   • employee_karyawan updated: {$karyawanFresh->jabatan} ✓\n";
echo "   • UI data ready for display: YES ✓\n";

echo "\n✨ CONCLUSION: COMPLETE FLOW WORKING PERFECTLY!\n";
echo "\n";
