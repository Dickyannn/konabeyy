# 📋 SETUP GUIDE - HR Portal

## ✅ Yang Sudah Disiapkan

### 1. **Database Migrations** (6 file)
- `0001_01_01_000003_create_master_tables.php` - Tabel master (golongan, unit, etc)
- `0001_01_01_000004_create_employee_tables.php` - Tabel karyawan & position
- `0001_01_01_000005_create_transaction_tables.php` - Tabel absensi, cuti, lembur
- `0001_01_01_000006_create_payroll_tables.php` - Tabel penggajian
- `0001_01_01_000007_create_bpjs_tables.php` - Tabel BPJS
- `0001_01_01_000008_create_auth_tables.php` - Tabel auth & users

### 2. **Models** (7 file)
- `User` - Model untuk auth_users dengan relationships
- `Role` - Model untuk auth_roles
- `MasterUnitPt` - Model untuk master_unit_pt
- `MasterCostCenter` - Model untuk master_cost_center
- `EmployeeKaryawan` - Model untuk employee_karyawan dengan self-join
- `EmployeePosition` - Model untuk employee_position
- `MasterStatusKaryawan`, `MasterStatusKawin`, `MasterGolongan` - Master data models

### 3. **Authentication**
- `AuthController` - Handle login, logout, audit logging
- `LoginRequest` - Form validation untuk login
- Protected routes dengan middleware 'auth'
- Guest routes untuk login

### 4. **Views**
- `resources/views/auth/login.blade.php` - Login page (HTML terpisah dari CSS/JS)
- `resources/css/auth/login.css` - Styling login
- `resources/js/auth/login.js` - JavaScript toggle password & loading state
- `resources/views/dashboard.blade.php` - Dashboard setelah login

### 5. **Controllers**
- `DashboardController` - Show dashboard

---

## 🚀 Cara Menjalankan

### Step 1: Setup Database MySQL
```powershell
# Buka terminal, buat database baru
mysql -u root -e "CREATE DATABASE laravel_hrms;"
```

### Step 2: Update `.env` (sudah dilakukan)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_hrms
DB_USERNAME=root
DB_PASSWORD=
```

### Step 3: Jalankan Migrations
```powershell
cd c:\laragon\www\laravel
php artisan migrate
```

### Step 4: Start Development Server
```powershell
php artisan serve
```

Akses: `http://localhost:8000`

---

## 🔐 Login Credentials (dari seed)

| Field | Nilai |
|-------|-------|
| **Email** | `admin@hrms.local` |
| **Password** | `Admin@12345` |
| **Role** | Master System (akses semua unit) |

⚠️ **PENTING**: Setelah login berhasil, **GANTI PASSWORD** ke password yang kuat!

---

## 📁 Struktur File

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── AuthController.php
│   │   └── DashboardController.php
│   └── Requests/
│       └── Auth/
│           └── LoginRequest.php
└── Models/
    ├── User.php
    ├── Role.php
    ├── MasterUnitPt.php
    ├── MasterCostCenter.php
    ├── EmployeeKaryawan.php
    ├── EmployeePosition.php
    ├── MasterStatusKaryawan.php
    ├── MasterStatusKawin.php
    └── MasterGolongan.php

resources/
├── css/
│   └── auth/
│       └── login.css
├── js/
│   └── auth/
│       └── login.js
└── views/
    ├── auth/
    │   └── login.blade.php
    └── dashboard.blade.php

routes/
└── web.php

config/
└── auth.php (sudah diupdate untuk auth_users)
```

---

## 🔄 Routes yang Sudah Siap

### Public Routes
- `GET /` - Welcome page
- `GET /login` - Tampilkan form login
- `POST /login` - Submit login

### Protected Routes (Memerlukan Auth)
- `GET /dashboard` - Dashboard
- `POST /logout` - Logout

---

## 🛠️ Yang Perlu Diimplementasikan Selanjutnya

Di file ini ada TODO comments:

1. **Password Reset**
   - `routes/web.php` (TODO: Password reset routes)
   - Implement: `PasswordResetController`

2. **Authorization (Role-based)**
   - Implement middleware: `app/Http/Middleware/CheckRole.php`
   - Add gate/permission checks

3. **Audit Logging**
   - `AuthController` sudah melogkan LOGIN/LOGOUT ke `auth_audit_log`

4. **Employee Dashboard**
   - Customize berdasarkan role

5. **API Routes** (opsional)
   - Setup `routes/api.php` untuk mobile/external apps

---

## ✔️ Checklist Verifikasi

- [x] Database migrations berhasil jalan
- [x] Semua models ter-create dengan proper relationships
- [x] Auth controller dan LoginRequest ter-create
- [x] Routes sudah ter-setup
- [x] Login page sudah responsive dan rapi
- [x] Dashboard page sudah menampilkan user info
- [ ] Test login dengan credential `admin@hrms.local` / `Admin@12345`
- [ ] Verify migrations tanpa error
- [ ] Pastikan CSS/JS terpisah berfungsi normal

---

## 🐛 Troubleshooting

### Error: "could not find driver"
**Solusi**: Update `.env` ke MySQL (sudah dilakukan)

### Error: Table not found
**Solusi**: Jalankan `php artisan migrate`

### Error: Class not found
**Solusi**: 
- Pastikan path import benar
- Run `composer dump-autoload`

### Login gagal
**Solusi**: 
- Pastikan migration sudah jalan
- Verify `auth_users` table punya seed data
- Check `.env` DB connection correct

---

## 📞 Quick Commands

```powershell
# Verify migrations
php artisan migrate:status

# Rollback migrations
php artisan migrate:rollback

# Fresh migrations (HATI-HATI: hapus semua data)
php artisan migrate:fresh

# Clear cache
php artisan config:cache

# Run tests
php artisan test

# Tinker console
php artisan tinker
```

---

**Created**: March 10, 2026  
**Last Updated**: 2026-03-10  
**Version**: 1.0
