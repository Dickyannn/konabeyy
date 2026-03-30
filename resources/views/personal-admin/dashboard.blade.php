<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Administration — HR Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:        #0092B4;
            --primary-dark:   #007A98;
            --primary-darker: #005F77;
            --primary-light:  #E8F7FB;
            --accent:         #F5A623;
            --danger:         #dc3545;
            --success:        #198754;
            --text-dark:      #1A2E3B;
            --text-muted:     #6B8896;
            --border:         #E2EEF2;
            --bg-page:        #F4F8FA;
            --bg-card:        #FFFFFF;
            --shadow-sm:      0 2px 8px rgba(0,100,130,0.07);
            --shadow-md:      0 6px 24px rgba(0,100,130,0.11);
        }
        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-page); color: var(--text-dark); margin: 0; min-height: 100vh; }

        /* ── NAVBAR ─────────────────────────────────────── */
        .navbar-hrms {
            background: #fff; border-bottom: 1.5px solid var(--border);
            padding: 0 2rem; height: 62px; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 1050;
            box-shadow: 0 2px 12px rgba(0,100,130,0.06);
        }
        .navbar-brand-area { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        .navbar-logo {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 0.95rem; letter-spacing: -0.5px; flex-shrink: 0;
        }
        .navbar-brand-text { font-weight: 700; font-size: 1rem; color: var(--primary); line-height: 1.1; }
        .navbar-brand-text small { display: block; font-size: 0.68rem; color: var(--text-muted); font-weight: 500; }
        .navbar-user {
            display: flex; align-items: center; gap: 0.65rem; cursor: pointer;
            padding: 0.4rem 0.75rem; border-radius: 10px; transition: background 0.15s;
        }
        .navbar-user:hover { background: var(--primary-light); }
        .navbar-avatar {
            width: 36px; height: 36px; border-radius: 50%; background: var(--primary-light);
            border: 2px solid var(--primary); display: flex; align-items: center;
            justify-content: center; color: var(--primary); font-size: 1rem;
        }
        .navbar-user-name { font-size: 0.875rem; font-weight: 700; color: var(--text-dark); line-height: 1.2; }
        .navbar-user-role { font-size: 0.7rem; color: var(--text-muted); font-weight: 500; }
        .dropdown-menu-user {
            border-radius: 12px; border: 1.5px solid var(--border);
            box-shadow: var(--shadow-md); min-width: 200px; padding: 0.5rem;
        }
        .dropdown-item-user {
            border-radius: 8px; padding: 0.55rem 0.9rem; font-size: 0.875rem; font-weight: 600;
            color: var(--text-dark); display: flex; align-items: center; gap: 0.6rem;
            cursor: pointer; transition: background 0.15s; background: none; border: none; width: 100%; text-align: left;
        }
        .dropdown-item-user:hover { background: var(--primary-light); color: var(--primary); }
        .dropdown-item-user.danger { color: var(--danger); }
        .dropdown-item-user.danger:hover { background: #fff0f0; }

        /* ── PAGE ───────────────────────────────────────── */
        .page-wrapper { max-width: 1300px; margin: 0 auto; padding: 2rem 1.5rem 3.5rem; }
        .page-header { margin-bottom: 1.75rem; }
        .page-header h1 { font-size: 1.65rem; font-weight: 800; color: var(--text-dark); margin: 0 0 0.2rem; }
        .page-header p { font-size: 0.875rem; color: var(--text-muted); margin: 0; font-weight: 500; }

        /* ── STAT CARDS ─────────────────────────────────── */
        .stat-card {
            background: var(--bg-card); border: 1.5px solid var(--border); border-radius: 16px;
            padding: 1.25rem 1.4rem; box-shadow: var(--shadow-sm);
            transition: transform 0.2s, box-shadow 0.2s; height: 100%; position: relative; overflow: hidden;
        }
        .stat-card::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            transform: scaleX(0); transform-origin: left; transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .stat-card:hover::after { transform: scaleX(1); }
        .stat-icon { font-size: 1.6rem; margin-bottom: 0.75rem; display: block; }
        .stat-badge {
            position: absolute; top: 1rem; right: 1rem;
            background: var(--accent); color: white; font-size: 0.6rem; font-weight: 800;
            padding: 0.15rem 0.5rem; border-radius: 20px; letter-spacing: 0.5px;
        }
        .stat-value { font-size: 1.9rem; font-weight: 800; color: var(--primary); line-height: 1; margin-bottom: 0.2rem; }
        .stat-value.danger { color: #e03444; }
        .stat-label { font-size: 0.875rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.1rem; }
        .stat-sub { font-size: 0.75rem; color: var(--text-muted); font-weight: 500; }

        /* ── ALERTS ─────────────────────────────────────── */
        .alert-warn-hrms {
            background: #FFFBF0; border: 1.5px solid #F5D88A; border-radius: 12px;
            padding: 0.8rem 1.1rem; font-size: 0.875rem; color: #7a5800;
            font-weight: 600; display: flex; align-items: center; gap: 0.6rem;
        }
        .alert-info-hrms {
            background: #EBF8FC; border: 1.5px solid #B3E5F2; border-radius: 12px;
            padding: 0.8rem 1.1rem; font-size: 0.875rem; color: var(--primary-darker);
            font-weight: 600; display: flex; align-items: center; gap: 0.6rem;
        }

        /* ── SECTION LABEL ──────────────────────────────── */
        .section-label {
            font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1.5px; color: var(--text-muted); margin-bottom: 1rem; margin-top: 2rem;
        }

        /* ── MENU CARDS ─────────────────────────────────── */
        .menu-card {
            background: var(--bg-card); border: 1.5px solid var(--border); border-radius: 16px;
            padding: 1.3rem 1.4rem; box-shadow: var(--shadow-sm); cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
            height: 100%; display: flex; flex-direction: column;
        }
        .menu-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); border-color: var(--primary); }
        .menu-card-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.85rem; }
        .menu-card-icon { font-size: 1.85rem; }
        .menu-badge { font-size: 0.68rem; font-weight: 700; padding: 0.2rem 0.65rem; border-radius: 20px; }
        .badge-master { background: rgba(0,146,180,0.12); color: var(--primary); border: 1px solid rgba(0,146,180,0.25); }
        .badge-view   { background: rgba(90,160,90,0.12); color: #2d7a2d; border: 1px solid rgba(90,160,90,0.25); }
        .badge-crud   { background: rgba(245,166,35,0.12); color: #a06800; border: 1px solid rgba(245,166,35,0.3); }
        .menu-card-title { font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.25rem; }
        .menu-card-desc { font-size: 0.8rem; color: var(--text-muted); font-weight: 500; line-height: 1.45; flex-grow: 1; }

        /* ── ACCESS NOTE ────────────────────────────────── */
        .access-note {
            background: #FFFBF0; border: 1.5px solid #F5D88A; border-radius: 12px;
            padding: 0.85rem 1.1rem; font-size: 0.82rem; color: #7a5800; font-weight: 500; margin-top: 2rem;
        }

        /* ── MODAL BASE ─────────────────────────────────── */
        .modal-content {
            border-radius: 20px; border: 1.5px solid var(--border);
            box-shadow: 0 24px 64px rgba(0,100,130,0.14);
        }
        .modal-header {
            border-bottom: 1.5px solid var(--border); padding: 1.25rem 1.5rem;
            border-radius: 20px 20px 0 0; background: #fff;
        }
        .modal-title { font-weight: 800; font-size: 1.05rem; color: var(--text-dark); }
        .modal-body { padding: 1.5rem; background: #fff; }
        .modal-footer { border-top: 1.5px solid var(--border); padding: 1rem 1.5rem; gap: 0.5rem; background: #fff; border-radius: 0 0 20px 20px; }

        /* ── MODAL TABS ─────────────────────────────────── */
        .modal-tabs {
            display: flex; gap: 0; border-bottom: 2px solid var(--border); margin-bottom: 1.5rem;
        }
        .modal-tab {
            padding: 0.6rem 1.1rem; font-size: 0.82rem; font-weight: 700; color: var(--text-muted);
            cursor: pointer; border-bottom: 2.5px solid transparent; margin-bottom: -2px;
            transition: color 0.15s, border-color 0.15s; background: none; border-top: none; border-left: none; border-right: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .modal-tab.active { color: var(--primary); border-bottom-color: var(--primary); }
        .modal-tab:hover:not(.active) { color: var(--primary-dark); }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        /* ── FORM ELEMENTS ──────────────────────────────── */
        .form-label { font-weight: 600; font-size: 0.82rem; color: var(--text-dark); margin-bottom: 0.35rem; }
        .form-control, .form-select {
            border: 1.5px solid var(--border); border-radius: 10px; font-size: 0.875rem;
            padding: 0.65rem 0.9rem; font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-dark); background: #FAFCFD; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,146,180,0.1); background: #fff; outline: none;
        }
        .form-control::placeholder { color: #b0c8d4; }
        .form-text { font-size: 0.75rem; color: var(--text-muted); }
        .modal-section-title {
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-muted); margin: 1.25rem 0 0.75rem;
            padding-bottom: 0.5rem; border-bottom: 1.5px solid var(--border);
        }
        .modal-section-title:first-child { margin-top: 0; }

        /* ── TABLE ──────────────────────────────────────── */
        .table-hrms { font-size: 0.82rem; }
        .table-hrms thead th {
            background: var(--primary-light); color: var(--primary-darker); font-weight: 700;
            font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;
            border: none; padding: 0.65rem 0.9rem; white-space: nowrap;
        }
        .table-hrms tbody td {
            padding: 0.75rem 0.9rem; border-color: var(--border);
            vertical-align: middle; color: var(--text-dark);
        }
        .table-hrms tbody tr:hover { background: #f7fbfc; }
        .table-hrms tbody tr:last-child td { border-bottom: none; }

        /* ── BUTTONS ────────────────────────────────────── */
        .btn-primary-hrms {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none; border-radius: 10px; color: #fff; font-weight: 700;
            font-size: 0.875rem; padding: 0.6rem 1.25rem;
            box-shadow: 0 3px 12px rgba(0,146,180,0.28); cursor: pointer;
            transition: opacity 0.2s, transform 0.15s; font-family: 'Plus Jakarta Sans', sans-serif;
            display: inline-flex; align-items: center; gap: 0.4rem;
        }
        .btn-primary-hrms:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-outline-hrms {
            border: 1.5px solid var(--border); border-radius: 10px; color: var(--text-muted);
            font-weight: 600; font-size: 0.875rem; padding: 0.6rem 1.25rem;
            background: transparent; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color 0.2s, color 0.2s;
        }
        .btn-outline-hrms:hover { border-color: var(--primary); color: var(--primary); }
        .btn-sm-action {
            padding: 0.28rem 0.65rem; font-size: 0.73rem; border-radius: 7px; font-weight: 600;
            border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif;
            display: inline-flex; align-items: center; gap: 0.3rem; white-space: nowrap;
        }
        .btn-edit   { background: rgba(0,146,180,0.1); color: var(--primary); }
        .btn-edit:hover { background: var(--primary); color: white; }
        .btn-del    { background: rgba(220,53,69,0.1); color: var(--danger); }
        .btn-del:hover { background: var(--danger); color: white; }
        .btn-view   { background: rgba(25,135,84,0.1); color: var(--success); }
        .btn-view:hover { background: var(--success); color: white; }

        /* ── BADGES ─────────────────────────────────────── */
        .badge-aktif    { background: #EDFAF3; color: #1a6b3c; border: 1px solid #A8E6C3; font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.65rem; border-radius: 20px; white-space: nowrap; }
        .badge-nonaktif { background: #FFF0F0; color: var(--danger); border: 1px solid #f5c6cb; font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.65rem; border-radius: 20px; }
        .badge-probation{ background: rgba(245,166,35,0.12); color: #a06800; border: 1px solid rgba(245,166,35,0.35); font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.65rem; border-radius: 20px; }
        .badge-permanent{ background: rgba(0,146,180,0.1); color: var(--primary-dark); border: 1px solid rgba(0,146,180,0.25); font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.65rem; border-radius: 20px; }
        .badge-warning  { background: rgba(220,53,69,0.1); color: #a02020; border: 1px solid rgba(220,53,69,0.2); font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.65rem; border-radius: 20px; }

        /* ── SEARCH BAR ─────────────────────────────────── */
        .search-bar {
            display: flex; gap: 0.65rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;
        }
        .search-input-wrap { position: relative; flex: 1; min-width: 180px; }
        .search-input-wrap i { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem; }
        .search-input-wrap input { padding-left: 2.2rem; width: 100%; }

        /* ── AVATAR ─────────────────────────────────────── */
        .avatar-sm {
            width: 30px; height: 30px; border-radius: 50%; background: var(--primary-light);
            border: 1.5px solid var(--primary); display: inline-flex; align-items: center;
            justify-content: center; color: var(--primary); font-size: 0.75rem; font-weight: 700;
            flex-shrink: 0;
        }

        /* ── EMPTY STATE ────────────────────────────────── */
        .empty-state { text-align: center; padding: 2.5rem 1rem; color: var(--text-muted); }
        .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; opacity: 0.4; }
        .empty-state p { font-size: 0.875rem; font-weight: 500; margin: 0; }

        /* ── KEPEGAWAIAN FIELD STYLES ──────────────────── */
        .kepegawaian-field:disabled {
            background-color: #f8f9fa !important;
            color: #6c757d !important;
            border-color: #dee2e6 !important;
            cursor: not-allowed;
        }
        
        .edit-only-notice {
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .edit-only-notice i {
            margin-right: 0.25rem;
        }

        /* ── RESPONSIVE ─────────────────────────────────── */
        @media (max-width: 767.98px) {
            .navbar-hrms { padding: 0 1rem; }
            .page-wrapper { padding: 1.25rem 1rem 2.5rem; }
            .page-header h1 { font-size: 1.35rem; }
            .stat-value { font-size: 1.55rem; }
        }
        @media (max-width: 575.98px) {
            .navbar-user-info { display: none; }
            .modal-dialog { margin: 0.5rem; }
        }
    </style>
</head>
<body>

<!-- ══════════════ NAVBAR ══════════════ -->
<nav class="navbar-hrms">
    <a href="#" class="navbar-brand-area">
        <div class="navbar-logo">HR</div>
        <div class="navbar-brand-text">
            HR Portal
            <small>PT Suri Tani Pemuka</small>
        </div>
    </a>
    <div class="dropdown">
        <div class="navbar-user d-flex" data-bs-toggle="dropdown">
            <div class="navbar-avatar"><i class="bi bi-person-fill"></i></div>
            <div class="navbar-user-info d-none d-sm-flex flex-column ms-1 me-1">
                <span class="navbar-user-name">Admin Personal</span>
                <span class="navbar-user-role">Personal Administration</span>
            </div>
            <i class="bi bi-chevron-down d-none d-sm-inline-flex align-self-center" style="color:var(--text-muted);font-size:.75rem;"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-user mt-1 p-2">
            <li><button class="dropdown-item-user"><i class="bi bi-person-circle"></i> Profil Saya</button></li>
            <li><hr style="margin:.4rem 0;border-color:var(--border);"></li>
            <li>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="dropdown-item-user danger" style="width:100%;text-align:left;border:none;background:none;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>

<!-- ══════════════ MAIN ══════════════ -->
<div class="page-wrapper">

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:12px;border:1.5px solid #198754;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px;border:1.5px solid #dc3545;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px;border:1.5px solid #dc3545;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <h1>Personal Administration</h1>
        <p>Kelola data kepegawaian secara lengkap</p>
    </div>

    <!-- ── STAT CARDS ──────────────────────────────── -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <span class="stat-icon">👥</span>
                <div class="stat-value">{{ $totalKaryawan }}</div>
                <div class="stat-label">Total Karyawan</div>
                <div class="stat-sub">aktif bulan ini</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                @if($karyawanBaru > 0)<span class="stat-badge">NEW</span>@endif
                <span class="stat-icon">✨</span>
                <div class="stat-value">{{ $karyawanBaru }}</div>
                <div class="stat-label">Karyawan Baru</div>
                <div class="stat-sub">bulan ini</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <span class="stat-icon">⏰</span>
                <div class="stat-value danger">{{ $kontrakBerakhir }}</div>
                <div class="stat-label">Kontrak Berakhir</div>
                <div class="stat-sub">dalam 30 hari</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <span class="stat-icon">🏅</span>
                <div class="stat-value">{{ $golonganH11 }}</div>
                <div class="stat-label">Golongan H-11</div>
                <div class="stat-sub">karyawan senior</div>
            </div>
        </div>
    </div>

    <!-- ── ALERTS ──────────────────────────────────── -->
    <div class="d-flex flex-column gap-2 mb-1">
        @if($kontrakBerakhir > 0)
        <div class="alert-warn-hrms">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            <span><strong>{{ $kontrakBerakhir }} kontrak</strong> akan berakhir dalam 30 hari — segera tindak lanjut perpanjangan.</span>
        </div>
        @endif
        @php
            $karyawanTanpaBpjs = $karyawans->filter(fn($k) => empty($k->bpjs_kesehatan_number) && empty($k->bpjs_tk_number))->count();
        @endphp
        @if($karyawanTanpaBpjs > 0)
        <div class="alert-info-hrms">
            <i class="bi bi-info-circle-fill flex-shrink-0"></i>
            <span><strong>{{ $karyawanTanpaBpjs }} karyawan</strong> belum upload dokumen BPJS.</span>
        </div>
        @endif
    </div>

    <!-- ── MENU CARDS ──────────────────────────────── -->
    <p class="section-label">Menu Tersedia</p>
    <div class="row g-3">

        <!-- 1. Data Karyawan -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalKaryawan">
                <div class="menu-card-top">
                    <span class="menu-card-icon">👤</span>
                    <span class="menu-badge badge-master">Master</span>
                </div>
                <div class="menu-card-title">Data Karyawan</div>
                <div class="menu-card-desc">NIP, nama, jabatan, cost center, golongan</div>
            </div>
        </div>

        <!-- 2. Kontrak Karyawan -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalKontrak">
                <div class="menu-card-top">
                    <span class="menu-card-icon">📄</span>
                    <span class="menu-badge badge-crud">CRUD</span>
                </div>
                <div class="menu-card-title">Kontrak Karyawan</div>
                <div class="menu-card-desc">Tanggal masuk, akhir kontrak, status permanent/probation</div>
            </div>
        </div>

        <!-- 3. Struktur Organisasi -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalStruktur">
                <div class="menu-card-top">
                    <span class="menu-card-icon">🏗️</span>
                    <span class="menu-badge badge-crud">CRUD</span>
                </div>
                <div class="menu-card-title">Struktur Organisasi</div>
                <div class="menu-card-desc">Atasan langsung, departemen, unit/PT</div>
            </div>
        </div>

        <!-- 4. Fasilitas Kendaraan -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalKendaraan">
                <div class="menu-card-top">
                    <span class="menu-card-icon">🚗</span>
                    <span class="menu-badge badge-crud">CRUD</span>
                </div>
                <div class="menu-card-title">Fasilitas Kendaraan</div>
                <div class="menu-card-desc">Car allowance berdasarkan golongan</div>
            </div>
        </div>

        <!-- 5. Data BPJS -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalBpjs">
                <div class="menu-card-top">
                    <span class="menu-card-icon">🛡️</span>
                    <span class="menu-badge badge-view">View</span>
                </div>
                <div class="menu-card-title">Data BPJS</div>
                <div class="menu-card-desc">No. BPJS Kesehatan &amp; Ketenagakerjaan</div>
            </div>
        </div>

        <!-- 6. Riwayat Jabatan -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalRiwayat">
                <div class="menu-card-top">
                    <span class="menu-card-icon">📈</span>
                    <span class="menu-badge badge-crud">CRUD</span>
                </div>
                <div class="menu-card-title">Riwayat Jabatan</div>
                <div class="menu-card-desc">History mutasi, promosi, demosi</div>
            </div>
        </div>

    </div>

    <div class="access-note">
        <i class="bi bi-shield-lock-fill me-2" style="color:var(--accent);"></i>
        <strong>Catatan akses:</strong> User ini punya akses PENUH ke semua data kepegawaian. Bisa tambah, edit, hapus data karyawan.
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════ -->
<!--  MODAL 1: DATA KARYAWAN                                    -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalKaryawan" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">👤 Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <!-- Custom Tabs -->
                <div class="modal-tabs">
                    <button class="modal-tab active" onclick="switchTab(this, 'karyawan-list')">
                        <i class="bi bi-table me-1"></i> Daftar Karyawan
                    </button>
                    <button class="modal-tab" onclick="switchTab(this, 'karyawan-form')">
                        <i class="bi bi-person-plus me-1"></i> Tambah / Edit
                    </button>
                </div>

                <!-- TAB: Daftar -->
                <div id="karyawan-list" class="tab-pane active">
                    <div class="search-bar">
                        <div class="search-input-wrap">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" placeholder="Cari NIP, nama, jabatan..." oninput="filterTable(this, 'tblKaryawan')">
                        </div>
                        <select class="form-select" style="width:auto;min-width:140px;" onchange="filterByGolongan(this)">
                            <option value="">Semua Golongan</option>
                            @foreach($golongans as $gol)
                                <option value="{{ $gol->kode_golongan }}">{{ $gol->kode_golongan }}</option>
                            @endforeach
                        </select>
                        <select class="form-select" style="width:auto;min-width:140px;" onchange="filterByUnit(this)">
                            <option value="">Semua Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->nama_pt }}">{{ $unit->nama_pt }}</option>
                            @endforeach
                        </select>
                        <button class="btn-primary-hrms" onclick="switchTabById('karyawan-form')">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hrms table-borderless" id="tblKaryawan">
                            <thead>
                                <tr>
                                    <th>NIP</th><th>Nama</th><th>Jabatan</th>
                                    <th>Golongan</th><th>Cost Center</th><th>Unit</th>
                                    <th>Status</th><th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($karyawans as $karyawan)
                                <tr>
                                    <td class="text-muted" style="font-size:.75rem;font-weight:600;">{{ $karyawan->nip }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm">{{ strtoupper(substr($karyawan->nama_karyawan, 0, 2)) }}</div>
                                            <strong>{{ $karyawan->nama_karyawan }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $karyawan->jabatan ?? '-' }}</td>
                                    <td><span class="badge-permanent">{{ $karyawan->golongan?->kode_golongan ?? '-' }}</span></td>
                                    <td>{{ $karyawan->currentPosition?->costCenter?->nama_cc ?? '-' }}</td>
                                    <td>{{ $karyawan->unit?->nama_pt ?? '-' }}</td>
                                    <td>
                                        @if($karyawan->is_active)
                                            <span class="badge-aktif">Aktif</span>
                                        @else
                                            <span class="badge-nonaktif">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn-sm-action btn-edit" onclick="editKaryawan({{ $karyawan->id }})">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <form action="{{ route('personal-admin.karyawan.destroy', $karyawan) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-sm-action btn-del" onclick="return confirm('Yakin ingin menonaktifkan karyawan ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Tidak ada data karyawan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2" style="font-size:.78rem;color:var(--text-muted);">
                        <span>Menampilkan {{ $karyawans->count() }} karyawan</span>
                    </div>
                </div>

                <!-- TAB: Form -->
                <div id="karyawan-form" class="tab-pane">
                    <form action="{{ route('personal-admin.karyawan.store') }}" method="POST">
                        @csrf
                        <div class="modal-section-title">Identitas Karyawan</div>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <label class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" name="nip" class="form-control" placeholder="STP-2024-XXX" required>
                            </div>
                            <div class="col-sm-8">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_karyawan" class="form-control" placeholder="Nama sesuai KTP" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">NIK (KTP)</label>
                                <input type="text" name="nik" class="form-control" placeholder="16 digit" maxlength="16">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="nomor_telepon" class="form-control" placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="email@perusahaan.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
                            </div>
                        </div>

                        <div class="modal-section-title">Data Kepegawaian</div>
                        <div class="row g-3" id="kepegawaian-section">
                            <div class="col-sm-4">
                                <label class="form-label">Golongan <span class="text-danger">*</span></label>
                                <select name="id_golongan" class="form-select kepegawaian-field" required>
                                    <option value="">-- Pilih Golongan --</option>
                                    @foreach($golongans as $gol)
                                        <option value="{{ $gol->id }}">{{ $gol->kode_golongan }} — {{ $gol->nama_golongan }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted edit-only-notice" style="display: none;">
                                    <i class="bi bi-info-circle"></i> Perubahan data kepegawaian melalui menu "Riwayat Jabatan"
                                </small>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" class="form-control kepegawaian-field" placeholder="Manager, Staff, Supervisor..." required>
                                <small class="text-muted edit-only-notice" style="display: none;">
                                    <i class="bi bi-info-circle"></i> Perubahan data kepegawaian melalui menu "Riwayat Jabatan"
                                </small>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Unit / PT <span class="text-danger">*</span></label>
                                <select name="id_unit" class="form-select kepegawaian-field" required>
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->nama_pt }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted edit-only-notice" style="display: none;">
                                    <i class="bi bi-info-circle"></i> Perubahan data kepegawaian melalui menu "Riwayat Jabatan"
                                </small>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Status Kawin</label>
                                <select name="id_status_kawin" class="form-select kepegawaian-field">
                                    <option value="">-- Pilih --</option>
                                    @foreach($statusKawins as $sk)
                                        <option value="{{ $sk->id }}">{{ $sk->deskripsi }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted edit-only-notice" style="display: none;">
                                    <i class="bi bi-info-circle"></i> Perubahan data kepegawaian melalui menu "Riwayat Jabatan"
                                </small>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Status Karyawan <span class="text-danger">*</span></label>
                                <select name="id_status_karyawan" class="form-select kepegawaian-field" required>
                                    @foreach($statusKaryawans as $status)
                                        <option value="{{ $status->id }}">{{ $status->nama_status }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted edit-only-notice" style="display: none;">
                                    <i class="bi bi-info-circle"></i> Perubahan data kepegawaian melalui menu "Riwayat Jabatan"
                                </small>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Tanggal Bergabung <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_masuk" class="form-control kepegawaian-field" required>
                                <small class="text-muted edit-only-notice" style="display: none;">
                                    <i class="bi bi-info-circle"></i> Perubahan data kepegawaian melalui menu "Riwayat Jabatan"
                                </small>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Status Aktif</label>
                                <select name="is_active" class="form-select kepegawaian-field">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                                <small class="text-muted edit-only-notice" style="display: none;">
                                    <i class="bi bi-info-circle"></i> Perubahan data kepegawaian melalui menu "Riwayat Jabatan"
                                </small>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan Data</button>
                            <button type="button" class="btn-outline-hrms" onclick="switchTabById('karyawan-list')">Batal</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════ -->
<!--  MODAL 2: KONTRAK KARYAWAN                                 -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalKontrak" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📄 Kontrak Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-tabs">
                    <button class="modal-tab active" onclick="switchTab(this, 'kontrak-list')"><i class="bi bi-table me-1"></i> Daftar Kontrak</button>
                    <button class="modal-tab" onclick="switchTab(this, 'kontrak-form')"><i class="bi bi-plus-circle me-1"></i> Tambah / Edit</button>
                </div>

                <!-- TAB: Daftar -->
                <div id="kontrak-list" class="tab-pane active">
                    <div class="search-bar">
                        <div class="search-input-wrap">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" placeholder="Cari nama karyawan...">
                        </div>
                        <select class="form-select" style="width:auto;min-width:150px;">
                            <option>Semua Status</option>
                            <option>Permanent</option>
                            <option>Probation</option>
                            <option>Berakhir 30 Hari</option>
                        </select>
                        <button class="btn-primary-hrms" onclick="switchTabById('kontrak-form')"><i class="bi bi-plus-lg"></i> Tambah</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hrms table-borderless">
                            <thead>
                                <tr><th>NIP</th><th>Nama</th><th>Tgl Masuk</th><th>Akhir Kontrak</th><th>Sisa Hari</th><th>Tipe</th><th>Status</th><th>Aksi</th></tr>
                            </thead>
                            <tbody>
                                @forelse($kontrakData as $kontrak)
                                @php
                                    $sisaHari = $kontrak->tanggal_selesai ? now()->diffInDays($kontrak->tanggal_selesai, false) : null;
                                    $tipeKontrak = $kontrak->tanggal_selesai ? 'Probation/PKWT' : 'Permanent';
                                @endphp
                                <tr>
                                    <td class="text-muted" style="font-size:.75rem;font-weight:600;">{{ $kontrak->karyawan->nip }}</td>
                                    <td><strong>{{ $kontrak->karyawan->nama_karyawan }}</strong></td>
                                    <td>{{ $kontrak->tanggal_mulai ? $kontrak->tanggal_mulai->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $kontrak->tanggal_selesai ? $kontrak->tanggal_selesai->format('d/m/Y') : '—' }}</td>
                                    <td>
                                        @if($sisaHari !== null)
                                            @if($sisaHari < 0)
                                                <span class="badge-nonaktif">Expired</span>
                                            @elseif($sisaHari <= 7)
                                                <span class="badge-warning" style="background:rgba(220,53,69,0.15);color:#8c1010;border-color:rgba(220,53,69,0.3);">{{ $sisaHari }} hari</span>
                                            @elseif($sisaHari <= 30)
                                                <span class="badge-warning">{{ $sisaHari }} hari</span>
                                            @else
                                                <span style="font-size:.8rem;">{{ $sisaHari }} hari</span>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td><span class="{{ $kontrak->tanggal_selesai ? 'badge-probation' : 'badge-permanent' }}">{{ $tipeKontrak }}</span></td>
                                    <td><span class="badge-aktif">Aktif</span></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn-sm-action btn-edit" onclick="editKontrak({{ $kontrak->id }})">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data kontrak</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB: Form -->
                <div id="kontrak-form" class="tab-pane">
                    <form action="{{ route('personal-admin.posisi.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                                <select name="id_karyawan" class="form-select" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach($karyawans as $k)
                                        <option value="{{ $k->id }}">{{ $k->nip }} — {{ $k->nama_karyawan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="nama_jabatan" class="form-control" placeholder="Manager Produksi" required>
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
                                <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Tanggal Akhir Kontrak</label>
                                <input type="date" name="tanggal_selesai" class="form-control">
                                <div class="form-text">Kosongkan untuk permanent</div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan Kontrak</button>
                            <button type="button" class="btn-outline-hrms" onclick="switchTabById('kontrak-list');resetKontrakForm()">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════ -->
<!--  MODAL 3: STRUKTUR ORGANISASI                              -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalStruktur" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🏗️ Struktur Organisasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-tabs">
                    <button class="modal-tab active" onclick="switchTab(this, 'struktur-list')"><i class="bi bi-table me-1"></i> Daftar</button>
                    <button class="modal-tab" onclick="switchTab(this, 'struktur-form')"><i class="bi bi-plus-circle me-1"></i> Tambah / Edit</button>
                </div>

                <div id="struktur-list" class="tab-pane active">
                    <div class="search-bar">
                        <div class="search-input-wrap">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" placeholder="Cari nama atau departemen...">
                        </div>
                        <button class="btn-primary-hrms" onclick="switchTabById('struktur-form')"><i class="bi bi-plus-lg"></i> Tambah</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hrms table-borderless">
                            <thead><tr><th>Karyawan</th><th>Jabatan</th><th>Atasan Langsung</th><th>Cost Center</th><th>Unit</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @forelse($positions as $position)
                                <tr>
                                    <td><strong>{{ $position->karyawan->nama_karyawan }}</strong></td>
                                    <td>{{ $position->nama_jabatan }}</td>
                                    <td><span class="text-muted" style="font-size:.78rem;">{{ $position->karyawan->atasan?->nama_karyawan ?? '— (Top Level)' }}</span></td>
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
                        </table>
                    </div>
                </div>

                <div id="struktur-form" class="tab-pane">
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
                                <input type="text" name="nama_jabatan" class="form-control" placeholder="Manager Produksi" required>
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
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════ -->
<!--  MODAL 4: FASILITAS KENDARAAN                              -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalKendaraan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🚗 Fasilitas Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert-warn-hrms mb-3" style="font-size:.8rem;">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                    Karyawan yang mendapat fasilitas kendaraan <strong>tidak</strong> mendapat uang makan &amp; transport.
                </div>
                <div class="modal-tabs">
                    <button class="modal-tab active" onclick="switchTab(this, 'kendaraan-list')"><i class="bi bi-table me-1"></i> Daftar</button>
                    <button class="modal-tab" onclick="switchTab(this, 'kendaraan-form')"><i class="bi bi-plus-circle me-1"></i> Tambah / Edit</button>
                </div>

                <div id="kendaraan-list" class="tab-pane active">
                    <div class="table-responsive">
                        <table class="table table-hrms table-borderless">
                            <thead><tr><th>Karyawan</th><th>Golongan</th><th>Tipe Fasilitas</th><th>Nominal/Bulan</th><th>Berlaku Mulai</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @forelse($fasilitasKendaraans as $fasilitas)
                                <tr>
                                    <td><strong>{{ $fasilitas->karyawan->nama_karyawan }}</strong></td>
                                    <td><span class="badge-permanent">{{ $fasilitas->karyawan->golongan->kode_golongan }}</span></td>
                                    <td>{{ $fasilitas->jenis_fasilitas }}</td>
                                    <td>Rp {{ number_format($fasilitas->nominal_allowance ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ $fasilitas->tgl_berlaku ? $fasilitas->tgl_berlaku->format('d/m/Y') : '-' }}</td>
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
                        </table>
                    </div>
                </div>

                <div id="kendaraan-form" class="tab-pane">
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
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════ -->
<!--  MODAL 5: DATA BPJS (VIEW ONLY)                            -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalBpjs" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🛡️ Data BPJS Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert-info-hrms mb-3" style="font-size:.8rem;">
                    <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                    Halaman ini hanya untuk <strong>melihat</strong> data BPJS. Perubahan data dilakukan oleh tim Payroll.
                </div>

                <div class="search-bar">
                    <div class="search-input-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" placeholder="Cari nama karyawan atau No. BPJS...">
                    </div>
                    <select class="form-select" style="width:auto;min-width:160px;">
                        <option>Semua Status Dokumen</option>
                        <option>Dokumen Lengkap</option>
                        <option>Dokumen Belum Upload</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-hrms table-borderless">
                        <thead>
                            <tr><th>NIP</th><th>Nama</th><th>No. BPJS Kesehatan</th><th>No. BPJS TK (JHT)</th><th>Tanggungan</th><th>Dokumen</th><th>Detail</th></tr>
                        </thead>
                        <tbody>
                            @forelse($karyawans as $karyawan)
                            <tr>
                                <td class="text-muted" style="font-size:.75rem;font-weight:600;">{{ $karyawan->nip }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm">{{ strtoupper(substr($karyawan->nama_karyawan, 0, 2)) }}</div>
                                        <strong>{{ $karyawan->nama_karyawan }}</strong>
                                    </div>
                                </td>
                                <td style="font-family:monospace;font-size:.8rem;">{{ $karyawan->bpjs_kesehatan_number ?? '—' }}</td>
                                <td style="font-family:monospace;font-size:.8rem;">{{ $karyawan->bpjs_tk_number ?? '—' }}</td>
                                <td>—</td>
                                <td>
                                    @if($karyawan->bpjs_kesehatan_number && $karyawan->bpjs_tk_number)
                                        <span class="badge-aktif">Lengkap</span>
                                    @elseif($karyawan->bpjs_kesehatan_number || $karyawan->bpjs_tk_number)
                                        <span class="badge-probation">Sebagian</span>
                                    @else
                                        <span class="badge-nonaktif">Belum Upload</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn-sm-action btn-view" {{ (!$karyawan->bpjs_kesehatan_number && !$karyawan->bpjs_tk_number) ? 'disabled style=opacity:.4;' : '' }}>
                                        <i class="bi bi-eye"></i> Lihat
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div style="font-size:.78rem;color:var(--text-muted);margin-top:.5rem;">
                    Menampilkan {{ $karyawans->count() }} karyawan
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════ -->
<!--  MODAL 6: RIWAYAT JABATAN                                  -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalRiwayat" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📈 Riwayat Jabatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-tabs">
                    <button class="modal-tab active" onclick="switchTab(this, 'riwayat-list')"><i class="bi bi-table me-1"></i> Semua Riwayat</button>
                    <button class="modal-tab" onclick="switchTab(this, 'riwayat-form')"><i class="bi bi-plus-circle me-1"></i> Catat Perubahan</button>
                </div>

                <div id="riwayat-list" class="tab-pane active">
                    <div class="search-bar">
                        <div class="search-input-wrap">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" placeholder="Cari nama karyawan...">
                        </div>
                        <select class="form-select" style="width:auto;min-width:140px;">
                            <option>Semua Tipe</option>
                            <option>Promosi</option>
                            <option>Mutasi</option>
                            <option>Demosi</option>
                            <option>Rotasi</option>
                        </select>
                        <button class="btn-primary-hrms" onclick="switchTabById('riwayat-form')"><i class="bi bi-plus-lg"></i> Catat</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hrms table-borderless">
                            <thead>
                                <tr><th>Tanggal</th><th>Karyawan</th><th>Tipe</th><th>Jabatan Lama</th><th>Jabatan Baru</th><th>Golongan</th><th>Unit</th><th>Keterangan</th><th>Aksi</th></tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatJabatans as $riwayat)
                                <tr>
                                    <td style="font-size:.78rem;white-space:nowrap;">{{ $riwayat->tgl_efektif ? $riwayat->tgl_efektif->format('d/m/Y') : '-' }}</td>
                                    <td><strong>{{ $riwayat->karyawan->nama_karyawan }}</strong></td>
                                    <td><span class="badge-aktif" style="background:rgba(0,146,180,0.1);color:var(--primary);border-color:rgba(0,146,180,0.25);">{{ ucfirst($riwayat->jenis_perubahan) }}</span></td>
                                    <td style="font-size:.8rem;">{{ $riwayat->jabatan_lama ?? '-' }}</td>
                                    <td style="font-size:.8rem;font-weight:700;">{{ $riwayat->jabatan_baru }}</td>
                                    <td><span class="badge-permanent">{{ $riwayat->golonganBaruRelation->kode_golongan }}</span></td>
                                    <td style="font-size:.78rem;">{{ $riwayat->karyawan->unit->nama_pt }}</td>
                                    <td style="font-size:.78rem;color:var(--text-muted);">{{ $riwayat->catatan ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn-sm-action btn-edit" onclick="editRiwayat({{ $riwayat->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('personal-admin.riwayat.destroy', $riwayat) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-sm-action btn-del" onclick="return confirm('Yakin ingin menghapus riwayat ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada data riwayat jabatan</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="riwayat-form" class="tab-pane">
                    <form action="{{ route('personal-admin.riwayat.store') }}" method="POST">
                        @csrf
                        <div class="modal-section-title">Catat Perubahan Jabatan</div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                                <select name="id_karyawan" class="form-select" required onchange="onKaryawanSelected(this)">
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
                                <input type="text" name="jabatan_lama" class="form-control" placeholder="Opsional">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Jabatan Baru <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan_baru" class="form-control" placeholder="Manager Produksi" required>
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
                                    <option value="">-- Pilih --</option>
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
                                <input type="text" name="nomor_sk" class="form-control" placeholder="SK/2024/XXX">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="catatan" class="form-control" placeholder="Keterangan singkat">
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan Riwayat</button>
                            <button type="button" class="btn-outline-hrms" onclick="switchTabById('riwayat-list');resetRiwayatForm()">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Custom Tab Switcher ──────────────────────────────────────
function switchTab(btn, targetId) {
    const modal = btn.closest('.modal-content') || btn.closest('.modal-body').parentElement;
    // Deactivate all tabs
    modal.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
    modal.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    // Activate target
    btn.classList.add('active');
    const pane = modal.querySelector('#' + targetId);
    if (pane) pane.classList.add('active');
    
    // Reset karyawan form when switching to it (for create mode)
    if (targetId === 'karyawan-form') {
        resetKaryawanForm();
    }
}

function switchTabById(targetId) {
    const pane = document.getElementById(targetId);
    if (!pane) return;
    const modal = pane.closest('.modal-content');
    modal.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    pane.classList.add('active');
    // Also activate corresponding button
    modal.querySelectorAll('.modal-tab').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes(targetId)) {
            btn.classList.add('active');
        }
    });
    
    // Reset karyawan form when switching to it (for create mode)
    if (targetId === 'karyawan-form') {
        resetKaryawanForm();
    }
}

// ── Reset Karyawan Form for Create Mode ──────────────────────
function resetKaryawanForm() {
    const form = document.querySelector('#karyawan-form form');
    if (!form) return;
    
    // Reset form action to create mode
    form.action = "{{ route('personal-admin.karyawan.store') }}";
    
    // Remove method input (for create mode)
    const methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) {
        methodInput.remove();
    }
    
    // Clear all form fields
    form.reset();
    
    // Re-enable kepegawaian fields (for create mode)
    const kepegawaianFields = form.querySelectorAll('.kepegawaian-field');
    kepegawaianFields.forEach(field => {
        field.disabled = false;
        field.style.backgroundColor = '';
        field.style.color = '';
        // Re-add required attribute for required fields
        if (field.name === 'id_golongan' || field.name === 'jabatan' || field.name === 'id_unit' || field.name === 'id_status_karyawan' || field.name === 'tanggal_masuk') {
            field.setAttribute('required', 'required');
        }
    });
    
    // Hide edit notices
    const editNotices = form.querySelectorAll('.edit-only-notice');
    editNotices.forEach(notice => {
        notice.style.display = 'none';
    });
    
    // Reset submit button text
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan Data';
    
    // Reset editing state
    editingKaryawan = null;
}

// ── Live search/filter table ─────────────────────────────────
function filterTable(input, tableId) {
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
}

// ── Filter by Golongan ───────────────────────────────────────
function filterByGolongan(select) {
    const filter = select.value.toLowerCase();
    const rows = document.querySelectorAll('#tblKaryawan tbody tr');
    rows.forEach(row => {
        if (!filter) {
            row.style.display = '';
        } else {
            const golongan = row.cells[3]?.textContent.toLowerCase() || '';
            row.style.display = golongan.includes(filter) ? '' : 'none';
        }
    });
}

// ── Filter by Unit ───────────────────────────────────────────
function filterByUnit(select) {
    const filter = select.value.toLowerCase();
    const rows = document.querySelectorAll('#tblKaryawan tbody tr');
    rows.forEach(row => {
        if (!filter) {
            row.style.display = '';
        } else {
            const unit = row.cells[5]?.textContent.toLowerCase() || '';
            row.style.display = unit.includes(filter) ? '' : 'none';
        }
    });
}

// ── Toggle kontrak date field ────────────────────────────────
function toggleKontrakDate(sel) {
    const wrap = document.getElementById('kontrakEndWrap');
    if (wrap) {
        wrap.style.opacity = sel.value === 'permanent' ? '0.4' : '1';
        wrap.querySelector('input').disabled = sel.value === 'permanent';
    }
}
</script>

<!-- CRUD Handlers -->
<script>
// Global variables to store edit data
let editingKaryawan = null;
let editingPosisi = null;
let editingKendaraan = null;
let editingRiwayat = null;
let editingKontrak = null;

// ── KARYAWAN CRUD ─────────────────────────────────────────
function editKaryawan(id) {
    editingKaryawan = id;
    switchTabById('karyawan-form');
    
    // Fetch karyawan data via AJAX
    fetch(`/personal-admin/karyawan/${id}`)
        .then(response => response.json())
        .then(result => {
            const karyawan = result.data;
            const form = document.querySelector('#karyawan-form form');
            
            if (form) {
                form.action = `/personal-admin/karyawan/${id}`;
                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';
                
                // Populate ONLY identity fields (editable during update)
                form.querySelector('input[name="nip"]').value = karyawan.nip || '';
                form.querySelector('input[name="nama_karyawan"]').value = karyawan.nama_karyawan || '';
                form.querySelector('input[name="nik"]').value = karyawan.nik || '';
                form.querySelector('input[name="tanggal_lahir"]').value = karyawan.tanggal_lahir || '';
                form.querySelector('select[name="jenis_kelamin"]').value = karyawan.jenis_kelamin || '';
                form.querySelector('input[name="nomor_telepon"]').value = karyawan.nomor_telepon || '';
                form.querySelector('input[name="email"]').value = karyawan.email || '';
                form.querySelector('textarea[name="alamat"]').value = karyawan.alamat || '';
                
                // Populate kepegawaian fields but disable them
                form.querySelector('select[name="id_golongan"]').value = karyawan.id_golongan || '';
                form.querySelector('input[name="jabatan"]').value = karyawan.jabatan || '';
                form.querySelector('select[name="id_unit"]').value = karyawan.id_unit || '';
                form.querySelector('select[name="id_status_kawin"]').value = karyawan.id_status_kawin || '';
                form.querySelector('select[name="id_status_karyawan"]').value = karyawan.id_status_karyawan || '';
                form.querySelector('input[name="tanggal_masuk"]').value = karyawan.tanggal_masuk || '';
                form.querySelector('select[name="is_active"]').value = karyawan.is_active ? '1' : '0';
                
                // Disable kepegawaian fields during edit
                const kepegawaianFields = form.querySelectorAll('.kepegawaian-field');
                kepegawaianFields.forEach(field => {
                    field.disabled = true;
                    field.style.backgroundColor = '#f8f9fa';
                    field.style.color = '#6c757d';
                    field.removeAttribute('required'); // Remove required validation for disabled fields
                });
                
                // Show edit notices
                const editNotices = form.querySelectorAll('.edit-only-notice');
                editNotices.forEach(notice => {
                    notice.style.display = 'block';
                });
                
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Update Data';
            }
        })
        .catch(error => {
            console.error('Error fetching karyawan data:', error);
            alert('Gagal mengambil data karyawan');
        });
}

function resetKaryawanForm() {
    editingKaryawan = null;
    const form = document.querySelector('#karyawan-form form');
    if (form) {
        form.action = '/personal-admin/karyawan';
        form.reset();
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan Data';
    }
}

// ── POSISI CRUD ───────────────────────────────────────────
function editPosisi(id) {
    editingPosisi = id;
    switchTabById('struktur-form');
    
    // Fetch posisi data via AJAX
    fetch(`/personal-admin/posisi/${id}`)
        .then(response => response.json())
        .then(result => {
            const posisi = result.data;
            const form = document.querySelector('#struktur-form form');
            
            if (form) {
                form.action = `/personal-admin/posisi/${id}`;
                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';
                
                // Populate form fields
                form.querySelector('select[name="id_karyawan"]').value = posisi.id_karyawan || '';
                form.querySelector('input[name="nama_jabatan"]').value = posisi.nama_jabatan || '';
                form.querySelector('select[name="id_cost_center"]').value = posisi.id_cost_center || '';
                form.querySelector('input[name="tanggal_mulai"]').value = posisi.tanggal_mulai ? posisi.tanggal_mulai.split('T')[0] : '';
                
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Update';
            }
        })
        .catch(error => {
            console.error('Error fetching posisi data:', error);
            alert('Gagal mengambil data posisi');
        });
}

function resetPosisiForm() {
    editingPosisi = null;
    const form = document.querySelector('#struktur-form form');
    if (form) {
        form.action = '/personal-admin/posisi';
        form.reset();
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan';
    }
}

// ── KENDARAAN CRUD ────────────────────────────────────────
function editKendaraan(id) {
    editingKendaraan = id;
    switchTabById('kendaraan-form');
    
    // Fetch kendaraan data via AJAX
    fetch(`/personal-admin/kendaraan/${id}`)
        .then(response => response.json())
        .then(result => {
            const kendaraan = result.data;
            const form = document.querySelector('#kendaraan-form form');
            
            if (form) {
                form.action = `/personal-admin/kendaraan/${id}`;
                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';
                
                // Populate form fields
                form.querySelector('select[name="id_karyawan"]').value = kendaraan.id_karyawan || '';
                form.querySelector('select[name="jenis_fasilitas"]').value = kendaraan.jenis_fasilitas || '';
                form.querySelector('input[name="nominal_allowance"]').value = kendaraan.nominal_allowance || '';
                form.querySelector('input[name="tgl_berlaku"]').value = kendaraan.tgl_berlaku ? kendaraan.tgl_berlaku.split('T')[0] : '';
                form.querySelector('input[name="nomor_polisi"]').value = kendaraan.nomor_polisi || '';
                
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Update';
            }
        })
        .catch(error => {
            console.error('Error fetching kendaraan data:', error);
            alert('Gagal mengambil data kendaraan');
        });
}

function resetKendaraanForm() {
    editingKendaraan = null;
    const form = document.querySelector('#kendaraan-form form');
    if (form) {
        form.action = '/personal-admin/kendaraan';
        form.reset();
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan';
    }
}

// ── RIWAYAT CRUD ──────────────────────────────────────────
function editRiwayat(id) {
    editingRiwayat = id;
    switchTabById('riwayat-form');
    
    // Fetch riwayat data via AJAX
    fetch(`/personal-admin/riwayat/${id}`)
        .then(response => response.json())
        .then(result => {
            const riwayat = result.data;
            const form = document.querySelector('#riwayat-form form');
            
            if (form) {
                form.action = `/personal-admin/riwayat/${id}`;
                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';
                
                // Populate form fields
                form.querySelector('select[name="id_karyawan"]').value = riwayat.id_karyawan || '';
                form.querySelector('select[name="jenis_perubahan"]').value = riwayat.jenis_perubahan || '';
                form.querySelector('input[name="jabatan_lama"]').value = riwayat.jabatan_lama || '';
                form.querySelector('input[name="jabatan_baru"]').value = riwayat.jabatan_baru || '';
                form.querySelector('select[name="golongan_lama"]').value = riwayat.golongan_lama || '';
                form.querySelector('select[name="golongan_baru"]').value = riwayat.golongan_baru || '';
                form.querySelector('input[name="tgl_efektif"]').value = riwayat.tgl_efektif ? riwayat.tgl_efektif.split('T')[0] : '';
                form.querySelector('input[name="nomor_sk"]').value = riwayat.nomor_sk || '';
                form.querySelector('input[name="catatan"]').value = riwayat.catatan || '';
                
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Update Riwayat';
            }
        })
        .catch(error => {
            console.error('Error fetching riwayat data:', error);
            alert('Gagal mengambil data riwayat');
        });
}

function resetRiwayatForm() {
    editingRiwayat = null;
    const form = document.querySelector('#riwayat-form form');
    if (form) {
        form.action = '/personal-admin/riwayat';
        form.reset();
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan Riwayat';
    }
}

// ── AUTO-POPULATE RIWAYAT FORM ────────────────────────────
function onKaryawanSelected(selectElement) {
    const karyawanId = selectElement.value;
    const form = selectElement.closest('form');
    
    if (!karyawanId) {
        // Clear jabatan_lama and golongan_lama if no karyawan selected
        form.querySelector('input[name="jabatan_lama"]').value = '';
        form.querySelector('select[name="golongan_lama"]').value = '';
        return;
    }
    
    // Fetch current employee data
    fetch(`/personal-admin/employee-current/${karyawanId}`)
        .then(response => response.json())
        .then(result => {
            const employee = result.data;
            
            // Auto-populate jabatan_lama and golongan_lama
            form.querySelector('input[name="jabatan_lama"]').value = employee.current_jabatan;
            form.querySelector('select[name="golongan_lama"]').value = employee.current_golongan_id || '';
        })
        .catch(error => {
            console.error('Error fetching employee current data:', error);
        });
}

// ── KONTRAK CRUD ──────────────────────────────────────────
function editKontrak(id) {
    editingKontrak = id;
    switchTabById('kontrak-form');
    
    // Fetch kontrak data via AJAX (using posisi endpoint)
    fetch(`/personal-admin/posisi/${id}`)
        .then(response => response.json())
        .then(result => {
            const kontrak = result.data;
            const form = document.querySelector('#kontrak-form form');
            
            if (form) {
                form.action = `/personal-admin/posisi/${id}`;
                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';
                
                // Populate form fields
                form.querySelector('select[name="id_karyawan"]').value = kontrak.id_karyawan || '';
                form.querySelector('input[name="nama_jabatan"]').value = kontrak.nama_jabatan || '';
                form.querySelector('select[name="id_cost_center"]').value = kontrak.id_cost_center || '';
                form.querySelector('input[name="tanggal_mulai"]').value = kontrak.tanggal_mulai ? kontrak.tanggal_mulai.split('T')[0] : '';
                form.querySelector('input[name="tanggal_selesai"]').value = kontrak.tanggal_selesai ? kontrak.tanggal_selesai.split('T')[0] : '';
                
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Update Kontrak';
            }
        })
        .catch(error => {
            console.error('Error fetching kontrak data:', error);
            alert('Gagal mengambil data kontrak');
        });
}

function resetKontrakForm() {
    editingKontrak = null;
    const form = document.querySelector('#kontrak-form form');
    if (form) {
        form.action = '/personal-admin/posisi';
        form.reset();
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan Kontrak';
    }
}
</script>
</body>
</html>