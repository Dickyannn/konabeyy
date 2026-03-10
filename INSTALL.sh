#!/usr/bin/env bash
# 🚀 HRMS Installation Script (Bash version for reference)
# For Windows, follow the PowerShell steps below

# ─────────────────────────────────────────────────────────────────
# STEP 1: Database Setup
# ─────────────────────────────────────────────────────────────────
# mysql -u root -e "CREATE DATABASE laravel_hrms;"

# ─────────────────────────────────────────────────────────────────
# STEP 2: Install Dependencies (if needed)
# ─────────────────────────────────────────────────────────────────
# composer install
# npm install

# ─────────────────────────────────────────────────────────────────
# STEP 3: Environment Setup
# ─────────────────────────────────────────────────────────────────
# cp .env.example .env
# php artisan key:generate

# ─────────────────────────────────────────────────────────────────
# STEP 4: Database Migrations & Seeding
# ─────────────────────────────────────────────────────────────────
php artisan migrate

# ─────────────────────────────────────────────────────────────────
# STEP 5: Build Assets (jika menggunakan Vite)
# ─────────────────────────────────────────────────────────────────
# npm run build  # untuk production
# npm run dev    # untuk development

# ─────────────────────────────────────────────────────────────────
# STEP 6: Start Application
# ─────────────────────────────────────────────────────────────────
php artisan serve

# ─────────────────────────────────────────────────────────────────
# Akses aplikasi
# ─────────────────────────────────────────────────────────────────
# Browser: http://localhost:8000/login
# Email: admin@hrms.local
# Password: Admin@12345
