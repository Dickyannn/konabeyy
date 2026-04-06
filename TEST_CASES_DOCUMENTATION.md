# Test Cases: History Data Karyawan - Complete Flow Verification

## Overview
Dokumentasi lengkap untuk menjalankan test case **Catat Perubahan di Data History Karyawan** dan memverifikasi bahwa data masuk ke database dengan benar serta dapat ditampilkan di UI.

---

## Test Case 1: Complete Flow - Catat Perubahan to Database Display

**File:** `test_catat_perubahan.php`

**Apa yang ditest:**
- ✅ Form submission simulation
- ✅ before_update snapshot saved to `obs_master_data_karyawan`
- ✅ Riwayat record saved to `employee_riwayat_jabatan`
- ✅ Employee data updated in `employee_karyawan`
- ✅ UI ready to display the data

**Cara menjalankan:**
```bash
php test_catat_perubahan.php
```

**Expected Output:**
```
✅ TEST RESULTS:
   • before_update saved to obs_master_data_karyawan: 2 records ✓
   • New data saved to employee_riwayat_jabatan: 1 records ✓
   • employee_karyawan updated: Supervisor Senior ✓
   • UI data ready for display: YES ✓

✨ CONCLUSION: COMPLETE FLOW WORKING PERFECTLY!
```

**Apa yang diperiksa:**
1. Initial state karyawan (NIP, Nama, Jabatan, Golongan)
2. Form data preparation (6 langkah simulasi)
3. Database integrity check (3 tabel)
4. UI display capability

---

## Test Case 2: UI Filters - NIP & Keyword Search

**File:** `test_ui_filter.php`

**Apa yang ditest:**
- ✅ NIP filter dropdown works correctly
- ✅ Keyword search filters all columns
- ✅ Multiple records display correctly
- ✅ Data consistency in tables

**Cara menjalankan:**
```bash
php test_ui_filter.php
```

**Expected Output:**
```
✅ TEST RESULTS:
   • NIP filter works: YES ✓
   • Keyword search works: YES ✓
   • Data display ready: YES ✓
   • Data integrity: PERFECT ✓

✨ CONCLUSION: UI FILTER & DISPLAY WORKING PERFECTLY!
```

**Scenario yang ditest:**
1. Filter by NIP (dari dropdown)
2. Search keywords: 'mutasi', 'Supervisor', '2026-04'
3. Complete table display
4. Data validation

---

## Test Case 3: Real Form Submission Simulation

**File:** `test_form_submission.php`

**Apa yang ditest:**
- ✅ POST form data simulation
- ✅ Controller logic execution (5 steps)
- ✅ Database state verification
- ✅ UI display simulation
- ✅ Final verification (4 checks)

**Cara menjalankan:**
```bash
php test_form_submission.php
```

**Expected Output:**
```
✅ PASS: obs_master_data_karyawan saved
✅ PASS: employee_riwayat_jabatan saved
✅ PASS: employee_karyawan updated
✅ PASS: UI can display data

🎉 ALL TESTS PASSED! Catat Perubahan flow is working perfectly!
```

**Flow yang ditest:**
```
POST Form Data
    ↓
[STEP 1] Form validation
    ↓
[STEP 2] Save before_update → obs_master_data_karyawan
    ↓
[STEP 3] Update end_date previous records
    ↓
[STEP 4] Create riwayat → employee_riwayat_jabatan
    ↓
[STEP 5] Update employee → employee_karyawan
    ↓
Database Verification ✓
    ↓
UI Display Ready ✓
```

---

## Data Flow Verification

### Flow Path: Catat Perubahan (Record Change)

```
┌─────────────────────┐
│  Form Submission    │
│ (Jenis Perubahan    │
│  Jabatan, Detail)   │
└──────────┬──────────┘
           │
     [VALIDATION]
           │
      ┌────┴────┐
      │          │
      ▼          ▼
   [STEP1]   [STEP2]
    │         │
    │    Save before_update
    │    to obs_master_data_
    │    karyawan (audit trail)
    │         │
    └────┬────┘
         │
      [STEP3]
      Update end_date
      previous records
         │
      [STEP4]
      Create riwayat
      in employee_
      riwayat_jabatan
         │
      [STEP5]
      Update employee
      in employee_
      karyawan
         │
   ┌─────┴─────┐
   │           │
   ▼           ▼
  [obs_]    [riwayat]
  record    record
   [UI Display]
     ✓
```

---

## Database Verification

### Table: obs_master_data_karyawan
**Purpose:** Menyimpan snapshot sebelum perubahan (audit trail)

**Contoh data:**
```
id  | id_karyawan | action_type  | jabatan      | golongan | change_reason
7   | 2           | before_update| Supervisor   | 1        | Mutasi ke Departemen Lain
```

**Kolom penting:**
- `id_karyawan`: Reference ke employee
- `action_type`: "before_update" (snapshot)
- `jabatan`, `golongan`: Nilai lama sebelum perubahan
- `change_reason`: Alasan perubahan
- `notes`: Informasi tambahan

### Table: employee_riwayat_jabatan
**Purpose:** Menyimpan riwayat perubahan (history)

**Contoh data:**
```
id | id_karyawan | nip      | jenis_perubahan | jabatan_lama  | jabatan_baru        | tgl_efektif
2  | 2           | TEST0002 | mutasi          | Supervisor    | Supervisor Senior   | 2026-04-06
```

**Kolom penting:**
- `jenis_perubahan`: Tipe perubahan (promosi/mutasi/demosi/rotasi)
- `jabatan_lama`, `jabatan_baru`: Transisi jabatan
- `golongan_lama`, `golongan_baru`: Transisi golongan
- `tgl_efektif`: Tanggal perubahan berlaku
- `end_date`: 9999-12-31 (aktif) atau tanggal skadwal (expired)
- `nomor_sk`: Nomor SK/notifikasi

### Table: employee_karyawan
**Purpose:** Data karyawan saat ini (main employee data)

**Update:**
- `jabatan`: Diupdate ke jabatan baru
- `id_golongan`: Diupdate ke golongan baru

---

## Menjalankan Semua Tests

**Jalankan sekaligus:**
```bash
echo "=== TEST 1 ===" && php test_catat_perubahan.php
echo -e "\n=== TEST 2 ===" && php test_ui_filter.php
echo -e "\n=== TEST 3 ===" && php test_form_submission.php
```

**Atau jalankan dengan script batch (Windows):**
```batch
@echo off
echo === TEST 1: Complete Flow ===
php test_catat_perubahan.php
echo.
echo === TEST 2: UI Filters ===
php test_ui_filter.php
echo.
echo === TEST 3: Form Submission ===
php test_form_submission.php
pause
```

---

## Troubleshooting

### Problem: "Karyawan TEST0002 not found"
**Solusi:** Seed test data terlebih dahulu:
```bash
php artisan db:seed TestDataSeeder
```

### Problem: "No golongan data found"
**Solusi:** Check master data:
```bash
php artisan tinker
>>> \App\Models\MasterGolongan::count()
```

### Problem: Test menunjukkan data tidak tersimpan
**Solusi:** Check error log:
```bash
tail -f storage/logs/laravel.log
```

---

## Expected Test Results Summary

| Test Case | Status | Key Checks |
|-----------|--------|-----------|
| Test 1: Complete Flow | ✅ PASS | obs save ✓, riwayat create ✓, employee update ✓, UI ready ✓ |
| Test 2: UI Filters | ✅ PASS | NIP filter ✓, keyword search ✓, data integrity ✓ |
| Test 3: Form Submission | ✅ PASS | Form data ✓, 5 steps ✓, DB verify ✓, UI display ✓ |

---

## Dashboard UI Verification (Manual)

Setelah test case berjalan, buka dashboard dan verify:

1. **Go to:** Admin Personalia → Personal Admin Dashboard
2. **Check:** History Data Karyawan Tab
3. **Verify:**
   - ✓ Dropdown NIP menampilkan semua karyawan
   - ✓ Memilih NIP menampilkan riwayat untuk karyawan tersebut
   - ✓ Keyword search menampilkan records yang cocok
   - ✓ Setiap record menampilkan: NIP, Nama, Jenis Perubahan, Detail, Tanggal Efektif, Status

4. **Catat Perubahan Form:**
   - ✓ Jumlah kolom lengkap (jenis_perubahan, detail, jabatan, golongan, tanggal, SK)
   - ✓ Form submit tidak ada error
   - ✓ Data langsung muncul di History table

---

## Notes

- Test menggunakan test data (TEST0001, TEST0002)
- Setiap test membuat records baru, data lama dihapus terlebih dahulu
- DB transactions tidak digunakan, changes langsung committed
- Untuk production testing, gunakan env `APP_ENV=testing`

---

**Last Updated:** 2026-04-06
**System:** Laravel 11 HRMS
**Test Status:** ✅ ALL PASSING
