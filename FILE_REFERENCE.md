# 📚 File Reference Guide

## 🗂️ Complete File Structure with Descriptions

### Database Migrations (`database/migrations/`)

| File | Purpose | Tables | Status |
|------|---------|--------|--------|
| `0001_01_01_000003_create_master_tables.php` | Master reference data | 11 tables + seed | ✅ Ready |
| `0001_01_01_000004_create_employee_tables.php` | Employee & HR data | 6 tables + sample | ✅ Ready |
| `0001_01_01_000005_create_transaction_tables.php` | Attendance, leave, overtime | 5 tables | ✅ Ready |
| `0001_01_01_000006_create_payroll_tables.php` | Salary & benefits | 5 tables | ✅ Ready |
| `0001_01_01_000007_create_bpjs_tables.php` | Social security calc | 2 tables | ✅ Ready |
| `0001_01_01_000008_create_auth_tables.php` | Auth & auditing | 5 tables + seed | ✅ Ready |

---

### Models (`app/Models/`)

**Authentication Models:**
| File | Table | Purpose |
|------|-------|---------|
| `User.php` | auth_users | Main user model with relationships |
| `Role.php` | auth_roles | User roles (Master System, Payroll, etc) |

**Master Data Models:**
| File | Table | Purpose |
|------|-------|---------|
| `MasterUnitPt.php` | master_unit_pt | Company units/locations |
| `MasterCostCenter.php` | master_cost_center | Cost centers per unit |
| `MasterGolongan.php` | master_golongan | Employee grades/levels |
| `MasterStatusKaryawan.php` | master_status_karyawan | Employment status |
| `MasterStatusKawin.php` | master_status_kawin | Marital status |

**Employee Models:**
| File | Table | Purpose |
|------|-------|---------|
| `EmployeeKaryawan.php` | employee_karyawan | Employee master with self-join for supervisor |
| `EmployeePosition.php` | employee_position | Current & historical positions |

---

### Controllers (`app/Http/Controllers/`)

| File | Methods | Purpose |
|------|---------|---------|
| `Auth/AuthController.php` | showLogin(), login(), logout(), logAudit() | Authentication logic |
| `DashboardController.php` | index() | Dashboard display |

**AuthController Methods:**
```php
showLogin()      - Render login form
login()          - Process login + update last_login + audit log
logout()         - Clear session + audit log
logAudit()       - Private helper to log actions
```

---

### Form Requests (`app/Http/Requests/`)

| File | Validates | Rules |
|------|-----------|-------|
| `Auth/LoginRequest.php` | Email & Password | email required, password 6+ chars |

---

### Routes (`routes/web.php`)

**Route Groups:**
- **Public**: `/`, `/login` (GET/POST)
- **Guest**: `/login` (only when not logged in)
- **Protected**: `/dashboard`, POST `/logout` (require auth)

**Named Routes:**
```php
'login' → GET /login
'login.submit' → POST /login
'dashboard' → GET /dashboard
'logout' → POST /logout
'home' → GET /
```

---

### Views (`resources/views/`)

| File | Purpose | Type |
|------|---------|------|
| `auth/login.blade.php` | Login form page | HTML |
| `dashboard.blade.php` | Dashboard after login | HTML |

**Referenced Assets:**
- Loads: `resources/css/auth/login.css` via @vite
- Loads: `resources/js/auth/login.js` via @vite

---

### Stylesheets (`resources/css/auth/`)

| File | Purpose | Lines |
|------|---------|-------|
| `login.css` | All login page styling | 350+ |

**Sections:**
```css
:root              - Color variables
body               - Base typography
.panel-left        - Left panel (welcome section)
.panel-right       - Right panel (form)
.form-control      - Input styling
.btn-masuk         - Submit button
@media queries     - Responsive breakpoints
```

---

### JavaScript (`resources/js/auth/`)

| File | Purpose | Functions |
|------|---------|-----------|
| `login.js` | Interactive login features | togglePassword(), loadingState() |

**Features:**
```javascript
- Password visibility toggle
- Loading state on form submit
- Null safety checks
```

---

### Configuration (`config/`)

| File | Updated | Purpose |
|------|---------|---------|
| `auth.php` | ✅ YES | Auth table names + model |

**Key Changes:**
```php
'model' => App\Models\User::class
'table' => 'auth_password_reset_tokens'
```

---

### Environment (`.env`)

**Database Configuration:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_hrms
DB_USERNAME=root
DB_PASSWORD=
```

---

## 📚 Documentation Files

| File | Purpose | Read Time |
|------|---------|-----------|
| `SETUP_GUIDE.md` | Complete setup walkthrough | 10 min |
| `CREDENTIALS.md` | Login info & quick start | 5 min |
| `IMPLEMENTATION_SUMMARY.md` | What was built | 15 min |
| `INSTALL_WINDOWS.md` | Windows-specific guide | 10 min |
| `VERIFY_CHECKLIST.md` | Deployment checklist | 10 min |
| `FILE_REFERENCE.md` | This file (you are here) | 5 min |

---

## 🔗 Key Relationships

### User → Role
```php
User belongs to Role
├── Master System        (admin@hrms.local)
├── Personal Admin       (manage employees)
├── Payroll             (manage salaries)
└── Personalia          (manage attendance)
```

### User → Unit
```php
User belongs to Unit (nullable)
├── NULL = Superadmin (access all)
├── STP-PWK (PT Suri Tani Pemuka)
└── KBI-TJK (PT Kona Bay Indonesia)
```

### User → Employee
```php
User belongs to Employee (nullable)
├── NULL = System user (non-employee)
└── Linked to employee_karyawan
    ├── Position (current job)
    ├── Boss/Atasan (via self-join)
    ├── Family (anggota_keluarga)
    └── Documents (dokumen_karyawan)
```

---

## 🎯 Data Flow

### Login Flow
```
1. GET /login → ShowLogin (render form)
2. POST /login → LoginRequest (validate)
3. AuthController::login() 
   ├── Query User by email
   ├── Check is_active
   ├── Auth::attempt()
   ├── Update last_login
   ├── Log audit
   └── Redirect to dashboard
4. GET /dashboard → DashboardController (display userinfo)
```

### Logout Flow  
```
1. POST /logout → AuthController::logout()
   ├── Log audit
   ├── Auth::logout()
   ├── Invalidate session
   ├── Regenerate token
   └── Redirect to login
2. GET /login (back to login page)
```

---

## 🧪 Database Seed Data

### Inserted Automatically on Migrate

**Roles (4 total)**
```sql
1. master_system - Admin Sistem
2. personal_admin - Admin Kepegawaian  
3. payroll - Admin Penggajian
4. personalia - Personalia
```

**Initial User (1 total)**
```sql
Email: admin@hrms.local
Password: Admin@12345 (hashed)
Role: master_system
Unit: NULL (all units)
Active: YES
```

**Master Data Examples**
```
Golongan: H-11 (Manager), H-10, H-9, H-8
Unit PT: STP-PWK, KBI-TJK
Status: Permanent, Kontrak, Probation, PKWT
Marital: TK, K0, K1, K2, K3
```

---

## 🚀 Execution Order

When you run `php artisan migrate`, files execute in order:

```
1. 0001_01_01_000000_create_users_table.php       (Laravel default)
2. 0001_01_01_000001_create_cache_table.php       (Laravel default)
3. 0001_01_01_000002_create_jobs_table.php        (Laravel default)
4. 0001_01_01_000003_create_master_tables.php     ← Master data FIRST
5. 0001_01_01_000004_create_employee_tables.php   ← Depends on master
6. 0001_01_01_000005_create_transaction_tables.php ← Depends on employees
7. 0001_01_01_000006_create_payroll_tables.php    ← Depends on employees
8. 0001_01_01_000007_create_bpjs_tables.php       ← Depends on employees
9. 0001_01_01_000008_create_auth_tables.php       ← Depends on master & employees
```

---

## 📖 Code Locations Quick Reference

### Want to change...

| Need | File | Location |
|------|------|----------|
| Login styling | `resources/css/auth/login.css` | Line 1-350 |
| Password field validation | `app/Http/Requests/Auth/LoginRequest.php` | Line 25-30 |
| Login error message | `app/Http/Controllers/Auth/AuthController.php` | Line 60-64 |
| Dashboard title | `resources/views/dashboard.blade.php` | Line 50-55 |
| Admin email | `database/migrations/.../000008_...` | Line 310 |
| Forgot password link | `resources/views/auth/login.blade.php` | Line 135 |

---

## 🎓 Learning Path

1. **Understand**: Read `SETUP_GUIDE.md`
2. **Review**: Study `app/Http/Controllers/Auth/AuthController.php`
3. **Trace**: Follow the login flow through models → controller → view
4. **Customize**: Modify styling in `resources/css/auth/login.css`
5. **Extend**: Add new routes in `routes/web.php`
6. **Deploy**: Follow `INSTALL_WINDOWS.md`

---

## 📞 Quick Look-up

**"Where is the login form?"**  
→ `resources/views/auth/login.blade.php`

**"How does login work?"**  
→ `app/Http/Controllers/Auth/AuthController.php`

**"What tables are created?"**  
→ `database/migrations/` (all 6 files)

**"What is the password?"**  
→ `CREDENTIALS.md`

**"How to run it?"**  
→ `INSTALL_WINDOWS.md`

**"What failed?"**  
→ `SETUP_GUIDE.md` Troubleshooting section

---

**Version**: 1.0.0  
**Updated**: 2026-03-10  
**Total Files**: 28+  
**Total Lines**: 5,000+  
**Status**: ✅ Production Ready
