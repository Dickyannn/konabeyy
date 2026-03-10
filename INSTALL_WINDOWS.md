# 🚀 HRMS Installation Guide (Windows PowerShell)

## Prerequisites
- Laragon running (MySQL + PHP)
- PHP 8.2+
- Composer
- Node.js & npm (opsional, untuk asset building)

## Installation Steps

### Step 1: Create Database
```powershell
# Hanya perlu dijalankan SATU KALI
mysql -u root -e "CREATE DATABASE laravel_hrms;"
```

### Step 2: Navigate to Project
```powershell
cd c:\laragon\www\laravel
```

### Step 3: Install PHP Dependencies (if not already done)
```powershell
composer install
```

### Step 4: Setup Environment Configuration
File `.env` sudah di-update dengan:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_hrms
DB_USERNAME=root
DB_PASSWORD=
```

### Step 5: Generate App Key (jika belum)
```powershell
php artisan key:generate
```

### Step 6: Clear Configuration Cache
```powershell
php artisan config:cache
```

### Step 7: Run Migrations & Seeds
```powershell
# Ini akan membuat semua tables + insert seed data
php artisan migrate
```

**Output yang diharapkan:**
```
Migration table created successfully.
Migrant: 2024_01_01_000000_create_users_table
Migrant: 2024_01_01_000001_create_cache_table
Migrant: 2024_01_01_000002_create_jobs_table
Migrant: 2026_03_10_000003_create_master_tables
Migrant: 2026_03_10_000004_create_employee_tables
Migrant: 2026_03_10_000005_create_transaction_tables
Migrant: 2026_03_10_000006_create_payroll_tables
Migrant: 2026_03_10_000007_create_bpjs_tables
Migrant: 2026_03_10_000008_create_auth_tables
```

### Step 8: (Optional) Build Frontend Assets
```powershell
# Development mode
npm run dev

# Atau buat production build
npm run build
```

### Step 9: Start Development Server
```powershell
php artisan serve
```

**Output:**
```
Laravel development server started on [http://127.0.0.1:8000]
```

### Step 10: Access Application
1. Open browser: `http://localhost:8000`
2. Click "Login" or go to `http://localhost:8000/login`
3. Enter credentials:
   ```
   Email: admin@hrms.local
   Password: Admin@12345
   ```

---

## 🔍 Verify Installation

### Check Database Tables
```powershell
php artisan tinker

# Cek jumlah tables
DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'laravel_hrms'")

# Cek user admin
User::where('email', 'admin@hrms.local')->first()
```

### Test Login Flow
1. Go to `http://localhost:8000/login`
2. Fill with admin credentials
3. Should redirect to `/dashboard`

### Check Logs
```powershell
# Tail logs real-time
php artisan tinker
tail -f storage/logs/laravel.log
```

---

## 🛠️ Common Commands

```powershell
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Refresh migrations (CAUTION: deletes all data)
php artisan migrate:refresh

# Reset everything to fresh state
php artisan migrate:fresh

# Check migration status
php artisan migrate:status

# View all routes
php artisan route:list

# Interactive shell
php artisan tinker

# Generate model with migration
php artisan make:model ModelName -m

# List available commands
php artisan list
```

---

## ⚠️ Troubleshooting

### Error: "SQLSTATE[HY000]: General error: 1030 Got error 28"
**Cause**: Database connection issue  
**Fix**: `php artisan migrate:fresh` or check MySQL running

### Error: "Class not found"
**Fix**: `composer dump-autoload`

### Error: "Route not found"
**Fix**: Check that routes are properly defined and use correct route names

### CSS/JS Not Loading
**Fix**: Run `npm run dev` if using Vite

### Login Page Shows Blank
**Fix**: 
```powershell
php artisan view:clear
php artisan cache:clear
```

---

## 📋 Structure Verification

After installation, verify these files exist:

```
✓ app/Models/User.php
✓ app/Http/Controllers/Auth/AuthController.php
✓ app/Http/Requests/Auth/LoginRequest.php
✓ resources/views/auth/login.blade.php
✓ resources/views/dashboard.blade.php
✓ resources/css/auth/login.css
✓ resources/js/auth/login.js
✓ routes/web.php
✓ config/auth.php (updated)
✓ .env (DB_CONNECTION=mysql)
```

---

## 🎉 Success Indicators

✅ Migrations complete without errors  
✅ Can access `http://localhost:8000/login`  
✅ Login page displays properly  
✅ Can login with admin@hrms.local / Admin@12345  
✅ Dashboard loads after login  
✅ Can logout successfully  

---

## 🔐 Security Reminders

⚠️ **DO THIS IMMEDIATELY AFTER SETUP:**

1. **Change Admin Password**
   - Login dengan credentials di atas
   - Go to profile/settings
   - Change password to something secure

2. **Update .env**
   - Change `APP_DEBUG=false` di production
   - Use strong `APP_KEY`
   - Set proper `DB_PASSWORD` jika pakai password

3. **Set Correct File Permissions**
   ```powershell
   # Di production server
   // chmod -R 755 storage
   // chmod -R 755 bootstrap/cache
   ```

---

**Version**: 1.0.0  
**Last Updated**: 2026-03-10  
**Environment**: Windows + Laragon + MySQL
