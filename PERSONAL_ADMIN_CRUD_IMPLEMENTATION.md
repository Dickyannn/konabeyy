# Personal Admin CRUD Implementation Guide

## Status Implementasi

### ✅ Yang Sudah Selesai:
1. **Controller** - Semua method CRUD sudah dibuat:
   - `karyawanStore`, `karyawanUpdate`, `karyawanDestroy` ✅
   - `posisiStore`, `posisiUpdate`, `posisiDestroy` ✅
   - `kendaraanStore`, `kendaraanUpdate`, `kendaraanDestroy` ✅
   - `riwayatStore`, `riwayatUpdate`, `riwayatDestroy` ✅

2. **Routes** - Semua routes sudah ditambahkan ✅

3. **Models** - Semua model sudah dibuat:
   - `EmployeeKaryawan` ✅
   - `EmployeePosition` ✅
   - `EmployeeFasilitasKendaraan` ✅
   - `EmployeeRiwayatJabatan` ✅

4. **View - Data Karyawan** - Sudah terhubung ke database ✅

### 🔄 Yang Perlu Dilengkapi di View:

#### 1. Modal Struktur Organisasi
**File**: `resources/views/personal-admin/dashboard.blade.php`
**Lokasi**: Sekitar baris 800-900

**Yang perlu diupdate**:
```blade
<!-- Tabel List -->
<tbody>
    @forelse($positions as $position)
    <tr>
        <td><strong>{{ $position->karyawan->nama_karyawan }}</strong></td>
        <td>{{ $position->nama_jabatan }}</td>
        <td>{{ $position->karyawan->atasan?->nama_karyawan ?? '— (Top Level)' }}</td>
        <td>{{ $position->costCenter->nama_cc }}</td>
        <td>{{ $position->karyawan->unit->nama_pt }}</td>
        <td>
            <div class="d-flex gap-1">
                <button class="btn-sm-action btn-edit" onclick="editPosisi({{ $position->id }})">
                    <i class="bi bi-pencil"></i>
                </button>
                <form action="{{ route('personal-admin.posisi.destroy', $position) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-sm-action btn-del" onclick="return confirm('Yakin?')">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada data</td></tr>
    @endforelse
</tbody>

<!-- Form -->
<form action="{{ route('personal-admin.posisi.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-sm-6">
            <label class="form-label">Karyawan <span class="text-danger">*</span></label>
            <select name="id_karyawan" class="form-select" required>
                <option value="">-- Pilih Karyawan --</option>
                @foreach($karyawans as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_karyawan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
            <input type="text" name="nama_jabatan" class="form-control" required>
        </div>
        <div class="col-sm-6">
            <label class="form-label">Cost Center <span class="text-danger">*</span></label>
            <select name="id_cost_center" class="form-select" required>
                <option value="">-- Pilih Cost Center --</option>
                @foreach($costCenters as $cc)
                    <option value="{{ $cc->id }}">{{ $cc->nama_cc }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_mulai" class="form-control" required>
        </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan</button>
        <button type="button" class="btn-outline-hrms" onclick="switchTabById('struktur-list')">Batal</button>
    </div>
</form>
```

#### 2. Modal Fasilitas Kendaraan
**Lokasi**: Sekitar baris 950-1050

**Yang perlu diupdate**:
```blade
<!-- Tabel List -->
<tbody>
    @forelse($fasilitasKendaraans as $fasilitas)
    <tr>
        <td><strong>{{ $fasilitas->karyawan->nama_karyawan }}</strong></td>
        <td><span class="badge-permanent">{{ $fasilitas->karyawan->golongan->kode_golongan }}</span></td>
        <td>{{ $fasilitas->jenis_fasilitas }}</td>
        <td>Rp {{ number_format($fasilitas->nominal_allowance, 0, ',', '.') }}</td>
        <td>{{ $fasilitas->tgl_berlaku->format('d/m/Y') }}</td>
        <td><span class="badge-aktif">Aktif</span></td>
        <td>
            <div class="d-flex gap-1">
                <button class="btn-sm-action btn-edit" onclick="editKendaraan({{ $fasilitas->id }})">
                    <i class="bi bi-pencil"></i>
                </button>
                <form action="{{ route('personal-admin.kendaraan.destroy', $fasilitas) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-sm-action btn-del" onclick="return confirm('Yakin?')">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data</td></tr>
    @endforelse
</tbody>

<!-- Form -->
<form action="{{ route('personal-admin.kendaraan.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-sm-6">
            <label class="form-label">Karyawan <span class="text-danger">*</span></label>
            <select name="id_karyawan" class="form-select" required>
                <option value="">-- Pilih Karyawan --</option>
                @foreach($karyawans as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_karyawan }} ({{ $k->golongan->kode_golongan }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label class="form-label">Tipe Fasilitas <span class="text-danger">*</span></label>
            <select name="jenis_fasilitas" class="form-select" required>
                <option value="Car Allowance">Car Allowance</option>
                <option value="Kendaraan Dinas">Kendaraan Dinas</option>
            </select>
        </div>
        <div class="col-sm-4">
            <label class="form-label">Nominal (Rp/bulan)</label>
            <input type="number" name="nominal_allowance" class="form-control" placeholder="3500000">
        </div>
        <div class="col-sm-4">
            <label class="form-label">Berlaku Mulai <span class="text-danger">*</span></label>
            <input type="date" name="tgl_berlaku" class="form-control" required>
        </div>
        <div class="col-sm-4">
            <label class="form-label">No. Polisi</label>
            <input type="text" name="nomor_polisi" class="form-control" placeholder="B 1234 XYZ">
        </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan</button>
        <button type="button" class="btn-outline-hrms" onclick="switchTabById('kendaraan-list')">Batal</button>
    </div>
</form>
```

#### 3. Modal Riwayat Jabatan
**Lokasi**: Sekitar baris 1050-1150

**Yang perlu diupdate**:
```blade
<!-- Tabel List -->
<tbody>
    @forelse($riwayatJabatans as $riwayat)
    <tr>
        <td style="font-size:.78rem;white-space:nowrap;">{{ $riwayat->tgl_efektif->format('d/m/Y') }}</td>
        <td><strong>{{ $riwayat->karyawan->nama_karyawan }}</strong></td>
        <td><span class="badge-aktif">{{ ucfirst($riwayat->jenis_perubahan) }}</span></td>
        <td style="font-size:.8rem;">{{ $riwayat->jabatan_lama ?? '-' }}</td>
        <td style="font-size:.8rem;font-weight:700;">{{ $riwayat->jabatan_baru }}</td>
        <td><span class="badge-permanent">{{ $riwayat->golonganBaruRelation->kode_golongan }}</span></td>
        <td style="font-size:.78rem;">{{ $riwayat->karyawan->unit->nama_pt }}</td>
        <td style="font-size:.78rem;color:var(--text-muted);">{{ $riwayat->catatan }}</td>
        <td>
            <div class="d-flex gap-1">
                <button class="btn-sm-action btn-edit" onclick="editRiwayat({{ $riwayat->id }})">
                    <i class="bi bi-pencil"></i>
                </button>
                <form action="{{ route('personal-admin.riwayat.destroy', $riwayat) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-sm-action btn-del" onclick="return confirm('Yakin?')">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada data</td></tr>
    @endforelse
</tbody>

<!-- Form -->
<form action="{{ route('personal-admin.riwayat.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-sm-6">
            <label class="form-label">Karyawan <span class="text-danger">*</span></label>
            <select name="id_karyawan" class="form-select" required>
                <option value="">-- Pilih Karyawan --</option>
                @foreach($karyawans as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_karyawan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label class="form-label">Tipe Perubahan <span class="text-danger">*</span></label>
            <select name="jenis_perubahan" class="form-select" required>
                <option value="">-- Pilih Tipe --</option>
                <option value="promosi">Promosi</option>
                <option value="mutasi">Mutasi</option>
                <option value="demosi">Demosi</option>
                <option value="rotasi">Rotasi</option>
            </select>
        </div>
        <div class="col-sm-6">
            <label class="form-label">Jabatan Lama</label>
            <input type="text" name="jabatan_lama" class="form-control">
        </div>
        <div class="col-sm-6">
            <label class="form-label">Jabatan Baru <span class="text-danger">*</span></label>
            <input type="text" name="jabatan_baru" class="form-control" required>
        </div>
        <div class="col-sm-4">
            <label class="form-label">Golongan Lama</label>
            <select name="golongan_lama" class="form-select">
                <option value="">-- Pilih --</option>
                @foreach($golongans as $gol)
                    <option value="{{ $gol->id }}">{{ $gol->kode_golongan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4">
            <label class="form-label">Golongan Baru <span class="text-danger">*</span></label>
            <select name="golongan_baru" class="form-select" required>
                @foreach($golongans as $gol)
                    <option value="{{ $gol->id }}">{{ $gol->kode_golongan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4">
            <label class="form-label">Tanggal Efektif <span class="text-danger">*</span></label>
            <input type="date" name="tgl_efektif" class="form-control" required>
        </div>
        <div class="col-sm-6">
            <label class="form-label">No. SK</label>
            <input type="text" name="nomor_sk" class="form-control">
        </div>
        <div class="col-sm-6">
            <label class="form-label">Keterangan</label>
            <input type="text" name="catatan" class="form-control">
        </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan Riwayat</button>
        <button type="button" class="btn-outline-hrms" onclick="switchTabById('riwayat-list')">Batal</button>
    </div>
</form>
```

#### 4. Tambahkan Script di Akhir File
```blade
<script src="{{ asset('js/personal-admin/crud-handlers.js') }}"></script>
```

## Cara Testing

1. Login sebagai personal_admin
2. Akses `/personal-admin/dashboard`
3. Test setiap modal:
   - Buka modal
   - Lihat data dari database
   - Klik tombol Tambah
   - Isi form dan submit
   - Klik tombol Edit
   - Update data dan submit
   - Klik tombol Delete
   - Konfirmasi delete

## Catatan Penting

- Modal "Kontrak Karyawan" menggunakan data dari `employee_position` dengan field `tanggal_selesai`
- Modal "Data BPJS" adalah view-only, data diambil dari field `bpjs_kesehatan_number` dan `bpjs_tk_number` di tabel `employee_karyawan`
- Semua operasi CRUD sudah menggunakan soft delete atau flag `is_active`/`is_current`
- Audit log sudah ditambahkan untuk operasi penting

## File yang Sudah Dibuat/Diupdate

1. ✅ `app/Models/EmployeeRiwayatJabatan.php` - Model baru
2. ✅ `app/Models/EmployeeFasilitasKendaraan.php` - Model baru
3. ✅ `app/Http/Controllers/PersonalAdmin/PersonalAdminController.php` - Updated
4. ✅ `routes/web.php` - Routes ditambahkan
5. ✅ `resources/js/personal-admin/crud-handlers.js` - JavaScript handlers
6. 🔄 `resources/views/personal-admin/dashboard.blade.php` - Perlu update manual untuk modal 2-6

## Next Steps

Karena file view sangat panjang (1200+ baris), update manual diperlukan untuk:
1. Modal Struktur Organisasi (baris ~800-900)
2. Modal Fasilitas Kendaraan (baris ~950-1050)
3. Modal Riwayat Jabatan (baris ~1050-1150)
4. Tambahkan script crud-handlers.js di akhir file

Gunakan template di atas untuk setiap modal.
