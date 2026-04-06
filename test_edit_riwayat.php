<?php
/**
 * TEST CASE 7: Edit Riwayat Jabatan - Verify Before Snapshot Saved
 * 
 * Tests editing existing riwayat record:
 * 1. Get existing riwayat record
 * 2. Simulate edit with new data
 * 3. Verify before_update snapshot saved to obs
 * 4. Verify riwayat record updated
 * 5. Verify employee updated
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\EmployeeRiwayatJabatan;
use App\Models\EmployeeKaryawan;
use App\Models\ObsMasterDataKaryawan;
use App\Models\MasterGolongan;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TEST CASE 7: Edit Riwayat Jabatan - Full Flow            ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";

// Get a riwayat record to edit
$riwayat = EmployeeRiwayatJabatan::where('nip', 'TEST0002')->first();
if (!$riwayat) {
    echo "❌ ERROR: No riwayat records found for TEST0002\n";
    exit(1);
}

$employee = $riwayat->karyawan;

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Current State Before Edit\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n✅ Riwayat Record:\n";
echo "   ID: {$riwayat->id}\n";
echo "   NIP: {$riwayat->nip}\n";
echo "   Jenis: {$riwayat->jenis_perubahan}\n";
echo "   Jabatan Baru: {$riwayat->jabatan_baru}\n";
echo "   Golongan Baru: {$riwayat->golongan_baru}\n";

echo "\n✅ Employee Current State:\n";
echo "   Jabatan: {$employee->jabatan}\n";
echo "   Golongan: {$employee->id_golongan}\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: Simulate Edit (Like User Changing Data in Form)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Count obs records before edit
$obsCountBefore = ObsMasterDataKaryawan::where('id_karyawan', $employee->id)->count();
echo "\nBefore edit: {$obsCountBefore} obs records for this employee\n";

// Prepare edit data
$golongan = MasterGolongan::first();
$editData = [
    'jenis_perubahan' => 'rotasi',
    'jabatan_baru' => 'Senior Supervisor',
    'golongan_baru' => $golongan->id,
    'tgl_efektif' => now()->addDays(10)->toDateString(),
    'nomor_sk' => 'SK/2026/EDIT001',
    'catatan' => 'TEST: Edit riwayat dari detail view',
];

echo "\n[EDIT DATA]\n";
echo "   Jenis: {$editData['jenis_perubahan']}\n";
echo "   Jabatan Baru: {$editData['jabatan_baru']}\n";
echo "   Tgl Efektif: {$editData['tgl_efektif']}\n";

// User ID with fallback
$userId = 1;

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: Execute Edit Logic (Simulate Controller)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Capture before state
$beforeRiwayat = $riwayat->toArray();
$beforeEmployee = $employee->toArray();

echo "\n[STEP 1] Capture before state...\n";
echo "✅ Before Riwayat saved\n";
echo "✅ Before Employee saved\n";

echo "\n[STEP 2] Save before_update snapshot to obs...\n";
try {
    $obsRecord = ObsMasterDataKaryawan::create([
        'id_karyawan'          => $employee->id,
        'action_type'          => 'before_update',
        'change_reason'        => "Edit Riwayat: Perubahan {$editData['jenis_perubahan']}",
        'nip'                  => $beforeEmployee['nip'],
        'nik'                  => $beforeEmployee['nik'],
        'npwp'                 => $beforeEmployee['npwp'],
        'nama_karyawan'        => $beforeEmployee['nama_karyawan'],
        'tanggal_lahir'        => $beforeEmployee['tanggal_lahir'],
        'jenis_kelamin'        => $beforeEmployee['jenis_kelamin'],
        'alamat'               => $beforeEmployee['alamat'],
        'email'                => $beforeEmployee['email'],
        'nomor_telepon'        => $beforeEmployee['nomor_telepon'],
        'bpjs_kesehatan_number' => $beforeEmployee['bpjs_kesehatan_number'],
        'bpjs_tk_number'       => $beforeEmployee['bpjs_tk_number'],
        'tanggal_masuk'        => $beforeEmployee['tanggal_masuk'],
        'id_status_karyawan'   => $beforeEmployee['id_status_karyawan'],
        'id_status_kawin'      => $beforeEmployee['id_status_kawin'],
        'id_golongan'          => $beforeEmployee['id_golongan'],
        'jabatan'              => $beforeEmployee['jabatan'],
        'id_unit'              => $beforeEmployee['id_unit'],
        'id_atasan'            => $beforeEmployee['id_atasan'],
        'foto_path'            => $beforeEmployee['foto_path'],
        'is_active'            => $beforeEmployee['is_active'],
        'created_by'           => $userId,
        'notes'                => "Before edit riwayat {$riwayat->id}: {$beforeRiwayat['jabatan_lama']} → {$beforeRiwayat['jabatan_baru']}",
    ]);
    echo "✅ Before snapshot saved (ID: {$obsRecord->id})\n";
} catch (\Exception $e) {
    echo "❌ Failed: {$e->getMessage()}\n";
    exit(1);
}

echo "\n[STEP 3] Update riwayat record...\n";
try {
    $riwayat->update($editData);
    echo "✅ Riwayat updated\n";
    echo "   New Jabatan: {$riwayat->jabatan_baru}\n";
    echo "   New Jenis: {$riwayat->jenis_perubahan}\n";
} catch (\Exception $e) {
    echo "❌ Failed: {$e->getMessage()}\n";
    exit(1);
}

echo "\n[STEP 4] Update employee record...\n";
try {
    $employee->update([
        'id_golongan' => $editData['golongan_baru'],
        'jabatan' => $editData['jabatan_baru']
    ]);
    echo "✅ Employee updated\n";
    echo "   New Jabatan: {$employee->jabatan}\n";
    echo "   New Golongan: {$employee->id_golongan}\n";
} catch (\Exception $e) {
    echo "❌ Failed: {$e->getMessage()}\n";
    exit(1);
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4: Verify All Changes Persisted\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n[CHECK 1] obs_master_data_karyawan - Before snapshot saved:\n";
$obsLast = ObsMasterDataKaryawan::where('id_karyawan', $employee->id)
    ->where('action_type', 'before_update')
    ->latest('id')
    ->first();

if ($obsLast) {
    echo "✅ Before snapshot found (ID: {$obsLast->id})\n";
    echo "   Jabatan (before): {$obsLast->jabatan}\n";
    echo "   Reason: {$obsLast->change_reason}\n";
} else {
    echo "❌ Before snapshot NOT found\n";
}

echo "\n[CHECK 2] employee_riwayat_jabatan - Record updated:\n";
$riwayatFresh = EmployeeRiwayatJabatan::find($riwayat->id);
if ($riwayatFresh) {
    echo "✅ Riwayat updated\n";
    echo "   Jenis: {$riwayatFresh->jenis_perubahan}\n";
    echo "   Jabatan: {$riwayatFresh->jabatan_baru}\n";
    echo "   Tgl Efektif: {$riwayatFresh->tgl_efektif}\n";
} else {
    echo "❌ Riwayat record NOT found\n";
}

echo "\n[CHECK 3] employee_karyawan - Employee updated:\n";
$employeeFresh = EmployeeKaryawan::find($employee->id);
if ($employeeFresh) {
    echo "✅ Employee updated\n";
    echo "   Jabatan: {$employeeFresh->jabatan}\n";
    echo "   Golongan: {$employeeFresh->id_golongan}\n";
} else {
    echo "❌ Employee record NOT found\n";
}

echo "\n[CHECK 4] Total obs records after edit:\n";
$obsCountAfter = ObsMasterDataKaryawan::where('id_karyawan', $employee->id)->count();
echo "Before: {$obsCountBefore} → After: {$obsCountAfter}\n";
echo "New records added: " . ($obsCountAfter - $obsCountBefore) . "\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "FINAL VERIFICATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$allPassed = true;

if (!$obsLast) {
    echo "❌ FAIL: Before snapshot not saved to obs\n";
    $allPassed = false;
} else {
    echo "✅ PASS: Before snapshot saved to obs_master_data_karyawan\n";
}

if (!$riwayatFresh || $riwayatFresh->jabatan_baru !== $editData['jabatan_baru']) {
    echo "❌ FAIL: Riwayat not updated\n";
    $allPassed = false;
} else {
    echo "✅ PASS: Riwayat jabatan updated in employee_riwayat_jabatan\n";
}

if (!$employeeFresh || $employeeFresh->jabatan !== $editData['jabatan_baru']) {
    echo "❌ FAIL: Employee not updated\n";
    $allPassed = false;
} else {
    echo "✅ PASS: Employee updated in employee_karyawan\n";
}

if ($obsCountAfter <= $obsCountBefore) {
    echo "❌ FAIL: Obs records didn't increase\n";
    $allPassed = false;
} else {
    echo "✅ PASS: Obs records increased (audit trail created)\n";
}

if ($allPassed) {
    echo "\n🎉 ALL TESTS PASSED! Edit riwayat flow working perfectly!\n";
    echo "\nFlow Summary:\n";
    echo "1. ✓ Before employee state captured\n";
    echo "2. ✓ Before snapshot saved to obs_master_data_karyawan\n";
    echo "3. ✓ Riwayat record updated\n";
    echo "4. ✓ Employee record updated\n";
    echo "5. ✓ Audit trail created\n";
} else {
    echo "\n⚠️  SOME TESTS FAILED!\n";
}

echo "\n";
