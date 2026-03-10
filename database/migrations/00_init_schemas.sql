-- =============================================================
--  FILE: 00_init_schemas.sql
--  Jalankan PERTAMA KALI sebelum file lainnya
--  Di DBeaver: klik kanan DB → Execute Script
-- =============================================================

-- Hapus schema lama jika mau rebuild dari nol (hati-hati di production!)
-- DROP SCHEMA IF EXISTS auth        CASCADE;
-- DROP SCHEMA IF EXISTS bpjs        CASCADE;
-- DROP SCHEMA IF EXISTS payroll     CASCADE;
-- DROP SCHEMA IF EXISTS transaction CASCADE;
-- DROP SCHEMA IF EXISTS employee    CASCADE;
-- DROP SCHEMA IF EXISTS master      CASCADE;

CREATE SCHEMA IF NOT EXISTS master;
CREATE SCHEMA IF NOT EXISTS employee;
CREATE SCHEMA IF NOT EXISTS payroll;
CREATE SCHEMA IF NOT EXISTS transaction;
CREATE SCHEMA IF NOT EXISTS bpjs;
CREATE SCHEMA IF NOT EXISTS auth;
