<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payroll Dashboard — HRMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:       #0092B4;
            --primary-dark:  #007A98;
            --primary-xdark: #005F77;
            --primary-light: #E8F7FB;
            --primary-pale:  #F2FBFD;
            --success:       #198754;
            --danger:        #dc3545;
            --warning-bg:    #FFFBF0;
            --warning-border:#F5D88A;
            --warning-text:  #7a5800;
            --border:        #E2EEF2;
            --bg:            #F4F8FA;
            --card:          #FFFFFF;
            --text:          #1A2E3B;
            --muted:         #6B8896;
            --shadow-sm:     0 2px 8px rgba(0,100,130,.07);
            --shadow-md:     0 6px 24px rgba(0,100,130,.12);
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; }

        /* ── NAVBAR ─────────────────────────────────────────── */
        .navbar-hrms {
            background: #fff; border-bottom: 1.5px solid var(--border);
            padding: 0 2rem; height: 62px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 1050;
            box-shadow: 0 2px 12px rgba(0,100,130,.06);
        }
        .brand { display:flex; align-items:center; gap:.75rem; text-decoration:none; }
        .brand-logo {
            width:38px; height:38px;
            background: linear-gradient(135deg, var(--primary), var(--primary-xdark));
            border-radius:10px; display:flex; align-items:center; justify-content:center;
            color:#fff; font-weight:800; font-size:.9rem; flex-shrink:0;
        }
        .brand-text { font-weight:700; font-size:1rem; color:var(--primary); line-height:1.15; }
        .brand-text small { display:block; font-size:.68rem; color:var(--muted); font-weight:500; }
        .nav-user {
            display:flex; align-items:center; gap:.6rem; padding:.35rem .7rem;
            border-radius:10px; cursor:pointer; transition:background .15s;
        }
        .nav-user:hover { background:var(--primary-light); }
        .nav-avatar {
            width:34px; height:34px; border-radius:50%;
            background:var(--primary-light); border:2px solid var(--primary);
            display:flex; align-items:center; justify-content:center;
            color:var(--primary); font-size:.95rem;
        }
        .nav-name { font-size:.85rem; font-weight:700; color:var(--text); line-height:1.2; }
        .nav-role { font-size:.68rem; color:var(--muted); font-weight:500; }
        .dropdown-menu { border-radius:12px; border:1.5px solid var(--border); box-shadow:var(--shadow-md); min-width:190px; padding:.5rem; }
        .dd-item {
            border-radius:8px; padding:.5rem .85rem; font-size:.85rem; font-weight:600;
            color:var(--text); display:flex; align-items:center; gap:.55rem;
            background:none; border:none; width:100%; text-align:left; cursor:pointer; transition:background .15s;
        }
        .dd-item:hover { background:var(--primary-light); color:var(--primary); }
        .dd-item.danger { color:var(--danger); }
        .dd-item.danger:hover { background:#fff0f0; }

        /* ── PAGE ────────────────────────────────────────────── */
        .page { max-width:1300px; margin:0 auto; padding:2rem 1.5rem 4rem; }
        .page-head { margin-bottom:1.75rem; }
        .page-head h1 { font-size:1.65rem; font-weight:800; margin:0 0 .2rem; }
        .page-head p  { font-size:.875rem; color:var(--muted); margin:0; font-weight:500; }

        /* ── FLASH ───────────────────────────────────────────── */
        .flash {
            display:flex; align-items:center; gap:.6rem;
            padding:.8rem 1.1rem; border-radius:12px; margin-bottom:1rem;
            font-size:.875rem; font-weight:600; border:1.5px solid;
        }
        .flash-success { background:#EDFAF3; border-color:#A8E6C3; color:#1a6b3c; }
        .flash-error   { background:#fff2f2; border-color:#f5c6cb; color:#842029; }

        /* ── STAT CARDS ──────────────────────────────────────── */
        .stat {
            background:var(--card); border:1.5px solid var(--border);
            border-radius:16px; padding:1.25rem 1.4rem;
            box-shadow:var(--shadow-sm); height:100%; position:relative;
            overflow:hidden; transition:transform .2s, box-shadow .2s;
        }
        .stat::after {
            content:''; position:absolute; bottom:0; left:0; right:0; height:3px;
            background:linear-gradient(90deg, var(--primary), var(--primary-dark));
            transform:scaleX(0); transform-origin:left; transition:transform .3s;
        }
        .stat:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
        .stat:hover::after { transform:scaleX(1); }
        .stat-icon  { font-size:1.55rem; margin-bottom:.7rem; display:block; }
        .stat-value { font-size:1.7rem; font-weight:800; color:var(--primary); line-height:1; margin-bottom:.2rem; }
        .stat-label { font-size:.875rem; font-weight:700; color:var(--text); margin-bottom:.1rem; }
        .stat-sub   { font-size:.75rem; color:var(--muted); font-weight:500; }

        /* ── ALERT BANNERS ───────────────────────────────────── */
        .alert-banner {
            display:flex; align-items:center; gap:.65rem;
            padding:.8rem 1.1rem; border-radius:12px; font-size:.875rem; font-weight:600; border:1.5px solid;
        }
        .alert-ok   { background:#EDFAF3; border-color:#A8E6C3; color:#1a6b3c; }
        .alert-info { background:var(--primary-light); border-color:rgba(0,146,180,.25); color:var(--primary-xdark); }
        .alert-warn { background:var(--warning-bg); border-color:var(--warning-border); color:var(--warning-text); }

        /* ── SECTION LABEL ───────────────────────────────────── */
        .sec-label {
            font-size:.7rem; font-weight:700; text-transform:uppercase;
            letter-spacing:1.5px; color:var(--muted); margin:2rem 0 1rem;
        }

        /* ── MENU CARDS ──────────────────────────────────────── */
        .menu-card {
            background:var(--card); border:1.5px solid var(--border);
            border-radius:16px; padding:1.3rem 1.4rem;
            box-shadow:var(--shadow-sm); cursor:pointer; height:100%;
            display:flex; flex-direction:column;
            transition:transform .2s, box-shadow .2s, border-color .2s;
        }
        .menu-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-md); border-color:var(--primary); }
        .menu-card-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:.85rem; }
        .menu-icon  { font-size:1.85rem; }
        .menu-badge { font-size:.68rem; font-weight:700; padding:.2rem .65rem; border-radius:20px; }
        .badge-proses { background:rgba(25,135,84,.12); color:#1a6b3c;       border:1px solid rgba(25,135,84,.25); }
        .badge-view   { background:rgba(0,146,180,.12); color:var(--primary); border:1px solid rgba(0,146,180,.25); }
        .badge-crud   { background:rgba(245,166,35,.12); color:#a06800;       border:1px solid rgba(245,166,35,.3); }
        .menu-title { font-size:.95rem; font-weight:700; color:var(--text); margin-bottom:.25rem; }
        .menu-desc  { font-size:.8rem; color:var(--muted); font-weight:500; line-height:1.45; flex-grow:1; }

        /* ── ACCESS NOTE ─────────────────────────────────────── */
        .access-note {
            background:var(--warning-bg); border:1.5px solid var(--warning-border);
            border-radius:12px; padding:.85rem 1.1rem;
            font-size:.82rem; color:var(--warning-text); font-weight:500; margin-top:2rem;
        }

        /* ── MODAL ───────────────────────────────────────────── */
        .modal-content  { border-radius:20px; border:1.5px solid var(--border); box-shadow:0 24px 64px rgba(0,100,130,.14); }
        .modal-header   { border-bottom:1.5px solid var(--border); padding:1.25rem 1.5rem; border-radius:20px 20px 0 0; }
        .modal-title    { font-weight:800; font-size:1.05rem; color:var(--text); }
        .modal-body     { padding:1.5rem; }
        .modal-footer   { border-top:1.5px solid var(--border); padding:1rem 1.5rem; border-radius:0 0 20px 20px; }

        /* ── CUSTOM TABS ─────────────────────────────────────── */
        .ctabs { display:flex; border-bottom:2px solid var(--border); margin-bottom:1.5rem; gap:0; }
        .ctab  {
            padding:.6rem 1.1rem; font-size:.82rem; font-weight:700;
            color:var(--muted); cursor:pointer; border:none; background:none;
            border-bottom:2.5px solid transparent; margin-bottom:-2px;
            transition:color .15s, border-color .15s;
            font-family:'Plus Jakarta Sans',sans-serif;
        }
        .ctab.active { color:var(--primary); border-bottom-color:var(--primary); }
        .ctab-pane   { display:none; }
        .ctab-pane.active { display:block; }

        /* ── FORM ────────────────────────────────────────────── */
        .form-label    { font-weight:600; font-size:.82rem; color:var(--text); margin-bottom:.35rem; }
        .form-control, .form-select {
            border:1.5px solid var(--border); border-radius:10px;
            font-size:.875rem; padding:.65rem .9rem;
            font-family:'Plus Jakarta Sans',sans-serif; color:var(--text);
            background:#FAFCFD; transition:border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color:var(--primary); box-shadow:0 0 0 3px rgba(0,146,180,.1);
            background:#fff; outline:none;
        }
        .form-control::placeholder { color:#b0c8d4; }
        .section-divider {
            font-size:.7rem; font-weight:700; text-transform:uppercase;
            letter-spacing:1px; color:var(--muted);
            margin:1.25rem 0 .75rem; padding-bottom:.5rem;
            border-bottom:1.5px solid var(--border);
        }
        .section-divider:first-child { margin-top:0; }

        /* ── TABLE ───────────────────────────────────────────── */
        .tbl            { font-size:.82rem; }
        .tbl thead th   {
            background:var(--primary-light); color:var(--primary-xdark);
            font-weight:700; font-size:.72rem; text-transform:uppercase;
            letter-spacing:.5px; border:none; padding:.65rem .9rem; white-space:nowrap;
        }
        .tbl tbody td   { padding:.75rem .9rem; border-color:var(--border); vertical-align:middle; }
        .tbl tbody tr:hover { background:var(--primary-pale); }
        .tbl tbody tr:last-child td { border-bottom:none; }
        .tbl-empty { text-align:center; padding:2.5rem 1rem; color:var(--muted); font-size:.825rem; }
        .tbl-empty i { display:block; font-size:1.75rem; opacity:.35; margin-bottom:.5rem; }

        /* ── SUMMARY BOX ─────────────────────────────────────── */
        .sum-box {
            background:var(--primary-light); border:1.5px solid rgba(0,146,180,.2);
            border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.1rem;
            display:flex; gap:2rem; flex-wrap:wrap; align-items:center;
        }
        .sum-item label { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--muted); display:block; margin-bottom:.1rem; }
        .sum-item .val  { font-size:1rem; font-weight:800; color:var(--primary); }

        /* ── PARAM BOX ───────────────────────────────────────── */
        .param-box {
            background:#F8FDFE; border:1.5px solid rgba(0,146,180,.18);
            border-radius:12px; padding:.9rem 1.1rem; margin-bottom:1rem;
            font-size:.8rem; color:var(--text); display:flex; gap:1.5rem; flex-wrap:wrap;
        }
        .param-item label { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--muted); display:block; margin-bottom:.1rem; }
        .param-item span  { font-weight:700; color:var(--primary); font-size:.875rem; }

        /* ── BUTTONS ─────────────────────────────────────────── */
        .btn-primary-hrms {
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            border:none; border-radius:10px; color:#fff; font-weight:700; font-size:.875rem;
            padding:.6rem 1.25rem; box-shadow:0 3px 12px rgba(0,146,180,.28);
            cursor:pointer; transition:opacity .2s,transform .15s;
            font-family:'Plus Jakarta Sans',sans-serif;
            display:inline-flex; align-items:center; gap:.4rem;
        }
        .btn-primary-hrms:hover { opacity:.88; transform:translateY(-1px); }
        .btn-success-hrms {
            background:linear-gradient(135deg,#198754,#146c43); border:none;
            border-radius:10px; color:#fff; font-weight:700; font-size:.875rem;
            padding:.6rem 1.25rem; box-shadow:0 3px 12px rgba(25,135,84,.25);
            cursor:pointer; transition:opacity .2s;
            font-family:'Plus Jakarta Sans',sans-serif;
            display:inline-flex; align-items:center; gap:.4rem;
        }
        .btn-success-hrms:hover { opacity:.88; }
        .btn-outline-hrms {
            border:1.5px solid var(--border); border-radius:10px; color:var(--muted);
            font-weight:600; font-size:.875rem; padding:.6rem 1.25rem;
            background:transparent; cursor:pointer;
            font-family:'Plus Jakarta Sans',sans-serif; transition:border-color .2s,color .2s;
        }
        .btn-outline-hrms:hover { border-color:var(--primary); color:var(--primary); }
        .btn-xs {
            padding:.25rem .6rem; font-size:.73rem; border-radius:7px; font-weight:600;
            border:none; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif;
            display:inline-flex; align-items:center; gap:.3rem; white-space:nowrap; transition:all .15s;
        }
        .btn-xs-edit  { background:rgba(0,146,180,.1);  color:var(--primary); }
        .btn-xs-edit:hover  { background:var(--primary); color:#fff; }
        .btn-xs-del   { background:rgba(220,53,69,.1);  color:var(--danger); }
        .btn-xs-del:hover   { background:var(--danger);  color:#fff; }
        .btn-xs-pay   { background:rgba(245,166,35,.12); color:#a06800; }
        .btn-xs-pay:hover   { background:#F5A623; color:#fff; }

        /* ── BADGES ──────────────────────────────────────────── */
        .bdg { font-size:.7rem; font-weight:700; padding:.2rem .65rem; border-radius:20px; white-space:nowrap; border:1px solid; }
        .bdg-ok       { background:#EDFAF3; color:#1a6b3c;           border-color:#A8E6C3; }
        .bdg-draft    { background:rgba(245,166,35,.12); color:#a06800;  border-color:rgba(245,166,35,.35); }
        .bdg-approved { background:rgba(0,146,180,.1); color:var(--primary-dark); border-color:rgba(0,146,180,.25); }
        .bdg-paid     { background:#EDFAF3; color:#1a6b3c;           border-color:#A8E6C3; }

        /* ── SEARCH BAR ──────────────────────────────────────── */
        .sbar { display:flex; gap:.6rem; align-items:center; margin-bottom:1rem; flex-wrap:wrap; }
        .srch-wrap { position:relative; flex:1; min-width:180px; }
        .srch-wrap i { position:absolute; left:.85rem; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.82rem; }
        .srch-wrap input { padding-left:2.2rem; width:100%; }

        /* ── AVATAR ──────────────────────────────────────────── */
        .ava {
            width:30px; height:30px; border-radius:50%;
            background:var(--primary-light); border:1.5px solid var(--primary);
            display:inline-flex; align-items:center; justify-content:center;
            color:var(--primary); font-size:.72rem; font-weight:800; flex-shrink:0;
        }

        /* ── RESPONSIVE ──────────────────────────────────────── */
        @media(max-width:767.98px) {
            .navbar-hrms { padding:0 1rem; }
            .page        { padding:1.25rem 1rem 3rem; }
            .page-head h1{ font-size:1.35rem; }
            .stat-value  { font-size:1.35rem; }
            .sum-box     { gap:1rem; }
        }
        @media(max-width:575.98px) {
            .hide-xs { display:none; }
            .modal-dialog { margin:.5rem; }
        }
    </style>
</head>
<body>

{{-- ═══════════ NAVBAR ═══════════ --}}
<nav class="navbar-hrms">
    <a href="#" class="brand">
        <div class="brand-logo">HR</div>
        <div class="brand-text">
            HR Portal
            <small>{{ auth()->user()->unit->nama_pt ?? 'PT Suri Tani Pemuka' }}</small>
        </div>
    </a>

    <div class="dropdown">
        <div class="nav-user" data-bs-toggle="dropdown">
            <div class="nav-avatar"><i class="bi bi-person-fill"></i></div>
            <div class="hide-xs">
                <div class="nav-name">{{ auth()->user()->nama }}</div>
                <div class="nav-role">Payroll</div>
            </div>
            <i class="bi bi-chevron-down hide-xs ms-1" style="color:var(--muted);font-size:.72rem;"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-end mt-1">
            <li><button class="dd-item"><i class="bi bi-person-circle"></i> Profil Saya</button></li>
            <li><hr style="margin:.4rem 0;border-color:var(--border);"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dd-item danger w-100">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>

{{-- ═══════════ CONTENT ═══════════ --}}
<div class="page">

    {{-- Flash --}}
    @if(session('success'))
        <div class="flash flash-success" id="flashMsg">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error') || $errors->any())
        <div class="flash flash-error" id="flashMsg">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    {{-- Page header --}}
    <div class="page-head">
        <h1>Payroll</h1>
        <p>Proses penggajian &amp; pembayaran — {{ $bulanLabel }}</p>
    </div>

    {{-- ═══ STAT CARDS ═══════════════════════════════════════ --}}
    {{--
     $totalGajiBulanIni  = formatRp( SUM(payroll.total_income) WHERE status IN('approved','paid') AND bulan=$bulan AND tahun=$tahun )
     $thrTerbayar        = formatRp( SUM(thr.jumlah_thr) WHERE status='paid' AND tahun=$tahun )
     $bpjsTkBulanIni     = formatRp( SUM(bpjs_tk.total_company + total_employee) WHERE periode='$tahun-$bulan-01' )
     $bpjsKesBulanIni    = formatRp( SUM(bpjs_kesehatan.total) WHERE periode='$tahun-$bulan-01' )
    --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="stat">
                <span class="stat-icon">💵</span>
                <div class="stat-value">{{ $totalGajiBulanIni }}</div>
                <div class="stat-label">Total Gaji Bulan Ini</div>
                <div class="stat-sub">{{ $jmlKaryawanGajian }} karyawan</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat">
                <span class="stat-icon">🎁</span>
                <div class="stat-value">{{ $thrTerbayar }}</div>
                <div class="stat-label">THR Terbayar</div>
                <div class="stat-sub">tahun {{ $tahun }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat">
                <span class="stat-icon">🛡️</span>
                <div class="stat-value">{{ $bpjsTkBulanIni }}</div>
                <div class="stat-label">BPJS Ketenagakerjaan</div>
                <div class="stat-sub">total iuran periode ini</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat">
                <span class="stat-icon">🏥</span>
                <div class="stat-value">{{ $bpjsKesBulanIni }}</div>
                <div class="stat-label">BPJS Kesehatan</div>
                <div class="stat-sub">total iuran periode ini</div>
            </div>
        </div>
    </div>

    {{-- ═══ ALERTS ════════════════════════════════════════════ --}}
    <div class="d-flex flex-column gap-2 mb-1">
        @if($sudahApproved)
            <div class="alert-banner alert-ok">
                <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                Penggajian <strong>{{ $bulanLabel }}</strong> sudah diproses &amp; disetujui.
            </div>
        @elseif($adaDraft)
            <div class="alert-banner alert-warn">
                <i class="bi bi-clock-fill flex-shrink-0"></i>
                Penggajian <strong>{{ $bulanLabel }}</strong> ada dalam status <strong>Draft</strong> — belum di-approve.
            </div>
        @else
            <div class="alert-banner alert-info">
                <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                Penggajian <strong>{{ $bulanLabel }}</strong> belum diproses. Buka menu <strong>Proses Penggajian</strong> untuk generate slip gaji.
            </div>
        @endif

        @if($deadlineBpjs)
            <div class="alert-banner alert-warn">
                <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                Deadline BPJS: upload data sebelum tanggal 15. Tersisa {{ 15 - now()->day }} hari.
            </div>
        @endif
    </div>

    {{-- ═══ MENU CARDS ════════════════════════════════════════ --}}
    <p class="sec-label">Menu Tersedia</p>
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mPenggajian">
                <div class="menu-card-top">
                    <span class="menu-icon">💸</span>
                    <span class="menu-badge badge-proses">Proses</span>
                </div>
                <div class="menu-title">Proses Penggajian</div>
                <div class="menu-desc">Generate slip gaji bulanan &amp; approve untuk semua karyawan aktif</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mLihatGaji">
                <div class="menu-card-top">
                    <span class="menu-icon">👁️</span>
                    <span class="menu-badge badge-view">View</span>
                </div>
                <div class="menu-title">Lihat Gaji Karyawan</div>
                <div class="menu-desc">Basic salary, take home pay &amp; komponen per individu</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mThr">
                <div class="menu-card-top">
                    <span class="menu-icon">🎁</span>
                    <span class="menu-badge badge-crud">CRUD</span>
                </div>
                <div class="menu-title">THR Lebaran</div>
                <div class="menu-desc">Hitung &amp; proses THR — 1× gaji atau proporsional masa kerja</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mInsentif">
                <div class="menu-card-top">
                    <span class="menu-icon">⭐</span>
                    <span class="menu-badge badge-crud">CRUD</span>
                </div>
                <div class="menu-title">Insentif</div>
                <div class="menu-desc">Tambah &amp; kelola insentif per karyawan per periode</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mBpjsTk">
                <div class="menu-card-top">
                    <span class="menu-icon">🛡️</span>
                    <span class="menu-badge badge-crud">CRUD</span>
                </div>
                <div class="menu-title">BPJS Ketenagakerjaan</div>
                <div class="menu-desc">Generate iuran JHT, JP, JKK, JKM — perusahaan &amp; karyawan</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mBpjsKes">
                <div class="menu-card-top">
                    <span class="menu-icon">🏥</span>
                    <span class="menu-badge badge-crud">CRUD</span>
                </div>
                <div class="menu-title">BPJS Kesehatan</div>
                <div class="menu-desc">Generate iuran 4% perusahaan + 1% karyawan</div>
            </div>
        </div>
    </div>

    <div class="access-note mt-4">
        <i class="bi bi-shield-lock-fill me-2" style="color:#F5A623;"></i>
        <strong>Catatan akses:</strong> User Payroll hanya bisa LIHAT gaji &amp; PROSES penggajian. Tidak bisa ubah data karyawan atau konfigurasi sistem.
    </div>

</div>{{-- /page --}}


{{-- ════════════════════════════════════════════════════════════
     MODAL 1 — PROSES PENGGAJIAN
     ════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mPenggajian" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">💸 Proses Penggajian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="ctabs">
            <button class="ctab active" onclick="ctab(this,'pgj-list')"><i class="bi bi-table me-1"></i>Riwayat Bulan Ini</button>
            <button class="ctab"        onclick="ctab(this,'pgj-act')"><i class="bi bi-play-circle me-1"></i>Proses / Approve</button>
        </div>

        {{-- Tab: Riwayat --}}
        <div id="pgj-list" class="ctab-pane active">
            <div class="sum-box">
                <div class="sum-item"><label>Total Gaji</label><div class="val">{{ $totalGajiBulanIni }}</div></div>
                <div class="sum-item"><label>Karyawan</label><div class="val">{{ $jmlKaryawanGajian }} orang</div></div>
                <div class="sum-item">
                    <label>Status</label>
                    <div class="val" style="color:{{ $sudahApproved ? 'var(--success)' : ($adaDraft ? '#a06800' : 'var(--muted)') }}">
                        @if($sudahApproved) ✓ Approved
                        @elseif($adaDraft)  ⏳ Draft
                        @else              — Belum diproses
                        @endif
                    </div>
                </div>
            </div>
            <div class="sbar">
                <div class="srch-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Cari nama / NIP..." oninput="tblFilter(this,'tPgj')">
                </div>
            </div>
            <div class="table-responsive">
            <table class="table tbl table-borderless" id="tPgj">
                <thead><tr>
                    <th>Karyawan</th><th>Golongan</th><th>Unit</th>
                    <th>Basic Salary</th><th>Total Income</th><th>Potongan</th>
                    <th>Take Home Pay</th><th>Status</th>
                </tr></thead>
                <tbody>
                @forelse($payrolls as $p)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="ava">{{ $p->karyawan->inisial ?? 'NA' }}</div>
                            <div>
                                <div style="font-weight:700;font-size:.82rem;">{{ $p->karyawan->nama_karyawan ?? '—' }}</div>
                                <div style="font-size:.7rem;color:var(--muted);">{{ $p->karyawan->nip ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="bdg bdg-approved">{{ $p->karyawan->golongan->kode_golongan ?? '—' }}</span></td>
                    <td style="font-size:.78rem;">{{ $p->karyawan->unit->nama_pt ?? '—' }}</td>
                    <td style="font-weight:600;">Rp {{ number_format($p->basic_salary, 0, ',', '.') }}</td>
                    <td style="font-weight:700;color:var(--primary);">Rp {{ number_format($p->total_income, 0, ',', '.') }}</td>
                    <td style="color:var(--danger);font-size:.78rem;">− Rp {{ number_format($p->total_deduction, 0, ',', '.') }}</td>
                    <td style="font-weight:800;color:var(--success);">Rp {{ number_format($p->take_home_pay, 0, ',', '.') }}</td>
                    <td>
                        @if($p->status==='approved'||$p->status==='paid') <span class="bdg bdg-paid">Approved</span>
                        @elseif($p->status==='draft') <span class="bdg bdg-draft">Draft</span>
                        @else <span class="bdg">{{ $p->status }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="tbl-empty">
                    <i class="bi bi-inbox"></i>Belum ada data penggajian {{ $bulanLabel }}.
                </td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            {{-- {{ $payrolls->links() }} --}}
        </div>

        {{-- Tab: Proses / Approve --}}
        <div id="pgj-act" class="ctab-pane">
            @if($sudahApproved)
                <div class="alert-banner alert-ok mb-3">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    Penggajian {{ $bulanLabel }} sudah approved. Tidak bisa diproses ulang.
                </div>
            @else
                {{-- Form Generate --}}
                @unless($adaDraft)
                <div class="section-divider">Generate Slip Gaji</div>
                <div class="alert-banner alert-info mb-3" style="font-size:.82rem;">
                    <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                    Proses akan generate slip gaji untuk <strong>semua karyawan aktif</strong>.
                    Karyawan dengan <em>Car Allowance</em> tidak mendapat uang makan &amp; transport.
                </div>
                @if($paramBpjs)
                <div class="param-box mb-3">
                    <div class="param-item"><label>JHT Karyawan</label><span>{{ $paramBpjs->jht_karyawan_pct }}%</span></div>
                    <div class="param-item"><label>JP Karyawan</label><span>{{ $paramBpjs->jp_karyawan_pct }}%</span></div>
                    <div class="param-item"><label>BPJS Kes Karyawan</label><span>{{ $paramBpjs->bpjs_kes_karyawan_pct }}%</span></div>
                    <div class="param-item"><label>Berlaku Mulai</label><span>{{ $paramBpjs->berlaku_mulai->format('d/m/Y') }}</span></div>
                </div>
                @else
                <div class="alert-banner alert-warn mb-3" style="font-size:.82rem;">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                    Parameter BPJS belum dikonfigurasi. Minta Master System untuk setting terlebih dahulu.
                </div>
                @endif
                <form method="POST" action="{{ route('payroll.penggajian.proses') }}" onsubmit="return confirm('Proses penggajian {{ $bulanLabel }}? Tidak bisa dibatalkan.')">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="form-label">Bulan <span class="text-danger">*</span></label>
                            <select name="bulan" class="form-select" required>
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}" @selected($m==$bulan)>
                                        {{ \Carbon\Carbon::create($tahun,$m,1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Tahun <span class="text-danger">*</span></label>
                            <input type="number" name="tahun" class="form-control" value="{{ $tahun }}" min="2020" max="2099" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-success-hrms" {{ !$paramBpjs ? 'disabled' : '' }}>
                            <i class="bi bi-play-circle-fill"></i> Generate Penggajian
                        </button>
                    </div>
                </form>
                @endunless

                {{-- Form Approve (jika sudah ada draft) --}}
                @if($adaDraft)
                <div class="section-divider">Approve Draft</div>
                <div class="alert-banner alert-warn mb-3" style="font-size:.82rem;">
                    <i class="bi bi-clock-fill flex-shrink-0"></i>
                    Penggajian {{ $bulanLabel }} ada dalam status <strong>Draft</strong>. Approve untuk finalisasi.
                </div>
                <form method="POST" action="{{ route('payroll.penggajian.approve') }}" onsubmit="return confirm('Approve penggajian {{ $bulanLabel }}?')">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    <button type="submit" class="btn-primary-hrms">
                        <i class="bi bi-check-lg"></i> Approve Penggajian
                    </button>
                </form>
                @endif
            @endif
        </div>
    </div>
</div>
</div>
</div>


{{-- ════════════════════════════════════════════════════════════
     MODAL 2 — LIHAT GAJI KARYAWAN (Read-only)
     ════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mLihatGaji" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">👁️ Lihat Gaji Karyawan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="alert-banner alert-info mb-3" style="font-size:.8rem;">
            <i class="bi bi-info-circle-fill flex-shrink-0"></i>
            Read-only. Perubahan gaji pokok (golongan) dilakukan di Master System.
        </div>
        <div class="sbar">
            <div class="srch-wrap">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" placeholder="Cari nama atau NIP..." oninput="tblFilter(this,'tGaji')">
            </div>
            <select class="form-select" style="width:auto;min-width:130px;">
                <option value="">Semua Golongan</option>
                @foreach($golongans as $g)
                    <option>{{ $g->kode_golongan }}</option>
                @endforeach
            </select>
        </div>
        <div class="table-responsive">
        <table class="table tbl table-borderless" id="tGaji">
            <thead><tr>
                <th>NIP</th><th>Nama Karyawan</th><th>Golongan</th><th>Unit</th>
                <th>Basic Salary</th><th>Total Income</th><th>Total Potongan</th><th>Take Home Pay</th><th>Status</th>
            </tr></thead>
            <tbody>
            @forelse($payrolls as $p)
            <tr>
                <td style="font-size:.72rem;font-weight:600;color:var(--muted);">{{ $p->karyawan->nip ?? '—' }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="ava">{{ $p->karyawan->inisial ?? 'NA' }}</div>
                        <strong style="font-size:.82rem;">{{ $p->karyawan->nama_karyawan ?? '—' }}</strong>
                    </div>
                </td>
                <td><span class="bdg bdg-approved">{{ $p->karyawan->golongan->kode_golongan ?? '—' }}</span></td>
                <td style="font-size:.78rem;">{{ $p->karyawan->unit->nama_pt ?? '—' }}</td>
                <td>Rp {{ number_format($p->basic_salary, 0, ',', '.') }}</td>
                <td style="font-weight:700;color:var(--primary);">Rp {{ number_format($p->total_income, 0, ',', '.') }}</td>
                <td style="color:var(--danger);">− Rp {{ number_format($p->total_deduction, 0, ',', '.') }}</td>
                <td style="font-weight:800;color:var(--success);">Rp {{ number_format($p->take_home_pay, 0, ',', '.') }}</td>
                <td>
                    @if($p->status==='approved'||$p->status==='paid') <span class="bdg bdg-paid">Approved</span>
                    @else <span class="bdg bdg-draft">{{ $p->status }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="tbl-empty"><i class="bi bi-inbox"></i>Belum ada data gaji bulan ini.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        {{-- {{ $payrolls->links() }} --}}
    </div>
</div>
</div>
</div>


{{-- ════════════════════════════════════════════════════════════
     MODAL 3 — THR LEBARAN
     Kolom DB: jumlah_thr (bukan total_thr), masa_kerja_bulan (bukan bulan_bekerja),
               basic_salary (bukan gaji_pokok)
     ════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mThr" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">🎁 THR Lebaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="ctabs">
            <button class="ctab active" onclick="ctab(this,'thr-list')"><i class="bi bi-table me-1"></i>Daftar THR</button>
            <button class="ctab"        onclick="ctab(this,'thr-hitung')"><i class="bi bi-calculator me-1"></i>Hitung THR</button>
        </div>

        <div id="thr-list" class="ctab-pane active">
            <div class="sbar">
                <div class="srch-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Cari nama karyawan..." oninput="tblFilter(this,'tThr')">
                </div>
            </div>
            <div class="table-responsive">
            <table class="table tbl table-borderless" id="tThr">
                <thead><tr>
                    <th>Karyawan</th><th>Golongan</th><th>Masa Kerja</th>
                    <th>Basic Salary</th><th>Jumlah THR</th><th>Keterangan</th>
                    <th>Status</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                @forelse($thrs as $t)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="ava">{{ $t->karyawan->inisial ?? 'NA' }}</div>
                            <div>
                                <div style="font-weight:700;font-size:.82rem;">{{ $t->karyawan->nama_karyawan ?? '—' }}</div>
                                <div style="font-size:.7rem;color:var(--muted);">{{ $t->karyawan->nip ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="bdg bdg-approved">{{ $t->karyawan->golongan->kode_golongan ?? '—' }}</span></td>
                    <td style="font-size:.78rem;">
                        @php $thn = intdiv($t->masa_kerja_bulan, 12); $bln = $t->masa_kerja_bulan % 12; @endphp
                        {{ $thn > 0 ? $thn.' thn ' : '' }}{{ $bln }} bln
                    </td>
                    <td>Rp {{ number_format($t->basic_salary, 0, ',', '.') }}</td>
                    <td style="font-weight:800;color:var(--primary);">Rp {{ number_format($t->jumlah_thr, 0, ',', '.') }}</td>
                    <td style="font-size:.75rem;color:var(--muted);">
                        {{ $t->masa_kerja_bulan >= 12 ? '1× gaji' : round($t->masa_kerja_bulan/12,2).'× (proporsional)' }}
                    </td>
                    <td>
                        @if($t->status==='paid')     <span class="bdg bdg-paid">Terbayar</span>
                        @elseif($t->status==='approved') <span class="bdg bdg-approved">Approved</span>
                        @else <span class="bdg bdg-draft">Draft</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($t->status !== 'paid')
                                <form method="POST" action="{{ route('payroll.thr.bayar', $t->id) }}" onsubmit="return confirm('Tandai THR sebagai terbayar?')">
                                    @csrf
                                    <button type="submit" class="btn-xs btn-xs-pay"><i class="bi bi-check2"></i> Bayar</button>
                                </form>
                                <form method="POST" action="{{ route('payroll.thr.destroy', $t->id) }}" onsubmit="return confirm('Hapus data THR ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-xs btn-xs-del"><i class="bi bi-trash"></i></button>
                                </form>
                            @else
                                <span style="font-size:.72rem;color:var(--muted);">{{ $t->created_at?->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="tbl-empty"><i class="bi bi-inbox"></i>Belum ada data THR tahun {{ $tahun }}.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            {{-- {{ $thrs->links() }} --}}
        </div>

        <div id="thr-hitung" class="ctab-pane">
            <div class="alert-banner alert-info mb-3" style="font-size:.82rem;">
                <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                Formula: masa kerja ≥ 12 bulan = <strong>1× basic salary</strong>.
                Kurang dari 12 bulan = <strong>(masa_kerja / 12) × basic salary</strong>.
            </div>
            @if($thrSudahAda)
                <div class="alert-banner alert-ok" style="font-size:.82rem;">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    THR tahun {{ $tahun }} sudah dihitung. Lihat di tab Daftar THR.
                </div>
            @else
                <form method="POST" action="{{ route('payroll.thr.hitung') }}" onsubmit="return confirm('Hitung THR untuk semua karyawan aktif tahun {{ $tahun }}?')">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label class="form-label">Tahun THR <span class="text-danger">*</span></label>
                            <input type="number" name="tahun" class="form-control" value="{{ $tahun }}" min="2020" max="2099" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-success-hrms">
                            <i class="bi bi-calculator-fill"></i> Hitung THR Semua Karyawan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
</div>
</div>


{{-- ════════════════════════════════════════════════════════════
     MODAL 4 — INSENTIF
     Kolom DB: bulan, tahun, jumlah, deskripsi  (BUKAN nominal/keterangan/periode_*)
     ════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mInsentif" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">⭐ Insentif</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="ctabs">
            <button class="ctab active" onclick="ctab(this,'ins-list')"><i class="bi bi-table me-1"></i>Daftar</button>
            <button class="ctab"        onclick="ctab(this,'ins-form')"><i class="bi bi-plus-circle me-1"></i>Tambah / Edit</button>
        </div>

        <div id="ins-list" class="ctab-pane active">
            <div class="sbar">
                <div class="srch-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Cari nama karyawan..." oninput="tblFilter(this,'tIns')">
                </div>
                <button class="btn-primary-hrms" onclick="ctabById('ins-form')">
                    <i class="bi bi-plus-lg"></i> Tambah
                </button>
            </div>
            <div class="table-responsive">
            <table class="table tbl table-borderless" id="tIns">
                <thead><tr>
                    <th>Karyawan</th><th>Bulan</th><th>Tahun</th>
                    <th>Jumlah</th><th>Deskripsi</th><th>Status</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                @forelse($insentifs as $ins)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="ava">{{ $ins->karyawan->inisial ?? 'NA' }}</div>
                            <div>
                                <div style="font-weight:700;font-size:.82rem;">{{ $ins->karyawan->nama_karyawan ?? '—' }}</div>
                                <div style="font-size:.7rem;color:var(--muted);">
                                    {{ $ins->karyawan->posisiAktif?->nama_jabatan ?? '' }}
                                    {{ $ins->karyawan->posisiAktif?->costCenter?->nama_cc ? '· '.$ins->karyawan->posisiAktif->costCenter->nama_cc : '' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.78rem;">{{ \Carbon\Carbon::create($ins->tahun,$ins->bulan,1)->translatedFormat('F') }}</td>
                    <td style="font-size:.78rem;">{{ $ins->tahun }}</td>
                    <td style="font-weight:700;color:var(--primary);">Rp {{ number_format($ins->jumlah, 0, ',', '.') }}</td>
                    <td style="font-size:.78rem;color:var(--muted);">{{ $ins->deskripsi ?? '—' }}</td>
                    <td>
                        @if($ins->status==='paid')     <span class="bdg bdg-paid">Paid</span>
                        @elseif($ins->status==='approved') <span class="bdg bdg-approved">Approved</span>
                        @else <span class="bdg bdg-draft">Draft</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($ins->status !== 'paid')
                                <button class="btn-xs btn-xs-edit" onclick='fillIns(@json($ins))'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('payroll.insentif.destroy', $ins->id) }}" onsubmit="return confirm('Hapus insentif ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-xs btn-xs-del"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="tbl-empty"><i class="bi bi-inbox"></i>Belum ada data insentif bulan ini.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            {{-- {{ $insentifs->links() }} --}}
        </div>

        <div id="ins-form" class="ctab-pane">
            <form method="POST" id="fIns" action="{{ route('payroll.insentif.store') }}">
                @csrf
                <input type="hidden" name="_method" id="insMethod" value="POST">
                <input type="hidden" name="_ins_id"  id="insId">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                        <select name="id_karyawan" id="insKaryawan" class="form-select" required>
                            <option value="">— Pilih Karyawan —</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->id }}">{{ $k->nip }} — {{ $k->nama_karyawan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Bulan <span class="text-danger">*</span></label>
                        <select name="bulan" id="insBulan" class="form-select" required>
                            @for($m=1;$m<=12;$m++)
                                <option value="{{ $m }}" @selected($m==$bulan)>
                                    {{ \Carbon\Carbon::create($tahun,$m,1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                        <input type="number" name="tahun" id="insTahun" class="form-control" value="{{ $tahun }}" min="2020" required>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label">Jumlah (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" id="insJumlah" class="form-control" placeholder="500000" min="0" required>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label">Status</label>
                        <select name="status" id="insStatus" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="approved">Approved</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" name="deskripsi" id="insDeskripsi" class="form-control" placeholder="Insentif produksi Q1...">
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan</button>
                    <button type="button" class="btn-outline-hrms" onclick="resetIns()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>


{{-- ════════════════════════════════════════════════════════════
     MODAL 5 — BPJS KETENAGAKERJAAN
     Kolom DB: periode (DATE), basic_salary,
               jht_company, jp_company, jkk, jkm, total_company,
               jht_employee, jp_employee, total_employee
     ════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mBpjsTk" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">🛡️ BPJS Ketenagakerjaan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="ctabs">
            <button class="ctab active" onclick="ctab(this,'btk-list')"><i class="bi bi-table me-1"></i>Iuran Periode Ini</button>
            <button class="ctab"        onclick="ctab(this,'btk-gen')"><i class="bi bi-lightning me-1"></i>Generate</button>
        </div>

        <div id="btk-list" class="ctab-pane active">
            @if($paramBpjs)
            <div class="param-box">
                <div class="param-item"><label>JHT Perusahaan</label><span>{{ $paramBpjs->jht_perusahaan_pct }}%</span></div>
                <div class="param-item"><label>JP Perusahaan</label><span>{{ $paramBpjs->jp_perusahaan_pct }}%</span></div>
                <div class="param-item"><label>JKK</label><span>{{ $paramBpjs->jkk_pct }}%</span></div>
                <div class="param-item"><label>JKM</label><span>{{ $paramBpjs->jkm_pct }}%</span></div>
                <div class="param-item"><label>JHT Karyawan</label><span>{{ $paramBpjs->jht_karyawan_pct }}%</span></div>
                <div class="param-item"><label>JP Karyawan</label><span>{{ $paramBpjs->jp_karyawan_pct }}%</span></div>
            </div>
            @endif
            <div class="sbar">
                <div class="srch-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Cari nama karyawan..." oninput="tblFilter(this,'tBpjsTk')">
                </div>
            </div>
            <div class="table-responsive">
            <table class="table tbl table-borderless" id="tBpjsTk">
                <thead><tr>
                    <th>Karyawan</th><th>Gol.</th><th>Basic Salary</th>
                    <th>Beban Perusahaan</th>
                    <th>↳ JHT</th><th>↳ JP</th><th>↳ JKK</th><th>↳ JKM</th>
                    <th>Beban Karyawan</th>
                    <th>Aksi</th>
                </tr></thead>
                <tbody>
                @forelse($bpjsTks as $b)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="ava">{{ $b->karyawan->inisial ?? 'NA' }}</div>
                            <strong style="font-size:.82rem;">{{ $b->karyawan->nama_karyawan ?? '—' }}</strong>
                        </div>
                    </td>
                    <td><span class="bdg bdg-approved">{{ $b->karyawan->golongan->kode_golongan ?? '—' }}</span></td>
                    <td>Rp {{ number_format($b->basic_salary, 0, ',', '.') }}</td>
                    <td style="font-weight:700;color:var(--primary);">Rp {{ number_format($b->total_company, 0, ',', '.') }}</td>
                    <td style="font-size:.75rem;">Rp {{ number_format($b->jht_company, 0, ',', '.') }}</td>
                    <td style="font-size:.75rem;">Rp {{ number_format($b->jp_company, 0, ',', '.') }}</td>
                    <td style="font-size:.75rem;">Rp {{ number_format($b->jkk, 0, ',', '.') }}</td>
                    <td style="font-size:.75rem;">Rp {{ number_format($b->jkm, 0, ',', '.') }}</td>
                    <td style="font-weight:600;color:#a06800;">Rp {{ number_format($b->total_employee, 0, ',', '.') }}</td>
                    <td>
                        <form method="POST" action="{{ route('payroll.bpjs-tk.destroy', $b->id) }}" onsubmit="return confirm('Hapus record BPJS TK ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-xs btn-xs-del"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="tbl-empty">
                    <i class="bi bi-inbox"></i>Belum ada data BPJS TK periode ini. Generate terlebih dahulu.
                </td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            {{-- {{ $bpjsTks->links() }} --}}
        </div>

        <div id="btk-gen" class="ctab-pane">
            @if($bpjsTkSudahAda)
                <div class="alert-banner alert-ok" style="font-size:.82rem;">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    BPJS TK periode {{ $bulanLabel }} sudah digenerate.
                </div>
            @else
                <div class="alert-banner alert-info mb-3" style="font-size:.82rem;">
                    <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                    Generate iuran untuk semua karyawan aktif menggunakan parameter BPJS yang berlaku.
                </div>
                <form method="POST" action="{{ route('payroll.bpjs-tk.generate') }}" onsubmit="return confirm('Generate BPJS TK?')">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="form-label">Bulan <span class="text-danger">*</span></label>
                            <select name="bulan" class="form-select" required>
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}" @selected($m==$bulan)>
                                        {{ \Carbon\Carbon::create($tahun,$m,1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Tahun <span class="text-danger">*</span></label>
                            <input type="number" name="tahun" class="form-control" value="{{ $tahun }}" min="2020" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-success-hrms" {{ !$paramBpjs ? 'disabled' : '' }}>
                            <i class="bi bi-lightning-fill"></i> Generate BPJS Ketenagakerjaan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
</div>
</div>


{{-- ════════════════════════════════════════════════════════════
     MODAL 6 — BPJS KESEHATAN
     Kolom DB: periode (DATE), basic_salary, beban_perusahaan,
               beban_karyawan, total (GENERATED)
     ════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mBpjsKes" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">🏥 BPJS Kesehatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="ctabs">
            <button class="ctab active" onclick="ctab(this,'bks-list')"><i class="bi bi-table me-1"></i>Iuran Periode Ini</button>
            <button class="ctab"        onclick="ctab(this,'bks-gen')"><i class="bi bi-lightning me-1"></i>Generate</button>
        </div>

        <div id="bks-list" class="ctab-pane active">
            @if($paramBpjs)
            <div class="param-box">
                <div class="param-item"><label>Beban Perusahaan</label><span>{{ $paramBpjs->bpjs_kes_perusahaan_pct }}% dari basic salary</span></div>
                <div class="param-item"><label>Beban Karyawan</label><span>{{ $paramBpjs->bpjs_kes_karyawan_pct }}% dari basic salary</span></div>
                <div class="param-item"><label>Total Iuran Periode Ini</label><span>{{ $bpjsKesBulanIni }}</span></div>
            </div>
            @endif
            <div class="sbar">
                <div class="srch-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Cari nama karyawan..." oninput="tblFilter(this,'tBpjsKes')">
                </div>
            </div>
            <div class="table-responsive">
            <table class="table tbl table-borderless" id="tBpjsKes">
                <thead><tr>
                    <th>Karyawan</th><th>Golongan</th><th>Basic Salary</th>
                    <th>Beban Perusahaan ({{ $paramBpjs?->bpjs_kes_perusahaan_pct ?? 4 }}%)</th>
                    <th>Beban Karyawan ({{ $paramBpjs?->bpjs_kes_karyawan_pct ?? 1 }}%)</th>
                    <th>Total</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                @forelse($bpjsKess as $b)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="ava">{{ $b->karyawan->inisial ?? 'NA' }}</div>
                            <strong style="font-size:.82rem;">{{ $b->karyawan->nama_karyawan ?? '—' }}</strong>
                        </div>
                    </td>
                    <td><span class="bdg bdg-approved">{{ $b->karyawan->golongan->kode_golongan ?? '—' }}</span></td>
                    <td>Rp {{ number_format($b->basic_salary, 0, ',', '.') }}</td>
                    <td style="font-weight:700;color:var(--primary);">Rp {{ number_format($b->beban_perusahaan, 0, ',', '.') }}</td>
                    <td style="font-weight:600;color:#a06800;">Rp {{ number_format($b->beban_karyawan, 0, ',', '.') }}</td>
                    {{-- total = GENERATED COLUMN di DB (beban_perusahaan + beban_karyawan) --}}
                    <td style="font-weight:800;">Rp {{ number_format($b->total, 0, ',', '.') }}</td>
                    <td>
                        <form method="POST" action="{{ route('payroll.bpjs-kes.destroy', $b->id) }}" onsubmit="return confirm('Hapus record ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-xs btn-xs-del"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="tbl-empty">
                    <i class="bi bi-inbox"></i>Belum ada data BPJS Kesehatan periode ini.
                </td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            {{-- {{ $bpjsKess->links() }} --}}
        </div>

        <div id="bks-gen" class="ctab-pane">
            @if($bpjsKesSudahAda)
                <div class="alert-banner alert-ok" style="font-size:.82rem;">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    BPJS Kesehatan periode {{ $bulanLabel }} sudah digenerate.
                </div>
            @else
                <div class="alert-banner alert-info mb-3" style="font-size:.82rem;">
                    <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                    Generate iuran BPJS Kesehatan: {{ $paramBpjs?->bpjs_kes_perusahaan_pct ?? 4 }}% perusahaan + {{ $paramBpjs?->bpjs_kes_karyawan_pct ?? 1 }}% karyawan dari basic salary.
                </div>
                <form method="POST" action="{{ route('payroll.bpjs-kes.generate') }}" onsubmit="return confirm('Generate BPJS Kesehatan?')">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <label class="form-label">Bulan <span class="text-danger">*</span></label>
                            <select name="bulan" class="form-select" required>
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}" @selected($m==$bulan)>
                                        {{ \Carbon\Carbon::create($tahun,$m,1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Tahun <span class="text-danger">*</span></label>
                            <input type="number" name="tahun" class="form-control" value="{{ $tahun }}" min="2020" required>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-success-hrms" {{ !$paramBpjs ? 'disabled' : '' }}>
                            <i class="bi bi-lightning-fill"></i> Generate BPJS Kesehatan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ─── Custom Tab Switcher ────────────────────────────────────
function ctab(btn, targetId) {
    const mc = btn.closest('.modal-content') || btn.closest('.page');
    mc.querySelectorAll('.ctab').forEach(t => t.classList.remove('active'));
    mc.querySelectorAll('.ctab-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    mc.querySelector('#' + targetId)?.classList.add('active');
}
function ctabById(targetId) {
    const pane = document.getElementById(targetId);
    if (!pane) return;
    const mc = pane.closest('.modal-content') || pane.closest('.page');
    mc.querySelectorAll('.ctab-pane').forEach(p => p.classList.remove('active'));
    pane.classList.add('active');
    mc.querySelectorAll('.ctab').forEach(b => {
        const oc = b.getAttribute('onclick') || '';
        b.classList.toggle('active', oc.includes("'" + targetId + "'") || oc.includes('"' + targetId + '"'));
    });
}

// ─── Live Table Filter ──────────────────────────────────────
function tblFilter(input, tblId) {
    const q = input.value.toLowerCase();
    document.querySelectorAll('#' + tblId + ' tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

// ─── Insentif: Fill Edit Form ───────────────────────────────
function fillIns(d) {
    document.getElementById('insMethod').value   = 'PUT';
    document.getElementById('insId').value       = d.id;
    document.getElementById('insKaryawan').value = d.id_karyawan;
    document.getElementById('insBulan').value    = d.bulan;
    document.getElementById('insTahun').value    = d.tahun;
    document.getElementById('insJumlah').value   = d.jumlah;
    document.getElementById('insStatus').value   = d.status;
    document.getElementById('insDeskripsi').value= d.deskripsi ?? '';
    // Ubah action ke PUT endpoint
    document.getElementById('fIns').action = '/payroll/insentif/' + d.id;
    ctabById('ins-form');
}
function resetIns() {
    document.getElementById('insMethod').value = 'POST';
    document.getElementById('fIns').reset();
    document.getElementById('fIns').action = '{{ route("payroll.insentif.store") }}';
    ctabById('ins-list');
}

// ─── Auto-dismiss flash ─────────────────────────────────────
setTimeout(() => {
    const el = document.getElementById('flashMsg');
    if (el) { el.style.transition = 'opacity .5s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 500); }
}, 5000);
</script>
</body>
</html>
