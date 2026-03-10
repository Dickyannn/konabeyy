-- =============================================================
--  FILE: 05_bpjs.sql
--  Schema: bpjs
--  Tabel  : bpjs_tk, bpjs_kesehatan
--
--  Dependency: 01_master.sql + 02_employee.sql harus sudah jalan
-- =============================================================


-- ── 1. bpjs.bpjs_tk ──────────────────────────────────────────
--  Kalkulasi BPJS Ketenagakerjaan (JHT, JP, JKK, JKM)
--  Snapshot id_parameter disimpan agar history tetap akurat
--  FK → employee.karyawan, master.parameter_bpjs
CREATE TABLE bpjs.bpjs_tk (
    id             SERIAL        NOT NULL,
    id_karyawan    INT           NOT NULL,
    periode        DATE          NOT NULL,   -- selalu tanggal 1, cth: 2024-05-01
    basic_salary   NUMERIC(15,2) NOT NULL,
    -- beban perusahaan
    jht_company    NUMERIC(15,2) NOT NULL,
    jp_company     NUMERIC(15,2) NOT NULL,
    jkk            NUMERIC(15,2) NOT NULL,
    jkm            NUMERIC(15,2) NOT NULL,
    total_company  NUMERIC(15,2) NOT NULL,
    -- beban karyawan
    jht_employee   NUMERIC(15,2) NOT NULL,
    jp_employee    NUMERIC(15,2) NOT NULL,
    total_employee NUMERIC(15,2) NOT NULL,
    -- snapshot parameter saat kalkulasi
    id_parameter   INT           NOT NULL,
    created_at     TIMESTAMPTZ   NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_bpjs_tk               PRIMARY KEY (id),
    CONSTRAINT uq_bpjs_tk_kryw_periode  UNIQUE      (id_karyawan, periode),

    CONSTRAINT fk_bpjs_tk_karyawan      FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_bpjs_tk_parameter     FOREIGN KEY (id_parameter)
        REFERENCES master.parameter_bpjs (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);


-- ── 2. bpjs.bpjs_kesehatan ───────────────────────────────────
--  Kalkulasi BPJS Kesehatan
--  total = generated otomatis (beban_perusahaan + beban_karyawan)
--  FK → employee.karyawan, master.parameter_bpjs
CREATE TABLE bpjs.bpjs_kesehatan (
    id               SERIAL        NOT NULL,
    id_karyawan      INT           NOT NULL,
    periode          DATE          NOT NULL,
    basic_salary     NUMERIC(15,2) NOT NULL,
    beban_perusahaan NUMERIC(15,2) NOT NULL,
    beban_karyawan   NUMERIC(15,2) NOT NULL,
    total            NUMERIC(15,2) NOT NULL
                         GENERATED ALWAYS AS (beban_perusahaan + beban_karyawan) STORED,
    id_parameter     INT           NOT NULL,
    created_at       TIMESTAMPTZ   NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_bpjs_kes               PRIMARY KEY (id),
    CONSTRAINT uq_bpjs_kes_kryw_periode  UNIQUE      (id_karyawan, periode),

    CONSTRAINT fk_bpjs_kes_karyawan      FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_bpjs_kes_parameter     FOREIGN KEY (id_parameter)
        REFERENCES master.parameter_bpjs (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);


-- =============================================================
--  INDEXES bpjs
-- =============================================================
CREATE INDEX idx_bpjs_tk_periode   ON bpjs.bpjs_tk        (id_karyawan, periode);
CREATE INDEX idx_bpjs_kes_periode  ON bpjs.bpjs_kesehatan (id_karyawan, periode);
