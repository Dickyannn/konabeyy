<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — HR Portal</title>

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ── Variabel warna ─────────────────────────────── */
        :root {
            --primary:        #0092B4;
            --primary-dark:   #007A98;
            --primary-darker: #005F77;
            --primary-light:  #E6F6FB;
            --accent:         #F5A623;
            --text-dark:      #1A2E3B;
            --text-muted:     #6B8896;
            --border:         #D9EDF3;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            margin: 0;
            background: #f0f8fb;
        }

        /* ── PANEL KIRI ─────────────────────────────────── */
        .panel-left {
            background: linear-gradient(145deg, var(--primary) 0%, var(--primary-darker) 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 3.5rem;
        }

        /* Dekorasi hexagon/diamond shapes */
        .shape {
            position: absolute;
            background: rgba(255,255,255,0.08);
            border-radius: 18px;
            transform: rotate(45deg);
        }
        .shape-1 { width: 160px; height: 160px; top: -40px;  right: 60px;  }
        .shape-2 { width: 110px; height: 110px; top: 60px;   right: -20px; background: rgba(255,255,255,0.05); }
        .shape-3 { width: 200px; height: 200px; bottom: 80px; right: -60px; }
        .shape-4 { width: 90px;  height: 90px;  bottom: -20px; right: 100px; background: rgba(255,255,255,0.12); }
        .shape-5 { width: 70px;  height: 70px;  top: 200px;  left: -20px; background: rgba(255,255,255,0.06); }

        /* Card foto udang */
        .photo-card {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            transform: rotate(-3deg);
            transition: transform 0.3s ease;
            background: white;
        }
        .photo-card:hover { transform: rotate(-1deg) scale(1.02); }

        .photo-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .photo-card-main {
            width: 200px;
            height: 220px;
            margin-bottom: 2rem;
        }

        .photo-card-float {
            position: absolute;
            top: 30px;
            right: -10px;
            width: 130px;
            height: 130px;
            transform: rotate(5deg);
            border: 4px solid rgba(255,255,255,0.4);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0,0,0,0.2);
        }
        .photo-card-float img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .welcome-text h1 {
            font-size: 2.2rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 0.5rem;
        }
        .welcome-text h1 span {
            color: var(--accent);
        }
        .welcome-text p {
            color: rgba(255,255,255,0.75);
            font-size: 0.95rem;
            margin-top: 1rem;
            font-weight: 500;
        }

        /* ── PANEL KANAN ─────────────────────────────────── */
        .panel-right {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            padding: 2.5rem;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.4rem;
            text-align: center;
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 0.88rem;
            text-align: center;
            margin-bottom: 2.2rem;
            font-weight: 500;
        }

        /* Form label */
        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-dark);
            margin-bottom: 0.4rem;
        }

        /* Input styling */
        .form-control {
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            color: var(--text-dark);
            background: #FAFCFD;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-control::placeholder { color: #b0c8d4; }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,146,180,0.12);
            background: #fff;
            outline: none;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
            background: #fff8f8;
        }

        /* Password wrapper */
        .input-password-wrap {
            position: relative;
        }
        .input-password-wrap .form-control {
            padding-right: 2.8rem;
        }
        .btn-toggle-pw {
            position: absolute;
            right: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0;
            font-size: 1rem;
            line-height: 1;
        }
        .btn-toggle-pw:hover { color: var(--primary); }

        /* Remember me */
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(0,146,180,0.15);
        }
        .form-check-label {
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Lupa kata sandi */
        .link-forgot {
            font-size: 0.875rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        .link-forgot:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Tombol masuk */
        .btn-masuk {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.8rem;
            width: 100%;
            letter-spacing: 0.5px;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(0,146,180,0.35);
            margin-top: 0.25rem;
        }
        .btn-masuk:hover {
            opacity: 0.92;
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(0,146,180,0.4);
        }
        .btn-masuk:active {
            transform: translateY(0);
        }
        .btn-masuk:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Alert error */
        .alert-login-error {
            background: #fff2f2;
            border: 1.5px solid #f5c6cb;
            border-radius: 12px;
            color: #842029;
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        /* Versi */
        .version-text {
            text-align: center;
            font-size: 0.78rem;
            color: #b0c8d4;
            margin-top: 1.75rem;
            font-weight: 500;
        }

        /* Divider logo */
        .logo-area {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-area .logo-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-darker) 100%);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0,146,180,0.3);
            margin-bottom: 0.75rem;
        }
        .logo-area .logo-icon span {
            color: #fff;
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: -0.5px;
        }

        /* ── RESPONSIVE ──────────────────────────────────── */

        /* Tablet: panel kiri jadi banner atas */
        @media (max-width: 991.98px) {
            .panel-left {
                min-height: 280px;
                padding: 2rem 2rem 3rem;
                justify-content: flex-end;
            }
            .photo-card-main  { width: 140px; height: 155px; margin-bottom: 0; }
            .photo-card-float { width: 90px;  height: 90px;  top: 15px; right: 0px; }
            .welcome-text h1  { font-size: 1.6rem; }
            .shape-1 { width: 100px; height: 100px; }
            .shape-3 { width: 130px; height: 130px; }
        }

        /* Mobile: single column */
        @media (max-width: 575.98px) {
            .panel-left {
                min-height: 220px;
                padding: 1.5rem 1.5rem 2.5rem;
            }
            .photo-card-main  { width: 110px; height: 120px; }
            .photo-card-float { width: 70px;  height: 70px;  }
            .welcome-text h1  { font-size: 1.35rem; }
            .panel-right      { padding: 2rem 1.25rem; }
            .login-box        { max-width: 100%; }
            .login-title      { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">

        {{-- ── PANEL KIRI ─────────────────────────────── --}}
        <div class="col-12 col-lg-6 panel-left">

            {{-- Dekorasi shapes --}}
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            <div class="shape shape-5"></div>

            {{-- Konten --}}
            <div class="d-flex align-items-end gap-4 position-relative">
                {{-- Card foto utama --}}
                <div class="photo-card photo-card-main flex-shrink-0">
                    <img
                        src="{{ asset('images/shrimp-main.jpg') }}"
                        alt="Udang"
                        onerror="this.style.display='none'; this.parentElement.style.background='rgba(255,255,255,0.15)'"
                    >
                </div>

                {{-- Card foto float --}}
                <div class="photo-card-float">
                    <img
                        src="{{ asset('images/shrimp-float.jpg') }}"
                        alt="Udang"
                        onerror="this.style.display='none'; this.parentElement.style.background='rgba(255,255,255,0.2)'"
                    >
                </div>

                {{-- Teks sambutan --}}
                <div class="welcome-text mt-3">
                    <h1>
                        Selamat Datang<br>
                        di <span>HR Portal</span>
                    </h1>
                    <p>PT Suri Tani Pemuka &amp; PT Kona Bay Indonesia</p>
                </div>
            </div>
        </div>

        {{-- ── PANEL KANAN ─────────────────────────────── --}}
        <div class="col-12 col-lg-6 panel-right">
            <div class="login-box">

                {{-- Logo --}}
                <div class="logo-area">
                    <div class="logo-icon">
                        <span>HR</span>
                    </div>
                    <h2 class="login-title">Login HR Portal</h2>
                    <p class="login-subtitle">Masukkan kredensial akun Anda untuk melanjutkan</p>
                </div>

                {{-- Error message --}}
                @if ($errors->any())
                    <div class="alert-login-error">
                        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success rounded-3 py-2 px-3 mb-3" style="font-size:.875rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login.submit') }}" id="loginForm" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">ID Pengguna</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Email akun Anda"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            autofocus
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-password-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Password Anda"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" class="btn-toggle-pw" id="togglePw" aria-label="Tampilkan password">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remember me + Lupa password --}}
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-masuk" id="btnMasuk">
                        <span id="btnText">Masuk</span>
                        <span id="btnSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Memproses...
                        </span>
                    </button>
                </form>

                <p class="version-text">Versi {{ config('app.version', '20240422.1') }}</p>
            </div>
        </div>

    </div>{{-- end row --}}
</div>{{-- end container --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    /**
     * Login Page JavaScript
     * - Toggle password visibility
     * - Form submission with loading state
     */

    document.addEventListener('DOMContentLoaded', () => {
        // ── Toggle Password Visibility ──────────────────
        const togglePwBtn = document.getElementById('togglePw');
        const pwInput     = document.getElementById('password');
        const eyeIcon     = document.getElementById('eyeIcon');

        if (togglePwBtn && pwInput && eyeIcon) {
            togglePwBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const isText = pwInput.type === 'text';
                pwInput.type = isText ? 'password' : 'text';
                eyeIcon.className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
            });
        }

        // ── Form Loading State ──────────────────────────
        const loginForm  = document.getElementById('loginForm');
        const btnMasuk   = document.getElementById('btnMasuk');
        const btnText    = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        if (loginForm && btnMasuk && btnText && btnSpinner) {
            loginForm.addEventListener('submit', () => {
                btnMasuk.disabled = true;
                btnText.classList.add('d-none');
                btnSpinner.classList.remove('d-none');
            });
        }
    });
</script>

</body>
</html>
