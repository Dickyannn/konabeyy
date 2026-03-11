<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Saya — HR Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/master/dashboard.css'])
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1rem;
            border: 3px solid rgba(255, 255, 255, 0.5);
        }

        .form-section {
            background: white;
            border: 1px solid #e8eef7;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f4f8;
        }

        .form-group-custom {
            margin-bottom: 1.5rem;
        }

        .form-label-custom {
            display: block;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-control-custom {
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control-custom:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-secondary-custom {
            background: #f0f4f8;
            border: 1px solid #cbd5e0;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            color: #2d3748;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-secondary-custom:hover {
            background: #e2e8f0;
            text-decoration: none;
            color: #2d3748;
        }

        .alert-custom {
            border-radius: 8px;
            border: 1px solid;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .alert-success-custom {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        .alert-error-custom {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .profile-header {
                padding: 1.5rem;
            }

            .form-section {
                padding: 1.5rem;
            }
        }

        .info-text {
            font-size: 0.9rem;
            color: #718096;
            margin-top: 0.3rem;
        }
    </style>
</head>
<body>

<nav class="navbar-hrms">
    <a href="#" class="navbar-brand-area">
        <div class="navbar-logo">HR</div>
        <div class="navbar-brand-text">
            HR Portal
            <small>PT Suri Tani Pemuka</small>
        </div>
    </a>

    <div class="dropdown">
        <div class="navbar-user" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="navbar-avatar"><i class="bi bi-person-fill"></i></div>
            <div class="navbar-user-info d-none d-sm-block">
                <div class="navbar-user-name">{{ auth()->user()->nama ?? 'User' }}</div>
                <div class="navbar-user-role">Master System</div>
            </div>
            <i class="bi bi-chevron-down navbar-caret d-none d-sm-block"></i>
        </div>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-user mt-1">
            <li>
                <a href="{{ route('profile.edit') }}" class="dropdown-item-user">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </a>
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

<div class="page-wrapper">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Profil Saya</h1>
                <p>Kelola informasi akun Anda</p>
            </div>
            <a href="{{ route('master.dashboard') }}" class="btn-secondary-custom">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- Success Alert --}}
    @if (session('success'))
        <div class="alert-custom alert-success-custom">
            <i class="bi bi-check-circle-fill" style="flex-shrink: 0; font-size: 1.2rem;"></i>
            <div>
                <strong>Berhasil!</strong>
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Error Alert --}}
    @if ($errors->any())
        <div class="alert-custom alert-error-custom">
            <i class="bi bi-exclamation-circle-fill" style="flex-shrink: 0; font-size: 1.2rem;"></i>
            <div>
                <strong>Error!</strong>
                <ul style="margin: 0.5rem 0 0 0; padding-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Profile Header --}}
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="bi bi-person-fill"></i>
        </div>
        <h2>{{ $user->nama }}</h2>
        <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">{{ $user->email }}</p>
    </div>

    {{-- Edit Profil Section --}}
    <div class="form-section">
        <div class="section-title">
            <i class="bi bi-pencil-square"></i> Edit Informasi Profil
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group-custom">
                    <label class="form-label-custom">Nama Lengkap</label>
                    <input
                        type="text"
                        class="form-control-custom w-100"
                        name="nama"
                        value="{{ old('nama', $user->nama) }}"
                        required
                    >
                    <div class="info-text">Nama yang ditampilkan di sistem</div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Email</label>
                    <input
                        type="email"
                        class="form-control-custom w-100"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                    >
                    <div class="info-text">Email untuk login dan notifikasi</div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn-primary-custom">
                    <i class="bi bi-check-lg"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Change Password Section --}}
    <div class="form-section">
        <div class="section-title">
            <i class="bi bi-key"></i> Ubah Password
        </div>

        <form method="POST" action="{{ route('profile.update-password') }}">
            @csrf
            @method('PUT')

            <div class="form-group-custom">
                <label class="form-label-custom">Password Saat Ini</label>
                <input
                    type="password"
                    class="form-control-custom w-100"
                    name="current_password"
                    required
                >
                <div class="info-text">Masukkan password saat ini untuk verifikasi</div>
            </div>

            <div class="form-row">
                <div class="form-group-custom">
                    <label class="form-label-custom">Password Baru</label>
                    <input
                        type="password"
                        class="form-control-custom w-100"
                        name="password"
                        required
                    >
                    <div class="info-text">Minimal 6 karakter</div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Konfirmasi Password</label>
                    <input
                        type="password"
                        class="form-control-custom w-100"
                        name="password_confirmation"
                        required
                    >
                    <div class="info-text">Ulangi password baru</div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn-primary-custom">
                    <i class="bi bi-shield-lock"></i> Ubah Password
                </button>
            </div>
        </form>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
