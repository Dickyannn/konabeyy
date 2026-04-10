<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .btn-success-hrms {
            background: linear-gradient(135deg, #198754, #157347);
            border: none; border-radius: 10px; color: #fff; font-weight: 700;
            font-size: 0.875rem; padding: 0.6rem 1.25rem;
            box-shadow: 0 3px 12px rgba(25,135,84,0.28); cursor: pointer;
            transition: opacity 0.2s, transform 0.15s; font-family: 'Plus Jakarta Sans', sans-serif;
            display: inline-flex; align-items: center; gap: 0.4rem;
            text-decoration: none;
        }
        .btn-success-hrms:hover { opacity: 0.9; transform: translateY(-1px); color: #fff; }
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

        <!-- 6. History Data Karyawan -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalRiwayat">
                <div class="menu-card-top">
                    <span class="menu-card-icon">📈</span>
                    <span class="menu-badge badge-crud">CRUD</span>
                </div>
                <div class="menu-card-title">History Data Karyawan</div>
                <div class="menu-card-desc">Riwayat perubahan jabatan, golongan, unit, dll</div>
            </div>
        </div>

        <!-- 7. Data Keluarga -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#modalKeluarga">
                <div class="menu-card-top">
                    <span class="menu-card-icon">👨‍👩‍👧‍👦</span>
                    <span class="menu-badge badge-crud">CRUD</span>
                </div>
                <div class="menu-card-title">Data Keluarga</div>
                <div class="menu-card-desc">Kelola data anggota keluarga karyawan</div>
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
                    <button class="modal-tab" onclick="switchTab(this, 'karyawan-tambah')">
                        <i class="bi bi-person-plus me-1"></i> Tambah
                    </button>
                    <button class="modal-tab" id="btnEditTab" onclick="switchTab(this, 'karyawan-edit')" style="display:none;">
                        <i class="bi bi-pencil me-1"></i> Edit
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
                        <a href="{{ route('personal-admin.export.karyawan') }}" class="btn-success-hrms" title="Export ke Excel">
                            <i class="bi bi-file-earmark-excel"></i> Export
                        </a>
                        <button class="btn-primary-hrms" onclick="switchTabById('karyawan-tambah')">
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
                                            <button class="btn-sm-action btn-view" onclick="viewKaryawan({{ $karyawan->id }})">
                                                <i class="bi bi-eye"></i> View
                                            </button>
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

                <!-- TAB: Tambah (Create) -->
                <div id="karyawan-tambah" class="tab-pane">
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
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <label class="form-label">Golongan <span class="text-danger">*</span></label>
                                <select name="id_golongan" class="form-select" required>
                                    <option value="">-- Pilih Golongan --</option>
                                    @foreach($golongans as $gol)
                                        <option value="{{ $gol->id }}">{{ $gol->kode_golongan }} — {{ $gol->nama_golongan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" name="jabatan" class="form-control" placeholder="Manager, Staff, Supervisor..." required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Unit / PT <span class="text-danger">*</span></label>
                                <select name="id_unit" class="form-select" required>
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->nama_pt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Status Kawin</label>
                                <select name="id_status_kawin" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach($statusKawins as $sk)
                                        <option value="{{ $sk->id }}">{{ $sk->deskripsi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Status Karyawan <span class="text-danger">*</span></label>
                                <select name="id_status_karyawan" class="form-select" required>
                                    @foreach($statusKaryawans as $status)
                                        <option value="{{ $status->id }}">{{ $status->nama_status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Tanggal Bergabung <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_masuk" class="form-control" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Status Aktif</label>
                                <select name="is_active" class="form-select">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan Karyawan</button>
                            <button type="button" class="btn-outline-hrms" onclick="switchTabById('karyawan-list')">Batal</button>
                        </div>
                    </form>
                </div>

                <!-- TAB: Edit (Update) -->
                <div id="karyawan-edit" class="tab-pane">
                    <form id="formEditKaryawan" action="" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
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

                        <div class="modal-section-title">Data Kepegawaian (Read Only)</div>
                        <div class="alert alert-info" style="background: #EBF8FC; border: 1.5px solid #B3E5F2; border-radius: 8px; padding: 0.75rem; margin-bottom: 1rem; font-size: 0.82rem;">
                            <i class="bi bi-info-circle me-2" style="color: var(--primary);"></i>
                            <strong>Perubahan data kepegawaian</strong> (Golongan, Jabatan, Unit, dll) dapat diubah melalui menu <strong>"Riwayat Jabatan"</strong>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <label class="form-label">Golongan</label>
                                <select name="id_golongan" class="form-select" disabled>
                                    @foreach($golongans as $gol)
                                        <option value="{{ $gol->id }}">{{ $gol->kode_golongan }} — {{ $gol->nama_golongan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" disabled>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Unit / PT</label>
                                <select name="id_unit" class="form-select" disabled>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->nama_pt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Status Kawin</label>
                                <select name="id_status_kawin" class="form-select" disabled>
                                    <option value="">-- Pilih --</option>
                                    @foreach($statusKawins as $sk)
                                        <option value="{{ $sk->id }}">{{ $sk->deskripsi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Status Karyawan</label>
                                <select name="id_status_karyawan" class="form-select" disabled>
                                    @foreach($statusKaryawans as $status)
                                        <option value="{{ $status->id }}">{{ $status->nama_status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Tanggal Bergabung</label>
                                <input type="date" name="tanggal_masuk" class="form-control" disabled>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Status Aktif</label>
                                <select name="is_active" class="form-select" disabled>
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Update Data</button>
                            <button type="button" class="btn-outline-hrms" onclick="resetEditForm()">Batal</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════ -->
<!--  MODAL 1-B: VIEW DETAIL KARYAWAN                            -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalViewKaryawan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">👤 Detail Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="position: relative; min-height: 400px;">
                    <div style="padding-bottom: 0;">

                    <!-- Section A: Identitas Karyawan -->
                    <div class="modal-section-title">A. Identitas Karyawan</div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">NIP</label>
                            <div style="font-size: 1rem; font-weight: 600; color: var(--text-dark);" id="view_nip">-</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Nama Lengkap</label>
                            <div style="font-size: 1rem; font-weight: 600; color: var(--text-dark);" id="view_nama_karyawan">-</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">NIK</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_nik">-</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Tanggal Lahir</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_tanggal_lahir">-</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Jenis Kelamin</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_jenis_kelamin">-</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">No HP</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_nomor_telepon">-</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Email</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_email">-</div>
                        </div>
                        <div class="col-sm-6"></div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Alamat</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_alamat">-</div>
                        </div>
                    </div>

                    <!-- Section B: Data Kepegawaian -->
                    <div class="modal-section-title" style="margin-top: 2rem;">B. Data Kepegawaian</div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Golongan</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_golongan">-</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Jabatan</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_jabatan">-</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Unit / PT</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_unit">-</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Status Kawin</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_status_kawin">-</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Status Karyawan</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_status_karyawan">-</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Tanggal Bergabung</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_tanggal_masuk">-</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-weight: 600; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Status Aktif</label>
                            <div style="font-size: 0.95rem; color: var(--text-dark);" id="view_is_active">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="modal-footer" style="border-top: 1.5px solid var(--border); padding: 1rem; background: #fafbfc;">
                <button type="button" class="btn-outline-hrms" data-bs-dismiss="modal">Tutup</button>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="btn-sm-action btn-edit" id="btnEditFromView" onclick="editKaryawanFromView()">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn-sm-action btn-del" id="btnDeleteFromView" onclick="deleteKaryawanFromView()">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
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
<!--  MODAL: HISTORY DATA KARYAWAN                              -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalRiwayat" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📈 History Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                <!-- Error Display Area -->
                <div id="riwayat-errors" style="display: none;" class="alert alert-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Terdapat kesalahan:</strong>
                    <ul id="riwayat-error-list" class="mb-0 mt-2"></ul>
                </div>

                <!-- Success Display Area -->
                <div id="riwayat-success" style="display: none;" class="alert alert-success mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <span id="riwayat-success-message"></span>
                </div>

                <div class="modal-tabs">
                    <button class="modal-tab active" onclick="switchTab(this, 'riwayat-list')">
                        <i class="bi bi-table me-1"></i> Semua Riwayat
                    </button>
                    <button class="modal-tab" onclick="switchTab(this, 'riwayat-form')">
                        <i class="bi bi-plus-circle me-1"></i> Catat Perubahan
                    </button>
                    <button class="modal-tab" id="riwayat-view-tab" onclick="switchTab(this, 'riwayat-view')" style="display: none;">
                        <i class="bi bi-eye me-1"></i> Detail Riwayat
                    </button>
                </div>

                <!-- TAB: Semua Riwayat (List) -->
                <div id="riwayat-list" class="tab-pane active">
                    <div class="search-bar">
                        <div class="search-input-wrap">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" placeholder="Cari atau filter tipe..." onkeyup="filterRiwayatTable(this)">
                        </div>
                        <select class="form-select" style="width:auto;min-width:220px;" onchange="filterRiwayatByKaryawan(this)" id="selectFilterKaryawan">
                            <option value="">-- Semua Karyawan --</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->nip }}" data-id="{{ $k->id }}">{{ $k->nip }} - {{ $k->nama_karyawan }}</option>
                            @endforeach
                        </select>
                        <button class="btn-success-hrms" onclick="exportRiwayat()" title="Export ke Excel">
                            <i class="bi bi-file-earmark-excel"></i> Export
                        </button>
                        <button class="btn-primary-hrms" onclick="switchTabById('riwayat-form')"><i class="bi bi-plus-lg"></i> Catat</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hrms table-borderless" id="tblRiwayat">
                            <thead>
                                <tr>
                                    <th>NIP</th><th>Nama</th><th>Tipe Perubahan</th><th>Detail Perubahan</th>
                                    <th>Tanggal Efektif</th><th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatJabatans as $riwayat)
                                <tr data-nip="{{ $riwayat->nip ?? $riwayat->karyawan->nip }}">
                                    <td style="font-size:.75rem;font-weight:600;">{{ $riwayat->nip ?? $riwayat->karyawan->nip }}</td>
                                    <td><strong>{{ $riwayat->nama ?? $riwayat->karyawan->nama_karyawan }}</strong></td>
                                    <td><span class="badge-aktif" style="background:rgba(0,146,180,0.1);color:var(--primary);border-color:rgba(0,146,180,0.25);">{{ ucfirst($riwayat->jenis_perubahan) }}</span></td>
                                    <td style="font-size:.8rem;">{{ $riwayat->detail_perubahan ?? '-' }}</td>
                                    <td style="font-size:.78rem;white-space:nowrap;">{{ $riwayat->tgl_efektif ? $riwayat->tgl_efektif->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn-sm-action btn-view" onclick="viewRiwayat({{ $riwayat->id }})" title="Lihat detail">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada data riwayat</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB: Catat Perubahan (Form) -->
                <div id="riwayat-form" class="tab-pane">
                    <form id="formRiwayat" action="{{ route('personal-admin.riwayat.store') }}" method="POST">
                        @csrf
                        <div class="modal-section-title">Catat Perubahan Data Karyawan</div>
                        
                        <!-- STEP 1: Pilih Karyawan -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label">Pilih Karyawan <span class="text-danger">*</span></label>
                                <select name="id_karyawan" id="selectKaryawan" class="form-select" required onchange="onKaryawanSelected(this)">
                                    <option value="">-- Pilih Karyawan (NIP - Nama) --</option>
                                    @foreach($karyawans as $k)
                                        <option value="{{ $k->id }}">{{ $k->nip }} - {{ $k->nama_karyawan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- HIDDEN: Divider yang muncul setelah karyawan dipilih -->
                        <div id="dividerKaryawanSelected" style="display:none;">
                            <hr style="border: 1px dashed var(--border); margin: 2rem 0;">
                            
                            <!-- STEP 2 & 3: Current Data vs Proposed Data (Side by side) -->
                            <div class="row g-4" id="containerCompareView" style="display:none;">
                                <!-- LEFT: Current Data (Read-only) -->
                                <div class="col-lg-6">
                                    <div style="background: #f8fbfc; padding: 1.5rem; border-radius: 12px; border: 1.5px solid var(--primary-light);">
                                        <h6 style="font-weight: 700; color: var(--primary); margin-bottom: 1rem;">
                                            <i class="bi bi-check-circle me-2"></i> Current Status (Existing Data)
                                        </h6>
                                        <div class="row g-2" id="containerCurrentData" style="font-size: 0.85rem;">
                                            <!-- Filled by JavaScript -->
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT: Proposed Data (Editable) -->
                                <div class="col-lg-6">
                                    <div style="background: #f5f9fc; padding: 1.5rem; border-radius: 12px; border: 1.5px solid var(--primary-light);">
                                        <h6 style="font-weight: 700; color: var(--primary); margin-bottom: 1rem;">
                                            <i class="bi bi-pencil-square me-2"></i> Proposed Status (New Data)
                                        </h6>
                                        
                                        <!-- STEP 4: Form untuk user isi data baru -->
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Tipe Perubahan <span class="text-danger">*</span></label>
                                                <select name="jenis_perubahan" id="selectTipePerubahan" class="form-select form-select-sm" required onchange="updateDetailPerubahan(this)">
                                                    <option value="">-- Pilih Tipe Perubahan --</option>
                                                    <option value="promotion">Promotion</option>
                                                    <option value="demotion">Demotion</option>
                                                    <option value="transfer">Transfer</option>
                                                    <option value="termination">Termination</option>
                                                    <option value="new_hire">New Hire</option>
                                                    <option value="contract_extension">Contract Extension</option>
                                                    <option value="actual_conversion">Actual Conversion</option>
                                                    <option value="change_of_status">Change of Status</option>
                                                    <option value="pass_probation">Pass Probation</option>
                                                    <option value="service_extension">Service Extension</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Detail Perubahan <span class="text-danger">*</span></label>
                                                <select name="detail_perubahan" id="selectDetailPerubahan" class="form-select form-select-sm" required>
                                                    <option value="">-- Pilih Tipe Perubahan Terlebih Dahulu --</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Jabatan Baru</label>
                                                <input type="text" name="jabatan_baru" class="form-control form-control-sm" placeholder="Isi jika ada perubahan jabatan">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Golongan Baru</label>
                                                <select name="golongan_baru" class="form-select form-select-sm">
                                                    <option value="">-- Pilih jika ada perubahan --</option>
                                                    @foreach($golongans as $gol)
                                                        <option value="{{ $gol->id }}">{{ $gol->kode_golongan }} — {{ $gol->nama_golongan }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Unit / PT Baru</label>
                                                <select name="unit_baru" class="form-select form-select-sm">
                                                    <option value="">-- Pilih jika ada perubahan --</option>
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->nama_pt }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Status Karyawan Baru</label>
                                                <select name="status_karyawan_baru" class="form-select form-select-sm">
                                                    <option value="">-- Pilih jika ada perubahan --</option>
                                                    @foreach($statusKaryawans as $status)
                                                        <option value="{{ $status->id }}">{{ $status->nama_status }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Tanggal Efektif <span class="text-danger">*</span></label>
                                                <input type="date" name="tgl_efektif" class="form-control form-control-sm" required>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">No. SK</label>
                                                <input type="text" name="nomor_sk" class="form-control form-control-sm" placeholder="SK/2024/XXX">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Keterangan</label>
                                                <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Keterangan singkat"></textarea>
                                            </div>
                                            <!-- Hidden fields for old data -->
                                            <input type="hidden" name="jabatan_lama" id="inputJabatanLama">
                                            <input type="hidden" name="golongan_lama" id="inputGolonganLama">
                                            <input type="hidden" name="unit_lama" id="inputUnitLama">
                                            <input type="hidden" name="status_karyawan_lama" id="inputStatusKaryawanLama">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 5: Submit Buttons (appears after karyawan selected) -->
                            <div class="d-flex gap-2 mt-4" id="containerFormButtons" style="display:none;">
                                <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
                                <button type="button" class="btn-outline-hrms" onclick="resetRiwayatForm();switchTabById('riwayat-list')">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB: Detail Riwayat (View) -->
                <div id="riwayat-view" class="tab-pane">
                    <!-- Content will be dynamically populated by viewRiwayat() function -->
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ══════════════════════════════════════════════════════════ -->
<!--  MODAL 7: DATA KELUARGA                                    -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalKeluarga" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">👨‍👩‍👧‍👦 Data Keluarga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <!-- Custom Tabs -->
                <div class="modal-tabs">
                    <button class="modal-tab active" onclick="switchTab(this, 'keluarga-list')">
                        <i class="bi bi-table me-1"></i> Data Keluarga
                    </button>
                    <button class="modal-tab" onclick="switchTab(this, 'keluarga-tambah')">
                        <i class="bi bi-person-plus me-1"></i> Tambah Data Keluarga
                    </button>
                </div>

                <!-- TAB: Data Keluarga (List) -->
                <div id="keluarga-list" class="tab-pane active">
                    <div class="search-bar">
                        <div class="search-input-wrap">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" placeholder="Cari NIP, nama..." oninput="filterTable(this, 'tblKeluarga')">
                        </div>
                        <button class="btn-primary-hrms" onclick="switchTabById('keluarga-tambah')">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hrms table-borderless" id="tblKeluarga">
                            <thead>
                                <tr>
                                    <th>NIP</th><th>Nama</th><th>Jabatan</th>
                                    <th>Golongan</th><th>Cost Center</th><th>Unit</th>
                                    <th>Status</th><th>Jumlah Keluarga</th><th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="keluargaTableBody">
                                <tr><td colspan="9" class="text-center py-4"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB: Tambah Data Keluarga -->
                <div id="keluarga-tambah" class="tab-pane">
                    <form action="{{ route('personal-admin.keluarga.store') }}" method="POST">
                        @csrf
                        <div class="modal-section-title">Pilih Karyawan</div>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                                <select name="id_karyawan" class="form-select" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach($karyawans as $k)
                                        <option value="{{ $k->id }}">{{ $k->nip }} - {{ $k->nama_karyawan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-section-title">Data Anggota Keluarga</div>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <label class="form-label">Status Keluarga <span class="text-danger">*</span></label>
                                <select name="status_keluarga" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="spouse">Suami/Istri</option>
                                    <option value="child">Anak</option>
                                    <option value="father">Ayah</option>
                                    <option value="mother">Ibu</option>
                                    <option value="in-law">Mertua</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">NIK</label>
                                <input type="text" name="nik" class="form-control" placeholder="16 digit" maxlength="16">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" required>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Status Aktif</label>
                                <select name="is_active" class="form-select">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan</button>
                            <button type="button" class="btn-outline-hrms" onclick="switchTabById('keluarga-list')">Batal</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- POPUP: View Data Keluarga -->
<div class="modal fade" id="popupViewKeluarga" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">👁️ View Data Keluarga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewKeluargaContent">
                <div class="text-center py-4"><i class="bi bi-hourglass-split"></i> Loading...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-success-hrms" onclick="exportKeluargaKaryawan()" title="Export Data Keluarga Karyawan Ini">
                    <i class="bi bi-file-earmark-excel"></i> Export
                </button>
                <button type="button" class="btn-primary-hrms" onclick="openEditKeluargaPopup()">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button type="button" class="btn-outline-hrms" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- POPUP: Edit Data Keluarga -->
<div class="modal fade" id="popupEditKeluarga" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">✏️ Edit Data Keluarga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info alert-dismissible fade show" role="alert" style="border-radius:12px;border:1.5px solid #0dcaf0;">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Edit Batch:</strong> Semua perubahan akan disimpan dalam satu transaksi. Data lama akan disimpan sebagai backup.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                
                <div id="editKeluargaErrorContainer" style="display:none;" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Terdapat kesalahan:</strong>
                    <ul id="editKeluargaErrors" class="mb-0 mt-2"></ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <form id="formEditKeluarga">
                    <input type="hidden" id="editKaryawanId" name="karyawan_id">
                    <div id="editKeluargaFormContent">
                        <div class="text-center py-4"><i class="bi bi-hourglass-split"></i> Loading...</div>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn-primary-hrms" onclick="submitEditKeluargaPopup()">
                            <i class="bi bi-check-lg"></i> Simpan Semua Perubahan
                        </button>
                        <button type="button" class="btn-outline-hrms" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
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
    
    // Hide Edit tab if switching away from it
    if (targetId === 'karyawan-list' || targetId === 'karyawan-tambah') {
        const editTabBtn = document.getElementById('btnEditTab');
        if (editTabBtn) editTabBtn.style.display = 'none';
    }
    
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
    
    // Hide Edit tab if switching away from it
    if (targetId === 'karyawan-list' || targetId === 'karyawan-tambah') {
        const editTabBtn = document.getElementById('btnEditTab');
        if (editTabBtn) editTabBtn.style.display = 'none';
    }
    
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
let currentViewKaryawanId = null;

// ── KARYAWAN VIEW/EDIT/DELETE ─────────────────────────────
function viewKaryawan(id) {
    currentViewKaryawanId = id;
    
    // Fetch karyawan data via AJAX using the view endpoint
    fetch(`/personal-admin/karyawan/${id}/view`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(result => {
            if (!result.data) {
                throw new Error('Data tidak ditemukan dalam response');
            }
            
            const karyawan = result.data;
            
            // Populate view modal with data
            document.getElementById('view_nip').textContent = karyawan.nip || '-';
            document.getElementById('view_nama_karyawan').textContent = karyawan.nama_karyawan || '-';
            document.getElementById('view_nik').textContent = karyawan.nik || '-';
            document.getElementById('view_tanggal_lahir').textContent = karyawan.tanggal_lahir || '-';
            document.getElementById('view_jenis_kelamin').textContent = karyawan.jenis_kelamin || '-';
            document.getElementById('view_nomor_telepon').textContent = karyawan.nomor_telepon || '-';
            document.getElementById('view_email').textContent = karyawan.email || '-';
            document.getElementById('view_alamat').textContent = karyawan.alamat || '-';
            
            // Data Kepegawaian
            document.getElementById('view_golongan').textContent = karyawan.golongan || '-';
            document.getElementById('view_jabatan').textContent = karyawan.jabatan || '-';
            document.getElementById('view_unit').textContent = karyawan.unit || '-';
            document.getElementById('view_status_kawin').textContent = karyawan.status_kawin || '-';
            document.getElementById('view_status_karyawan').textContent = karyawan.status_karyawan || '-';
            document.getElementById('view_tanggal_masuk').textContent = karyawan.tanggal_masuk || '-';
            document.getElementById('view_is_active').textContent = karyawan.is_active || '-';
            
            // Show the modal
            // Show Edit tab button when viewing a karyawan
            document.getElementById('btnEditTab').style.display = 'inline-block';
            
            const modal = new bootstrap.Modal(document.getElementById('modalViewKaryawan'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching karyawan data:', error);
            alert('Gagal mengambil data karyawan: ' + error.message);
        });
}

function editKaryawanFromView() {
    if (!currentViewKaryawanId) {
        alert('ID karyawan tidak ditemukan');
        return;
    }
    
    // Close view modal
    const viewModal = bootstrap.Modal.getInstance(document.getElementById('modalViewKaryawan'));
    if (viewModal) viewModal.hide();
    
    // Open main karyawan modal and switch to edit tab
    const mainModal = new bootstrap.Modal(document.getElementById('modalKaryawan'));
    mainModal.show();
    
    // Call the existing editKaryawan function
    setTimeout(() => {
        editKaryawan(currentViewKaryawanId);
    }, 300);
}

function deleteKaryawanFromView() {
    if (!currentViewKaryawanId) {
        alert('ID karyawan tidak ditemukan');
        return;
    }
    
    if (confirm('Yakin ingin menonaktifkan karyawan ini? Data akan disimpan ke archive.')) {
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        // Create and submit delete form using fetch
        fetch(`/personal-admin/karyawan/${currentViewKaryawanId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.ok) {
                // Redirect or reload on success
                window.location.href = '/personal-admin/dashboard';
            }
        })
        .catch(error => {
            console.error('Error deleting karyawan:', error);
            alert('Gagal menonaktifkan karyawan');
        });
    }
}

// ── KARYAWAN CRUD ─────────────────────────────────────────
function editKaryawan(id) {
    editingKaryawan = id;
    switchTabById('karyawan-edit');
    
    // Show loading state
    const form = document.querySelector('#formEditKaryawan');
    if (form) {
        // Disable form while loading, but remember which fields were originally disabled
        const inputs = form.querySelectorAll('input, select, textarea');
        const originallyDisabled = new Map();
        inputs.forEach(input => {
            originallyDisabled.set(input, input.disabled);
            input.disabled = true;
        });
        
        // Store the map for later use
        form._originallyDisabled = originallyDisabled;
    }
    
    // Fetch karyawan data via AJAX
    fetch(`/personal-admin/karyawan/${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(result => {
            console.log('Fetched karyawan data:', result); // Debug log
            const karyawan = result.data;
            
            if (form) {
                form.action = `/personal-admin/karyawan/${id}`;
                
                // Restore original disabled state for all fields
                const originallyDisabled = form._originallyDisabled;
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = originallyDisabled.get(input) || false;
                });
                
                // Populate identity fields (editable)
                const nipInput = form.querySelector('input[name="nip"]');
                if (nipInput) nipInput.value = karyawan.nip || '';
                
                const namaInput = form.querySelector('input[name="nama_karyawan"]');
                if (namaInput) namaInput.value = karyawan.nama_karyawan || '';
                
                const nikInput = form.querySelector('input[name="nik"]');
                if (nikInput) nikInput.value = karyawan.nik || '';
                
                const tanggalLahirInput = form.querySelector('input[name="tanggal_lahir"]');
                if (tanggalLahirInput) {
                    tanggalLahirInput.value = karyawan.tanggal_lahir || '';
                    console.log('Set tanggal_lahir to:', karyawan.tanggal_lahir); // Debug log
                }
                
                const jenisKelaminSelect = form.querySelector('select[name="jenis_kelamin"]');
                if (jenisKelaminSelect) jenisKelaminSelect.value = karyawan.jenis_kelamin || '';
                
                const teleponInput = form.querySelector('input[name="nomor_telepon"]');
                if (teleponInput) teleponInput.value = karyawan.nomor_telepon || '';
                
                const emailInput = form.querySelector('input[name="email"]');
                if (emailInput) emailInput.value = karyawan.email || '';
                
                const alamatTextarea = form.querySelector('textarea[name="alamat"]');
                if (alamatTextarea) alamatTextarea.value = karyawan.alamat || '';
                
                // Populate kepegawaian fields (these should remain disabled as per the HTML)
                const golonganSelect = form.querySelector('select[name="id_golongan"]');
                if (golonganSelect) golonganSelect.value = karyawan.id_golongan || '';
                
                const jabatanInput = form.querySelector('input[name="jabatan"]');
                if (jabatanInput) jabatanInput.value = karyawan.jabatan || '';
                
                const unitSelect = form.querySelector('select[name="id_unit"]');
                if (unitSelect) unitSelect.value = karyawan.id_unit || '';
                
                const statusKawinSelect = form.querySelector('select[name="id_status_kawin"]');
                if (statusKawinSelect) statusKawinSelect.value = karyawan.id_status_kawin || '';
                
                const statusKaryawanSelect = form.querySelector('select[name="id_status_karyawan"]');
                if (statusKaryawanSelect) statusKaryawanSelect.value = karyawan.id_status_karyawan || '';
                
                const tanggalMasukInput = form.querySelector('input[name="tanggal_masuk"]');
                if (tanggalMasukInput) tanggalMasukInput.value = karyawan.tanggal_masuk || '';
                
                const isActiveSelect = form.querySelector('select[name="is_active"]');
                if (isActiveSelect) isActiveSelect.value = karyawan.is_active ? '1' : '0';
            }
        })
        .catch(error => {
            console.error('Error fetching karyawan data:', error);
            alert('Gagal mengambil data karyawan: ' + error.message);
            
            // Restore original disabled state on error
            if (form && form._originallyDisabled) {
                const originallyDisabled = form._originallyDisabled;
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = originallyDisabled.get(input) || false;
                });
            }
        });
}

function resetEditForm() {
    editingKaryawan = null;
    const form = document.querySelector('#formEditKaryawan');
    if (form) {
        form.reset();
        
        // Reset any stored disabled state
        if (form._originallyDisabled) {
            delete form._originallyDisabled;
        }
        
        switchTabById('karyawan-list');
    }
}

// Debug function to check field states
function debugFormFields() {
    const form = document.querySelector('#formEditKaryawan');
    if (!form) {
        console.log('Form not found');
        return;
    }
    
    console.log('=== FORM FIELD STATES ===');
    
    // Identity fields (should be editable)
    const identityFields = [
        'nip', 'nama_karyawan', 'nik', 'tanggal_lahir', 
        'jenis_kelamin', 'nomor_telepon', 'email', 'alamat'
    ];
    
    console.log('Identity Fields (should be editable):');
    identityFields.forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            console.log(`- ${fieldName}: disabled=${field.disabled}, value="${field.value}"`);
        } else {
            console.log(`- ${fieldName}: NOT FOUND`);
        }
    });
    
    // Kepegawaian fields (should be disabled)
    const kepegawaianFields = [
        'id_golongan', 'jabatan', 'id_unit', 'id_status_kawin', 
        'id_status_karyawan', 'tanggal_masuk', 'is_active'
    ];
    
    console.log('Kepegawaian Fields (should be disabled):');
    kepegawaianFields.forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            console.log(`- ${fieldName}: disabled=${field.disabled}, value="${field.value}"`);
        } else {
            console.log(`- ${fieldName}: NOT FOUND`);
        }
    });
    
    console.log('=== END FORM FIELD STATES ===');
}

// ── RESET TAMBAH FORM ──────────────────────────────────────
function resetTambahForm() {
    const form = document.querySelector('#karyawan-tambah form');
    if (form) {
        form.reset();
        switchTabById('karyawan-list');
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

// ── RIWAYAT JABATAN / HISTORY DATA KARYAWAN ────────────────
function onKaryawanSelected(selectElement) {
    const karyawanId = selectElement.value;
    
    if (!karyawanId) {
        // Hide compare view and buttons if no karyawan selected
        document.getElementById('containerCompareView').style.display = 'none';
        document.getElementById('containerFormButtons').style.display = 'none';
        document.getElementById('dividerKaryawanSelected').style.display = 'none';
        return;
    }

    // Show the divider and containers
    document.getElementById('dividerKaryawanSelected').style.display = 'block';
    
    // Fetch employee current data
    fetch(`/personal-admin/riwayat/karyawan/${karyawanId}/data`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(result => {
            if (!result.data) {
                throw new Error('Data tidak ditemukan dalam response');
            }
            
            const karyawan = result.data;
            
            // Store current data for later use
            window.currentKaryawanData = karyawan;
            
            // Populate current data (left panel)
            const currentDataContainer = document.getElementById('containerCurrentData');
            currentDataContainer.innerHTML = `
                <div class="col-12 mb-2"><strong>${karyawan.nip} - ${karyawan.nama_karyawan}</strong></div>
                <div class="col-6"><label style="font-weight: 600; font-size: 0.75rem;">Jabatan:</label><div style="font-size: 0.8rem;">${karyawan.jabatan || '-'}</div></div>
                <div class="col-6"><label style="font-weight: 600; font-size: 0.75rem;">Golongan:</label><div style="font-size: 0.8rem;">${karyawan.golongan_nama || '-'}</div></div>
                <div class="col-6"><label style="font-weight: 600; font-size: 0.75rem;">Unit/PT:</label><div style="font-size: 0.8rem;">${karyawan.unit_nama || '-'}</div></div>
                <div class="col-6"><label style="font-weight: 600; font-size: 0.75rem;">Status Karyawan:</label><div style="font-size: 0.8rem;">${karyawan.status_karyawan_nama || '-'}</div></div>
                <div class="col-6"><label style="font-weight: 600; font-size: 0.75rem;">Status Kawin:</label><div style="font-size: 0.8rem;">${karyawan.status_kawin_nama || '-'}</div></div>
                <div class="col-6"><label style="font-weight: 600; font-size: 0.75rem;">Status:</label><div style="font-size: 0.8rem;">${karyawan.is_active ? 'Aktif' : 'Nonaktif'}</div></div>
            `;
            
            // Populate hidden fields for old data (don't pre-fill the form fields)
            document.getElementById('inputJabatanLama').value = karyawan.jabatan || '';
            document.getElementById('inputGolonganLama').value = karyawan.id_golongan || '';
            document.getElementById('inputUnitLama').value = karyawan.id_unit || '';
            document.getElementById('inputStatusKaryawanLama').value = karyawan.id_status_karyawan || '';
            
            // Clear all proposed data fields (they should be empty for user to fill)
            document.querySelector('select[name="jenis_perubahan"]').value = '';
            document.querySelector('select[name="detail_perubahan"]').innerHTML = '<option value="">-- Pilih Tipe Perubahan Terlebih Dahulu --</option>';
            document.querySelector('input[name="jabatan_baru"]').value = '';
            document.querySelector('select[name="golongan_baru"]').value = '';
            document.querySelector('select[name="unit_baru"]').value = '';
            document.querySelector('select[name="status_karyawan_baru"]').value = '';
            document.querySelector('input[name="tgl_efektif"]').value = '';
            document.querySelector('input[name="nomor_sk"]').value = '';
            document.querySelector('textarea[name="catatan"]').value = '';
            
            // Show compare view and buttons
            document.getElementById('containerCompareView').style.display = 'flex';
            document.getElementById('containerFormButtons').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error fetching karyawan data:', error);
            alert('Gagal mengambil data karyawan: ' + error.message);
            
            // Hide containers on error
            document.getElementById('containerCompareView').style.display = 'none';
            document.getElementById('containerFormButtons').style.display = 'none';
            document.getElementById('dividerKaryawanSelected').style.display = 'none';
        });
}

function resetRiwayatForm() {
    const form = document.getElementById('formRiwayat');
    if (form) {
        form.reset();
        
        // Reset the detail perubahan dropdown
        const detailSelect = document.getElementById('selectDetailPerubahan');
        if (detailSelect) {
            detailSelect.innerHTML = '<option value="">-- Pilih Tipe Perubahan Terlebih Dahulu --</option>';
        }
        
        // Hide containers
        document.getElementById('containerCompareView').style.display = 'none';
        document.getElementById('containerFormButtons').style.display = 'none';
        document.getElementById('dividerKaryawanSelected').style.display = 'none';
        
        // Hide error/success messages
        document.getElementById('riwayat-errors').style.display = 'none';
        document.getElementById('riwayat-success').style.display = 'none';
    }
}

function viewRiwayat(id) {
    // Show the view tab
    const viewTab = document.getElementById('riwayat-view-tab');
    if (viewTab) {
        viewTab.style.display = 'block';
        switchTab(viewTab, 'riwayat-view');
    }
    
    // Fetch riwayat data and show comparison
    fetch(`/personal-admin/riwayat/${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(result => {
            if (!result.data) {
                throw new Error('Data tidak ditemukan dalam response');
            }
            
            const riwayat = result.data;
            
            // Create view content in the riwayat-view tab pane
            const viewPane = document.getElementById('riwayat-view');
            if (!viewPane) {
                // Create the view tab pane if it doesn't exist
                const tabContainer = document.querySelector('#modalRiwayat .modal-body');
                const newViewPane = document.createElement('div');
                newViewPane.id = 'riwayat-view';
                newViewPane.className = 'tab-pane';
                tabContainer.appendChild(newViewPane);
            }
            
            const content = document.getElementById('riwayat-view');
            if (content) {
                content.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="mb-0">Detail Riwayat Perubahan</h6>
                        <button class="btn-outline-hrms" onclick="backToRiwayatList()">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </button>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-3">
                            <label class="form-label">NIP - Nama</label>
                            <div class="form-control-plaintext">${riwayat.nip || '-'} - ${riwayat.nama || '-'}</div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Tipe Perubahan</label>
                            <div class="form-control-plaintext">
                                <span class="badge-aktif" style="background:rgba(0,146,180,0.1);color:var(--primary);border-color:rgba(0,146,180,0.25);">
                                    ${riwayat.tipe_perubahan || riwayat.jenis_perubahan || '-'}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Detail Perubahan</label>
                            <div class="form-control-plaintext">${riwayat.detail_perubahan || '-'}</div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Tanggal Efektif</label>
                            <div class="form-control-plaintext">${riwayat.tanggal_efektif ? formatDate(riwayat.tanggal_efektif) : (riwayat.tgl_efektif ? formatDate(riwayat.tgl_efektif) : '-')}</div>
                        </div>
                    </div>

                    <div class="modal-section-title">Perbandingan Data</div>
                    <div class="row g-4">
                        <!-- Current Status (Before) -->
                        <div class="col-lg-6">
                            <div style="background: #f8fbfc; padding: 1.5rem; border-radius: 12px; border: 1.5px solid var(--primary-light);">
                                <h6 style="font-weight: 700; color: var(--primary); margin-bottom: 1rem;">
                                    <i class="bi bi-arrow-left-circle me-2"></i> Current Status (Before)
                                </h6>
                                <div class="row g-2" style="font-size: 0.9rem;">
                                    ${riwayat.current_data ? renderDetailComparison(riwayat.current_data) : '<div class="col-12 text-muted">Data tidak tersedia</div>'}
                                </div>
                            </div>
                        </div>

                        <!-- Proposed Status (After) -->
                        <div class="col-lg-6">
                            <div style="background: #f5f9fc; padding: 1.5rem; border-radius: 12px; border: 1.5px solid var(--primary-light);">
                                <h6 style="font-weight: 700; color: var(--primary); margin-bottom: 1rem;">
                                    <i class="bi bi-arrow-right-circle me-2"></i> Proposed Status (After)
                                </h6>
                                <div class="row g-2" style="font-size: 0.9rem;">
                                    ${riwayat.proposed_data ? renderDetailComparison(riwayat.proposed_data) : '<div class="col-12 text-muted">Data tidak tersedia</div>'}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ${riwayat.nomor_sk || riwayat.catatan ? `
                    <div class="modal-section-title">Informasi Tambahan</div>
                    <div class="row g-3">
                        ${riwayat.nomor_sk ? `
                        <div class="col-sm-6">
                            <label class="form-label">No. SK</label>
                            <div class="form-control-plaintext">${riwayat.nomor_sk}</div>
                        </div>
                        ` : ''}
                        ${riwayat.catatan ? `
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <div class="form-control-plaintext">${riwayat.catatan}</div>
                        </div>
                        ` : ''}
                    </div>
                    ` : ''}
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching riwayat data:', error);
            alert('Gagal mengambil data riwayat: ' + error.message);
        });
}

// ── NEW RIWAYAT FILTER FUNCTIONS ──────────────────────────
function filterRiwayatTable(input) {
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#tblRiwayat tbody tr');
    
    rows.forEach(row => {
        if (!filter) {
            row.style.display = '';
        } else {
            // Search in all cells: NIP, Nama, Tipe Perubahan, Detail Perubahan
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(filter) ? '' : 'none';
        }
    });
}

function filterRiwayatByKaryawan(selectElement) {
    const nip = selectElement.value;
    const rows = document.querySelectorAll('#tblRiwayat tbody tr');
    
    rows.forEach(row => {
        if (!nip) {
            row.style.display = '';
        } else {
            const rowNip = row.getAttribute('data-nip') || '';
            row.style.display = rowNip === nip ? '' : 'none';
        }
    });
}

// ── NEW RIWAYAT FUNCTIONS ─────────────────────────────────────
function populateProposedDataFields(karyawan) {
    const proposedContainer = document.getElementById('proposedDataFields');
    
    proposedContainer.innerHTML = `
        <div class="col-12">
            <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Jabatan Baru <span class="text-danger">*</span></label>
            <input type="text" name="jabatan_baru" class="form-control form-control-sm" value="${karyawan.jabatan || ''}" required>
        </div>
        <div class="col-12">
            <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Golongan Baru <span class="text-danger">*</span></label>
            <select name="golongan_baru" class="form-select form-select-sm" required>
                <option value="">-- Pilih --</option>
                @foreach($golongans as $gol)
                    <option value="{{ $gol->id }}" ${karyawan.id_golongan == {{ $gol->id }} ? 'selected' : ''}>{{ $gol->kode_golongan }} — {{ $gol->nama_golongan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Unit / PT Baru <span class="text-danger">*</span></label>
            <select name="unit_baru" class="form-select form-select-sm" required>
                <option value="">-- Pilih --</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" ${karyawan.id_unit == {{ $unit->id }} ? 'selected' : ''}>{{ $unit->nama_pt }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label" style="font-size: 0.8rem; font-weight: 700;">Status Karyawan Baru</label>
            <select name="status_karyawan_baru" class="form-select form-select-sm">
                <option value="">-- Pilih --</option>
                @foreach($statusKaryawans as $status)
                    <option value="{{ $status->id }}" ${karyawan.id_status_karyawan == {{ $status->id }} ? 'selected' : ''}>{{ $status->nama_status }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Hidden fields for old data -->
        <input type="hidden" name="nip" value="${karyawan.nip}">
        <input type="hidden" name="nama" value="${karyawan.nama_karyawan}">
        <input type="hidden" name="jabatan_lama" value="${karyawan.jabatan || ''}">
        <input type="hidden" name="golongan_lama" value="${karyawan.id_golongan || ''}">
        <input type="hidden" name="unit_lama" value="${karyawan.id_unit || ''}">
        <input type="hidden" name="status_karyawan_lama" value="${karyawan.id_status_karyawan || ''}">
    `;
}

function filterRiwayatTable(input) {
    const searchTerm = input.value.toLowerCase();
    const rows = document.querySelectorAll('#tblRiwayat tbody tr');
    
    rows.forEach(row => {
        const nipNama = row.cells[0]?.textContent.toLowerCase() || '';
        const tipePerubahan = row.cells[1]?.textContent.toLowerCase() || '';
        const detailPerubahan = row.cells[2]?.textContent.toLowerCase() || '';
        
        const matches = nipNama.includes(searchTerm) || 
                       tipePerubahan.includes(searchTerm) || 
                       detailPerubahan.includes(searchTerm);
        
        row.style.display = matches ? '' : 'none';
    });
}

// Helper function to render data comparison
function renderDataComparison(data) {
    if (!data || typeof data !== 'object') {
        return '<div class="col-12 text-muted">Data tidak tersedia</div>';
    }
    
    let html = '';
    
    // Basic employee info with proper field handling
    const nip = data.nip || '-';
    const nama = data.nama || data.nama_karyawan || '-';
    const jabatan = data.jabatan || '-';
    const golongan = data.golongan || data.golongan_nama || (data.id_golongan ? `ID: ${data.id_golongan}` : '-');
    const unit = data.unit || data.unit_nama || (data.id_unit ? `ID: ${data.id_unit}` : '-');
    const statusKaryawan = data.status_karyawan || (data.id_status_karyawan ? `ID: ${data.id_status_karyawan}` : '-');
    const tglMasuk = data.tanggal_masuk ? formatDate(data.tanggal_masuk) : '-';
    const status = data.is_active !== undefined ? (data.is_active ? 'Aktif' : 'Nonaktif') : '-';
    
    html += `
        <div class="col-12">
            <div class="row g-2">
                <div class="col-6">
                    <small class="text-muted d-block">NIP</small>
                    <strong>${nip}</strong>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Nama</small>
                    <strong>${nama}</strong>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Jabatan</small>
                    <strong>${jabatan}</strong>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Golongan</small>
                    <strong>${golongan}</strong>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Unit</small>
                    <strong>${unit}</strong>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Status</small>
                    <strong>${status}</strong>
                </div>
            </div>
        </div>
    `;
    
    return html || '<div class="col-12 text-muted">Data tidak tersedia</div>';
}

// Helper function to render detail compaison display in nice format
function renderDetailComparison(data) {
    if (!data || typeof data !== 'object') {
        return '<div class="col-12 text-muted"><small>Data tidak tersedia</small></div>';
    }
    
    let html = '';
    
    const getDisplayValue = (val) => val && val !== '-' && val !== 'N/A' ? val : '-';
    
    const fields = [
        { label: 'NIP', value: getDisplayValue(data.nip) },
        { label: 'Nama', value: getDisplayValue(data.nama || data.nama_karyawan) },
        { label: 'Jabatan', value: getDisplayValue(data.jabatan) },
        { label: 'Golongan', value: getDisplayValue(data.golongan || data.golongan_nama) },
        { label: 'Unit', value: getDisplayValue(data.unit || data.unit_nama) },
        { label: 'Status', value: getDisplayValue(data.status_karyawan) },
    ];
    
    fields.forEach(field => {
        html += `
            <div class="col-12 mb-3">
                <label class="form-label" style="font-size: 0.8rem; font-weight: 700; margin-bottom: 0.5rem;">
                    ${field.label}
                </label>
                <div class="form-control form-control-sm" style="background: white; border: 1px solid #dee2e6; color: #495057; font-weight: 500;">
                    ${field.value}
                </div>
            </div>
        `;
    });
    
    return html || '<div class="col-12 text-muted"><small>Data tidak tersedia</small></div>';
}

// Old comparison table function (keeping for reference, not used anymore)
function renderComparisonTable(currentData, proposedData) {
    if (!currentData || !proposedData) {
        return '<tr><td colspan="3" class="text-center text-muted">Data tidak tersedia</td></tr>';
    }
    
    const fields = [
        { label: 'NIP', key: 'nip' },
        { label: 'Nama', key: 'nama' },
        { label: 'Jabatan', key: 'jabatan' },
        { label: 'Golongan', key: 'golongan' },
        { label: 'Unit', key: 'unit' },
        { label: 'Status', key: 'status_karyawan' },
    ];
    
    let html = '';
    
    fields.forEach(field => {
        const currentValue = currentData[field.key] || '-';
        const proposedValue = proposedData[field.key] || '-';
        const isChanged = currentValue !== proposedValue;
        
        const rowBg = isChanged ? 'background: #fff3cd;' : '';
        const cellStyle = isChanged ? 'color: var(--primary); font-weight: 600;' : '';
        
        html += `
            <tr style="${rowBg}">
                <td style="${cellStyle}">${field.label}</td>
                <td style="${cellStyle}">${currentValue}</td>
                <td style="${cellStyle}">${proposedValue}</td>
            </tr>
        `;
    });
    
    return html;
}

// Helper function to go back to riwayat list
function backToRiwayatList() {
    // Hide view tab
    const viewTab = document.getElementById('riwayat-view-tab');
    if (viewTab) {
        viewTab.style.display = 'none';
    }
    
    // Switch back to list tab
    switchTabById('riwayat-list');
}

// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return '-';
    
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit', 
            year: 'numeric'
        });
    } catch (e) {
        return dateString;
    }
}

// Function to update detail perubahan based on tipe perubahan
function updateDetailPerubahan(selectElement) {
    const tipePerubahan = selectElement.value;
    const detailSelect = document.getElementById('selectDetailPerubahan');
    
    // Clear existing options
    detailSelect.innerHTML = '<option value="">-- Pilih Detail Perubahan --</option>';
    
    // Define the action type to reason mapping (SAP Standard)
    const actionReasons = {
        'promotion': [
            'Job Grade Promotion',
            'Position Promotion'
        ],
        'demotion': [
            'Poor Performance'
        ],
        'transfer': [
            'End of Covering Peer Position',
            'Intra-Unit Transfer',
            'Organization Restructuring',
            'Start Covering Peer Position',
            'Start of Intl. Assignment',
            'Transfer between Unit',
            'Transfer to Other Entity'
        ],
        'termination': [
            'Cancel Join',
            'Criminal Offence',
            'Deceased',
            'Dismissal - Major Misconduct',
            'Dismissal - Minor Misconduct',
            'End of Contract',
            'Failed Probation',
            'Long Sickness',
            'Mass Termination',
            'Pension',
            'Poor Performance',
            'Resign - Back to School',
            'Resign - Career Opportunities',
            'Resign - Family',
            'Resign - Management',
            'Resign - Medical',
            'Resign - Rem & Benefits',
            'Resign - Work Arrangements',
            'Resign - Work Environment'
        ],
        'new_hire': [
            'Contract Position',
            'Pensioner',
            'Permanent Position'
        ],
        'contract_extension': [
            'Contract Position'
        ],
        'actual_conversion': [
            'Actual Conversion'
        ],
        'change_of_status': [
            'Change of Employment Type'
        ],
        'pass_probation': [
            'Permanent Position'
        ],
        'service_extension': [
            'Permanent Position'
        ]
    };
    
    // Populate detail options based on selected tipe
    if (actionReasons[tipePerubahan]) {
        actionReasons[tipePerubahan].forEach(reason => {
            const option = document.createElement('option');
            option.value = reason;
            option.textContent = reason;
            detailSelect.appendChild(option);
        });
    }
}

// Handle tipe_perubahan change to populate detail_perubahan options
document.addEventListener('DOMContentLoaded', function() {

    // ── FIX: Reset modal saat ditutup ─────────────────────
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            // Blur semua focused element
            this.querySelectorAll('*').forEach(el => { try { el.blur(); } catch(e) {} });
            
            // Reset ke tab pertama
            const tabs = this.querySelectorAll('.modal-tab');
            const panes = this.querySelectorAll('.tab-pane');
            tabs.forEach(t => t.classList.remove('active'));
            panes.forEach(p => p.classList.remove('active'));
            if (tabs[0]) tabs[0].classList.add('active');
            if (panes[0]) panes[0].classList.add('active');
            
            // Sembunyikan edit tab karyawan
            const editTab = document.getElementById('btnEditTab');
            if (editTab) editTab.style.display = 'none';
            
            // Sembunyikan view tab riwayat
            const riwayatViewTab = document.getElementById('riwayat-view-tab');
            if (riwayatViewTab) riwayatViewTab.style.display = 'none';
            
            // Reset riwayat form if this is the riwayat modal
            if (this.id === 'modalRiwayat') {
                resetRiwayatForm();
            }
        });
    });

    // Handle tipe_perubahan change
    const tipePerubahanSelect = document.querySelector('select[name="jenis_perubahan"]');
    
    if (tipePerubahanSelect) {
        tipePerubahanSelect.addEventListener('change', function() {
            updateDetailPerubahan(this);
        });
    }

    // Handle riwayat form submission with AJAX
    const riwayatForm = document.getElementById('formRiwayat');
    if (riwayatForm) {
        riwayatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Hide previous errors/success messages
            document.getElementById('riwayat-errors').style.display = 'none';
            document.getElementById('riwayat-success').style.display = 'none';
            
            // Get form data
            const formData = new FormData(this);
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            // Submit form via AJAX
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.text().then(text => {
                        // Check if response is JSON (error) or HTML (success redirect)
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            // HTML response means success, reload page
                            window.location.reload();
                            return null;
                        }
                    });
                } else {
                    return response.json();
                }
            })
            .then(result => {
                if (result && result.errors) {
                    // Show validation errors
                    const errorList = document.getElementById('riwayat-error-list');
                    errorList.innerHTML = '';
                    
                    Object.values(result.errors).forEach(errorArray => {
                        errorArray.forEach(error => {
                            const li = document.createElement('li');
                            li.textContent = error;
                            errorList.appendChild(li);
                        });
                    });
                    
                    document.getElementById('riwayat-errors').style.display = 'block';
                    
                    // Scroll to top of modal to show errors
                    document.querySelector('#modalRiwayat .modal-body').scrollTop = 0;
                } else if (result && result.message) {
                    // Show success message
                    document.getElementById('riwayat-success-message').textContent = result.message;
                    document.getElementById('riwayat-success').style.display = 'block';
                    
                    // Reset form and switch to list tab
                    resetRiwayatForm();
                    switchTabById('riwayat-list');
                    
                    // Reload page after short delay to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            })
            .catch(error => {
                console.error('Error submitting riwayat form:', error);
                
                // Show generic error
                const errorList = document.getElementById('riwayat-error-list');
                errorList.innerHTML = '<li>Terjadi kesalahan saat menyimpan data. Silakan coba lagi.</li>';
                document.getElementById('riwayat-errors').style.display = 'block';
                
                // Scroll to top of modal to show errors
                document.querySelector('#modalRiwayat .modal-body').scrollTop = 0;
            });
        });
    }

    // Handle karyawan tambah form submission with AJAX
    const karyawanTambahForm = document.querySelector('#karyawan-tambah form');
    if (karyawanTambahForm) {
        karyawanTambahForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            // Submit form via AJAX
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.text().then(text => {
                        // Check if response is JSON (error) or HTML (success redirect)
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            // HTML response means success, reload page
                            window.location.reload();
                            return null;
                        }
                    });
                } else {
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            // If not JSON, create a generic error response
                            return {
                                errors: {
                                    general: ['Server error: ' + response.status + ' - ' + text.substring(0, 100)]
                                }
                            };
                        }
                    });
                }
            })
            .then(result => {
                if (result && result.errors) {
                    // Show validation errors in the main modal error area
                    let errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px;border:1.5px solid #dc3545;">';
                    errorHtml += '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Terdapat kesalahan:</strong>';
                    errorHtml += '<ul class="mb-0 mt-2">';
                    
                    Object.values(result.errors).forEach(errorArray => {
                        errorArray.forEach(error => {
                            errorHtml += `<li>${error}</li>`;
                        });
                    });
                    
                    errorHtml += '</ul>';
                    errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    errorHtml += '</div>';
                    
                    // Insert error message at the top of the modal body
                    const modalBody = document.querySelector('#modalKaryawan .modal-body');
                    const existingAlert = modalBody.querySelector('.alert');
                    if (existingAlert) {
                        existingAlert.remove();
                    }
                    modalBody.insertAdjacentHTML('afterbegin', errorHtml);
                    
                    // Scroll to top of modal to show errors
                    modalBody.scrollTop = 0;
                } else if (result && result.message) {
                    // Show success and reload
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error submitting karyawan form:', error);
                
                // Show generic error
                let errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px;border:1.5px solid #dc3545;">';
                errorHtml += '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Terjadi kesalahan saat menyimpan data. Silakan coba lagi.</strong>';
                errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                errorHtml += '</div>';
                
                // Insert error message at the top of the modal body
                const modalBody = document.querySelector('#modalKaryawan .modal-body');
                const existingAlert = modalBody.querySelector('.alert');
                if (existingAlert) {
                    existingAlert.remove();
                }
                modalBody.insertAdjacentHTML('afterbegin', errorHtml);
                
                // Scroll to top of modal to show errors
                modalBody.scrollTop = 0;
            });
        });
    }

    // Handle karyawan edit form submission with AJAX
    // AJAX form submission for edit form
    const karyawanEditForm = document.querySelector('#formEditKaryawan');
    if (karyawanEditForm) {
        karyawanEditForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Get form data
            const formData = new FormData(this);
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            console.log('Submitting edit form to:', this.action); // Debug log
            
            // Submit form via AJAX
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status); // Debug log
                if (response.ok) {
                    return response.text().then(text => {
                        // Check if response is JSON (error) or HTML (success redirect)
                        try {
                            const jsonResponse = JSON.parse(text);
                            console.log('JSON response:', jsonResponse); // Debug log
                            return jsonResponse;
                        } catch (e) {
                            // HTML response means success, reload page
                            console.log('HTML response received, reloading page'); // Debug log
                            window.location.reload();
                            return null;
                        }
                    });
                } else {
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            // If not JSON, create a generic error response
                            return {
                                errors: {
                                    general: ['Server error: ' + response.status + ' - ' + text.substring(0, 100)]
                                }
                            };
                        }
                    });
                }
            })
            .then(result => {
                // Reset button state
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                
                if (result && result.errors) {
                    console.log('Validation errors:', result.errors); // Debug log
                    // Show validation errors in the main modal error area
                    let errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px;border:1.5px solid #dc3545;">';
                    errorHtml += '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Terdapat kesalahan:</strong>';
                    errorHtml += '<ul class="mb-0 mt-2">';
                    
                    Object.values(result.errors).forEach(errorArray => {
                        errorArray.forEach(error => {
                            errorHtml += `<li>${error}</li>`;
                        });
                    });
                    
                    errorHtml += '</ul>';
                    errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    errorHtml += '</div>';
                    
                    // Insert error message at the top of the modal body
                    const modalBody = document.querySelector('#modalKaryawan .modal-body');
                    const existingAlert = modalBody.querySelector('.alert');
                    if (existingAlert) {
                        existingAlert.remove();
                    }
                    modalBody.insertAdjacentHTML('afterbegin', errorHtml);
                    
                    // Scroll to top of modal to show errors
                    modalBody.scrollTop = 0;
                } else if (result && result.success) {
                    console.log('Success response:', result.message); // Debug log
                    // Show success message and reload
                    alert(result.message || 'Data berhasil diupdate');
                    window.location.reload();
                } else if (result && result.message) {
                    // Show success and reload
                    console.log('Success message:', result.message); // Debug log
                    alert(result.message);
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error submitting karyawan edit form:', error);
                
                // Reset button state
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                
                // Show generic error
                let errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:12px;border:1.5px solid #dc3545;">';
                errorHtml += '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Terjadi kesalahan saat menyimpan data. Silakan coba lagi.</strong>';
                errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                errorHtml += '</div>';
                
                // Insert error message at the top of the modal body
                const modalBody = document.querySelector('#modalKaryawan .modal-body');
                const existingAlert = modalBody.querySelector('.alert');
                if (existingAlert) {
                    existingAlert.remove();
                }
                modalBody.insertAdjacentHTML('afterbegin', errorHtml);
                
                // Scroll to top of modal to show errors
                modalBody.scrollTop = 0;
            });
        });
    }
});

// ══════════════════════════════════════════════════════
//  EXPORT FUNCTIONS
// ══════════════════════════════════════════════════════

/**
 * Export Riwayat Jabatan
 * Export all or per employee based on filter
 */
function exportRiwayat() {
    const selectFilter = document.getElementById('selectFilterKaryawan');
    const selectedOption = selectFilter.options[selectFilter.selectedIndex];
    const karyawanId = selectedOption.getAttribute('data-id');
    
    let url = '{{ route("personal-admin.export.riwayat") }}';
    
    if (karyawanId) {
        url += '?id_karyawan=' + karyawanId;
    }
    
    window.location.href = url;
}

/**
 * Export Data Keluarga untuk karyawan yang sedang dilihat
 */
function exportKeluargaKaryawan() {
    if (!currentKaryawanIdForKeluarga) {
        alert('Tidak ada data karyawan yang dipilih');
        return;
    }
    
    const url = '{{ route("personal-admin.export.keluarga") }}?id_karyawan=' + currentKaryawanIdForKeluarga;
    window.location.href = url;
}
</script>
</body>
</html>


<script>
// ══════════════════════════════════════════════════════════
//  DATA KELUARGA FUNCTIONS
// ══════════════════════════════════════════════════════════

let currentKaryawanIdForKeluarga = null;

// Load keluarga data when modal opens
document.getElementById('modalKeluarga')?.addEventListener('shown.bs.modal', function() {
    loadKeluargaData();
});

function loadKeluargaData() {
    fetch('/personal-admin/keluarga')
        .then(response => response.json())
        .then(result => {
            const tbody = document.getElementById('keluargaTableBody');
            if (!result.data || result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada data</td></tr>';
                return;
            }

            tbody.innerHTML = result.data.map(k => `
                <tr>
                    <td class="text-muted" style="font-size:.75rem;font-weight:600;">${k.nip}</td>
                    <td style="font-weight:600;">${k.nama}</td>
                    <td>${k.jabatan}</td>
                    <td><span class="badge-aktif" style="background:rgba(0,146,180,0.1);color:var(--primary);border-color:rgba(0,146,180,0.25);">${k.golongan}</span></td>
                    <td>${k.cost_center}</td>
                    <td>${k.unit}</td>
                    <td><span class="badge-permanent">${k.status}</span></td>
                    <td><strong style="color:var(--primary);">${k.jumlah_keluarga}</strong></td>
                    <td>
                        <button class="btn-sm-action btn-view" onclick="viewKeluargaData(${k.id})" title="Lihat detail">
                            <i class="bi bi-eye"></i> View
                        </button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading keluarga data:', error);
            document.getElementById('keluargaTableBody').innerHTML = 
                '<tr><td colspan="9" class="text-center py-4 text-danger">Error loading data</td></tr>';
        });
}

function viewKeluargaData(karyawanId) {
    currentKaryawanIdForKeluarga = karyawanId;
    
    fetch(`/personal-admin/keluarga/${karyawanId}/view`)
        .then(response => response.json())
        .then(result => {
            const data = result.data;
            const karyawan = data.karyawan;
            const familyMembers = data.family_members;

            const content = `
                <div class="modal-section-title">Informasi Karyawan</div>
                <div class="row g-3 mb-4">
                    <div class="col-sm-3">
                        <label class="form-label">NIP</label>
                        <div class="form-control-plaintext">${karyawan.nip}</div>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Nama</label>
                        <div class="form-control-plaintext">${karyawan.nama}</div>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Jabatan</label>
                        <div class="form-control-plaintext">${karyawan.jabatan}</div>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Unit</label>
                        <div class="form-control-plaintext">${karyawan.unit}</div>
                    </div>
                </div>

                <div class="modal-section-title">Anggota Keluarga</div>
                <div class="table-responsive">
                    <table class="table table-hrms table-borderless">
                        <thead>
                            <tr>
                                <th>Status Keluarga</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Tanggal Lahir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${familyMembers.length > 0 ? familyMembers.map(member => `
                                <tr>
                                    <td><span class="badge-aktif" style="background:rgba(0,146,180,0.1);color:var(--primary);">${member.status_keluarga_label}</span></td>
                                    <td>${member.nik}</td>
                                    <td style="font-weight:600;">${member.nama}</td>
                                    <td>${member.tanggal_lahir_display}</td>
                                    <td><span class="${member.is_active ? 'badge-aktif' : 'badge-nonaktif'}">${member.is_active_label}</span></td>
                                </tr>
                            `).join('') : '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada anggota keluarga</td></tr>'}
                        </tbody>
                    </table>
                </div>
            `;

            document.getElementById('viewKeluargaContent').innerHTML = content;
            
            // Show popup
            const popup = new bootstrap.Modal(document.getElementById('popupViewKeluarga'));
            popup.show();
        })
        .catch(error => {
            console.error('Error viewing keluarga data:', error);
            alert('Gagal memuat data keluarga');
        });
}

function openEditKeluargaPopup() {
    if (!currentKaryawanIdForKeluarga) {
        alert('Karyawan ID tidak ditemukan');
        return;
    }

    // Hide view popup
    bootstrap.Modal.getInstance(document.getElementById('popupViewKeluarga'))?.hide();

    // Load edit data
    fetch(`/personal-admin/keluarga/${currentKaryawanIdForKeluarga}/edit`)
        .then(response => response.json())
        .then(result => {
            const data = result.data;
            document.getElementById('editKaryawanId').value = data.karyawan_id;

            let formHtml = `
                <div class="alert alert-info" style="border-radius:12px;border:1.5px solid #0dcaf0;font-size:0.875rem;">
                    <i class="bi bi-person-fill me-2"></i>
                    <strong>${data.karyawan_nip} - ${data.karyawan_nama}</strong>
                </div>
            `;

            if (data.family_members.length === 0) {
                formHtml += `
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.4;"></i>
                        <p>Belum ada anggota keluarga. Tambahkan melalui menu "Tambah Data Keluarga".</p>
                    </div>
                `;
            } else {
                data.family_members.forEach((member, index) => {
                    formHtml += `
                        <div class="modal-section-title">Anggota Keluarga ${index + 1}</div>
                        <div class="row g-3 mb-3">
                            <input type="hidden" name="family_members[${index}][id]" value="${member.id}">
                            <div class="col-sm-3">
                                <label class="form-label">Status Keluarga <span class="text-danger">*</span></label>
                                <select name="family_members[${index}][status_keluarga]" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="spouse" ${member.status_keluarga === 'spouse' ? 'selected' : ''}>Suami/Istri</option>
                                    <option value="child" ${member.status_keluarga === 'child' ? 'selected' : ''}>Anak</option>
                                    <option value="father" ${member.status_keluarga === 'father' ? 'selected' : ''}>Ayah</option>
                                    <option value="mother" ${member.status_keluarga === 'mother' ? 'selected' : ''}>Ibu</option>
                                    <option value="in-law" ${member.status_keluarga === 'in-law' ? 'selected' : ''}>Mertua</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">NIK</label>
                                <input type="text" name="family_members[${index}][nik]" class="form-control" value="${member.nik || ''}" maxlength="16">
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="family_members[${index}][nama]" class="form-control" value="${member.nama}" required>
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="family_members[${index}][tanggal_lahir]" class="form-control" value="${member.tanggal_lahir || ''}">
                            </div>
                            <div class="col-sm-1">
                                <label class="form-label">Status Aktif</label>
                                <select name="family_members[${index}][is_active]" class="form-select">
                                    <option value="1" ${member.is_active ? 'selected' : ''}>Aktif</option>
                                    <option value="0" ${!member.is_active ? 'selected' : ''}>Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    `;
                });
            }

            formHtml += `
                <button type="button" class="btn-outline-hrms mt-3" onclick="addFamilyMemberEditRowPopup()">
                    <i class="bi bi-plus-lg"></i> Tambah Anggota
                </button>
            `;

            document.getElementById('editKeluargaFormContent').innerHTML = formHtml;
            
            // Show edit popup
            const popup = new bootstrap.Modal(document.getElementById('popupEditKeluarga'));
            popup.show();
        })
        .catch(error => {
            console.error('Error loading edit data:', error);
            alert('Gagal memuat data untuk edit');
        });
}

function addFamilyMemberEditRowPopup() {
    const container = document.getElementById('editKeluargaFormContent');
    const currentCount = container.querySelectorAll('[name^="family_members"]').length / 6; // 6 fields per member
    const index = currentCount;

    const newRow = `
        <div class="modal-section-title">Anggota Keluarga ${index + 1}</div>
        <div class="row g-3 mb-3">
            <input type="hidden" name="family_members[${index}][id]" value="0">
            <div class="col-sm-3">
                <label class="form-label">Status Keluarga <span class="text-danger">*</span></label>
                <select name="family_members[${index}][status_keluarga]" class="form-select" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="spouse">Suami/Istri</option>
                    <option value="child">Anak</option>
                    <option value="father">Ayah</option>
                    <option value="mother">Ibu</option>
                    <option value="in-law">Mertua</option>
                </select>
            </div>
            <div class="col-sm-3">
                <label class="form-label">NIK</label>
                <input type="text" name="family_members[${index}][nik]" class="form-control" maxlength="16">
            </div>
            <div class="col-sm-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="family_members[${index}][nama]" class="form-control" required>
            </div>
            <div class="col-sm-2">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="family_members[${index}][tanggal_lahir]" class="form-control">
            </div>
            <div class="col-sm-1">
                <label class="form-label">Status Aktif</label>
                <select name="family_members[${index}][is_active]" class="form-select">
                    <option value="1" selected>Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
        </div>
    `;

    // Insert before the "Tambah Anggota" button
    const addButton = container.querySelector('button[onclick="addFamilyMemberEditRowPopup()"]');
    addButton.insertAdjacentHTML('beforebegin', newRow);
}

function submitEditKeluargaPopup() {
    const form = document.getElementById('formEditKeluarga');
    const formData = new FormData(form);
    const karyawanId = document.getElementById('editKaryawanId').value;

    // Convert FormData to JSON
    const familyMembers = [];
    const entries = Array.from(formData.entries());
    
    // Group by index
    const grouped = {};
    entries.forEach(([key, value]) => {
        const match = key.match(/family_members\[(\d+)\]\[(\w+)\]/);
        if (match) {
            const index = match[1];
            const field = match[2];
            if (!grouped[index]) grouped[index] = {};
            grouped[index][field] = value;
        }
    });

    // Convert to array
    Object.values(grouped).forEach(member => {
        familyMembers.push({
            id: parseInt(member.id) || 0,
            status_keluarga: member.status_keluarga,
            nik: member.nik || null,
            nama: member.nama,
            tanggal_lahir: member.tanggal_lahir || null,
            is_active: member.is_active === '1',
        });
    });

    // Hide error container
    document.getElementById('editKeluargaErrorContainer').style.display = 'none';

    // Submit via AJAX
    fetch(`/personal-admin/keluarga/${karyawanId}/update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ family_members: familyMembers })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Close popup
            bootstrap.Modal.getInstance(document.getElementById('popupEditKeluarga'))?.hide();
            
            // Reload data
            loadKeluargaData();
            
            // Show success message
            alert('✓ Data keluarga berhasil diupdate');
        } else {
            // Show error
            const errorContainer = document.getElementById('editKeluargaErrorContainer');
            const errorList = document.getElementById('editKeluargaErrors');
            errorList.innerHTML = `<li>${result.message || 'Terjadi kesalahan'}</li>`;
            errorContainer.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error updating keluarga:', error);
        const errorContainer = document.getElementById('editKeluargaErrorContainer');
        const errorList = document.getElementById('editKeluargaErrors');
        errorList.innerHTML = '<li>Terjadi kesalahan saat menyimpan data</li>';
        errorContainer.style.display = 'block';
    });
}
</script>
