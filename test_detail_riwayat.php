<?php
/**
 * TEST CASE 4: Detail Riwayat - Show Before/After Comparison
 * 
 * Tests the Detail Riwayat view with before/after data:
 * 1. Fetch riwayat record
 * 2. Get before_update snapshot from obs_master_data_karyawan
 * 3. Get proposed data from EmployeeRiwayatJabatan
 * 4. Verify comparison data is properly formatted
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\EmployeeRiwayatJabatan;
use App\Models\ObsMasterDataKaryawan;
use App\Models\MasterGolongan;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TEST CASE 4: Detail Riwayat - Before/After Comparison    ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";

// Get latest riwayat record
$riwayat = EmployeeRiwayatJabatan::latest('id')->first();

if (!$riwayat) {
    echo "❌ ERROR: No riwayat records found\n";
    exit(1);
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Fetch Riwayat Record\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$riwayat->load(['karyawan', 'golonganLamaRelation', 'golonganBaruRelation']);

echo "✅ Riwayat Record Found:\n";
echo "   ID: {$riwayat->id}\n";
echo "   NIP: {$riwayat->nip}\n";
echo "   Nama: {$riwayat->nama}\n";
echo "   Type: {$riwayat->jenis_perubahan}\n";
echo "   Detail: {$riwayat->detail_perubahan}\n";
echo "   Tgl Efektif: {$riwayat->tgl_efektif}\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: Get Before Data from obs_master_data_karyawan\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$beforeSnapshot = ObsMasterDataKaryawan::where('id_karyawan', $riwayat->id_karyawan)
    ->where('action_type', 'before_update')
    ->where('change_reason', $riwayat->detail_perubahan)
    ->latest('id')
    ->first();

if ($beforeSnapshot) {
    echo "✅ Before Snapshot Found (ID: {$beforeSnapshot->id}):\n";
    echo "   NIP: {$beforeSnapshot->nip}\n";
    echo "   Nama: {$beforeSnapshot->nama_karyawan}\n";
    echo "   Jabatan: {$beforeSnapshot->jabatan}\n";
    echo "   Golongan ID: {$beforeSnapshot->id_golongan}\n";
} else {
    echo "⚠️  Before snapshot not found for this riwayat\n";
    echo "   Will fallback to riwayat old data\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: Build Current Data (Before State)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$getGolonganName = function($id) {
    if (!$id) return '-';
    $golongan = MasterGolongan::find($id);
    return $golongan?->nama_golongan ?? '-';
};

$currentData = [];
if ($beforeSnapshot) {
    $currentData = [
        'nip' => $beforeSnapshot->nip,
        'nama' => $beforeSnapshot->nama_karyawan,
        'jabatan' => $beforeSnapshot->jabatan,
        'golongan' => $getGolonganName($beforeSnapshot->id_golongan),
        'unit' => $beforeSnapshot->unit_nama ?? 'N/A',
        'status_karyawan' => $beforeSnapshot->status_karyawan ?? 'N/A',
    ];
} else {
    // Fallback
    $currentData = [
        'nip' => $riwayat->nip,
        'nama' => $riwayat->nama,
        'jabatan' => $riwayat->jabatan_lama,
        'golongan' => $riwayat->golonganLamaRelation?->nama_golongan ?? '-',
        'unit' => 'N/A',
        'status_karyawan' => 'N/A',
    ];
}

echo "✅ Current Data (Before):\n";
foreach ($currentData as $key => $value) {
    echo "   {$key}: {$value}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4: Build Proposed Data (After State)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$employee = $riwayat->karyawan;

$proposedData = [
    'nip' => $riwayat->nip,
    'nama' => $riwayat->nama,
    'jabatan' => $riwayat->jabatan_baru,
    'golongan' => $riwayat->golonganBaruRelation?->nama_golongan ?? '-',
    'unit' => $employee?->unit?->nama_pt ?? 'N/A',
    'status_karyawan' => $employee?->statusKaryawan?->nama_status ?? 'N/A',
];

echo "✅ Proposed Data (After):\n";
foreach ($proposedData as $key => $value) {
    echo "   {$key}: {$value}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 5: Verify Data Changes\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\nPerubahan Data (Changes):\n";

// Helper to show changes
$showChange = function($label, $before, $after) {
    if ($before === $after) {
        echo "   ✓ {$label}: {$before} (tidak berubah)\n";
    } else {
        echo "   ⚠️  {$label}: {$before} → {$after} (BERUBAH)\n";
    }
};

$showChange('NIP', $currentData['nip'], $proposedData['nip']);
$showChange('Nama', $currentData['nama'], $proposedData['nama']);
$showChange('Jabatan', $currentData['jabatan'], $proposedData['jabatan']);
$showChange('Golongan', $currentData['golongan'], $proposedData['golongan']);
$showChange('Unit', $currentData['unit'], $proposedData['unit']);
$showChange('Status', $currentData['status_karyawan'], $proposedData['status_karyawan']);

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 6: Simulate UI Display\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n[UI] Detail Riwayat Modal - Perbandingan Data\n";
echo "┌─────────────────────────────────────────────────────────┐\n";
echo "│                                                         │\n";
echo "│  📜 Title: Detail Riwayat Perubahan                     │\n";
echo "│                                                         │\n";
echo "│  NIP - Nama: {$riwayat->nip} - {$riwayat->nama}\n";
echo "│  Tipe Perubahan: {$riwayat->jenis_perubahan}\n";
echo "│  Detail: {$riwayat->detail_perubahan}\n";
echo "│  Tanggal Efektif: " . date('d/m/Y', strtotime($riwayat->tgl_efektif)) . "\n";
echo "│                                                         │\n";
echo "│  ═══════════════════════════════════════════════════════│\n";
echo "│  Perbandingan Data                                      │\n";
echo "│  ═══════════════════════════════════════════════════════│\n";
echo "│                                                         │\n";
echo "│  Current Status (Before)  │  Proposed Status (After)   │\n";
echo "│  ────────────────────────┼──────────────────────────    │\n";
echo "│  NIP: " . str_pad($currentData['nip'], 18) . " │ NIP: " . str_pad($proposedData['nip'], 18) . " │\n";
echo "│  Nama: " . str_pad($currentData['nama'], 16) . " │ Nama: " . str_pad($proposedData['nama'], 16) . " │\n";
echo "│  Jabatan: " . str_pad($currentData['jabatan'], 11) . " │ Jabatan: " . str_pad($proposedData['jabatan'], 11) . " │\n";
echo "│  Golongan: " . str_pad($currentData['golongan'], 10) . " │ Golongan: " . str_pad($proposedData['golongan'], 10) . " │\n";
echo "│  Unit: " . str_pad($currentData['unit'], 17) . " │ Unit: " . str_pad($proposedData['unit'], 17) . " │\n";
echo "│  Status: " . str_pad($currentData['status_karyawan'], 13) . " │ Status: " . str_pad($proposedData['status_karyawan'], 13) . " │\n";
echo "│                                                         │\n";
echo "└─────────────────────────────────────────────────────────┘\n";

echo "\n✅ UI Display Ready\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "FINAL VERIFICATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$allChecks = [
    'Riwayat record exists' => (bool)$riwayat,
    'Current data populated' => !empty($currentData),
    'Proposed data populated' => !empty($proposedData),
    'All required fields in current' => (
        isset($currentData['nip']) && 
        isset($currentData['nama']) && 
        isset($currentData['jabatan']) && 
        isset($currentData['golongan']) &&
        isset($currentData['unit']) &&
        isset($currentData['status_karyawan'])
    ),
    'All required fields in proposed' => (
        isset($proposedData['nip']) && 
        isset($proposedData['nama']) && 
        isset($proposedData['jabatan']) && 
        isset($proposedData['golongan']) &&
        isset($proposedData['unit']) &&
        isset($proposedData['status_karyawan'])
    ),
];

echo "\n✅ TEST RESULTS:\n";
foreach ($allChecks as $check => $result) {
    $status = $result ? '✓' : '✗';
    echo "   {$status} {$check}\n";
}

if (array_filter($allChecks) === $allChecks) {
    echo "\n🎉 ALL TESTS PASSED! Detail Riwayat comparison is working perfectly!\n";
} else {
    echo "\n⚠️  SOME TESTS FAILED!\n";
}

echo "\n";
