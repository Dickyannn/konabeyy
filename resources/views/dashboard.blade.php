<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard — HR Portal</title>

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #0092B4;
            --primary-dark: #007A98;
            --text-dark: #1A2E3B;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.3rem;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        .container-main {
            padding: 2rem 0;
        }

        .card-dashboard {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .card-dashboard .card-body {
            padding: 2rem;
        }

        .badge-role {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .info-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
        }

        .info-item-label {
            font-size: 0.875rem;
            color: #6b8896;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .info-item-value {
            font-size: 1rem;
            color: var(--text-dark);
            font-weight: 700;
        }
    </style>
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-dark">
    <div class="container-lg">
        <span class="navbar-brand">
            <i class="bi bi-briefcase-fill me-2"></i>HR Portal
        </span>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white small">
                <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->nama }}
            </span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-logout btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- Main Content --}}
<div class="container-lg container-main">
    <div class="row">
        <div class="col-12">
            <div class="card card-dashboard">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="h3 mb-1">Selamat Datang, {{ auth()->user()->nama }}! 👋</h1>
                            <p class="text-muted mb-0">Sistem Informasi Manajemen Sumber Daya Manusia</p>
                        </div>
                        <span class="badge badge-role" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white;">
                            {{ auth()->user()->role?->nama_role ?? 'User' }}
                        </span>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-item-label">Email</div>
                            <div class="info-item-value text-primary">{{ auth()->user()->email }}</div>
                        </div>

                        @if(auth()->user()->unit)
                            <div class="info-item">
                                <div class="info-item-label">Unit Perusahaan</div>
                                <div class="info-item-value">{{ auth()->user()->unit->nama_pt }}</div>
                            </div>
                        @endif

                        @if(auth()->user()->karyawan)
                            <div class="info-item">
                                <div class="info-item-label">Nama Karyawan</div>
                                <div class="info-item-value">{{ auth()->user()->karyawan->nama_karyawan }}</div>
                            </div>
                        @endif

                        <div class="info-item">
                            <div class="info-item-label">Status</div>
                            <div class="info-item-value">
                                <span class="badge bg-success">Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="mb-3">📋 Fitur yang Tersedia</h5>
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        Fitur-fitur akan ditambahkan sesuai dengan role dan permission yang dimiliki.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
