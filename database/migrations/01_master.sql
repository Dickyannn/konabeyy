-- =============================================================
--  FILE: 01_master.sql
--  Schema: master
--  Tabel  : golongan, unit_pt, cost_center, status_karyawan,
--           status_kawin, parameter_bpjs, payroll_component,
--           car_allowance, komponen_tunjangan,
--           shift_kerja, hari_libur
--
--  Semua FK di schema ini internal (master → master)
--  Tidak ada FK keluar ke schema lain
-- =============================================================


-- ── 1. master.golongan ───────────────────────────────────────
--  Tabel induk untuk level/grade karyawan
--  Direferens oleh: employee.karyawan, master.car_allowance,
--                   master.komponen_tunjangan, employee.riwayat_jabatan
CREATE TABLE master.golongan (
    id             SERIAL       NOT NULL,
    kode_golongan  VARCHAR(20)  NOT NULL,
    nama_golongan  VARCHAR(100) NOT NULL,
    gaji_pokok_min NUMERIC(15,2),
    gaji_pokok_max NUMERIC(15,2),
    deskripsi      VARCHAR(200),
    is_active      BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at     TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    updated_at     TIMESTAMPTZ  NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_golongan        PRIMARY KEY (id),
    CONSTRAINT uq_golongan_kode   UNIQUE      (kode_golongan)
);


-- ── 2. master.unit_pt ────────────────────────────────────────
--  Daftar perusahaan / unit lokasi
--  Direferens oleh: master.cost_center, employee.karyawan, auth.users
CREATE TABLE master.unit_pt (
    id          SERIAL       NOT NULL,
    kode_unit   VARCHAR(20)  NOT NULL,
    nama_pt     VARCHAR(100) NOT NULL,
    lokasi      VARCHAR(100),
    is_active   BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMPTZ  NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_unit_pt       PRIMARY KEY (id),
    CONSTRAINT uq_unit_pt_kode  UNIQUE      (kode_unit)
);


-- ── 3. master.cost_center ────────────────────────────────────
--  Pusat biaya per unit PT
--  FK → master.unit_pt
--  Direferens oleh: employee.position
CREATE TABLE master.cost_center (
    id        SERIAL       NOT NULL,
    id_unit   INT          NOT NULL,
    kode_cc   VARCHAR(20)  NOT NULL,
    nama_cc   VARCHAR(100) NOT NULL,
    is_active BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_cost_center            PRIMARY KEY (id),
    CONSTRAINT uq_cost_center_kode_unit  UNIQUE      (id_unit, kode_cc),
    CONSTRAINT fk_cost_center_unit       FOREIGN KEY (id_unit)
        REFERENCES master.unit_pt (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


-- ── 4. master.status_karyawan ────────────────────────────────
--  Permanent, Kontrak, Probation, PKWT
--  Direferens oleh: employee.karyawan, transaction.kontrak_karyawan
CREATE TABLE master.status_karyawan (
    id          SERIAL      NOT NULL,
    nama_status VARCHAR(50) NOT NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_status_karyawan      PRIMARY KEY (id),
    CONSTRAINT uq_status_karyawan_nama UNIQUE      (nama_status)
);


-- ── 5. master.status_kawin ───────────────────────────────────
--  TK, K0, K1, K2, K3
--  Direferens oleh: employee.karyawan
CREATE TABLE master.status_kawin (
    id          SERIAL      NOT NULL,
    kode_status VARCHAR(10) NOT NULL,
    deskripsi   VARCHAR(50) NOT NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_status_kawin      PRIMARY KEY (id),
    CONSTRAINT uq_status_kawin_kode UNIQUE      (kode_status)
);


-- ── 6. master.parameter_bpjs ─────────────────────────────────
--  % iuran BPJS per periode — disimpan history supaya kalkulasi
--  lama tetap akurat meski % berubah
--  Direferens oleh: bpjs.bpjs_tk, bpjs.bpjs_kesehatan
CREATE TABLE master.parameter_bpjs (
    id                      SERIAL       NOT NULL,
    berlaku_mulai           DATE         NOT NULL,
    berlaku_selesai         DATE,
    -- Ketenagakerjaan beban perusahaan
    jht_perusahaan_pct      NUMERIC(5,3) NOT NULL DEFAULT 3.700,
    jp_perusahaan_pct       NUMERIC(5,3) NOT NULL DEFAULT 2.000,
    jkk_pct                 NUMERIC(5,3) NOT NULL DEFAULT 0.240,
    jkm_pct                 NUMERIC(5,3) NOT NULL DEFAULT 0.300,
    -- Ketenagakerjaan beban karyawan
    jht_karyawan_pct        NUMERIC(5,3) NOT NULL DEFAULT 2.000,
    jp_karyawan_pct         NUMERIC(5,3) NOT NULL DEFAULT 1.000,
    -- Kesehatan
    bpjs_kes_perusahaan_pct NUMERIC(5,3) NOT NULL DEFAULT 4.000,
    bpjs_kes_karyawan_pct   NUMERIC(5,3) NOT NULL DEFAULT 1.000,
    created_at              TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    created_by              VARCHAR(100),

    CONSTRAINT pk_parameter_bpjs PRIMARY KEY (id)
);


-- ── 7. master.payroll_component ──────────────────────────────
--  Daftar komponen gaji yang bisa dikonfigurasi
--  Direferens oleh: payroll.payroll_detail
CREATE TABLE master.payroll_component (
    id             SERIAL      NOT NULL,
    nama_component VARCHAR(100) NOT NULL,
    component_type VARCHAR(20)  NOT NULL,
    is_active      BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at     TIMESTAMPTZ  NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_payroll_component         PRIMARY KEY (id),
    CONSTRAINT ck_payroll_component_type    CHECK (component_type IN ('income','deduction','benefit'))
);


-- ── 8. master.car_allowance ──────────────────────────────────
--  Nominal car allowance per golongan per periode
--  FK → master.golongan
--  Direferens oleh: employee.fasilitas_kendaraan (referensi nominal)
CREATE TABLE master.car_allowance (
    id              SERIAL        NOT NULL,
    id_golongan     INT           NOT NULL,
    nominal         NUMERIC(15,2) NOT NULL,
    berlaku_mulai   DATE          NOT NULL,
    berlaku_selesai DATE,
    created_at      TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ   NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_car_allowance         PRIMARY KEY (id),
    CONSTRAINT fk_car_allowance_golongan FOREIGN KEY (id_golongan)
        REFERENCES master.golongan (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


-- ── 9. master.komponen_tunjangan ─────────────────────────────
--  Uang makan & transport per golongan per periode
--  HANYA berlaku jika karyawan TIDAK dapat car allowance
--  FK → master.golongan
CREATE TABLE master.komponen_tunjangan (
    id              SERIAL        NOT NULL,
    id_golongan     INT           NOT NULL,
    uang_makan      NUMERIC(12,2) NOT NULL DEFAULT 0,
    uang_transport  NUMERIC(12,2) NOT NULL DEFAULT 0,
    tunjangan_lain  NUMERIC(12,2) NOT NULL DEFAULT 0,
    berlaku_mulai   DATE          NOT NULL,
    berlaku_selesai DATE,
    created_at      TIMESTAMPTZ   NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_komponen_tunjangan          PRIMARY KEY (id),
    CONSTRAINT fk_komponen_tunjangan_golongan FOREIGN KEY (id_golongan)
        REFERENCES master.golongan (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


-- ── 10. master.shift_kerja ───────────────────────────────────
--  Definisi shift — Normal, Pagi, Siang, Malam
--  Direferens oleh: transaction.attendance
CREATE TABLE master.shift_kerja (
    id              SERIAL      NOT NULL,
    nama_shift      VARCHAR(50) NOT NULL,
    jam_masuk       TIME        NOT NULL,
    jam_pulang      TIME        NOT NULL,
    toleransi_menit INT         NOT NULL DEFAULT 15,
    is_active       BOOLEAN     NOT NULL DEFAULT TRUE,

    CONSTRAINT pk_shift_kerja PRIMARY KEY (id)
);


-- ── 11. master.hari_libur ────────────────────────────────────
--  Kalender hari libur nasional & cuti bersama
CREATE TABLE master.hari_libur (
    id         SERIAL       NOT NULL,
    tanggal    DATE         NOT NULL,
    keterangan VARCHAR(100) NOT NULL,
    tahun      INT          NOT NULL
                   GENERATED ALWAYS AS (EXTRACT(YEAR FROM tanggal)::INT) STORED,

    CONSTRAINT pk_hari_libur      PRIMARY KEY (id),
    CONSTRAINT uq_hari_libur_tgl  UNIQUE      (tanggal)
);


-- =============================================================
--  SEED DATA master
-- =============================================================

INSERT INTO master.status_karyawan (nama_status) VALUES
    ('Permanent'), ('Kontrak'), ('Probation'), ('PKWT');

INSERT INTO master.status_kawin (kode_status, deskripsi) VALUES
    ('TK', 'Tidak Kawin'),
    ('K0', 'Kawin tanpa anak'),
    ('K1', 'Kawin 1 anak'),
    ('K2', 'Kawin 2 anak'),
    ('K3', 'Kawin 3 anak');

INSERT INTO master.golongan (kode_golongan, nama_golongan, gaji_pokok_min, gaji_pokok_max, deskripsi) VALUES
    ('H-11', 'Manager',      15000000, 25000000, 'Manager Level'),
    ('H-10', 'Supervisor',    8000000, 15000000, 'Supervisor Level'),
    ('H-9',  'Staff Senior',  5000000,  8000000, 'Staff Senior'),
    ('H-8',  'Staff',         3500000,  5000000, 'Staff Junior');

INSERT INTO master.unit_pt (kode_unit, nama_pt, lokasi) VALUES
    ('STP-PWK', 'PT Suri Tani Pemuka',   'Purwakarta'),
    ('KBI-TJK', 'PT Kona Bay Indonesia', 'Tejakula');

INSERT INTO master.cost_center (id_unit, kode_cc, nama_cc) VALUES
    (1, 'UMUM',     'Umum'),
    (1, 'PRODUKSI', 'Produksi'),
    (1, 'SALES',    'Sales'),
    (2, 'UMUM',     'Umum'),
    (2, 'PRODUKSI', 'Produksi'),
    (2, 'SALES',    'Sales');

INSERT INTO master.parameter_bpjs (berlaku_mulai) VALUES ('2024-01-01');

INSERT INTO master.payroll_component (nama_component, component_type) VALUES
    ('Basic Salary',       'income'),
    ('Uang Makan',         'income'),
    ('Uang Transport',     'income'),
    ('Car Allowance',      'income'),
    ('Lembur',             'income'),
    ('THR',                'income'),
    ('Insentif',           'income'),
    ('Potongan JHT',       'deduction'),
    ('Potongan JP',        'deduction'),
    ('Potongan BPJS Kes.', 'deduction');

INSERT INTO master.car_allowance (id_golongan, nominal, berlaku_mulai) VALUES
    (1, 3500000, '2024-01-01'),
    (2, 2000000, '2024-01-01'),
    (3, 1000000, '2024-01-01');

INSERT INTO master.komponen_tunjangan (id_golongan, uang_makan, uang_transport, berlaku_mulai) VALUES
    (1, 35000, 30000, '2024-01-01'),
    (2, 30000, 25000, '2024-01-01'),
    (3, 25000, 20000, '2024-01-01'),
    (4, 20000, 15000, '2024-01-01');

INSERT INTO master.shift_kerja (nama_shift, jam_masuk, jam_pulang) VALUES
    ('Normal',      '08:00', '17:00'),
    ('Shift Pagi',  '06:00', '14:00'),
    ('Shift Siang', '14:00', '22:00'),
    ('Shift Malam', '22:00', '06:00');
