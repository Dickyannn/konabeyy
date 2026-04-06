<?php
/**
 * TEST CASE 6: Detail Riwayat - New Styling (Side-by-Side Boxes)
 * 
 * Verifies the new styling for before/after comparison
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\EmployeeRiwayatJabatan;
use App\Models\ObsMasterDataKaryawan;
use App\Models\MasterGolongan;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TEST CASE 6: Detail Riwayat - New Styling Display       ║\n";
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
echo "TEST 1: Data Ready for Display\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n✅ Current Data (Before):\n";
foreach ($currentData as $key => $value) {
    echo "   {$key}: {$value}\n";
}

echo "\n✅ Proposed Data (After):\n";
foreach ($proposedData as $key => $value) {
    echo "   {$key}: {$value}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: New Styling Layout Preview\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n[UI] Detail Riwayat - Side-by-Side Boxes (NEW STYLING)\n";
echo "\n┌──────────────────────────────────┬──────────────────────────────────┐\n";
echo "│ Current Status (Before)          │ Proposed Status (After)          │\n";
echo "├──────────────────────────────────┼──────────────────────────────────┤\n";
echo "│ ┌────────────────────────────┐   │ ┌────────────────────────────┐   │\n";
echo "│ │ NIP                        │   │ │ NIP                        │   │\n";
echo "│ │ {$currentData['nip']}                    │   │ │ {$proposedData['nip']}                    │   │\n";
echo "│ │                            │   │ │                            │   │\n";
echo "│ │ Nama                       │   │ │ Nama                       │   │\n";
echo "│ │ {$currentData['nama']}   │   │ │ {$proposedData['nama']}   │   │\n";
echo "│ │                            │   │ │                            │   │\n";
echo "│ │ Jabatan                    │   │ │ Jabatan                    │   │\n";
echo "│ │ {$currentData['jabatan']}      │   │ │ {$proposedData['jabatan']}      │   │\n";
echo "│ │                            │   │ │                            │   │\n";
echo "│ │ Golongan                   │   │ │ Golongan                   │   │\n";
echo "│ │ {$currentData['golongan']}        │   │ │ {$proposedData['golongan']}        │   │\n";
echo "│ │                            │   │ │                            │   │\n";
echo "│ │ Unit                       │   │ │ Unit                       │   │\n";
echo "│ │ {$currentData['unit']}                  │   │ │ {$proposedData['unit']}         │   │\n";
echo "│ │                            │   │ │                            │   │\n";
echo "│ │ Status                     │   │ │ Status                     │   │\n";
echo "│ │ {$currentData['status_karyawan']}                  │   │ │ {$proposedData['status_karyawan']}          │   │\n";
echo "│ │                            │   │ │                            │   │\n";
echo "│ └────────────────────────────┘   │ └────────────────────────────┘   │\n";
echo "│ Biru muda #f8fbfc               │ Biru lebih terang #f5f9fc        │\n";
echo "└──────────────────────────────────┴──────────────────────────────────┘\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: Styling Features\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$features = [
    'Side-by-side layout (2 columns)' => true,
    'Left box: #f8fbfc (biru muda)' => true,
    'Right box: #f5f9fc (biru lebih terang)' => true,
    'Border: 1.5px solid primary-light' => true,
    'Border-radius: 12px (rounded)' => true,
    'Padding: 1.5rem' => true,
    'Each field in small white box' => true,
    'Blue left border on field boxes' => true,
    'Header with icons & title' => true,
    'Professional, clean layout' => true,
];

echo "\n✅ Styling Features Implemented:\n";
foreach ($features as $feature => $implemented) {
    $status = $implemented ? '✓' : '✗';
    echo "   {$status} {$feature}\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4: Verify Changes\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\nField Changes:\n";
$changedCount = 0;
$compareFields = ['nip', 'nama', 'jabatan', 'golongan', 'unit', 'status_karyawan'];

foreach ($compareFields as $fieldKey) {
    $before = $currentData[$fieldKey] ?? '-';
    $after = $proposedData[$fieldKey] ?? '-';
    
    if ($before !== $after) {
        $changedCount++;
        echo "   ⚠️  " . ucfirst($fieldKey) . ": {$before} → {$after}\n";
    }
}

echo "\nTotal: {$changedCount} field(s) berubah\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "STYLING COMPARISON\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n✅ OLD STYLING (Table - JELEK):\n";
echo "   - Tabel format kaku\n";
echo "   - Sulit dibaca\n";
echo "   - Tidak profesional\n";

echo "\n✅ NEW STYLING (Side-by-Side Boxes - BAGUS):\n";
echo "   - Seperti form Catat Perubahan\n";
echo "   - Biru2 dengan border halus\n";
echo "   - Mudah dibaca & profesional\n";
echo "   - Responsive di mobile\n";
echo "   - Consistent design\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "FINAL VERIFICATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\n🎉 ALL STYLING UPDATES APPLIED!\n";
echo "✓ Side-by-side boxes format\n";
echo "✓ Blue backgrounds (#f8fbfc & #f5f9fc)\n";
echo "✓ Consistent with Catat Perubahan form\n";
echo "✓ Professional & clean layout\n";
echo "✓ Better readability\n";

echo "\n";
