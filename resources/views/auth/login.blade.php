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
    
    {{-- Auth Login CSS --}}
    @vite(['resources/css/auth/login.css'])
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
                    {{--
                        Ganti src dengan foto udang / tambak sesuai aset perusahaan.
                        Letakkan gambar di: public/images/shrimp-main.jpg
                    --}}
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
                        {{-- TODO: Implement password reset --}}
                        {{-- <a href="{{ route('password.request') }}" class="link-forgot">Lupa Kata Sandi?</a> --}}
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

{{-- Auth Login JS --}}
@vite(['resources/js/auth/login.js'])
</body>
</html>
