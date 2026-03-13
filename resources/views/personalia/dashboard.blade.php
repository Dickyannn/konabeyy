<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Personalia — HRMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<style>
:root {
    --primary:       #0092B4;
    --primary-dark:  #007A98;
    --primary-xdark: #005F77;
    --primary-light: #E8F7FB;
    --primary-pale:  #F2FBFD;
    --border:        #E2EEF2;
    --bg:            #F4F8FA;
    --card:          #FFFFFF;
    --text:          #1A2E3B;
    --muted:         #6B8896;
    --success:       #198754;
    --danger:        #dc3545;
    --warn-bg:       #FFFBF0;
    --warn-border:   #F5D88A;
    --warn-text:     #7a5800;
    --shadow-sm:     0 2px 8px rgba(0,100,130,.07);
    --shadow-md:     0 6px 24px rgba(0,100,130,.12);
}
*,*::before,*::after{box-sizing:border-box}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);margin:0}

/* ── NAVBAR ── */
.navbar-hrms{background:#fff;border-bottom:1.5px solid var(--border);padding:0 2rem;height:62px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:1050;box-shadow:0 2px 12px rgba(0,100,130,.06)}
.brand{display:flex;align-items:center;gap:.75rem;text-decoration:none}
.brand-logo{width:38px;height:38px;background:linear-gradient(135deg,var(--primary),var(--primary-xdark));border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem}
.brand-text{font-weight:700;font-size:1rem;color:var(--primary);line-height:1.15}
.brand-text small{display:block;font-size:.68rem;color:var(--muted);font-weight:500}
.nav-user{display:flex;align-items:center;gap:.6rem;padding:.35rem .7rem;border-radius:10px;cursor:pointer;transition:background .15s}
.nav-user:hover{background:var(--primary-light)}
.nav-avatar{width:34px;height:34px;border-radius:50%;background:var(--primary-light);border:2px solid var(--primary);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:.95rem}
.nav-name{font-size:.85rem;font-weight:700;color:var(--text);line-height:1.2}
.nav-role{font-size:.68rem;color:var(--muted);font-weight:500}
.dropdown-menu{border-radius:12px;border:1.5px solid var(--border);box-shadow:var(--shadow-md);min-width:190px;padding:.5rem}
.dd-item{border-radius:8px;padding:.5rem .85rem;font-size:.85rem;font-weight:600;color:var(--text);display:flex;align-items:center;gap:.55rem;background:none;border:none;width:100%;text-align:left;cursor:pointer;transition:background .15s;font-family:'Plus Jakarta Sans',sans-serif}
.dd-item:hover{background:var(--primary-light);color:var(--primary)}
.dd-item.danger{color:var(--danger)}
.dd-item.danger:hover{background:#fff0f0}

/* ── PAGE ── */
.page{max-width:1300px;margin:0 auto;padding:2rem 1.5rem 4rem}
.page-head{margin-bottom:1.75rem}
.page-head h1{font-size:1.65rem;font-weight:800;margin:0 0 .2rem}
.page-head p{font-size:.875rem;color:var(--muted);margin:0;font-weight:500}

/* ── FLASH ── */
.flash{display:flex;align-items:center;gap:.6rem;padding:.8rem 1.1rem;border-radius:12px;margin-bottom:1rem;font-size:.875rem;font-weight:600;border:1.5px solid}
.flash-success{background:#EDFAF3;border-color:#A8E6C3;color:#1a6b3c}
.flash-error{background:#fff2f2;border-color:#f5c6cb;color:#842029}

/* ── STAT CARDS ── */
.stat{background:var(--card);border:1.5px solid var(--border);border-radius:16px;padding:1.25rem 1.4rem;box-shadow:var(--shadow-sm);height:100%;position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s}
.stat::after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--primary),var(--primary-dark));transform:scaleX(0);transform-origin:left;transition:transform .3s}
.stat:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
.stat:hover::after{transform:scaleX(1)}
.stat-icon{font-size:1.55rem;margin-bottom:.7rem;display:block}
.stat-value{font-size:1.7rem;font-weight:800;color:var(--primary);line-height:1;margin-bottom:.2rem}
.stat-label{font-size:.875rem;font-weight:700;color:var(--text);margin-bottom:.1rem}
.stat-sub{font-size:.75rem;color:var(--muted);font-weight:500}

/* ── ALERTS ── */
.alert-banner{display:flex;align-items:center;gap:.65rem;padding:.8rem 1.1rem;border-radius:12px;font-size:.875rem;font-weight:600;border:1.5px solid}
.alert-ok{background:#EDFAF3;border-color:#A8E6C3;color:#1a6b3c}
.alert-info{background:var(--primary-light);border-color:rgba(0,146,180,.25);color:var(--primary-xdark)}
.alert-warn{background:var(--warn-bg);border-color:var(--warn-border);color:var(--warn-text)}

/* ── SEC LABEL ── */
.sec-label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--muted);margin:2rem 0 1rem}

/* ── MENU CARDS ── */
.menu-card{background:var(--card);border:1.5px solid var(--border);border-radius:16px;padding:1.3rem 1.4rem;box-shadow:var(--shadow-sm);cursor:pointer;height:100%;display:flex;flex-direction:column;transition:transform .2s,box-shadow .2s,border-color .2s}
.menu-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md);border-color:var(--primary)}
.menu-card-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.85rem}
.menu-icon{font-size:1.85rem}
.menu-badge{font-size:.68rem;font-weight:700;padding:.2rem .65rem;border-radius:20px}
.badge-view{background:rgba(0,146,180,.12);color:var(--primary);border:1px solid rgba(0,146,180,.25)}
.badge-crud{background:rgba(245,166,35,.12);color:#a06800;border:1px solid rgba(245,166,35,.3)}
.badge-chart{background:rgba(111,66,193,.12);color:#5a0fa0;border:1px solid rgba(111,66,193,.25)}
.badge-cal{background:rgba(25,135,84,.12);color:#1a6b3c;border:1px solid rgba(25,135,84,.25)}
.menu-title{font-size:.95rem;font-weight:700;color:var(--text);margin-bottom:.25rem}
.menu-desc{font-size:.8rem;color:var(--muted);font-weight:500;line-height:1.45;flex-grow:1}

/* ── MODAL ── */
.modal-content{border-radius:20px;border:1.5px solid var(--border);box-shadow:0 24px 64px rgba(0,100,130,.14)}
.modal-header{border-bottom:1.5px solid var(--border);padding:1.25rem 1.5rem;border-radius:20px 20px 0 0}
.modal-title{font-weight:800;font-size:1.05rem;color:var(--text)}
.modal-body{padding:1.5rem}
.modal-footer{border-top:1.5px solid var(--border);padding:1rem 1.5rem;border-radius:0 0 20px 20px}

/* ── TABS ── */
.ctabs{display:flex;border-bottom:2px solid var(--border);margin-bottom:1.5rem;gap:0}
.ctab{padding:.6rem 1.1rem;font-size:.82rem;font-weight:700;color:var(--muted);cursor:pointer;border:none;background:none;border-bottom:2.5px solid transparent;margin-bottom:-2px;transition:color .15s,border-color .15s;font-family:'Plus Jakarta Sans',sans-serif}
.ctab.active{color:var(--primary);border-bottom-color:var(--primary)}
.ctab-pane{display:none}
.ctab-pane.active{display:block}

/* ── FORM ── */
.form-label{font-weight:600;font-size:.82rem;color:var(--text);margin-bottom:.35rem}
.form-control,.form-select{border:1.5px solid var(--border);border-radius:10px;font-size:.875rem;padding:.65rem .9rem;font-family:'Plus Jakarta Sans',sans-serif;color:var(--text);background:#FAFCFD;transition:border-color .2s,box-shadow .2s}
.form-control:focus,.form-select:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(0,146,180,.1);background:#fff;outline:none}
.sec-div{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin:1.25rem 0 .75rem;padding-bottom:.5rem;border-bottom:1.5px solid var(--border)}
.sec-div:first-child{margin-top:0}

/* ── TABLE ── */
.tbl{font-size:.82rem}
.tbl thead th{background:var(--primary-light);color:var(--primary-xdark);font-weight:700;font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;border:none;padding:.65rem .9rem;white-space:nowrap}
.tbl tbody td{padding:.75rem .9rem;border-color:var(--border);vertical-align:middle}
.tbl tbody tr:hover{background:var(--primary-pale)}
.tbl tbody tr:last-child td{border-bottom:none}
.tbl-empty{text-align:center;padding:2.5rem 1rem;color:var(--muted);font-size:.825rem}
.tbl-empty i{display:block;font-size:1.75rem;opacity:.35;margin-bottom:.5rem}

/* ── CHART CONTAINERS ── */
.chart-card{background:var(--card);border:1.5px solid var(--border);border-radius:16px;padding:1.25rem 1.4rem;box-shadow:var(--shadow-sm);height:100%}
.chart-title{font-size:.85rem;font-weight:700;color:var(--text);margin-bottom:1rem}
.chart-wrap{position:relative;height:220px}
.chart-wrap-tall{position:relative;height:280px}

/* ── BUTTONS ── */
.btn-primary-hrms{background:linear-gradient(135deg,var(--primary),var(--primary-dark));border:none;border-radius:10px;color:#fff;font-weight:700;font-size:.875rem;padding:.6rem 1.25rem;box-shadow:0 3px 12px rgba(0,146,180,.28);cursor:pointer;transition:opacity .2s,transform .15s;font-family:'Plus Jakarta Sans',sans-serif;display:inline-flex;align-items:center;gap:.4rem}
.btn-primary-hrms:hover{opacity:.88;transform:translateY(-1px)}
.btn-success-hrms{background:linear-gradient(135deg,#198754,#146c43);border:none;border-radius:10px;color:#fff;font-weight:700;font-size:.875rem;padding:.6rem 1.25rem;box-shadow:0 3px 12px rgba(25,135,84,.25);cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;display:inline-flex;align-items:center;gap:.4rem}
.btn-danger-hrms{background:linear-gradient(135deg,#dc3545,#b02a37);border:none;border-radius:10px;color:#fff;font-weight:700;font-size:.875rem;padding:.6rem 1.25rem;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;display:inline-flex;align-items:center;gap:.4rem}
.btn-outline-hrms{border:1.5px solid var(--border);border-radius:10px;color:var(--muted);font-weight:600;font-size:.875rem;padding:.6rem 1.25rem;background:transparent;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;transition:border-color .2s,color .2s}
.btn-outline-hrms:hover{border-color:var(--primary);color:var(--primary)}
.btn-xs{padding:.25rem .6rem;font-size:.73rem;border-radius:7px;font-weight:600;border:none;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;display:inline-flex;align-items:center;gap:.3rem;white-space:nowrap;transition:all .15s}
.btn-xs-edit{background:rgba(0,146,180,.1);color:var(--primary)}
.btn-xs-edit:hover{background:var(--primary);color:#fff}
.btn-xs-del{background:rgba(220,53,69,.1);color:var(--danger)}
.btn-xs-del:hover{background:var(--danger);color:#fff}
.btn-xs-ok{background:rgba(25,135,84,.1);color:var(--success)}
.btn-xs-ok:hover{background:var(--success);color:#fff}
.btn-xs-rej{background:rgba(220,53,69,.1);color:var(--danger)}

/* ── BADGES ── */
.bdg{font-size:.7rem;font-weight:700;padding:.2rem .65rem;border-radius:20px;white-space:nowrap;border:1px solid}
.bdg-hadir{background:#EDFAF3;color:#1a6b3c;border-color:#A8E6C3}
.bdg-izin{background:rgba(0,146,180,.1);color:var(--primary-dark);border-color:rgba(0,146,180,.25)}
.bdg-sakit{background:rgba(255,152,0,.1);color:#a06800;border-color:rgba(255,152,0,.3)}
.bdg-alpha{background:#fff2f2;color:#842029;border-color:#f5c6cb}
.bdg-cuti{background:rgba(111,66,193,.1);color:#5a0fa0;border-color:rgba(111,66,193,.25)}
.bdg-libur{background:#f0f0f0;color:#555;border-color:#ccc}
.bdg-pending{background:var(--warn-bg);color:var(--warn-text);border-color:var(--warn-border)}
.bdg-approved{background:#EDFAF3;color:#1a6b3c;border-color:#A8E6C3}
.bdg-rejected{background:#fff2f2;color:#842029;border-color:#f5c6cb}

/* ── AVATAR ── */
.ava{width:30px;height:30px;border-radius:50%;background:var(--primary-light);border:1.5px solid var(--primary);display:inline-flex;align-items:center;justify-content:center;color:var(--primary);font-size:.72rem;font-weight:800;flex-shrink:0}

/* ── SEARCH ── */
.sbar{display:flex;gap:.6rem;align-items:center;margin-bottom:1rem;flex-wrap:wrap}
.srch-wrap{position:relative;flex:1;min-width:180px}
.srch-wrap i{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.82rem}
.srch-wrap input{padding-left:2.2rem;width:100%}

/* ── CALENDAR ── */
.cal-wrap{user-select:none}
.cal-nav{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem}
.cal-nav-btn{background:none;border:1.5px solid var(--border);border-radius:8px;width:32px;height:32px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .15s}
.cal-nav-btn:hover{border-color:var(--primary);color:var(--primary)}
.cal-month{font-size:.95rem;font-weight:800;color:var(--text)}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:2px}
.cal-head{text-align:center;font-size:.68rem;font-weight:700;color:var(--muted);padding:.4rem 0;text-transform:uppercase}
.cal-day{text-align:center;padding:.4rem .2rem;border-radius:8px;font-size:.78rem;font-weight:600;color:var(--text);cursor:default;position:relative;min-height:36px;transition:background .15s}
.cal-day.empty{background:transparent;cursor:default}
.cal-day.today{background:var(--primary);color:#fff;font-weight:800}
.cal-day.libur{background:#fff0f0;color:#842029}
.cal-day.weekend{background:#f8f4ff;color:#5a0fa0}
.cal-day:not(.empty):not(.today):hover{background:var(--primary-light)}
.cal-dot{width:5px;height:5px;border-radius:50%;background:currentColor;margin:1px auto 0;opacity:.6}

/* ── PROGRESS BAR ── */
.prog-wrap{background:var(--border);border-radius:99px;height:7px;overflow:hidden;margin-top:.3rem}
.prog-bar{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--primary),var(--primary-dark));transition:width .5s}

/* ── ACCESS NOTE ── */
.access-note{background:var(--warn-bg);border:1.5px solid var(--warn-border);border-radius:12px;padding:.85rem 1.1rem;font-size:.82rem;color:var(--warn-text);font-weight:500;margin-top:2rem}

@media(max-width:767.98px){
    .navbar-hrms{padding:0 1rem}
    .page{padding:1.25rem 1rem 3rem}
    .hide-xs{display:none}
    .modal-dialog{margin:.5rem}
}
</style>
</head>
<body>

{{-- ════ NAVBAR ════ --}}
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
                <div class="nav-role">Personalia</div>
            </div>
            <i class="bi bi-chevron-down hide-xs ms-1" style="color:var(--muted);font-size:.72rem"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-end mt-1">
            <li><button class="dd-item"><i class="bi bi-person-circle"></i> Profil Saya</button></li>
            <li><hr style="margin:.4rem 0;border-color:var(--border)"></li>
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

{{-- ════ CONTENT ════ --}}
<div class="page">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flash flash-success" id="flashMsg"><i class="bi bi-check-circle-fill flex-shrink-0"></i>{{ session('success') }}</div>
    @endif
    @if(session('error') || $errors->any())
    <div class="flash flash-error" id="flashMsg"><i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>{{ session('error') ?? $errors->first() }}</div>
    @endif

    <div class="page-head">
        <h1>Personalia</h1>
        <p>Data karyawan &amp; kehadiran — {{ $bulanLabel }}</p>
    </div>

    {{-- ════ STAT CARDS ════
         Semua diambil real-time dari DB:
         hadirHariIni   = COUNT(attendance) WHERE tanggal=today AND status='Hadir'
         belumAbsen     = total_karyawan_aktif - COUNT(attendance WHERE tanggal=today)
         lemburBulanIni = SUM(lembur.total_jam) WHERE status='Approved' AND bulan/tahun=now
         rataRataCuti   = AVG(saldo_cuti.sisa) WHERE tahun=now()->year
    --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="stat">
                <span class="stat-icon">✅</span>
                <div class="stat-value">{{ $hadirHariIni }}</div>
                <div class="stat-label">Hadir Hari Ini</div>
                <div class="stat-sub">dari {{ $totalKaryawan }} karyawan</div>
                {{-- Sumber: transaction.attendance WHERE tanggal=today() AND status='Hadir' --}}
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat">
                <span class="stat-icon">❌</span>
                <div class="stat-value" style="{{ $belumAbsen > 0 ? 'color:var(--danger)' : '' }}">{{ $belumAbsen }}</div>
                <div class="stat-label">Belum Absen</div>
                <div class="stat-sub">perlu tindak lanjut</div>
                {{-- Sumber: total_karyawan_aktif - COUNT(attendance WHERE tanggal=today()) --}}
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat">
                <span class="stat-icon">🌙</span>
                <div class="stat-value">{{ $lemburBulanIni }} <span style="font-size:1rem;font-weight:600">jam</span></div>
                <div class="stat-label">Lembur Bulan Ini</div>
                <div class="stat-sub">total semua karyawan</div>
                {{-- Sumber: SUM(lembur.total_jam) WHERE status='Approved' AND bulan/tahun=now --}}
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat">
                <span class="stat-icon">🗓️</span>
                <div class="stat-value">{{ $rataRataCuti }} <span style="font-size:1rem;font-weight:600">hari</span></div>
                <div class="stat-label">Sisa Cuti (avg)</div>
                <div class="stat-sub">rata-rata per orang</div>
                {{-- Sumber: AVG(saldo_cuti.sisa) WHERE tahun=now()->year --}}
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    <div class="d-flex flex-column gap-2 mb-1">
        @if($belumAbsen > 0)
        <div class="alert-banner alert-warn">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            <strong>{{ $belumAbsen }} karyawan</strong> belum tap absensi hari ini.
        </div>
        @else
        <div class="alert-banner alert-ok">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            Semua karyawan sudah absen hari ini. 
        </div>
        @endif

        @if($cutiPending > 0)
        <div class="alert-banner alert-info">
            <i class="bi bi-calendar-check flex-shrink-0"></i>
            <strong>{{ $cutiPending }} pengajuan cuti</strong> menunggu approval — buka menu Cuti &amp; Izin.
        </div>
        @endif

        @if($lemburPending > 0)
        <div class="alert-banner alert-warn">
            <i class="bi bi-moon-stars-fill flex-shrink-0"></i>
            <strong>{{ $lemburPending }} pengajuan lembur</strong> menunggu approval.
        </div>
        @endif
    </div>

    {{-- ════ MENU CARDS ════ --}}
    <p class="sec-label">Menu Tersedia</p>
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mKaryawan">
                <div class="menu-card-top"><span class="menu-icon">👥</span><span class="menu-badge badge-view">View</span></div>
                <div class="menu-title">Data Karyawan</div>
                <div class="menu-desc">Lihat profil, jabatan, cost center karyawan</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mAbsensi">
                <div class="menu-card-top"><span class="menu-icon">📅</span><span class="menu-badge badge-crud">CRUD</span></div>
                <div class="menu-title">Absensi Harian</div>
                <div class="menu-desc">Input, edit &amp; rekap kehadiran harian karyawan</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mCuti">
                <div class="menu-card-top"><span class="menu-icon">🗓️</span><span class="menu-badge badge-crud">CRUD</span></div>
                <div class="menu-title">Cuti &amp; Izin</div>
                <div class="menu-desc">Pengajuan, approval, &amp; saldo cuti tahunan</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mLembur">
                <div class="menu-card-top"><span class="menu-icon">🌙</span><span class="menu-badge badge-crud">CRUD</span></div>
                <div class="menu-title">Lembur</div>
                <div class="menu-desc">Pencatatan &amp; persetujuan lembur karyawan</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mRekap">
                <div class="menu-card-top"><span class="menu-icon">📊</span><span class="menu-badge badge-chart">Analitik</span></div>
                <div class="menu-title">Rekap Absensi</div>
                <div class="menu-desc">Laporan &amp; chart per departemen / cost center</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="menu-card" data-bs-toggle="modal" data-bs-target="#mJadwal">
                <div class="menu-card-top"><span class="menu-icon">📆</span><span class="menu-badge badge-cal">Kalender</span></div>
                <div class="menu-title">Jadwal Kerja</div>
                <div class="menu-desc">Shift, hari libur nasional &amp; kalender kerja</div>
            </div>
        </div>
    </div>

    <div class="access-note">
        <i class="bi bi-shield-lock-fill me-2" style="color:#F5A623"></i>
        <strong>Catatan akses:</strong> User Personalia hanya bisa lihat data karyawan (READ ONLY) &amp; mengelola absensi. <strong>Tidak bisa lihat nominal gaji.</strong>
    </div>

</div>{{-- /page --}}


{{-- ══════════════════════════════════════════════════════════
     MODAL 1 — DATA KARYAWAN (View Only)
     Sumber: employee.karyawan JOIN golongan, unit, position
     ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mKaryawan" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">👥 Data Karyawan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="alert-banner alert-info mb-3" style="font-size:.8rem">
            <i class="bi bi-info-circle-fill flex-shrink-0"></i>
            Read-only. Perubahan data karyawan dilakukan oleh Personal Administration.
        </div>
        <div class="sbar">
            <div class="srch-wrap">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" placeholder="Cari nama / NIP..." oninput="tblFilter(this,'tKar')">
            </div>
            <select class="form-select" style="width:auto;min-width:130px" onchange="filterSelect(this,'tKar',3)">
                <option value="">Semua Unit</option>
                @foreach($unitPts as $u)
                <option>{{ $u->nama_pt }}</option>
                @endforeach
            </select>
        </div>
        <div class="table-responsive">
        <table class="table tbl table-borderless" id="tKar">
            <thead><tr>
                <th>NIP</th><th>Nama Karyawan</th><th>Golongan</th><th>Unit / PT</th>
                <th>Jabatan Aktif</th><th>Dept / Cost Center</th>
                <th>Status</th><th>Tgl Masuk</th>
            </tr></thead>
            <tbody>
            @forelse($karyawans as $k)
            <tr>
                <td style="font-size:.72rem;font-weight:600;color:var(--muted)">{{ $k->nip }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="ava">{{ $k->inisial }}</div>
                        <div>
                            <div style="font-weight:700;font-size:.82rem">{{ $k->nama_karyawan }}</div>
                            <div style="font-size:.7rem;color:var(--muted)">{{ $k->email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td><span class="bdg bdg-izin">{{ $k->golongan->kode_golongan ?? '—' }}</span></td>
                <td style="font-size:.78rem">{{ $k->unit->nama_pt ?? '—' }}</td>
                <td style="font-size:.78rem;font-weight:600">{{ $k->currentPosition->nama_jabatan ?? '—' }}</td>
                <td style="font-size:.75rem;color:var(--muted)">{{ $k->currentPosition?->costCenter?->nama_cc ?? '—' }}</td>
                <td><span class="bdg bdg-hadir">Aktif</span></td>
                <td style="font-size:.75rem">{{ $k->tanggal_masuk?->format('d/m/Y') ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="tbl-empty"><i class="bi bi-inbox"></i>Belum ada data karyawan aktif.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
        {{ $karyawans->links() }}
    </div>
</div>
</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL 2 — ABSENSI HARIAN
     Sumber: transaction.attendance
     KOLOM: id_karyawan, tanggal, id_shift, status, check_in, check_out,
            terlambat_menit, keterangan, source
     STATUS: Hadir | Izin | Sakit | Alpha | Cuti | Libur
     ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mAbsensi" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">📅 Absensi Harian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="ctabs">
            <button class="ctab active" onclick="ctab(this,'abs-list')"><i class="bi bi-table me-1"></i>Hari Ini</button>
            <button class="ctab"        onclick="ctab(this,'abs-input')"><i class="bi bi-plus-circle me-1"></i>Input Absensi</button>
        </div>

        {{-- Tab: Hari Ini --}}
        <div id="abs-list" class="ctab-pane active">
            <div class="sbar">
                <div class="srch-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Cari nama..." oninput="tblFilter(this,'tAbs')">
                </div>
                <div style="font-size:.78rem;color:var(--muted);font-weight:600">
                    📅 {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-auto"><span class="bdg bdg-hadir">✅ Hadir: {{ $absensiHariIni->where('status','Hadir')->count() }}</span></div>
                <div class="col-auto"><span class="bdg bdg-izin">📋 Izin: {{ $absensiHariIni->where('status','Izin')->count() }}</span></div>
                <div class="col-auto"><span class="bdg bdg-sakit">🤒 Sakit: {{ $absensiHariIni->where('status','Sakit')->count() }}</span></div>
                <div class="col-auto"><span class="bdg bdg-alpha">❌ Alpha: {{ $absensiHariIni->where('status','Alpha')->count() }}</span></div>
                <div class="col-auto"><span class="bdg bdg-cuti">🏖️ Cuti: {{ $absensiHariIni->where('status','Cuti')->count() }}</span></div>
            </div>
            <div class="table-responsive">
            <table class="table tbl table-borderless" id="tAbs">
                <thead><tr>
                    <th>Karyawan</th><th>Shift</th><th>Status</th>
                    <th>Check In</th><th>Check Out</th><th>Terlambat</th>
                    <th>Keterangan</th><th>Source</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                @forelse($absensiHariIni as $a)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="ava">{{ $a->karyawan->inisial ?? 'NA' }}</div>
                            <div>
                                <div style="font-weight:700;font-size:.82rem">{{ $a->karyawan->nama_karyawan ?? '—' }}</div>
                                <div style="font-size:.7rem;color:var(--muted)">{{ $a->karyawan->nip ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.75rem">{{ $a->shift->nama_shift ?? '—' }}</td>
                    <td>
                        @php $bdgClass = ['Hadir'=>'bdg-hadir','Izin'=>'bdg-izin','Sakit'=>'bdg-sakit','Alpha'=>'bdg-alpha','Cuti'=>'bdg-cuti','Libur'=>'bdg-libur'][$a->status] ?? 'bdg-libur'; @endphp
                        <span class="bdg {{ $bdgClass }}">{{ $a->status }}</span>
                    </td>
                    <td style="font-weight:600;font-size:.82rem">{{ $a->check_in ?? '—' }}</td>
                    <td style="font-size:.82rem">{{ $a->check_out ?? '—' }}</td>
                    <td>
                        @if($a->terlambat_menit > 0)
                            <span style="color:var(--danger);font-weight:700;font-size:.78rem">{{ $a->terlambat_menit }} mnt</span>
                        @else
                            <span style="color:var(--muted);font-size:.75rem">—</span>
                        @endif
                    </td>
                    <td style="font-size:.75rem;color:var(--muted)">{{ Str::limit($a->keterangan, 30) ?? '—' }}</td>
                    <td><span class="bdg bdg-libur" style="font-size:.67rem">{{ $a->source }}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn-xs btn-xs-edit" onclick='fillAbsensi(@json($a))'><i class="bi bi-pencil"></i></button>
                            <form method="POST" action="{{ route('personalia.absensi.destroy', $a->id) }}" onsubmit="return confirm('Hapus record absensi?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-xs btn-xs-del"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="tbl-empty"><i class="bi bi-inbox"></i>Belum ada data absensi hari ini.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            {{ $absensiHariIni->links() }}
        </div>

        {{-- Tab: Input Absensi --}}
        <div id="abs-input" class="ctab-pane">
            <form method="POST" id="fAbsensi" action="{{ route('personalia.absensi.store') }}">
                @csrf
                <input type="hidden" name="_method" id="absMethod" value="POST">
                <input type="hidden" name="_abs_id" id="absId">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                        <select name="id_karyawan" id="absKar" class="form-select" required>
                            <option value="">— Pilih Karyawan —</option>
                            @foreach($karyawans as $k)
                            <option value="{{ $k->id }}">{{ $k->nip }} — {{ $k->nama_karyawan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="absTgl" class="form-control" value="{{ today()->toDateString() }}" required>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Shift</label>
                        <select name="id_shift" id="absShift" class="form-select">
                            <option value="">— Tanpa Shift —</option>
                            @foreach($shifts as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_shift }} ({{ $s->jam_masuk }} – {{ $s->jam_pulang }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="absStatus" class="form-select" required>
                            <option value="Hadir">✅ Hadir</option>
                            <option value="Izin">📋 Izin</option>
                            <option value="Sakit">🤒 Sakit</option>
                            <option value="Alpha">❌ Alpha</option>
                            <option value="Cuti">🏖️ Cuti</option>
                            <option value="Libur">🏠 Libur</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Check In</label>
                        <input type="time" name="check_in" id="absCheckIn" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Check Out</label>
                        <input type="time" name="check_out" id="absCheckOut" class="form-control">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Terlambat (menit)</label>
                        <input type="number" name="terlambat_menit" id="absTerlambat" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" id="absKet" class="form-control" placeholder="Surat sakit, izin keluarga...">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Source</label>
                        <select name="source" id="absSource" class="form-select">
                            <option value="manual">Manual</option>
                            <option value="fingerprint">Fingerprint</option>
                            <option value="mobile">Mobile</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan</button>
                    <button type="button" class="btn-outline-hrms" onclick="resetAbsensi()">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL 3 — CUTI & IZIN
     Sumber: transaction.cuti + transaction.saldo_cuti
     KOLOM: id_karyawan, jenis_cuti, tanggal_mulai, tanggal_selesai,
            jumlah_hari (GENERATED), alasan, status,
            approved_by, approved_at, catatan_approver
     STATUS: Pending | Approved | Rejected | Cancelled
     ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mCuti" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">🗓️ Cuti &amp; Izin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="ctabs">
            <button class="ctab active" onclick="ctab(this,'cut-list')"><i class="bi bi-table me-1"></i>Daftar Pengajuan</button>
            <button class="ctab"        onclick="ctab(this,'cut-form')"><i class="bi bi-plus-circle me-1"></i>Ajukan Cuti</button>
        </div>

        <div id="cut-list" class="ctab-pane active">
            @if($cutiPending > 0)
            <div class="alert-banner alert-warn mb-3" style="font-size:.82rem">
                <i class="bi bi-clock-fill flex-shrink-0"></i>
                <strong>{{ $cutiPending }} pengajuan</strong> menunggu approval.
            </div>
            @endif
            <div class="sbar">
                <div class="srch-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Cari nama karyawan..." oninput="tblFilter(this,'tCut')">
                </div>
                <select class="form-select" style="width:auto;min-width:120px" onchange="filterSelect(this,'tCut',5)">
                    <option value="">Semua Status</option>
                    <option>Pending</option><option>Approved</option>
                    <option>Rejected</option><option>Cancelled</option>
                </select>
                <button class="btn-primary-hrms" onclick="ctabById('cut-form')"><i class="bi bi-plus-lg"></i> Ajukan</button>
            </div>
            <div class="table-responsive">
            <table class="table tbl table-borderless" id="tCut">
                <thead><tr>
                    <th>Karyawan</th><th>Jenis</th><th>Mulai</th><th>Selesai</th>
                    <th>Hari</th><th>Status</th><th>Alasan</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                @forelse($cutis as $c)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="ava">{{ $c->karyawan->inisial ?? 'NA' }}</div>
                            <div>
                                <div style="font-weight:700;font-size:.82rem">{{ $c->karyawan->nama_karyawan ?? '—' }}</div>
                                <div style="font-size:.7rem;color:var(--muted)">{{ $c->karyawan->golongan->kode_golongan ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="bdg bdg-cuti">{{ $c->jenis_cuti }}</span></td>
                    <td style="font-size:.78rem">{{ $c->tanggal_mulai?->format('d/m/Y') }}</td>
                    <td style="font-size:.78rem">{{ $c->tanggal_selesai?->format('d/m/Y') }}</td>
                    <td style="font-weight:700;color:var(--primary)">{{ $c->jumlah_hari }} <span style="font-size:.72rem;font-weight:500;color:var(--muted)">hari</span></td>
                    <td>
                        <span class="bdg {{ $c->status==='Approved' ? 'bdg-approved' : ($c->status==='Rejected' ? 'bdg-rejected' : 'bdg-pending') }}">
                            {{ $c->status }}
                        </span>
                    </td>
                    <td style="font-size:.75rem;color:var(--muted)">{{ Str::limit($c->alasan, 30) ?? '—' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($c->status === 'Pending')
                            <form method="POST" action="{{ route('personalia.cuti.approve', $c->id) }}" class="d-inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="Approved">
                                <button type="submit" class="btn-xs btn-xs-ok" title="Approve"><i class="bi bi-check-lg"></i></button>
                            </form>
                            <form method="POST" action="{{ route('personalia.cuti.approve', $c->id) }}" class="d-inline" onsubmit="return confirm('Tolak pengajuan cuti ini?')">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="Rejected">
                                <button type="submit" class="btn-xs btn-xs-rej" title="Reject"><i class="bi bi-x-lg"></i></button>
                            </form>
                            <form method="POST" action="{{ route('personalia.cuti.destroy', $c->id) }}" onsubmit="return confirm('Hapus pengajuan?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-xs btn-xs-del"><i class="bi bi-trash"></i></button>
                            </form>
                            @else
                            <span style="font-size:.72rem;color:var(--muted)">{{ $c->created_at?->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="tbl-empty"><i class="bi bi-inbox"></i>Belum ada pengajuan cuti.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            {{ $cutis->links() }}
        </div>

        <div id="cut-form" class="ctab-pane">
            <form method="POST" action="{{ route('personalia.cuti.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                        <select name="id_karyawan" class="form-select" required>
                            <option value="">— Pilih Karyawan —</option>
                            @foreach($karyawans as $k)
                            <option value="{{ $k->id }}">{{ $k->nip }} — {{ $k->nama_karyawan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                        <select name="jenis_cuti" class="form-select" required>
                            <option value="Tahunan">Tahunan</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Melahirkan">Melahirkan</option>
                            <option value="Khusus">Khusus</option>
                            <option value="Besar">Besar</option>
                        </select>
                    </div>
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-control" required>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_selesai" class="form-control" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Alasan</label>
                        <input type="text" name="alasan" class="form-control" placeholder="Keperluan keluarga, melahirkan...">
                    </div>
                </div>
                <div class="alert-banner alert-info mt-3" style="font-size:.78rem">
                    <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                    Jumlah hari dihitung otomatis dari DB: <strong>tanggal_selesai − tanggal_mulai + 1</strong>. Status awal = <strong>Pending</strong>.
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Ajukan Cuti</button>
                    <button type="button" class="btn-outline-hrms" onclick="ctabById('cut-list')">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL 4 — LEMBUR
     Sumber: transaction.lembur
     KOLOM: id_karyawan, tanggal, jam_mulai, jam_selesai, total_jam (app layer),
            keterangan, status, approved_by, approved_at,
            nominal_lembur, sudah_dibayar
     STATUS: Pending | Approved | Rejected
     ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mLembur" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">🌙 Lembur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="ctabs">
            <button class="ctab active" onclick="ctab(this,'lem-list')"><i class="bi bi-table me-1"></i>Daftar Lembur</button>
            <button class="ctab"        onclick="ctab(this,'lem-form')"><i class="bi bi-plus-circle me-1"></i>Catat Lembur</button>
        </div>

        <div id="lem-list" class="ctab-pane active">
            <div class="sbar">
                <div class="srch-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Cari nama karyawan..." oninput="tblFilter(this,'tLem')">
                </div>
                <select class="form-select" style="width:auto;min-width:120px" onchange="filterSelect(this,'tLem',5)">
                    <option value="">Semua Status</option>
                    <option>Pending</option><option>Approved</option><option>Rejected</option>
                </select>
                <button class="btn-primary-hrms" onclick="ctabById('lem-form')"><i class="bi bi-plus-lg"></i> Catat</button>
            </div>
            <div class="table-responsive">
            <table class="table tbl table-borderless" id="tLem">
                <thead><tr>
                    <th>Karyawan</th><th>Tanggal</th><th>Jam Mulai</th>
                    <th>Jam Selesai</th><th>Total Jam</th><th>Status</th>
                    <th>Nominal</th><th>Keterangan</th><th>Aksi</th>
                </tr></thead>
                <tbody>
                @forelse($lemburs as $l)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="ava">{{ $l->karyawan->inisial ?? 'NA' }}</div>
                            <div>
                                <div style="font-weight:700;font-size:.82rem">{{ $l->karyawan->nama_karyawan ?? '—' }}</div>
                                <div style="font-size:.7rem;color:var(--muted)">{{ $l->karyawan->golongan->kode_golongan ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.78rem">{{ $l->tanggal?->format('d/m/Y') }}</td>
                    <td style="font-weight:600;font-size:.82rem">{{ $l->jam_mulai }}</td>
                    <td style="font-size:.82rem">{{ $l->jam_selesai }}</td>
                    <td style="font-weight:800;color:var(--primary)">{{ $l->total_jam }} <span style="font-size:.72rem;font-weight:500;color:var(--muted)">jam</span></td>
                    <td>
                        <span class="bdg {{ $l->status==='Approved' ? 'bdg-approved' : ($l->status==='Rejected' ? 'bdg-rejected' : 'bdg-pending') }}">
                            {{ $l->status }}
                        </span>
                    </td>
                    <td style="font-size:.78rem">
                        @if($l->nominal_lembur)
                            <span style="font-weight:700;color:var(--success)">Rp {{ number_format($l->nominal_lembur, 0, ',', '.') }}</span>
                        @else
                            <span style="color:var(--muted)">—</span>
                        @endif
                    </td>
                    <td style="font-size:.75rem;color:var(--muted)">{{ Str::limit($l->keterangan, 25) ?? '—' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            @if($l->status === 'Pending')
                            <form method="POST" action="{{ route('personalia.lembur.approve', $l->id) }}" class="d-inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="Approved">
                                <button type="submit" class="btn-xs btn-xs-ok"><i class="bi bi-check-lg"></i></button>
                            </form>
                            <form method="POST" action="{{ route('personalia.lembur.approve', $l->id) }}" class="d-inline" onsubmit="return confirm('Tolak lembur?')">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="Rejected">
                                <button type="submit" class="btn-xs btn-xs-rej"><i class="bi bi-x-lg"></i></button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('personalia.lembur.destroy', $l->id) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-xs btn-xs-del"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="tbl-empty"><i class="bi bi-inbox"></i>Belum ada data lembur.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            {{ $lemburs->links() }}
        </div>

        <div id="lem-form" class="ctab-pane">
            <form method="POST" action="{{ route('personalia.lembur.store') }}">
                @csrf
                <div class="alert-banner alert-info mb-3" style="font-size:.82rem">
                    <i class="bi bi-info-circle-fill flex-shrink-0"></i>
                    Total jam dihitung otomatis dari jam_mulai &amp; jam_selesai. Status awal = <strong>Pending</strong>.
                </div>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Karyawan <span class="text-danger">*</span></label>
                        <select name="id_karyawan" class="form-select" required>
                            <option value="">— Pilih Karyawan —</option>
                            @foreach($karyawans as $k)
                            <option value="{{ $k->id }}">{{ $k->nip }} — {{ $k->nama_karyawan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ today()->toDateString() }}" required>
                    </div>
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3">
                        <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" id="lemJamMulai" class="form-control" required oninput="hitungJam()">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_selesai" id="lemJamSelesai" class="form-control" required oninput="hitungJam()">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Total Jam (preview)</label>
                        <input type="text" id="lemTotalPreview" class="form-control" placeholder="—" readonly style="background:#f8f9fa;font-weight:700;color:var(--primary)">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Lembur produksi, deadline project...">
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-primary-hrms"><i class="bi bi-check-lg"></i> Simpan Lembur</button>
                    <button type="button" class="btn-outline-hrms" onclick="ctabById('lem-list')">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL 5 — REKAP ABSENSI (CHART DASHBOARD)
     Sumber:
       - statusTodayJson : attendance hari ini, groupBy status
       - rekapBulanJson  : attendance bulan ini, groupBy status
       - trenHadirJson   : attendance 7 hari, count hadir per hari
       - rekapPerDept    : per karyawan bulan ini (total_hadir, total_terlambat, total_alpha)
     ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mRekap" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">📊 Rekap Absensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="ctabs">
            <button class="ctab active" onclick="ctab(this,'rek-chart')"><i class="bi bi-bar-chart-fill me-1"></i>Dashboard</button>
            <button class="ctab"        onclick="ctab(this,'rek-tbl')"><i class="bi bi-table me-1"></i>Detail Karyawan</button>
        </div>

        {{-- Tab: Chart Dashboard --}}
        <div id="rek-chart" class="ctab-pane active">
            <div class="row g-3 mb-3">
                {{-- Pie: Status Hari Ini --}}
                <div class="col-md-4">
                    <div class="chart-card">
                        <div class="chart-title">🎯 Status Kehadiran Hari Ini</div>
                        <div class="chart-wrap"><canvas id="chartPieToday"></canvas></div>
                    </div>
                </div>
                {{-- Pie: Rekap Bulan Ini --}}
                <div class="col-md-4">
                    <div class="chart-card">
                        <div class="chart-title">📊 Rekap Bulan {{ $bulanLabel }}</div>
                        <div class="chart-wrap"><canvas id="chartPieBulan"></canvas></div>
                    </div>
                </div>
                {{-- Donut: Hadir vs Tidak --}}
                <div class="col-md-4">
                    <div class="chart-card">
                        <div class="chart-title">✅ Kehadiran vs Ketidakhadiran</div>
                        <div class="chart-wrap"><canvas id="chartDonut"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- Bar: Tren 7 Hari --}}
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="chart-card">
                        <div class="chart-title">📈 Tren Kehadiran 7 Hari Terakhir</div>
                        <div class="chart-wrap-tall"><canvas id="chartBar7"></canvas></div>
                    </div>
                </div>
                {{-- Mini stats --}}
                <div class="col-md-4">
                    <div class="chart-card h-100">
                        <div class="chart-title">⚡ Statistik Cepat — {{ $bulanLabel }}</div>
                        <div class="d-flex flex-column gap-3 mt-2">
                            @php
                                $rb = json_decode($rekapBulanJson, true);
                                $totalRb = array_sum($rb);
                                $hadirRb = $rb['Hadir'] ?? 0;
                                $alphaRb = $rb['Alpha'] ?? 0;
                                $sakitRb = $rb['Sakit'] ?? 0;
                                $pctHadir = $totalRb > 0 ? round($hadirRb/$totalRb*100) : 0;
                                $pctAlpha = $totalRb > 0 ? round($alphaRb/$totalRb*100) : 0;
                            @endphp
                            <div>
                                <div class="d-flex justify-content-between">
                                    <span style="font-size:.8rem;font-weight:700">✅ Tingkat Kehadiran</span>
                                    <span style="font-size:.8rem;font-weight:800;color:var(--primary)">{{ $pctHadir }}%</span>
                                </div>
                                <div class="prog-wrap"><div class="prog-bar" style="width:{{ $pctHadir }}%"></div></div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between">
                                    <span style="font-size:.8rem;font-weight:700">❌ Alpha / Bolos</span>
                                    <span style="font-size:.8rem;font-weight:800;color:var(--danger)">{{ $pctAlpha }}%</span>
                                </div>
                                <div class="prog-wrap"><div class="prog-bar" style="width:{{ $pctAlpha }}%;background:linear-gradient(90deg,#dc3545,#b02a37)"></div></div>
                            </div>
                            <hr style="border-color:var(--border);margin:.25rem 0">
                            <div class="row g-2 text-center">
                                <div class="col-6">
                                    <div style="font-size:1.4rem;font-weight:800;color:var(--primary)">{{ $hadirRb }}</div>
                                    <div style="font-size:.7rem;color:var(--muted);font-weight:600">Total Hadir</div>
                                </div>
                                <div class="col-6">
                                    <div style="font-size:1.4rem;font-weight:800;color:var(--danger)">{{ $alphaRb }}</div>
                                    <div style="font-size:.7rem;color:var(--muted);font-weight:600">Total Alpha</div>
                                </div>
                                <div class="col-6">
                                    <div style="font-size:1.4rem;font-weight:800;color:#a06800">{{ $sakitRb }}</div>
                                    <div style="font-size:.7rem;color:var(--muted);font-weight:600">Total Sakit</div>
                                </div>
                                <div class="col-6">
                                    <div style="font-size:1.4rem;font-weight:800;color:#5a0fa0">{{ $lemburBulanIni }}</div>
                                    <div style="font-size:.7rem;color:var(--muted);font-weight:600">Jam Lembur</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Detail Karyawan --}}
        <div id="rek-tbl" class="ctab-pane">
            <div class="sbar">
                <div class="srch-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Cari nama..." oninput="tblFilter(this,'tRek')">
                </div>
            </div>
            <div class="table-responsive">
            <table class="table tbl table-borderless" id="tRek">
                <thead><tr>
                    <th>Karyawan</th><th>Unit</th><th>Total Hadir</th>
                    <th>Total Terlambat</th><th>Alpha</th><th>Tingkat Hadir</th>
                </tr></thead>
                <tbody>
                @forelse($rekapPerDept as $r)
                @php
                    $hariKerja = now()->daysInMonth;
                    $pct = $hariKerja > 0 ? round($r->total_hadir / $hariKerja * 100) : 0;
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="ava">{{ $r->karyawan->inisial ?? 'NA' }}</div>
                            <div>
                                <div style="font-weight:700;font-size:.82rem">{{ $r->karyawan->nama_karyawan ?? '—' }}</div>
                                <div style="font-size:.7rem;color:var(--muted)">{{ $r->karyawan->golongan->kode_golongan ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.78rem">{{ $r->karyawan->unit->nama_pt ?? '—' }}</td>
                    <td style="font-weight:700;color:var(--primary)">{{ $r->total_hadir }} <span style="font-size:.72rem;color:var(--muted);font-weight:500">/ {{ $hariKerja }}</span></td>
                    <td style="color:var(--danger);font-size:.8rem">{{ $r->total_terlambat ?? 0 }} mnt</td>
                    <td>
                        @if(($r->total_alpha ?? 0) > 0)
                            <span style="font-weight:800;color:var(--danger)">{{ $r->total_alpha }}</span>
                        @else
                            <span style="color:var(--muted)">0</span>
                        @endif
                    </td>
                    <td style="min-width:100px">
                        <div class="d-flex align-items-center gap-2">
                            <div style="font-size:.78rem;font-weight:700;width:35px;flex-shrink:0;color:{{ $pct >= 90 ? 'var(--success)' : ($pct >= 75 ? '#a06800' : 'var(--danger)') }}">{{ $pct }}%</div>
                            <div class="prog-wrap flex-grow-1">
                                <div class="prog-bar" style="width:{{ $pct }}%;background:{{ $pct >= 90 ? 'linear-gradient(90deg,var(--success),#146c43)' : ($pct >= 75 ? 'linear-gradient(90deg,#F5A623,#a06800)' : 'linear-gradient(90deg,var(--danger),#b02a37)') }}"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="tbl-empty"><i class="bi bi-inbox"></i>Belum ada data rekap bulan ini.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
            {{ $rekapPerDept->links() }}
        </div>
    </div>
</div>
</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL 6 — JADWAL KERJA (KALENDER INTERAKTIF)
     Sumber:
       - master.shift_kerja (daftar shift aktif)
       - master.hari_libur  (hari libur nasional tahun ini)
     ══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mJadwal" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">📆 Jadwal Kerja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="ctabs">
            <button class="ctab active" onclick="ctab(this,'jdw-cal')"><i class="bi bi-calendar3 me-1"></i>Kalender</button>
            <button class="ctab"        onclick="ctab(this,'jdw-shift')"><i class="bi bi-clock me-1"></i>Daftar Shift</button>
            <button class="ctab"        onclick="ctab(this,'jdw-libur')"><i class="bi bi-flag me-1"></i>Hari Libur</button>
        </div>

        {{-- Tab: Kalender --}}
        <div id="jdw-cal" class="ctab-pane active">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="chart-card cal-wrap" id="calContainer">
                        <div class="cal-nav">
                            <button class="cal-nav-btn" onclick="calPrev()"><i class="bi bi-chevron-left"></i></button>
                            <div class="cal-month" id="calMonthLabel"></div>
                            <button class="cal-nav-btn" onclick="calNext()"><i class="bi bi-chevron-right"></i></button>
                        </div>
                        <div class="cal-grid" id="calGrid"></div>
                        <div class="d-flex gap-3 mt-3 flex-wrap" style="font-size:.72rem;font-weight:600">
                            <span><span style="display:inline-block;width:12px;height:12px;background:var(--primary);border-radius:50%;margin-right:4px"></span>Hari Ini</span>
                            <span><span style="display:inline-block;width:12px;height:12px;background:#fff0f0;border:1px solid #f5c6cb;border-radius:3px;margin-right:4px"></span>Hari Libur</span>
                            <span><span style="display:inline-block;width:12px;height:12px;background:#f8f4ff;border:1px solid #d8b4fe;border-radius:3px;margin-right:4px"></span>Sabtu/Minggu</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="chart-card">
                        <div class="chart-title">📋 Detail Tanggal Dipilih</div>
                        <div id="calDetail" style="font-size:.82rem;color:var(--muted)">
                            Klik tanggal di kalender untuk lihat detail.
                        </div>
                    </div>
                    <div class="chart-card mt-3">
                        <div class="chart-title">🏖️ Hari Libur Bulan Ini</div>
                        <div id="liburBulanIni" style="font-size:.8rem"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Daftar Shift --}}
        <div id="jdw-shift" class="ctab-pane">
            <div class="table-responsive">
            <table class="table tbl table-borderless">
                <thead><tr>
                    <th>Nama Shift</th><th>Jam Masuk</th><th>Jam Pulang</th>
                    <th>Durasi</th><th>Toleransi Terlambat</th><th>Status</th>
                </tr></thead>
                <tbody>
                @forelse($shifts as $s)
                @php
                    $masuk  = \Carbon\Carbon::parse('2000-01-01 '.$s->jam_masuk);
                    $pulang = \Carbon\Carbon::parse('2000-01-01 '.$s->jam_pulang);
                    $durasi = $masuk->diffInHours($pulang);
                @endphp
                <tr>
                    <td style="font-weight:700">{{ $s->nama_shift }}</td>
                    <td style="font-weight:700;color:var(--primary)">{{ $s->jam_masuk }}</td>
                    <td>{{ $s->jam_pulang }}</td>
                    <td>{{ $durasi }} jam</td>
                    <td>{{ $s->toleransi_menit }} menit</td>
                    <td><span class="bdg {{ $s->is_active ? 'bdg-hadir' : 'bdg-rejected' }}">{{ $s->is_active ? 'Aktif' : 'Non-aktif' }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="tbl-empty"><i class="bi bi-inbox"></i>Belum ada shift. Tambahkan di Master System.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div>

        {{-- Tab: Hari Libur --}}
        <div id="jdw-libur" class="ctab-pane">
            <div class="table-responsive">
            <table class="table tbl table-borderless">
                <thead><tr>
                    <th>Tanggal</th><th>Hari</th><th>Keterangan</th>
                </tr></thead>
                <tbody>
                @forelse($hariLiburs as $h)
                <tr>
                    <td style="font-weight:700;color:var(--danger)">{{ $h->tanggal->format('d/m/Y') }}</td>
                    <td style="font-size:.78rem">{{ $h->tanggal->translatedFormat('l') }}</td>
                    <td>{{ $h->keterangan }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="tbl-empty"><i class="bi bi-inbox"></i>Belum ada data hari libur {{ $tahun }}.</td></tr>
                @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Data dari PHP/DB ───────────────────────────────────────────
const STATUS_TODAY  = @json(json_decode($statusTodayJson, true) ?? []);
const REKAP_BULAN   = @json(json_decode($rekapBulanJson, true) ?? []);
const TREN_HADIR    = @json(json_decode($trenHadirJson, true) ?? []);
const HARI_LIBURS   = @json($hariLiburs->pluck('keterangan','tanggal'));
// Format: {"2025-01-01":"Tahun Baru","2025-03-31":"Hari Raya Idul Fitri", ...}

const WARNA = {
    Hadir:  '#22c55e', Izin:  '#0092B4', Sakit: '#F5A623',
    Alpha:  '#dc3545', Cuti:  '#7c3aed', Libur: '#94a3b8'
};

// ── Custom Tab ─────────────────────────────────────────────────
function ctab(btn, id) {
    const mc = btn.closest('.modal-content') || document.body;
    mc.querySelectorAll('.ctab').forEach(t => t.classList.remove('active'));
    mc.querySelectorAll('.ctab-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    mc.querySelector('#'+id)?.classList.add('active');

    // Init chart hanya saat tab rekap dibuka pertama kali
    if (id === 'rek-chart' && !window._chartsInit) { initCharts(); window._chartsInit = true; }
    if (id === 'jdw-cal'   && !window._calInit)    { initCalendar(); window._calInit = true; }
}
function ctabById(id) {
    const el = document.getElementById(id); if (!el) return;
    const mc = el.closest('.modal-content');
    mc.querySelectorAll('.ctab-pane').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    mc.querySelectorAll('.ctab').forEach(b => {
        const oc = b.getAttribute('onclick') || '';
        b.classList.toggle('active', oc.includes("'"+id+"'") || oc.includes('"'+id+'"'));
    });
}

// ── Init charts on Rekap modal open ───────────────────────────
document.getElementById('mRekap').addEventListener('shown.bs.modal', () => {
    if (!window._chartsInit) { initCharts(); window._chartsInit = true; }
});
document.getElementById('mJadwal').addEventListener('shown.bs.modal', () => {
    if (!window._calInit) { initCalendar(); window._calInit = true; }
});

function initCharts() {
    const labels  = Object.keys(STATUS_TODAY);
    const values  = Object.values(STATUS_TODAY);
    const colors  = labels.map(l => WARNA[l] || '#ccc');

    const labelsBulan  = Object.keys(REKAP_BULAN);
    const valuesBulan  = Object.values(REKAP_BULAN);
    const colorsBulan  = labelsBulan.map(l => WARNA[l] || '#ccc');

    // Chart 1: Pie hari ini
    new Chart(document.getElementById('chartPieToday'), {
        type: 'doughnut',
        data: {
            labels, datasets: [{
                data: values, backgroundColor: colors,
                borderWidth: 2, borderColor: '#fff'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11, family: "'Plus Jakarta Sans'" }, padding: 12 } }
            }
        }
    });

    // Chart 2: Pie bulan ini
    new Chart(document.getElementById('chartPieBulan'), {
        type: 'pie',
        data: {
            labels: labelsBulan, datasets: [{
                data: valuesBulan, backgroundColor: colorsBulan,
                borderWidth: 2, borderColor: '#fff'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11, family: "'Plus Jakarta Sans'" }, padding: 10 } }
            }
        }
    });

    // Chart 3: Donut Hadir vs Tidak
    const hadir = REKAP_BULAN['Hadir'] || 0;
    const tidak = Object.entries(REKAP_BULAN).filter(([k]) => k !== 'Hadir' && k !== 'Libur').reduce((s,[,v]) => s+v, 0);
    new Chart(document.getElementById('chartDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Tidak Hadir'],
            datasets: [{
                data: [hadir, tidak],
                backgroundColor: ['#22c55e', '#dc3545'],
                borderWidth: 3, borderColor: '#fff',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 12, family: "'Plus Jakarta Sans'", weight: '700' }, padding: 16 } }
            }
        }
    });

    // Chart 4: Bar 7 hari
    const trenLabels = Object.keys(TREN_HADIR).map(d => {
        const dt = new Date(d);
        return dt.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
    });
    const trenValues = Object.values(TREN_HADIR);
    new Chart(document.getElementById('chartBar7'), {
        type: 'bar',
        data: {
            labels: trenLabels,
            datasets: [{
                label: 'Jumlah Hadir',
                data: trenValues,
                backgroundColor: 'rgba(0,146,180,.75)',
                borderColor: '#0092B4',
                borderWidth: 1.5,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { font: { size: 11 } }, grid: { color: 'rgba(0,0,0,.06)' } },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });
}

// ── KALENDER ───────────────────────────────────────────────────
let calYear  = new Date().getFullYear();
let calMonth = new Date().getMonth(); // 0-based

function initCalendar() { renderCalendar(calYear, calMonth); }
function calPrev() { calMonth--; if (calMonth < 0) { calMonth = 11; calYear--; } renderCalendar(calYear, calMonth); }
function calNext() { calMonth++; if (calMonth > 11) { calMonth = 0;  calYear++; } renderCalendar(calYear, calMonth); }

function renderCalendar(year, month) {
    const days = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    document.getElementById('calMonthLabel').textContent = months[month] + ' ' + year;

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month+1, 0).getDate();
    const today = new Date();

    const grid = document.getElementById('calGrid');
    grid.innerHTML = '';

    // Header
    days.forEach(d => {
        const h = document.createElement('div');
        h.className = 'cal-head'; h.textContent = d; grid.appendChild(h);
    });

    // Empty cells
    for (let i = 0; i < firstDay; i++) {
        const e = document.createElement('div'); e.className = 'cal-day empty'; grid.appendChild(e);
    }

    // Libur set
    const liburSet = new Set(Object.keys(HARI_LIBURS));

    // Days
    for (let d = 1; d <= daysInMonth; d++) {
        const cell = document.createElement('div');
        const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const dayOfWeek = new Date(year, month, d).getDay();
        const isToday = d === today.getDate() && month === today.getMonth() && year === today.getFullYear();
        const isLibur = liburSet.has(dateStr);
        const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;

        cell.className = 'cal-day' + (isToday ? ' today' : '') + (isLibur ? ' libur' : '') + (!isToday && isWeekend ? ' weekend' : '');
        cell.innerHTML = d + (isLibur ? '<div class="cal-dot"></div>' : '');
        cell.onclick = () => showCalDetail(dateStr, d, dayOfWeek, isLibur, isWeekend);
        grid.appendChild(cell);
    }

    // Update libur bulan ini panel
    const bulanLibur = Object.entries(HARI_LIBURS).filter(([k]) => k.startsWith(`${year}-${String(month+1).padStart(2,'0')}`));
    const panel = document.getElementById('liburBulanIni');
    if (bulanLibur.length === 0) {
        panel.innerHTML = '<span style="color:var(--muted)">Tidak ada hari libur bulan ini.</span>';
    } else {
        panel.innerHTML = bulanLibur.map(([k,v]) => `
            <div class="d-flex gap-2 align-items-center mb-2">
                <span style="font-weight:700;color:var(--danger);min-width:70px">${new Date(k).toLocaleDateString('id-ID',{day:'numeric',month:'short'})}</span>
                <span>${v}</span>
            </div>`).join('');
    }
}

function showCalDetail(dateStr, day, dow, isLibur, isWeekend) {
    const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const liburKet = HARI_LIBURS[dateStr] || null;
    const [y, m, d2] = dateStr.split('-');
    const tgl = `${days[dow]}, ${d2}/${m}/${y}`;

    let html = `<div style="font-weight:800;font-size:.95rem;margin-bottom:.75rem">${tgl}</div>`;

    if (liburKet) {
        html += `<div class="alert-banner alert-warn" style="font-size:.78rem;margin-bottom:.75rem"><i class="bi bi-flag-fill flex-shrink-0"></i><strong>Hari Libur:</strong> ${liburKet}</div>`;
    } else if (isWeekend) {
        html += `<div class="alert-banner" style="background:#f8f4ff;border-color:#d8b4fe;color:#5a0fa0;font-size:.78rem;margin-bottom:.75rem"><i class="bi bi-house-fill flex-shrink-0"></i>Hari Libur (Weekend)</div>`;
    } else {
        html += `<div class="alert-banner alert-ok" style="font-size:.78rem;margin-bottom:.75rem"><i class="bi bi-briefcase-fill flex-shrink-0"></i>Hari Kerja Normal</div>`;
    }

    // Show shifts aktif
    html += `<div style="font-size:.78rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.5rem">Shift Tersedia</div>`;
    const shifts = document.querySelectorAll('#jdw-shift tbody tr');
    if (shifts.length > 0 && shifts[0].cells.length > 1) {
        shifts.forEach(r => {
            if (r.cells.length > 1) {
                html += `<div class="d-flex justify-content-between align-items-center mb-1 p-2" style="background:var(--primary-light);border-radius:8px">
                    <span style="font-weight:700;font-size:.8rem">${r.cells[0]?.textContent?.trim()}</span>
                    <span style="font-size:.78rem;color:var(--primary)">${r.cells[1]?.textContent?.trim()} – ${r.cells[2]?.textContent?.trim()}</span>
                </div>`;
            }
        });
    } else {
        html += `<span style="color:var(--muted);font-size:.78rem">Belum ada shift dikonfigurasi.</span>`;
    }

    document.getElementById('calDetail').innerHTML = html;
}

// ── Table Filter ───────────────────────────────────────────────
function tblFilter(input, tblId) {
    const q = input.value.toLowerCase();
    document.querySelectorAll('#'+tblId+' tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
function filterSelect(sel, tblId, colIdx) {
    const q = sel.value.toLowerCase();
    document.querySelectorAll('#'+tblId+' tbody tr').forEach(r => {
        const cell = r.cells[colIdx];
        r.style.display = (!q || cell?.textContent?.toLowerCase().includes(q)) ? '' : 'none';
    });
}

// ── Fill Absensi Edit Form ──────────────────────────────────────
function fillAbsensi(d) {
    document.getElementById('absMethod').value  = 'PUT';
    document.getElementById('absId').value      = d.id;
    document.getElementById('absKar').value     = d.id_karyawan;
    document.getElementById('absTgl').value     = d.tanggal?.substring(0,10) ?? '';
    document.getElementById('absShift').value   = d.id_shift ?? '';
    document.getElementById('absStatus').value  = d.status;
    document.getElementById('absCheckIn').value = d.check_in ?? '';
    document.getElementById('absCheckOut').value= d.check_out ?? '';
    document.getElementById('absTerlambat').value = d.terlambat_menit ?? 0;
    document.getElementById('absKet').value     = d.keterangan ?? '';
    document.getElementById('absSource').value  = d.source ?? 'manual';
    // Switch action ke PUT endpoint
    document.getElementById('fAbsensi').action  = '/personalia/absensi/' + d.id;
    ctabById('abs-input');
}
function resetAbsensi() {
    document.getElementById('absMethod').value = 'POST';
    document.getElementById('fAbsensi').reset();
    document.getElementById('fAbsensi').action = '{{ route("personalia.absensi.store") }}';
    ctabById('abs-list');
}

// ── Lembur: hitung preview total jam ──────────────────────────
function hitungJam() {
    const m = document.getElementById('lemJamMulai').value;
    const s = document.getElementById('lemJamSelesai').value;
    if (!m || !s) { document.getElementById('lemTotalPreview').value = '—'; return; }
    const [hm, mm] = m.split(':').map(Number);
    const [hs, ms2] = s.split(':').map(Number);
    const diff = (hs * 60 + ms2) - (hm * 60 + mm);
    if (diff <= 0) { document.getElementById('lemTotalPreview').value = 'Cek jam!'; return; }
    const jam = Math.floor(diff / 60);
    const mnt = diff % 60;
    document.getElementById('lemTotalPreview').value = jam + 'j ' + (mnt > 0 ? mnt + 'm' : '');
}

// ── Auto dismiss flash ─────────────────────────────────────────
setTimeout(() => {
    const el = document.getElementById('flashMsg');
    if (el) { el.style.transition = 'opacity .5s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 500); }
}, 5000);
</script>
</body>
</html>
