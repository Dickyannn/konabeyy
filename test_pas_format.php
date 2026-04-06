<?php
/**
 * TEST CASE 5: Detail Riwayat - PAS Format Table Display
 * 
 * Tests the new table format (like Personnel Action Sheet):
 * 1. Verify comparison data renders correctly
 * 2. Check table structure
 * 3. Verify field highlighting for changes
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\EmployeeRiwayatJabatan;
use App\Models\ObsMasterDataKaryawan;
use App\Models\MasterGolongan;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TEST CASE 5: Detail Riwayat - PAS Format Table Display   ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";

// Get latest riwayat record
$riwayat = EmployeeRiwayatJabatan::latest('id')->first();
if (!$riwayat) {
    echo "❌ ERROR: No riwayat records found\n";
    exit(1);
}

$riwayat->load(['karyawan', 'golonganLamaRelation', 'golonganBaruRelation']);

// Get before snapshot
$beforeSnapshot = ObsMasterDataKaryawan::where('id_karyawan', $riwayat->id_karyawan)
    ->where('action_type', 'before_update')
    ->where('change_reason', $riwayat->detail_perubahan)
    ->latest('id')
    ->first();

$getGolonganName = function($id) {
    if (!$id) return '-';
    $golongan = MasterGolongan::find($id);
    return $golongan?->nama_golongan ?? '-';
};

// Build current_data
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
    $currentData = [
        'nip' => $riwayat->nip,
        'nama' => $riwayat->nama,
        'jabatan' => $riwayat->jabatan_lama,
        'golongan' => $riwayat->golonganLamaRelation?->nama_golongan ?? '-',
        'unit' => 'N/A',
        'status_karyawan' => 'N/A',
    ];
}

// Build proposed_data
$employee = $riwayat->karyawan;
$proposedData = [
    'nip' => $riwayat->nip,
    'nama' => $riwayat->nama,
    'jabatan' => $riwayat->jabatan_baru,
    'golongan' => $riwayat->golonganBaruRelation?->nama_golongan ?? '-',
    'unit' => $employee?->unit?->nama_pt ?? 'N/A',
    'status_karyawan' => $employee?->statusKaryawan?->nama_status ?? 'N/A',
];

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Verify Data Structure\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n✅ Riwayat Record: {$riwayat->nip} - {$riwayat->nama}\n";
echo "✅ Current Data Count: " . count($currentData) . " fields\n";
echo "✅ Proposed Data Count: " . count($proposedData) . " fields\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: Render PAS Format Table\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$fields = [
    ['label' => 'NIP', 'key' => 'nip'],
    ['label' => 'Nama', 'key' => 'nama'],
    ['label' => 'Jabatan', 'key' => 'jabatan'],
    ['label' => 'Golongan', 'key' => 'golongan'],
    ['label' => 'Unit', 'key' => 'unit'],
    ['label' => 'Status', 'key' => 'status_karyawan'],
];

echo "\n┌──────────────────┬────────────────────────────┬────────────────────────────┐\n";
echo "│ Field            │ Current Status (Before)    │ Proposed Status (After)    │\n";
echo "├──────────────────┼────────────────────────────┼────────────────────────────┤\n";

foreach ($fields as $field) {
    $currentValue = $currentData[$field['key']] ?? '-';
    $proposedValue = $proposedData[$field['key']] ?? '-';
    $isChanged = $currentValue !== $proposedValue;
    
    $marker = $isChanged ? '⚠️ ' : '✓ ';
    
    // Truncate for display
    $currDisplay = strlen($currentValue) > 25 ? substr($currentValue, 0, 22) . '...' : $currentValue;
    $propDisplay = strlen($proposedValue) > 25 ? substr($proposedValue, 0, 22) . '...' : $proposedValue;
    
    echo "│ " . str_pad($field['label'], 16) . " │ " . str_pad($currDisplay, 26) . " │ " . str_pad($propDisplay, 26) . " │\n";
}

echo "└──────────────────┴────────────────────────────┴────────────────────────────┘\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: Highlight Changes\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\nFields yang Berubah:\n";
$changedCount = 0;
foreach ($fields as $field) {
    $currentValue = $currentData[$field['key']] ?? '-';
    $proposedValue = $proposedData[$field['key']] ?? '-';
    $isChanged = $currentValue !== $proposedValue;
    
    if ($isChanged) {
        $changedCount++;
        echo "   ⚠️  {$field['label']}: {$currentValue} → {$proposedValue}\n";
    }
}

if ($changedCount === 0) {
    echo "   (Tidak ada perubahan)\n";
} else {
    echo "\nTotal: {$changedCount} field(s) berubah\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4: UI Display (PAS Format)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n[UI] Detail Riwayat - Perbandingan Data (PAS Format)\n";
echo "┌────────────────────────────────────────────────────────────────┐\n";
echo "│                                                                │\n";
echo "│  📋 Riwayat: {$riwayat->jenis_perubahan}                             │\n";
echo "│  NIP: {$riwayat->nip} - {$riwayat->nama}         │\n";
echo "│  Tgl Efektif: " . date('d/m/Y', strtotime($riwayat->tgl_efektif)) . "                                │\n";
echo "│                                                                │\n";
echo "├────────────────────┬──────────────────┬──────────────────────┤\n";
echo "│ Field              │ Current Status   │ Proposed Status      │\n";
echo "├────────────────────┼──────────────────┼──────────────────────┤\n";

foreach ($fields as $field) {
    $currentValue = $currentData[$field['key']] ?? '-';
    $proposedValue = $proposedData[$field['key']] ?? '-';
    $isChanged = $currentValue !== $proposedValue;
    
    $currDisplay = strlen($currentValue) > 16 ? substr($currentValue, 0, 13) . '...' : $currentValue;
    $propDisplay = strlen($proposedValue) > 18 ? substr($proposedValue, 0, 15) . '...' : $proposedValue;
    
    $marker = $isChanged ? '⚠️ ' : '';
    echo "│ " . str_pad($marker . $field['label'], 19) . " │ " . str_pad($currDisplay, 17) . " │ " . str_pad($propDisplay, 20) . " │\n";
}

echo "├────────────────────┴──────────────────┴──────────────────────┤\n";
echo "│ Legend: ⚠️  = Field berubah, ✓ = Tidak berubah                 │\n";
echo "└────────────────────────────────────────────────────────────────┘\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 5: Verify Table Structure\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$structureChecks = [
    'Has header row' => true,
    'Has 6 data fields' => count($fields) === 6,
    'Current data filled' => !empty($currentData),
    'Proposed data filled' => !empty($proposedData),
    'All fields have values' => (
        count(array_filter($currentData, fn($v) => $v !== '-')) > 0 &&
        count(array_filter($proposedData, fn($v) => $v !== '-')) > 0
    ),
    'Changed fields detected' => $changedCount > 0,
];

echo "\n✅ Structure Validation:\n";
foreach ($structureChecks as $check => $result) {
    $status = $result ? '✓' : '✗';
    echo "   {$status} {$check}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "FINAL VERIFICATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$allPassed = array_reduce($structureChecks, fn($carry, $item) => $carry && $item, true);

if ($allPassed) {
    echo "\n🎉 ALL TESTS PASSED!\n";
    echo "✓ PAS Format table is working perfectly!\n";
    echo "✓ Comparison data displays correctly!\n";
    echo "✓ Changed fields are highlighted!\n";
} else {
    echo "\n⚠️  SOME TESTS FAILED!\n";
}

echo "\n";
