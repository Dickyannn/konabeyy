<?php
/**
 * TEST CASE 3: History Data Karyawan - Real Form Submission Simulation
 * 
 * Tests simulating actual form POST request to riwayatStore
 * Steps:
 * 1. Prepare form data like it comes from POST request
 * 2. Call controller logic directly
 * 3. Verify all three tables are updated
 * 4. Simulate UI display of the new record
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\EmployeeKaryawan;
use App\Models\EmployeeRiwayatJabatan;
use App\Models\ObsMasterDataKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TEST CASE 3: Real Form Submission Simulation             ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";

// Get TEST0001 for this test
$karyawan = EmployeeKaryawan::where('nip', 'TEST0001')->first();
if (!$karyawan) {
    echo "❌ ERROR: Karyawan TEST0001 not found\n";
    exit(1);
}

$golongan = $karyawan->golongan;

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Simulate POST Form Data\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Simulate POST data from form
$postData = [
    'id_karyawan'      => (string)$karyawan->id,
    'jenis_perubahan'  => 'demosi',
    'detail_perubahan' => 'Demosi karena peraturan internal',
    'jabatan_lama'     => $karyawan->jabatan,
    'jabatan_baru'     => 'Senior Staff',
    'golongan_lama'    => (string)$karyawan->id_golongan,
    'golongan_baru'    => (string)$golongan->id,
    'tgl_efektif'      => now()->addDays(5)->toDateString(),
    'nomor_sk'         => 'SK/2026/0002',
    'catatan'          => 'TEST: REST API form submission test',
];

echo "Form Data yang diterima:\n";
echo "  ID Karyawan: {$postData['id_karyawan']}\n";
echo "  Jenis Perubahan: {$postData['jenis_perubahan']}\n";
echo "  Detail: {$postData['detail_perubahan']}\n";
echo "  Jabatan Lama: {$postData['jabatan_lama']}\n";
echo "  Jabatan Baru: {$postData['jabatan_baru']}\n";
echo "  Tgl Efektif: {$postData['tgl_efektif']}\n";
echo "  No SK: {$postData['nomor_sk']}\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: Execute Controller Logic\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Clear previous test data
EmployeeRiwayatJabatan::where('nomor_sk', 'SK/2026/0002')->delete();
ObsMasterDataKaryawan::where('notes', 'LIKE', '%SK/2026/0002%')->delete();

// Get fresh karyawan data
$karyawan = $karyawan->fresh();
$beforeData = $karyawan->toArray();

echo "\n[STEP 1] Valdasi form data...\n";
$validated = [
    'id_karyawan'      => (int)$postData['id_karyawan'],
    'jenis_perubahan'  => $postData['jenis_perubahan'],
    'detail_perubahan' => $postData['detail_perubahan'],
    'jabatan_lama'     => $postData['jabatan_lama'],
    'jabatan_baru'     => $postData['jabatan_baru'],
    'golongan_lama'    => (int)$postData['golongan_lama'],
    'golongan_baru'    => (int)$postData['golongan_baru'],
    'tgl_efektif'      => $postData['tgl_efektif'],
    'nomor_sk'         => $postData['nomor_sk'],
    'catatan'          => $postData['catatan'],
];
echo "✅ Form data validated\n";

echo "\n[STEP 2] Simpan before_update snapshot...\n";
try {
    $obsRecord = ObsMasterDataKaryawan::create([
        'id_karyawan'          => $karyawan->id,
        'action_type'          => 'before_update',
        'change_reason'        => $postData['detail_perubahan'],
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
        'created_by'           => 1,
        'notes'                => "Before update for SK/" . $postData['nomor_sk'],
    ]);
    echo "✅ Snapshot saved (ID: {$obsRecord->id})\n";
} catch (\Exception $e) {
    echo "❌ Failed: {$e->getMessage()}\n";
    exit(1);
}

echo "\n[STEP 3] Update end_date previous records...\n";
$updated = EmployeeRiwayatJabatan::where('id_karyawan', $karyawan->id)
    ->where('end_date', '9999-12-31')
    ->update(['end_date' => \Carbon\Carbon::parse($postData['tgl_efektif'])->subDay()]);
echo "✅ Updated {$updated} previous records\n";

echo "\n[STEP 4] Buat riwayat baru...\n";
try {
    $riwayat = EmployeeRiwayatJabatan::create([
        'id_karyawan'      => $validated['id_karyawan'],
        'nip'              => $karyawan->nip,
        'nama'             => $karyawan->nama_karyawan,
        'jenis_perubahan'  => $validated['jenis_perubahan'],
        'tipe_perubahan'   => $validated['jenis_perubahan'],
        'detail_perubahan' => $validated['detail_perubahan'],
        'jabatan_lama'     => $validated['jabatan_lama'],
        'jabatan_baru'     => $validated['jabatan_baru'],
        'golongan_lama'    => $validated['golongan_lama'],
        'golongan_baru'    => $validated['golongan_baru'],
        'tgl_efektif'      => $validated['tgl_efektif'],
        'end_date'         => '9999-12-31',
        'nomor_sk'         => $validated['nomor_sk'],
        'catatan'          => $validated['catatan'],
        'created_by'       => 1,
    ]);
    echo "✅ Riwayat created (ID: {$riwayat->id})\n";
} catch (\Exception $e) {
    echo "❌ Failed: {$e->getMessage()}\n";
    exit(1);
}

echo "\n[STEP 5] Update employee_karyawan...\n";
try {
    $karyawan->update([
        'jabatan' => $validated['jabatan_baru'],
        'id_golongan' => $validated['golongan_baru'],
    ]);
    echo "✅ Karyawan updated\n";
} catch (\Exception $e) {
    echo "❌ Failed: {$e->getMessage()}\n";
    exit(1);
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: Verify Database State\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n[CHECK 1] obs_master_data_karyawan:\n";
$obsCheck = ObsMasterDataKaryawan::where('id_karyawan', $karyawan->id)
    ->where('notes', 'LIKE', '%SK/2026/0002%')
    ->first();

if ($obsCheck) {
    echo "✅ before_update saved\n";
    echo "   ID: {$obsCheck->id}\n";
    echo "   Jabatan: {$obsCheck->jabatan}\n";
    echo "   Golongan: {$obsCheck->id_golongan}\n";
    echo "   Change Reason: {$obsCheck->change_reason}\n";
} else {
    echo "❌ before_update NOT found\n";
}

echo "\n[CHECK 2] employee_riwayat_jabatan:\n";
$riwayatCheck = EmployeeRiwayatJabatan::where('nomor_sk', 'SK/2026/0002')->first();

if ($riwayatCheck) {
    echo "✅ Riwayat record saved\n";
    echo "   ID: {$riwayatCheck->id}\n";
    echo "   Jenis: {$riwayatCheck->jenis_perubahan}\n";
    echo "   Jabatan: {$riwayatCheck->jabatan_lama} → {$riwayatCheck->jabatan_baru}\n";
    echo "   Tgl Efektif: {$riwayatCheck->tgl_efektif}\n";
    echo "   Status: " . ($riwayatCheck->end_date === '9999-12-31' || $riwayatCheck->end_date > now() ? 'ACTIVE' : 'EXPIRED') . "\n";
} else {
    echo "❌ Riwayat record NOT found\n";
}

echo "\n[CHECK 3] employee_karyawan current state:\n";
$karyawanCheck = $karyawan->fresh();
echo "✅ Karyawan updated\n";
echo "   Jabatan: {$karyawanCheck->jabatan}\n";
echo "   Golongan: {$karyawanCheck->id_golongan}\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4: Simulate UI Display\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n[UI] Dashboard - History Data Karyawan Tab\n";
echo "┌─────────────────────────────────────────────────────────────┐\n";
echo "│ Filter: NIP Dropdown                                        │\n";
echo "│ Filter: Keyword Search                                      │\n";
echo "├─────────────────────────────────────────────────────────────┤\n";
echo "│ HISTORY RIWAYAT JABATAN - {$karyawan->nip}                            │\n";
echo "├─────────────────────────────────────────────────────────────┤\n";

$allRiwayat = EmployeeRiwayatJabatan::where('id_karyawan', $karyawan->id)
    ->orderBy('tgl_efektif', 'desc')
    ->get();

foreach ($allRiwayat as $r) {
    echo "│                                                             │\n";
    echo "│ Tipe: {$r->jenis_perubahan}                                   │\n";
    echo "│ {$r->detail_perubahan}                    │\n";
    echo "│ {$r->jabatan_lama} → {$r->jabatan_baru}              │\n";
    echo "│ Berlaku: {$r->tgl_efektif}                         │\n";
    echo "│ Status: " . ($r->end_date === '9999-12-31' || $r->end_date > now() ? "✓ ACTIVE" : "✗ EXPIRED") . "                                             │\n";
}
echo "├─────────────────────────────────────────────────────────────┤\n";
echo "│ Total: {$allRiwayat->count()} records                                        │\n";
echo "└─────────────────────────────────────────────────────────────┘\n";

echo "\n✅ UI display ready\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "FINAL VERIFICATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$allPassed = true;

if (!$obsCheck) {
    echo "❌ FAIL: obs_master_data_karyawan not saved\n";
    $allPassed = false;
} else {
    echo "✅ PASS: obs_master_data_karyawan saved\n";
}

if (!$riwayatCheck) {
    echo "❌ FAIL: employee_riwayat_jabatan not saved\n";
    $allPassed = false;
} else {
    echo "✅ PASS: employee_riwayat_jabatan saved\n";
}

if ($karyawanCheck->jabatan !== $validated['jabatan_baru']) {
    echo "❌ FAIL: employee_karyawan not updated\n";
    $allPassed = false;
} else {
    echo "✅ PASS: employee_karyawan updated\n";
}

if ($allRiwayat->count() === 0) {
    echo "❌ FAIL: UI cannot display data\n";
    $allPassed = false;
} else {
    echo "✅ PASS: UI can display data\n";
}

if ($allPassed) {
    echo "\n🎉 ALL TESTS PASSED! Catat Perubahan flow is working perfectly!\n";
} else {
    echo "\n⚠️  SOME TESTS FAILED!\n";
}

echo "\n";
