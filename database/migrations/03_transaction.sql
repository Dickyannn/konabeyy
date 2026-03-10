-- =============================================================
--  FILE: 03_transaction.sql
--  Schema: transaction
--  Tabel  : attendance, cuti, saldo_cuti, lembur,
--           kontrak_karyawan
--
--  Dependency: 01_master.sql + 02_employee.sql harus sudah jalan
-- =============================================================


-- ── 1. transaction.attendance ────────────────────────────────
--  Absensi harian — 1 baris per karyawan per hari (UNIQUE)
--  FK → employee.karyawan, master.shift_kerja
CREATE TABLE transaction.attendance (
    id              SERIAL      NOT NULL,
    id_karyawan     INT         NOT NULL,
    tanggal         DATE        NOT NULL,
    id_shift        INT,                     -- nullable: guest/tanpa shift
    status          VARCHAR(20) NOT NULL,
    check_in        TIME,
    check_out       TIME,
    terlambat_menit INT         NOT NULL DEFAULT 0,
    keterangan      TEXT,
    source          VARCHAR(20) NOT NULL DEFAULT 'manual',
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by      VARCHAR(100),

    CONSTRAINT pk_attendance              PRIMARY KEY (id),
    CONSTRAINT uq_attendance_kryw_tgl     UNIQUE      (id_karyawan, tanggal),
    CONSTRAINT ck_attendance_status       CHECK       (status IN ('Hadir','Izin','Sakit','Alpha','Cuti','Libur')),
    CONSTRAINT ck_attendance_source       CHECK       (source IN ('manual','fingerprint','mobile')),

    CONSTRAINT fk_attendance_karyawan     FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_attendance_shift        FOREIGN KEY (id_shift)
        REFERENCES master.shift_kerja (id)
        ON UPDATE CASCADE ON DELETE SET NULL
);


-- ── 2. transaction.cuti ──────────────────────────────────────
--  Pengajuan cuti karyawan + alur approval
--  jumlah_hari = generated otomatis dari selisih tanggal
--  FK → employee.karyawan (2x: pemohon & approver)
CREATE TABLE transaction.cuti (
    id               SERIAL       NOT NULL,
    id_karyawan      INT          NOT NULL,
    jenis_cuti       VARCHAR(50)  NOT NULL,
    tanggal_mulai    DATE         NOT NULL,
    tanggal_selesai  DATE         NOT NULL,
    jumlah_hari      INT          NOT NULL
                         GENERATED ALWAYS AS (tanggal_selesai - tanggal_mulai + 1) STORED,
    alasan           TEXT,
    status           VARCHAR(20)  NOT NULL DEFAULT 'Pending',
    approved_by      INT,                    -- NULL sampai diapprove/reject
    approved_at      TIMESTAMPTZ,
    catatan_approver TEXT,
    dokumen_path     VARCHAR(500),
    created_at       TIMESTAMPTZ  NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_cuti                  PRIMARY KEY (id),
    CONSTRAINT ck_cuti_jenis            CHECK (jenis_cuti IN ('Tahunan','Sakit','Melahirkan','Khusus','Besar')),
    CONSTRAINT ck_cuti_status           CHECK (status IN ('Pending','Approved','Rejected','Cancelled')),
    CONSTRAINT ck_cuti_tgl              CHECK (tanggal_selesai >= tanggal_mulai),

    CONSTRAINT fk_cuti_karyawan         FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_cuti_approved_by      FOREIGN KEY (approved_by)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE SET NULL
);


-- ── 3. transaction.saldo_cuti ────────────────────────────────
--  Kuota & sisa cuti tahunan per karyawan per tahun
--  sisa = generated otomatis dari kuota - terpakai
--  FK → employee.karyawan
CREATE TABLE transaction.saldo_cuti (
    id          SERIAL      NOT NULL,
    id_karyawan INT         NOT NULL,
    tahun       INT         NOT NULL,
    kuota       INT         NOT NULL DEFAULT 12,
    terpakai    INT         NOT NULL DEFAULT 0,
    sisa        INT         NOT NULL
                    GENERATED ALWAYS AS (kuota - terpakai) STORED,
    updated_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_saldo_cuti            PRIMARY KEY (id),
    CONSTRAINT uq_saldo_cuti_kryw_thn   UNIQUE      (id_karyawan, tahun),
    CONSTRAINT ck_saldo_cuti_terpakai   CHECK       (terpakai >= 0),
    CONSTRAINT ck_saldo_cuti_kuota      CHECK       (terpakai <= kuota),

    CONSTRAINT fk_saldo_cuti_karyawan   FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);


-- ── 4. transaction.lembur ────────────────────────────────────
--  Pencatatan & approval lembur
--  nominal_lembur dihitung di app layer setelah approve
--  sudah_dibayar di-update oleh modul payroll
--  FK → employee.karyawan (2x: pemohon & approver)
CREATE TABLE transaction.lembur (
    id             SERIAL        NOT NULL,
    id_karyawan    INT           NOT NULL,
    tanggal        DATE          NOT NULL,
    jam_mulai      TIME          NOT NULL,
    jam_selesai    TIME          NOT NULL,
    total_jam      NUMERIC(4,2)  NOT NULL,
    keterangan     TEXT,
    status         VARCHAR(20)   NOT NULL DEFAULT 'Pending',
    approved_by    INT,
    approved_at    TIMESTAMPTZ,
    nominal_lembur NUMERIC(15,2),
    sudah_dibayar  BOOLEAN       NOT NULL DEFAULT FALSE,
    created_at     TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    created_by     VARCHAR(100),

    CONSTRAINT pk_lembur                PRIMARY KEY (id),
    CONSTRAINT ck_lembur_status         CHECK (status IN ('Pending','Approved','Rejected')),
    CONSTRAINT ck_lembur_total_jam      CHECK (total_jam > 0),

    CONSTRAINT fk_lembur_karyawan       FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_lembur_approved_by    FOREIGN KEY (approved_by)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE SET NULL
);


-- ── 5. transaction.kontrak_karyawan ──────────────────────────
--  History setiap perubahan status kontrak karyawan
--  (Baru, Perpanjang, Angkat Tetap, PHK, Resign, Pensiun)
--  FK → employee.karyawan, master.status_karyawan (2x: lama & baru)
CREATE TABLE transaction.kontrak_karyawan (
    id               SERIAL       NOT NULL,
    id_karyawan      INT          NOT NULL,
    id_status_lama   INT,                    -- NULL untuk kontrak pertama
    id_status_baru   INT          NOT NULL,
    jenis_perubahan  VARCHAR(50)  NOT NULL,
    tanggal_berlaku  DATE         NOT NULL,
    tanggal_berakhir DATE,
    nomor_dokumen    VARCHAR(100),
    catatan          TEXT,
    dokumen_path     VARCHAR(500),
    created_at       TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    created_by       VARCHAR(100),

    CONSTRAINT pk_kontrak_karyawan              PRIMARY KEY (id),
    CONSTRAINT ck_kontrak_karyawan_jenis        CHECK (jenis_perubahan IN
        ('Baru','Perpanjang','Angkat Tetap','PHK','Resign','Pensiun')),

    CONSTRAINT fk_kontrak_karyawan_karyawan     FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_kontrak_karyawan_status_lama  FOREIGN KEY (id_status_lama)
        REFERENCES master.status_karyawan (id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    CONSTRAINT fk_kontrak_karyawan_status_baru  FOREIGN KEY (id_status_baru)
        REFERENCES master.status_karyawan (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);


-- =============================================================
--  INDEXES transaction
-- =============================================================
CREATE INDEX idx_attendance_kryw_tgl  ON transaction.attendance      (id_karyawan, tanggal);
CREATE INDEX idx_attendance_status    ON transaction.attendance      (status, tanggal);
CREATE INDEX idx_cuti_kryw_status     ON transaction.cuti            (id_karyawan, status);
CREATE INDEX idx_cuti_periode         ON transaction.cuti            (tanggal_mulai, tanggal_selesai);
CREATE INDEX idx_lembur_kryw_status   ON transaction.lembur          (id_karyawan, status);
CREATE INDEX idx_lembur_dibayar       ON transaction.lembur          (sudah_dibayar) WHERE sudah_dibayar = FALSE;
CREATE INDEX idx_kontrak_kryw         ON transaction.kontrak_karyawan(id_karyawan, tanggal_berlaku);
