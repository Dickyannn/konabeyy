-- =============================================================
--  FILE: 06_auth.sql
--  Schema: auth
--  Tabel  : roles, users, password_reset_tokens,
--           sessions, audit_log
--
--  Dependency: 01_master.sql + 02_employee.sql harus sudah jalan
--  (auth.users FK ke master.unit_pt & employee.karyawan)
-- =============================================================


-- ── 1. auth.roles ────────────────────────────────────────────
--  4 role: master_system, personal_admin, payroll, personalia
--  Tidak ada FK keluar
CREATE TABLE auth.roles (
    id         SERIAL       NOT NULL,
    kode_role  VARCHAR(50)  NOT NULL,
    nama_role  VARCHAR(100) NOT NULL,
    deskripsi  TEXT,
    created_at TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ  NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_roles          PRIMARY KEY (id),
    CONSTRAINT uq_roles_kode     UNIQUE      (kode_role)
);


-- ── 2. auth.users ────────────────────────────────────────────
--  Akun login sistem
--  FK → auth.roles, master.unit_pt, employee.karyawan
--  id_unit NULL = akses semua unit (superadmin)
--  id_karyawan NULL = user sistem murni (bukan karyawan)
CREATE TABLE auth.users (
    id                SERIAL       NOT NULL,
    nama              VARCHAR(150) NOT NULL,
    email             VARCHAR(150) NOT NULL,
    email_verified_at TIMESTAMPTZ,
    password          VARCHAR(255) NOT NULL,
    remember_token    VARCHAR(100),
    id_role           INT          NOT NULL,
    id_unit           INT,
    id_karyawan       INT,
    is_active         BOOLEAN      NOT NULL DEFAULT TRUE,
    last_login        TIMESTAMPTZ,
    created_at        TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    updated_at        TIMESTAMPTZ  NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_users               PRIMARY KEY (id),
    CONSTRAINT uq_users_email         UNIQUE      (email),

    CONSTRAINT fk_users_role          FOREIGN KEY (id_role)
        REFERENCES auth.roles (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_users_unit          FOREIGN KEY (id_unit)
        REFERENCES master.unit_pt (id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    CONSTRAINT fk_users_karyawan      FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE SET NULL
);


-- ── 3. auth.password_reset_tokens ────────────────────────────
--  Token reset password — 1 token aktif per email (PK = email)
--  Tidak ada FK (email tidak harus ada di users, antisipasi edge case)
CREATE TABLE auth.password_reset_tokens (
    email      VARCHAR(150) NOT NULL,
    token      VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ  NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_password_reset_tokens PRIMARY KEY (email)
);


-- ── 4. auth.sessions ─────────────────────────────────────────
--  Session Laravel — PK adalah session ID string bukan integer
--  user_id nullable: guest session tidak punya user
--  FK → auth.users
CREATE TABLE auth.sessions (
    id            VARCHAR(255) NOT NULL,
    user_id       BIGINT,
    ip_address    VARCHAR(45),
    user_agent    TEXT,
    payload       TEXT         NOT NULL,
    last_activity INT          NOT NULL,

    CONSTRAINT pk_sessions          PRIMARY KEY (id),
    CONSTRAINT fk_sessions_user     FOREIGN KEY (user_id)
        REFERENCES auth.users (id)
        ON UPDATE CASCADE ON DELETE SET NULL    -- session tetap ada meski user dihapus
);


-- ── 5. auth.audit_log ────────────────────────────────────────
--  Log semua aktivitas penting: LOGIN, LOGOUT, CREATE, UPDATE,
--  DELETE, RESET_PASSWORD, EXPORT, dll
--  PK BIGSERIAL karena volume baris bisa sangat besar
--  FK → auth.users (SET NULL agar log tetap ada meski user dihapus)
CREATE TABLE auth.audit_log (
    id         BIGSERIAL    NOT NULL,
    id_user    INT,
    action     VARCHAR(50)  NOT NULL,
    table_name VARCHAR(100),
    record_id  INT,
    old_data   JSONB,
    new_data   JSONB,
    ip_address VARCHAR(45),
    created_at TIMESTAMPTZ  NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_audit_log         PRIMARY KEY (id),
    CONSTRAINT fk_audit_log_user    FOREIGN KEY (id_user)
        REFERENCES auth.users (id)
        ON UPDATE CASCADE ON DELETE SET NULL
);


-- =============================================================
--  INDEXES auth
-- =============================================================
CREATE INDEX idx_users_role         ON auth.users     (id_role);
CREATE INDEX idx_users_unit         ON auth.users     (id_unit);
CREATE INDEX idx_users_aktif        ON auth.users     (is_active);
CREATE INDEX idx_sessions_user      ON auth.sessions  (user_id, last_activity);
CREATE INDEX idx_audit_user_tgl     ON auth.audit_log (id_user, created_at);
CREATE INDEX idx_audit_table_rec    ON auth.audit_log (table_name, record_id);
CREATE INDEX idx_audit_action       ON auth.audit_log (action, created_at);


-- =============================================================
--  SEED DATA auth
-- =============================================================

INSERT INTO auth.roles (kode_role, nama_role, deskripsi) VALUES
    ('master_system',  'Master System',           'Admin sistem — konfigurasi parameter, kelola user'),
    ('personal_admin', 'Personal Administration', 'Akses penuh data kepegawaian (CRUD)'),
    ('payroll',        'Payroll',                 'Proses penggajian, THR, insentif, laporan BPJS'),
    ('personalia',     'Personalia',              'Data karyawan (read-only) + kelola absensi & cuti');

-- Akun superadmin awal — GANTI PASSWORD SETELAH DEPLOY PERTAMA!
-- Password di bawah adalah bcrypt hash dari: Admin@12345
INSERT INTO auth.users (nama, email, email_verified_at, password, id_role, id_unit, is_active)
VALUES (
    'Super Admin',
    'admin@hrms.local',
    NOW(),
    '$2y$12$placeholder_hash_ganti_via_seeder',   -- ganti via php artisan db:seed
    1,      -- id role master_system
    NULL,   -- NULL = akses semua unit
    TRUE
);
