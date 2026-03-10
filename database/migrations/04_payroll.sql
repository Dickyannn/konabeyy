-- =============================================================
--  FILE: 04_payroll.sql
--  Schema: payroll
--  Tabel  : payroll (header), payroll_detail,
--           uang_makan_transport, thr, insentif
--
--  Dependency: 01_master.sql + 02_employee.sql harus sudah jalan
-- =============================================================


-- ── 1. payroll.payroll (header) ──────────────────────────────
--  Satu baris per karyawan per bulan
--  take_home_pay = generated otomatis (total_income - total_deduction)
--  FK → employee.karyawan
CREATE TABLE payroll.payroll (
    id              SERIAL        NOT NULL,
    id_karyawan     INT           NOT NULL,
    bulan           SMALLINT      NOT NULL,
    tahun           SMALLINT      NOT NULL,
    basic_salary    NUMERIC(15,2) NOT NULL,
    total_income    NUMERIC(15,2) NOT NULL DEFAULT 0,
    total_deduction NUMERIC(15,2) NOT NULL DEFAULT 0,
    take_home_pay   NUMERIC(15,2) NOT NULL
                        GENERATED ALWAYS AS (total_income - total_deduction) STORED,
    status          VARCHAR(20)   NOT NULL DEFAULT 'draft',
    approved_by     VARCHAR(100),
    approved_at     TIMESTAMPTZ,
    created_at      TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    created_by      VARCHAR(100),

    CONSTRAINT pk_payroll               PRIMARY KEY (id),
    CONSTRAINT uq_payroll_kryw_bln_thn  UNIQUE      (id_karyawan, bulan, tahun),
    CONSTRAINT ck_payroll_bulan         CHECK       (bulan BETWEEN 1 AND 12),
    CONSTRAINT ck_payroll_status        CHECK       (status IN ('draft','processed','approved','paid')),

    CONSTRAINT fk_payroll_karyawan      FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE RESTRICT   -- jangan hapus karyawan yg punya payroll
);


-- ── 2. payroll.payroll_detail ────────────────────────────────
--  Rincian komponen per slip gaji
--  ON DELETE CASCADE: hapus header → detail ikut terhapus
--  FK → payroll.payroll, master.payroll_component
CREATE TABLE payroll.payroll_detail (
    id           SERIAL        NOT NULL,
    id_payroll   INT           NOT NULL,
    id_component INT           NOT NULL,
    amount       NUMERIC(15,2) NOT NULL DEFAULT 0,
    keterangan   VARCHAR(200),
    created_at   TIMESTAMPTZ   NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_payroll_detail              PRIMARY KEY (id),
    CONSTRAINT uq_payroll_detail_cmp          UNIQUE      (id_payroll, id_component),

    CONSTRAINT fk_payroll_detail_payroll      FOREIGN KEY (id_payroll)
        REFERENCES payroll.payroll (id)
        ON UPDATE CASCADE ON DELETE CASCADE,   -- header dihapus → detail ikut

    CONSTRAINT fk_payroll_detail_component    FOREIGN KEY (id_component)
        REFERENCES master.payroll_component (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);


-- ── 3. payroll.uang_makan_transport ──────────────────────────
--  Rekap uang makan & transport per bulan
--  HANYA diisi untuk karyawan yang TIDAK dapat car allowance
--  FK → employee.karyawan
CREATE TABLE payroll.uang_makan_transport (
    id             SERIAL        NOT NULL,
    id_karyawan    INT           NOT NULL,
    bulan          SMALLINT      NOT NULL,
    tahun          SMALLINT      NOT NULL,
    hari_kerja     INT           NOT NULL DEFAULT 0,
    uang_makan     NUMERIC(12,2) NOT NULL DEFAULT 0,
    uang_transport NUMERIC(12,2) NOT NULL DEFAULT 0,
    created_at     TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    created_by     VARCHAR(100),

    CONSTRAINT pk_umt               PRIMARY KEY (id),
    CONSTRAINT uq_umt_kryw_bln_thn  UNIQUE      (id_karyawan, bulan, tahun),
    CONSTRAINT ck_umt_bulan         CHECK       (bulan BETWEEN 1 AND 12),

    CONSTRAINT fk_umt_karyawan      FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);


-- ── 4. payroll.thr ───────────────────────────────────────────
--  THR Lebaran — 1 baris per karyawan per tahun
--  masa_kerja_bulan dihitung di app layer saat proses
--  jumlah_thr = proporsional jika masa_kerja_bulan < 12
--  FK → employee.karyawan
CREATE TABLE payroll.thr (
    id               SERIAL        NOT NULL,
    id_karyawan      INT           NOT NULL,
    tahun            SMALLINT      NOT NULL,
    basic_salary     NUMERIC(15,2) NOT NULL,
    masa_kerja_bulan INT           NOT NULL,
    jumlah_thr       NUMERIC(15,2) NOT NULL,
    bulan_proses     SMALLINT      NOT NULL,
    status           VARCHAR(20)   NOT NULL DEFAULT 'draft',
    created_at       TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    created_by       VARCHAR(100),

    CONSTRAINT pk_thr               PRIMARY KEY (id),
    CONSTRAINT uq_thr_kryw_thn      UNIQUE      (id_karyawan, tahun),
    CONSTRAINT ck_thr_status        CHECK       (status IN ('draft','approved','paid')),
    CONSTRAINT ck_thr_bulan_proses  CHECK       (bulan_proses BETWEEN 1 AND 12),

    CONSTRAINT fk_thr_karyawan      FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);


-- ── 5. payroll.insentif ──────────────────────────────────────
--  Insentif non-rutin — input manual per periode
--  FK → employee.karyawan
CREATE TABLE payroll.insentif (
    id          SERIAL        NOT NULL,
    id_karyawan INT           NOT NULL,
    bulan       SMALLINT      NOT NULL,
    tahun       SMALLINT      NOT NULL,
    jumlah      NUMERIC(15,2) NOT NULL,
    deskripsi   VARCHAR(200),
    status      VARCHAR(20)   NOT NULL DEFAULT 'draft',
    created_at  TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    created_by  VARCHAR(100),

    CONSTRAINT pk_insentif          PRIMARY KEY (id),
    CONSTRAINT ck_insentif_bulan    CHECK       (bulan BETWEEN 1 AND 12),
    CONSTRAINT ck_insentif_status   CHECK       (status IN ('draft','approved','paid')),

    CONSTRAINT fk_insentif_karyawan FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);


-- =============================================================
--  INDEXES payroll
-- =============================================================
CREATE INDEX idx_payroll_kryw_periode   ON payroll.payroll              (id_karyawan, tahun, bulan);
CREATE INDEX idx_payroll_status         ON payroll.payroll              (status);
CREATE INDEX idx_payroll_detail_payroll ON payroll.payroll_detail       (id_payroll);
CREATE INDEX idx_thr_tahun              ON payroll.thr                  (tahun, status);
CREATE INDEX idx_insentif_periode       ON payroll.insentif             (tahun, bulan, status);
