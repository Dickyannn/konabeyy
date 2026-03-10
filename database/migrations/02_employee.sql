-- =============================================================
--  FILE: 02_employee.sql
--  Schema: employee
--  Tabel  : karyawan, position, anggota_keluarga,
--           fasilitas_kendaraan, riwayat_jabatan, dokumen_karyawan
--
--  Dependency: 01_master.sql harus sudah dijalankan
-- =============================================================


-- ── 1. employee.karyawan ─────────────────────────────────────
--  Tabel master utama seluruh sistem
--  Self-join: id_atasan → karyawan.id sendiri
--  FK → master.status_karyawan, master.status_kawin,
--        master.golongan, master.unit_pt
CREATE TABLE employee.karyawan (
    id                    SERIAL       NOT NULL,
    nip                   VARCHAR(20)  NOT NULL,
    nik                   VARCHAR(20),
    npwp                  VARCHAR(30),
    nama_karyawan         VARCHAR(150) NOT NULL,
    tanggal_lahir         DATE,
    jenis_kelamin         VARCHAR(15),
    alamat                TEXT,
    email                 VARCHAR(150),
    nomor_telepon         VARCHAR(25),
    bpjs_kesehatan_number VARCHAR(50),
    bpjs_tk_number        VARCHAR(50),
    tanggal_masuk         DATE         NOT NULL,
    tanggal_keluar        DATE,
    id_status_karyawan    INT          NOT NULL,
    id_status_kawin       INT,
    id_golongan           INT          NOT NULL,
    id_unit               INT          NOT NULL,
    id_atasan             INT,                     -- self-join, nullable
    foto_path             VARCHAR(255),
    is_active             BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at            TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    updated_at            TIMESTAMPTZ  NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_karyawan               PRIMARY KEY (id),
    CONSTRAINT uq_karyawan_nip           UNIQUE      (nip),
    CONSTRAINT uq_karyawan_nik           UNIQUE      (nik),
    CONSTRAINT uq_karyawan_email         UNIQUE      (email),
    CONSTRAINT ck_karyawan_jk            CHECK       (jenis_kelamin IN ('Laki-laki','Perempuan')),

    CONSTRAINT fk_karyawan_status        FOREIGN KEY (id_status_karyawan)
        REFERENCES master.status_karyawan (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_karyawan_status_kawin  FOREIGN KEY (id_status_kawin)
        REFERENCES master.status_kawin (id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    CONSTRAINT fk_karyawan_golongan      FOREIGN KEY (id_golongan)
        REFERENCES master.golongan (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_karyawan_unit          FOREIGN KEY (id_unit)
        REFERENCES master.unit_pt (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_karyawan_atasan        FOREIGN KEY (id_atasan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE SET NULL    -- atasan dihapus → NULL, bukan cascade
);


-- ── 2. employee.position ─────────────────────────────────────
--  Jabatan aktif + history per karyawan
--  tanggal_selesai NULL = jabatan yang sedang aktif sekarang
--  FK → employee.karyawan, master.cost_center
CREATE TABLE employee.position (
    id              SERIAL       NOT NULL,
    id_karyawan     INT          NOT NULL,
    id_cost_center  INT          NOT NULL,
    nama_jabatan    VARCHAR(150) NOT NULL,
    tanggal_mulai   DATE         NOT NULL,
    tanggal_selesai DATE,
    is_current      BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMPTZ  NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_position              PRIMARY KEY (id),
    CONSTRAINT fk_position_karyawan     FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_position_cost_center  FOREIGN KEY (id_cost_center)
        REFERENCES master.cost_center (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);


-- ── 3. employee.anggota_keluarga ─────────────────────────────
--  Data suami/istri/anak untuk keperluan BPJS & administrasi
--  FK → employee.karyawan
CREATE TABLE employee.anggota_keluarga (
    id              SERIAL       NOT NULL,
    id_karyawan     INT          NOT NULL,
    nama            VARCHAR(150) NOT NULL,
    hubungan        VARCHAR(50)  NOT NULL,   -- Suami, Istri, Anak
    tanggal_lahir   DATE,
    tanggal_menikah DATE,
    is_tanggungan   BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMPTZ  NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_anggota_keluarga          PRIMARY KEY (id),
    CONSTRAINT fk_anggota_keluarga_karyawan FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);


-- ── 4. employee.fasilitas_kendaraan ──────────────────────────
--  Assign kendaraan dinas / car allowance ke karyawan
--  ATURAN BISNIS: jika ada record is_active=TRUE di sini
--                 → karyawan TIDAK dapat uang_makan & uang_transport
--  FK → employee.karyawan
CREATE TABLE employee.fasilitas_kendaraan (
    id                SERIAL        NOT NULL,
    id_karyawan       INT           NOT NULL,
    jenis_fasilitas   VARCHAR(50)   NOT NULL,
    nominal_allowance NUMERIC(15,2),            -- diambil dari master.car_allowance saat assign
    nomor_polisi      VARCHAR(20),              -- diisi jika jenis = Kendaraan Dinas
    tgl_berlaku       DATE          NOT NULL,
    tgl_berakhir      DATE,
    is_active         BOOLEAN       NOT NULL DEFAULT TRUE,
    created_at        TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
    created_by        VARCHAR(100),

    CONSTRAINT pk_fasilitas_kendaraan           PRIMARY KEY (id),
    CONSTRAINT ck_fasilitas_kendaraan_jenis     CHECK (jenis_fasilitas IN ('Kendaraan Dinas','Car Allowance')),
    CONSTRAINT fk_fasilitas_kendaraan_karyawan  FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);


-- ── 5. employee.riwayat_jabatan ──────────────────────────────
--  Audit trail perubahan karir (promosi, mutasi, dll)
--  READ-ONLY setelah insert — tidak boleh di-UPDATE
--  FK → employee.karyawan, master.golongan (2x: lama & baru)
CREATE TABLE employee.riwayat_jabatan (
    id              SERIAL       NOT NULL,
    id_karyawan     INT          NOT NULL,
    jabatan_lama    VARCHAR(150),
    jabatan_baru    VARCHAR(150) NOT NULL,
    golongan_lama   INT,
    golongan_baru   INT          NOT NULL,
    jenis_perubahan VARCHAR(50)  NOT NULL,
    tgl_efektif     DATE         NOT NULL,
    nomor_sk        VARCHAR(100),
    catatan         TEXT,
    created_at      TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    created_by      VARCHAR(100),

    CONSTRAINT pk_riwayat_jabatan              PRIMARY KEY (id),
    CONSTRAINT ck_riwayat_jabatan_jenis        CHECK (jenis_perubahan IN ('Promosi','Demosi','Mutasi','Rotasi','Pengangkatan')),

    CONSTRAINT fk_riwayat_jabatan_karyawan     FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_riwayat_jabatan_gol_lama     FOREIGN KEY (golongan_lama)
        REFERENCES master.golongan (id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    CONSTRAINT fk_riwayat_jabatan_gol_baru     FOREIGN KEY (golongan_baru)
        REFERENCES master.golongan (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);


-- ── 6. employee.dokumen_karyawan ─────────────────────────────
--  Penyimpanan path file dokumen (kontrak, SK, KTP, dll)
--  FK → employee.karyawan
CREATE TABLE employee.dokumen_karyawan (
    id            SERIAL       NOT NULL,
    id_karyawan   INT          NOT NULL,
    jenis_dokumen VARCHAR(100) NOT NULL,
    nama_file     VARCHAR(255) NOT NULL,
    file_path     VARCHAR(500) NOT NULL,
    tgl_upload    TIMESTAMPTZ  NOT NULL DEFAULT NOW(),
    uploaded_by   VARCHAR(100),
    keterangan    TEXT,

    CONSTRAINT pk_dokumen_karyawan          PRIMARY KEY (id),
    CONSTRAINT fk_dokumen_karyawan_karyawan FOREIGN KEY (id_karyawan)
        REFERENCES employee.karyawan (id)
        ON UPDATE CASCADE ON DELETE CASCADE
);


-- =============================================================
--  INDEXES employee
-- =============================================================
CREATE INDEX idx_karyawan_nip      ON employee.karyawan (nip);
CREATE INDEX idx_karyawan_unit     ON employee.karyawan (id_unit);
CREATE INDEX idx_karyawan_golongan ON employee.karyawan (id_golongan);
CREATE INDEX idx_karyawan_atasan   ON employee.karyawan (id_atasan);
CREATE INDEX idx_karyawan_aktif    ON employee.karyawan (is_active);
CREATE INDEX idx_position_current  ON employee.position (id_karyawan, is_current);


-- =============================================================
--  SEED DATA employee
-- =============================================================

INSERT INTO employee.karyawan
    (nip, nik, npwp, nama_karyawan, tanggal_lahir, jenis_kelamin,
     alamat, email, nomor_telepon,
     bpjs_kesehatan_number, bpjs_tk_number,
     tanggal_masuk, id_status_karyawan, id_status_kawin, id_golongan, id_unit)
VALUES
    ('21000001', '327601', '09.888', 'Bambang',
     '1985-01-10', 'Laki-laki', 'Purwakarta',
     'bambang@mail.com', '08123456789',
     'BPJS001', 'BPJSTK001',
     '2021-01-01', 1, 2, 1, 1);

INSERT INTO employee.position (id_karyawan, id_cost_center, nama_jabatan, tanggal_mulai)
VALUES (1, 1, 'F&A Manager', '2021-01-01');

INSERT INTO employee.anggota_keluarga (id_karyawan, nama, hubungan, tanggal_lahir)
VALUES (1, 'Siti', 'Istri', '1987-02-10');
