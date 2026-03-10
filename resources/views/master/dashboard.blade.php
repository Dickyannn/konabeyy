<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Master System — HR Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/master/dashboard.css'])
</head>
<body>

{{-- ═══════════════════════════════════════════════════════ --}}
{{--  NAVBAR                                                 --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<nav class="navbar-hrms">

    {{-- Logo + Nama --}}
    <a href="#" class="navbar-brand-area">
        <div class="navbar-logo">HR</div>
        <div class="navbar-brand-text">
            HR Portal
            <small>PT Suri Tani Pemuka</small>
        </div>
    </a>

    {{-- User dropdown --}}
    <div class="dropdown">
        <div class="navbar-user" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="navbar-avatar"><i class="bi bi-person-fill"></i></div>
            <div class="navbar-user-info d-none d-sm-block">
                <div class="navbar-user-name">{{ auth()->user()->nama ?? 'Super Admin' }}</div>
                <div class="navbar-user-role">Master System</div>
            </div>
            <i class="bi bi-chevron-down navbar-caret d-none d-sm-block"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-user mt-1">
            <li>
                <div class="dropdown-item-user">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </div>
            </li>
            <li><hr style="margin: 0.4rem 0; border-color: var(--border);"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item-user danger w-100 border-0 bg-transparent text-start">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>


{{-- ═══════════════════════════════════════════════════════ --}}
{{--  MAIN CONTENT                                           --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="page-wrapper">

    {{-- Page header --}}
    <div class="page-header">
        <h1>Master System</h1>
        <p>Konfigurasi &amp; parameter sistem</p>
    </div>

    {{-- ── STAT CARDS ─────────────────────────────────── --}}
    {{--
        Data diambil dari controller:
          $totalGolongan = \App\Models\Master\Golongan::where('is_active', true)->count();
          $totalCostCenter = \App\Models\Master\CostCenter::where('is_active', true)->count();
          $userAktif = \App\Models\User::where('is_active', true)->count();
          $logBulanIni = \App\Models\Auth\AuditLog::whereMonth('created_at', now()->month)
                           ->whereYear('created_at', now()->year)->count();

        LOG PERUBAHAN = jumlah baris di auth.audit_log bulan ini
        Mencakup: LOGIN, LOGOUT, CREATE_USER, UPDATE_USER, RESET_PASSWORD,
                  aksi CRUD di semua modul. Berguna untuk audit aktivitas sistem.
    --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="stat-card">
                <span class="stat-icon">🏅</span>
                <div class="stat-value">{{ $totalGolongan ?? 12 }}</div>
                <div class="stat-label">Total Golongan</div>
                <div class="stat-sub">level aktif</div>
            </div>
        </div>
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="stat-card">
                <span class="stat-icon">🏢</span>
                <div class="stat-value">{{ $totalCostCenter ?? 8 }}</div>
                <div class="stat-label">Cost Center</div>
                <div class="stat-sub">unit terdaftar</div>
            </div>
        </div>
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="stat-card">
                <span class="stat-icon">👤</span>
                <div class="stat-value">{{ $userAktif ?? 24 }}</div>
                <div class="stat-label">User Aktif</div>
                <div class="stat-sub">dengan akses sistem</div>
            </div>
        </div>
        <div class="col-6 col-sm-6 col-lg-3">
            <div class="stat-card">
                <span class="stat-icon">📝</span>
                <div class="stat-value">{{ $logBulanIni ?? 156 }}</div>
                <div class="stat-label">Log Perubahan</div>
                <div class="stat-sub">aktivitas bulan ini</div>
            </div>
        </div>
    </div>

    {{-- ── ALERTS ──────────────────────────────────────── --}}
    <div class="d-flex flex-column gap-2 mb-1">
        <div class="alert-info-hrms">
            <i class="bi bi-info-circle-fill flex-shrink-0"></i>
            Parameter BPJS belum diupdate untuk 2024
        </div>
        <div class="alert-success-hrms">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            Backup database terakhir: hari ini 06:00
        </div>
    </div>

    {{-- ── MENU CARDS ──────────────────────────────────── --}}
    <p class="section-label">Menu Tersedia</p>

    <div class="row g-3">

        {{-- 1. Master Golongan --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalGolongan">
                <div class="menu-card-top">
                    <span class="menu-card-icon">🏅</span>
                    <span class="menu-badge badge-config">Config</span>
                </div>
                <div class="menu-card-title">Master Golongan</div>
                <div class="menu-card-desc">Setup level golongan karyawan (H-1 s/d H-15)</div>
            </div>
        </div>

        {{-- 2. Car Allowance --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalCarAllowance">
                <div class="menu-card-top">
                    <span class="menu-card-icon">🚗</span>
                    <span class="menu-badge badge-config">Config</span>
                </div>
                <div class="menu-card-title">Car Allowance</div>
                <div class="menu-card-desc">Besaran car allowance per golongan</div>
            </div>
        </div>

        {{-- 3. Parameter BPJS --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalBpjs">
                <div class="menu-card-top">
                    <span class="menu-card-icon">🛡️</span>
                    <span class="menu-badge badge-config">Config</span>
                </div>
                <div class="menu-card-title">Parameter BPJS</div>
                <div class="menu-card-desc">% JHT, JP, JKK, JKM — perusahaan &amp; karyawan</div>
            </div>
        </div>

        {{-- 4. Komponen Gaji --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalKomponenGaji">
                <div class="menu-card-top">
                    <span class="menu-card-icon">💰</span>
                    <span class="menu-badge badge-config">Config</span>
                </div>
                <div class="menu-card-title">Komponen Gaji</div>
                <div class="menu-card-desc">Uang makan, transport, tunjangan per golongan</div>
            </div>
        </div>

        {{-- 5. User Management --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalUser">
                <div class="menu-card-top">
                    <span class="menu-card-icon">👤</span>
                    <span class="menu-badge badge-admin">Admin</span>
                </div>
                <div class="menu-card-title">User Management</div>
                <div class="menu-card-desc">Hak akses tiap user: Personal Admin / Payroll / dll</div>
            </div>
        </div>

        {{-- 6. Master Unit/PT --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalUnitPt">
                <div class="menu-card-top">
                    <span class="menu-card-icon">🏢</span>
                    <span class="menu-badge badge-config">Config</span>
                </div>
                <div class="menu-card-title">Master Unit/PT</div>
                <div class="menu-card-desc">Data PT (STP Purwakarta, KBI Tejakula, dll)</div>
            </div>
        </div>

    </div>{{-- end row menu --}}

    {{-- Access note --}}
    <div class="access-note">
        <i class="bi bi-shield-lock-fill me-2" style="color:var(--accent);"></i>
        <strong>Catatan akses:</strong> User ini adalah ADMIN SISTEM. Bisa setting semua parameter: BPJS %, golongan, user management.
    </div>

</div>{{-- end page-wrapper --}}


{{-- ═══════════════════════════════════════════════════════ --}}
{{--  MODAL 1: MASTER GOLONGAN                               --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalGolongan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🏅 Master Golongan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Form tambah --}}
                <div class="modal-section-title">Tambah / Edit Golongan</div>
                <form method="POST" action="{{ route('master.golongan.store') }}" id="formGolongan">
                    @csrf
                    <input type="hidden" name="_method" id="methodGolongan" value="POST">
                    <input type="hidden" name="id" id="idGolongan">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label class="form-label">Kode Golongan <span class="text-danger">*</span></label>
                            <input type="text" name="kode_golongan" id="kodeGolongan" class="form-control" placeholder="H-11" required>
                        </div>
                        <div class="col-sm-8">
                            <label class="form-label">Nama Golongan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_golongan" id="namaGolongan" class="form-control" placeholder="Manager Level" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Gaji Pokok Min (Rp)</label>
                            <input type="number" name="gaji_pokok_min" id="gajiMin" class="form-control" placeholder="15000000">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Gaji Pokok Max (Rp)</label>
                            <input type="number" name="gaji_pokok_max" id="gajiMax" class="form-control" placeholder="25000000">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" name="deskripsi" id="deskGolongan" class="form-control" placeholder="Keterangan tambahan">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-primary-hrms">
                            <i class="bi bi-check-lg me-1"></i> Simpan
                        </button>
                        <button type="button" class="btn-outline-hrms" onclick="resetFormGolongan()">Reset</button>
                    </div>
                </form>

                {{-- Tabel --}}
                <div class="modal-section-title mt-4">Daftar Golongan</div>
                <div class="table-responsive">
                    <table class="table table-modal table-borderless">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Gaji Min</th>
                                <th>Gaji Max</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($golongans ?? [] as $g)
                            <tr>
                                <td><strong>{{ $g->kode_golongan }}</strong></td>
                                <td>{{ $g->nama_golongan }}</td>
                                <td>Rp {{ number_format($g->gaji_pokok_min,0,',','.') }}</td>
                                <td>Rp {{ number_format($g->gaji_pokok_max,0,',','.') }}</td>
                                <td>
                                    @if($g->is_active)
                                        <span class="badge-aktif">Aktif</span>
                                    @else
                                        <span class="badge-nonaktif">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="d-flex gap-1">
                                    <button class="btn-sm-action btn-edit" onclick="editGolongan({{ $g }})">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('master.golongan.destroy', $g->id) }}" onsubmit="return confirm('Hapus golongan ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-sm-action btn-del">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-3" style="font-size:.825rem;">Belum ada data golongan.</td></tr>
                            @endforelse

                            {{-- Contoh preview data (hapus saat sudah terhubung DB) --}}
                            @if(empty($golongans) || count($golongans) === 0)
                            <tr>
                                <td><strong>H-11</strong></td>
                                <td>Manager</td>
                                <td>Rp 15.000.000</td>
                                <td>Rp 25.000.000</td>
                                <td><span class="badge-aktif">Aktif</span></td>
                                <td class="d-flex gap-1">
                                    <button class="btn-sm-action btn-edit"><i class="bi bi-pencil"></i> Edit</button>
                                    <button class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>H-10</strong></td>
                                <td>Supervisor</td>
                                <td>Rp 8.000.000</td>
                                <td>Rp 15.000.000</td>
                                <td><span class="badge-aktif">Aktif</span></td>
                                <td class="d-flex gap-1">
                                    <button class="btn-sm-action btn-edit"><i class="bi bi-pencil"></i> Edit</button>
                                    <button class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════ --}}
{{--  MODAL 2: CAR ALLOWANCE                                 --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalCarAllowance" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🚗 Car Allowance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert-info-hrms mb-3" style="font-size:.8rem;">
                    <i class="bi bi-info-circle-fill"></i>
                    Karyawan yang mendapat car allowance <strong>tidak</strong> mendapat uang makan &amp; transport.
                </div>

                <div class="modal-section-title">Tambah / Edit Car Allowance</div>
                <form method="POST" action="{{ route('master.car-allowance.store') }}" id="formCA">
                    @csrf
                    <input type="hidden" name="_method" id="methodCA" value="POST">
                    <input type="hidden" name="id" id="idCA">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label class="form-label">Golongan <span class="text-danger">*</span></label>
                            <select name="id_golongan" id="golonganCA" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach($golongans ?? [] as $g)
                                    <option value="{{ $g->id }}">{{ $g->kode_golongan }} — {{ $g->nama_golongan }}</option>
                                @endforeach
                                <option value="1">H-11 — Manager</option>
                                <option value="2">H-10 — Supervisor</option>
                                <option value="3">H-9 — Staff Senior</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Nominal (Rp/bulan) <span class="text-danger">*</span></label>
                            <input type="number" name="nominal" id="nominalCA" class="form-control" placeholder="3500000" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Berlaku Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="berlaku_mulai" id="berlakuCA" class="form-control" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg me-1"></i> Simpan</button>
                        <button type="button" class="btn-outline-hrms" onclick="resetFormCA()">Reset</button>
                    </div>
                </form>

                <div class="modal-section-title mt-4">Daftar Car Allowance</div>
                <div class="table-responsive">
                    <table class="table table-modal table-borderless">
                        <thead>
                            <tr><th>Golongan</th><th>Nominal</th><th>Berlaku Mulai</th><th>Berlaku Selesai</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($carAllowances ?? [] as $ca)
                            <tr>
                                <td>{{ $ca->golongan->kode_golongan }} — {{ $ca->golongan->nama_golongan }}</td>
                                <td>Rp {{ number_format($ca->nominal,0,',','.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($ca->berlaku_mulai)->format('d/m/Y') }}</td>
                                <td>{{ $ca->berlaku_selesai ? \Carbon\Carbon::parse($ca->berlaku_selesai)->format('d/m/Y') : '<span class="badge-aktif">Aktif</span>' }}</td>
                                <td class="d-flex gap-1">
                                    <button class="btn-sm-action btn-edit">Edit</button>
                                    <button class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3" style="font-size:.825rem;">Belum ada data.</td></tr>
                            @endforelse
                            @if(empty($carAllowances) || count($carAllowances) === 0)
                            <tr><td>H-11 — Manager</td><td>Rp 3.500.000</td><td>01/01/2024</td><td><span class="badge-aktif">Aktif</span></td><td class="d-flex gap-1"><button class="btn-sm-action btn-edit">Edit</button><button class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button></td></tr>
                            <tr><td>H-10 — Supervisor</td><td>Rp 2.000.000</td><td>01/01/2024</td><td><span class="badge-aktif">Aktif</span></td><td class="d-flex gap-1"><button class="btn-sm-action btn-edit">Edit</button><button class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button></td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════ --}}
{{--  MODAL 3: PARAMETER BPJS                               --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalBpjs" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🛡️ Parameter BPJS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('master.parameter-bpjs.update', $paramBpjs->id ?? 1) }}">
                    @csrf @method('PUT')

                    <div class="modal-section-title">BPJS Ketenagakerjaan — Beban Perusahaan</div>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="form-label">JHT Perusahaan (%)</label>
                            <input type="number" step="0.001" name="jht_perusahaan_pct" class="form-control"
                                value="{{ $paramBpjs->jht_perusahaan_pct ?? 3.700 }}" required>
                            <div class="form-text">Default: 3,7%</div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">JP Perusahaan (%)</label>
                            <input type="number" step="0.001" name="jp_perusahaan_pct" class="form-control"
                                value="{{ $paramBpjs->jp_perusahaan_pct ?? 2.000 }}" required>
                            <div class="form-text">Default: 2%</div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">JKK (%)</label>
                            <input type="number" step="0.001" name="jkk_pct" class="form-control"
                                value="{{ $paramBpjs->jkk_pct ?? 0.240 }}" required>
                            <div class="form-text">Default: 0,24%</div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">JKM (%)</label>
                            <input type="number" step="0.001" name="jkm_pct" class="form-control"
                                value="{{ $paramBpjs->jkm_pct ?? 0.300 }}" required>
                            <div class="form-text">Default: 0,3%</div>
                        </div>
                    </div>

                    <div class="modal-section-title">BPJS Ketenagakerjaan — Beban Karyawan</div>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="form-label">JHT Karyawan (%)</label>
                            <input type="number" step="0.001" name="jht_karyawan_pct" class="form-control"
                                value="{{ $paramBpjs->jht_karyawan_pct ?? 2.000 }}" required>
                            <div class="form-text">Default: 2%</div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">JP Karyawan (%)</label>
                            <input type="number" step="0.001" name="jp_karyawan_pct" class="form-control"
                                value="{{ $paramBpjs->jp_karyawan_pct ?? 1.000 }}" required>
                            <div class="form-text">Default: 1%</div>
                        </div>
                    </div>

                    <div class="modal-section-title">BPJS Kesehatan</div>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="form-label">BPJS Kes. Perusahaan (%)</label>
                            <input type="number" step="0.001" name="bpjs_kes_perusahaan_pct" class="form-control"
                                value="{{ $paramBpjs->bpjs_kes_perusahaan_pct ?? 4.000 }}" required>
                            <div class="form-text">Default: 4%</div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">BPJS Kes. Karyawan (%)</label>
                            <input type="number" step="0.001" name="bpjs_kes_karyawan_pct" class="form-control"
                                value="{{ $paramBpjs->bpjs_kes_karyawan_pct ?? 1.000 }}" required>
                            <div class="form-text">Default: 1%</div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Berlaku Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="berlaku_mulai" class="form-control"
                                value="{{ $paramBpjs->berlaku_mulai ?? '2024-01-01' }}" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn-primary-hrms">
                            <i class="bi bi-check-lg me-1"></i> Simpan Parameter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════ --}}
{{--  MODAL 4: KOMPONEN GAJI                                 --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalKomponenGaji" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">💰 Komponen Gaji</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div class="modal-section-title">Tambah / Edit Komponen</div>
                <form method="POST" action="{{ route('master.komponen-gaji.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label class="form-label">Golongan <span class="text-danger">*</span></label>
                            <select name="id_golongan" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                @foreach($golongans ?? [] as $g)
                                    <option value="{{ $g->id }}">{{ $g->kode_golongan }}</option>
                                @endforeach
                                <option>H-11</option><option>H-10</option><option>H-9</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Uang Makan (Rp/hari)</label>
                            <input type="number" name="uang_makan" class="form-control" placeholder="35000">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Uang Transport (Rp/hari)</label>
                            <input type="number" name="uang_transport" class="form-control" placeholder="30000">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Tunjangan Lain (Rp/bln)</label>
                            <input type="number" name="tunjangan_lain" class="form-control" placeholder="0">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Berlaku Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="berlaku_mulai" class="form-control" required>
                        </div>
                    </div>
                    <div class="alert-info-hrms mt-3" style="font-size:.78rem;">
                        <i class="bi bi-info-circle-fill"></i>
                        Komponen ini hanya berlaku untuk karyawan yang <strong>tidak</strong> memiliki car allowance.
                    </div>
                    <button type="submit" class="btn-primary-hrms mt-3">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </form>

                <div class="modal-section-title mt-4">Daftar Komponen Gaji</div>
                <div class="table-responsive">
                    <table class="table table-modal table-borderless">
                        <thead>
                            <tr><th>Golongan</th><th>Uang Makan/hari</th><th>Transport/hari</th><th>Berlaku Mulai</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($komponenGajis ?? [] as $k)
                            <tr>
                                <td>{{ $k->golongan->kode_golongan }}</td>
                                <td>Rp {{ number_format($k->uang_makan,0,',','.') }}</td>
                                <td>Rp {{ number_format($k->uang_transport,0,',','.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($k->berlaku_mulai)->format('d/m/Y') }}</td>
                                <td class="d-flex gap-1">
                                    <button class="btn-sm-action btn-edit">Edit</button>
                                    <button class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3" style="font-size:.825rem;">Belum ada data.</td></tr>
                            @endforelse
                            @if(empty($komponenGajis) || count($komponenGajis) === 0)
                            <tr><td>H-11</td><td>Rp 35.000</td><td>Rp 30.000</td><td>01/01/2024</td><td class="d-flex gap-1"><button class="btn-sm-action btn-edit">Edit</button><button class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button></td></tr>
                            <tr><td>H-10</td><td>Rp 30.000</td><td>Rp 25.000</td><td>01/01/2024</td><td class="d-flex gap-1"><button class="btn-sm-action btn-edit">Edit</button><button class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button></td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════ --}}
{{--  MODAL 5: USER MANAGEMENT                               --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">👤 User Management</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div class="modal-section-title">Tambah / Edit User</div>
                <form method="POST" action="{{ route('master.users.store') }}" id="formUser">
                    @csrf
                    <input type="hidden" name="_method" id="methodUser" value="POST">
                    <input type="hidden" name="id" id="idUser">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="namaUser" class="form-control" placeholder="Nama lengkap" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="emailUser" class="form-control" placeholder="email@perusahaan.com" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="id_role" id="roleUser" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles ?? [] as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_role }}</option>
                                @endforeach
                                <option value="1">Master System</option>
                                <option value="2">Personal Administration</option>
                                <option value="3">Payroll</option>
                                <option value="4">Personalia</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Unit / PT</label>
                            <select name="id_unit" class="form-select">
                                <option value="">-- Semua Unit --</option>
                                @foreach($units ?? [] as $u)
                                    <option value="{{ $u->id }}">{{ $u->nama_pt }}</option>
                                @endforeach
                                <option value="1">PT Suri Tani Pemuka</option>
                                <option value="2">PT Kona Bay Indonesia</option>
                            </select>
                            <div class="form-text">Kosong = akses semua unit</div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Password <span class="text-danger" id="pwRequired">*</span></label>
                            <input type="password" name="password" id="passwordUser" class="form-control" placeholder="Min 8 karakter">
                            <div class="form-text" id="pwHint">Isi untuk buat/reset password</div>
                        </div>
                        <div class="col-sm-4" id="pwConfirmWrap">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-primary-hrms" id="btnUser"><i class="bi bi-check-lg me-1"></i> Tambah User</button>
                        <button type="button" class="btn-outline-hrms" onclick="resetFormUser()">Reset</button>
                    </div>
                </form>

                <div class="modal-section-title mt-4">Daftar User</div>
                <div class="table-responsive">
                    <table class="table table-modal table-borderless">
                        <thead>
                            <tr><th>Nama</th><th>Email</th><th>Role</th><th>Unit</th><th>Status</th><th>Last Login</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($users ?? [] as $u)
                            <tr>
                                <td><strong>{{ $u->nama }}</strong></td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->role->nama_role ?? '-' }}</td>
                                <td>{{ $u->unit->nama_pt ?? 'Semua' }}</td>
                                <td>
                                    @if($u->is_active)
                                        <span class="badge-aktif">Aktif</span>
                                    @else
                                        <span class="badge-nonaktif">Nonaktif</span>
                                    @endif
                                </td>
                                <td style="font-size:.75rem;color:var(--text-muted);">
                                    {{ $u->last_login ? \Carbon\Carbon::parse($u->last_login)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <button class="btn-sm-action btn-edit" onclick="editUser({{ $u }})">Edit</button>
                                        <form method="POST" action="{{ route('master.users.toggle-active', $u->id) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn-sm-action" style="background:rgba(245,166,35,0.1);color:#c47a00;">
                                                {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-3" style="font-size:.825rem;">Belum ada user.</td></tr>
                            @endforelse
                            @if(empty($users) || count($users) === 0)
                            <tr><td><strong>Super Admin</strong></td><td>admin@hrms.local</td><td>Master System</td><td>Semua</td><td><span class="badge-aktif">Aktif</span></td><td style="font-size:.75rem;color:var(--text-muted);">10/03/2026 08:12</td><td><div class="d-flex gap-1"><button class="btn-sm-action btn-edit">Edit</button></div></td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════ --}}
{{--  MODAL 6: MASTER UNIT / PT                              --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalUnitPt" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🏢 Master Unit / PT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div class="modal-section-title">Tambah / Edit Unit</div>
                <form method="POST" action="{{ route('master.unit-pt.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label class="form-label">Kode Unit <span class="text-danger">*</span></label>
                            <input type="text" name="kode_unit" class="form-control" placeholder="STP-PWK" required>
                        </div>
                        <div class="col-sm-8">
                            <label class="form-label">Nama PT <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pt" class="form-control" placeholder="PT Suri Tani Pemuka" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Purwakarta">
                        </div>
                        <div class="col-sm-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActiveUnit" checked>
                                <label class="form-check-label" for="isActiveUnit" style="font-size:.875rem;font-weight:600;">Aktif</label>
                            </div>
                        </div>
                    </div>

                    {{-- Cost Center --}}
                    <div class="modal-section-title">Cost Center untuk Unit Ini</div>
                    <div class="row g-3 mb-2" id="ccList">
                        <div class="col-sm-4">
                            <input type="text" name="cost_centers[]" class="form-control" placeholder="Umum">
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="cost_centers[]" class="form-control" placeholder="Produksi">
                        </div>
                        <div class="col-sm-4">
                            <input type="text" name="cost_centers[]" class="form-control" placeholder="Sales">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary-hrms mt-2">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                </form>

                <div class="modal-section-title mt-4">Daftar Unit / PT</div>
                <div class="table-responsive">
                    <table class="table table-modal table-borderless">
                        <thead>
                            <tr><th>Kode</th><th>Nama PT</th><th>Lokasi</th><th>Cost Center</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($units ?? [] as $u)
                            <tr>
                                <td><strong>{{ $u->kode_unit }}</strong></td>
                                <td>{{ $u->nama_pt }}</td>
                                <td>{{ $u->lokasi }}</td>
                                <td style="font-size:.75rem;">{{ $u->costCenters->pluck('nama_cc')->join(', ') }}</td>
                                <td><span class="{{ $u->is_active ? 'badge-aktif' : 'badge-nonaktif' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="d-flex gap-1">
                                    <button class="btn-sm-action btn-edit">Edit</button>
                                    <button class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-3" style="font-size:.825rem;">Belum ada data.</td></tr>
                            @endforelse
                            @if(empty($units) || count($units) === 0)
                            <tr><td><strong>STP-PWK</strong></td><td>PT Suri Tani Pemuka</td><td>Purwakarta</td><td style="font-size:.75rem;">Umum, Produksi, Sales</td><td><span class="badge-aktif">Aktif</span></td><td class="d-flex gap-1"><button class="btn-sm-action btn-edit">Edit</button><button class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button></td></tr>
                            <tr><td><strong>KBI-TJK</strong></td><td>PT Kona Bay Indonesia</td><td>Tejakula</td><td style="font-size:.75rem;">Umum, Produksi, Sales</td><td><span class="badge-aktif">Aktif</span></td><td class="d-flex gap-1"><button class="btn-sm-action btn-edit">Edit</button><button class="btn-sm-action btn-del"><i class="bi bi-trash"></i></button></td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@vite(['resources/js/master/dashboard.js'])

</body>
</html>
