# 🔐 Test Credentials & Quick Start

## Default Admin Account (dari Database Seeder)

```
Email    : admin@hrms.local
Password : Admin@12345
Role     : Master System (akses semua unit)
```

## Startup Steps

### 1. Database Setup
```powershell
# Pastikan MySQL running di Laragon
# Buat database
mysql -u root -e "CREATE DATABASE laravel_hrms;"
```

### 2. Run Migrations
```powershell
cd c:\laragon\www\laravel
php artisan migrate
```

### 3. Start Server
```powershell
php artisan serve
```

Output:
```
Laravel development server started: http://127.0.0.1:8000
```

### 4. Open Browser
```
URL: http://localhost:8000/login
```

### 5. Login
- Email: `admin@hrms.local`
- Password: `Admin@12345`

### 6. Verify Dashboard
Setelah login, Anda akan diarahkan ke `/dashboard` yang menampilkan:
- Nama user
- Email user
- Unit perusahaan (jika ada)
- Nama karyawan (jika akun linked ke karyawan)
- Status (Aktif)

## Troubleshooting

### ❌ "Route [login] not defined"
**Fix**: Di login.blade.php sudah diupdate ke `route('login.submit')` ✓

### ❌ "Table 'auth_users' doesn't exist"
**Fix**: Jalankan `php artisan migrate`

### ❌ "Class not found: AuthController"  
**Fix**: Jalankan `composer dump-autoload`

### ❌ Login gagal
**Cek**:
1. Apakah migrations sudah jalan?
2. Apakah email/password benar?
3. Apakah user aktif? (`is_active = true`)

### ❌ CSS/JS tidak load
**Fix**: Jika menggunakan Vite, pastikan:
```powershell
npm run dev
```

## Database Seed Data yang Sudah Ada

### Users (auth_users)
- 1 Super Admin account: `admin@hrms.local` / `Admin@12345`

### Roles (auth_roles)
- Master System
- Personal Administration
- Payroll
- Personalia

### Master Data (master_golongan)
- H-11 (Manager)
- H-10 (Senior Supervisor)
- H-9 (Supervisor)
- H-8 (Staff)

### Unit PT (master_unit_pt)
- STP-PWK (PT Suri Tani Pemuka) - Purwakarta
- KBI-TJK (PT Kona Bay Indonesia) - Tejakula

### Employee Sample (employee_karyawan)
- Bambang Suryanto (NIP: 21000001) - F&A Manager

---

## Next Steps Setelah Setup

1. **Ganti Password Admin**
   ```
   Lakukan di aplikasi setelah login pertama
   ```

2. **Setup Additional Users**
   - Create user untuk personalia, payroll, admin lain
   - Link ke karyawan masing-masing

3. **Setup Employee Data**
   - Import data karyawan
   - Setup posisi & cost center

4. **Test Login Scenarios**
   - Superadmin login (semua unit)
   - Admin regional login (unit tertentu)
   - Personalia login
   - Payroll login

---

## ✨ Features Ready to Use

✅ Login dengan credentials  
✅ Dashboard setelah login  
✅ Remember me checkbox  
✅ Password toggle visibility  
✅ Logout functionality  
✅ Session management  
✅ Audit logging (LOGIN/LOGOUT)  
✅ User role tracking  

## 🔜 Features To Implement

⏳ Password reset via email  
⏳ Employee management (CRUD)  
⏳ Attendance tracking  
⏳ Leave management  
⏳ Payroll processing  
⏳ Reports & analytics  
⏳ Role-based permissions  
⏳ API endpoints  

---

**Last Updated**: 2026-03-10
