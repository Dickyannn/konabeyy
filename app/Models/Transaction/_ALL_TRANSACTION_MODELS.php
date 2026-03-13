<?php
// ╔══════════════════════════════════════════════════════════════════╗
// ║  Letakkan masing-masing class di file terpisah                  ║
// ╚══════════════════════════════════════════════════════════════════╝

// ─────────────────────────────────────────────────────────────────
// FILE: app/Models/Transaction/Attendance.php
// TABLE: transaction.attendance
// KOLOM: id, id_karyawan, tanggal, id_shift, status, check_in,
//        check_out, terlambat_menit, keterangan, source, created_at, created_by
// STATUS VALUES: 'Hadir','Izin','Sakit','Alpha','Cuti','Libur'
// SOURCE VALUES: 'manual','fingerprint','mobile'
// UNIQUE (id_karyawan, tanggal)
// ─────────────────────────────────────────────────────────────────
namespace App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee\Karyawan;
use App\Models\Master\ShiftKerja;

class Attendance extends Model
{
    protected $table      = 'transaction.attendance';
    public    $timestamps = false;
    protected $fillable   = [
        'id_karyawan','tanggal','id_shift','status',
        'check_in','check_out','terlambat_menit',
        'keterangan','source','created_by',
    ];
    protected $casts = [
        'tanggal'    => 'date',
        'created_at' => 'datetime',
    ];

    public function karyawan() { return $this->belongsTo(Karyawan::class,   'id_karyawan'); }
    public function shift()    { return $this->belongsTo(ShiftKerja::class, 'id_shift'); }

    // Scope: hari ini
    public function scopeHariIni($q)
    {
        return $q->where('tanggal', today());
    }
    // Scope: bulan tertentu
    public function scopeBulan($q, int $bulan, int $tahun)
    {
        return $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }
    // Scope: hadir saja
    public function scopeHadir($q) { return $q->where('status', 'Hadir'); }
}


// ─────────────────────────────────────────────────────────────────
// FILE: app/Models/Transaction/Cuti.php
// TABLE: transaction.cuti
// KOLOM: id, id_karyawan, jenis_cuti, tanggal_mulai, tanggal_selesai,
//        jumlah_hari (GENERATED), alasan, status, approved_by,
//        approved_at, catatan_approver, dokumen_path, created_at
// STATUS: 'Pending','Approved','Rejected','Cancelled'
// JENIS:  'Tahunan','Sakit','Melahirkan','Khusus','Besar'
// ─────────────────────────────────────────────────────────────────
namespace App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee\Karyawan;

class Cuti extends Model
{
    protected $table      = 'transaction.cuti';
    public    $timestamps = false;
    protected $fillable   = [
        'id_karyawan','jenis_cuti','tanggal_mulai','tanggal_selesai',
        // jumlah_hari = GENERATED ALWAYS AS (tanggal_selesai - tanggal_mulai + 1) STORED
        'alasan','status','approved_by','approved_at',
        'catatan_approver','dokumen_path',
    ];
    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'approved_at'     => 'datetime',
        'created_at'      => 'datetime',
        'jumlah_hari'     => 'integer', // GENERATED — read-only
    ];

    public function karyawan()  { return $this->belongsTo(Karyawan::class, 'id_karyawan'); }
    public function approver()  { return $this->belongsTo(Karyawan::class, 'approved_by'); }

    public function scopePending($q)  { return $q->where('status', 'Pending'); }
    public function scopeApproved($q) { return $q->where('status', 'Approved'); }
}


// ─────────────────────────────────────────────────────────────────
// FILE: app/Models/Transaction/SaldoCuti.php
// TABLE: transaction.saldo_cuti
// KOLOM: id, id_karyawan, tahun, kuota, terpakai,
//        sisa (GENERATED = kuota - terpakai), updated_at
// UNIQUE (id_karyawan, tahun)
// ─────────────────────────────────────────────────────────────────
namespace App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee\Karyawan;

class SaldoCuti extends Model
{
    protected $table      = 'transaction.saldo_cuti';
    public    $timestamps = false;
    protected $fillable   = ['id_karyawan','tahun','kuota','terpakai'];
    // sisa = GENERATED ALWAYS AS (kuota - terpakai) STORED — read-only
    protected $casts      = ['sisa' => 'integer', 'updated_at' => 'datetime'];

    public function karyawan() { return $this->belongsTo(Karyawan::class, 'id_karyawan'); }
}


// ─────────────────────────────────────────────────────────────────
// FILE: app/Models/Transaction/Lembur.php
// TABLE: transaction.lembur
// KOLOM: id, id_karyawan, tanggal, jam_mulai, jam_selesai, total_jam,
//        keterangan, status, approved_by, approved_at,
//        nominal_lembur, sudah_dibayar, created_at, created_by
// STATUS: 'Pending','Approved','Rejected'
// ─────────────────────────────────────────────────────────────────
namespace App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee\Karyawan;

class Lembur extends Model
{
    protected $table      = 'transaction.lembur';
    public    $timestamps = false;
    protected $fillable   = [
        'id_karyawan','tanggal','jam_mulai','jam_selesai','total_jam',
        'keterangan','status','approved_by','approved_at',
        'nominal_lembur','sudah_dibayar','created_by',
    ];
    protected $casts = [
        'tanggal'       => 'date',
        'approved_at'   => 'datetime',
        'created_at'    => 'datetime',
        'total_jam'     => 'float',
        'nominal_lembur'=> 'float',
        'sudah_dibayar' => 'boolean',
    ];

    public function karyawan()  { return $this->belongsTo(Karyawan::class, 'id_karyawan'); }
    public function approver()  { return $this->belongsTo(Karyawan::class, 'approved_by'); }

    public function scopePending($q)  { return $q->where('status', 'Pending'); }
    public function scopeApproved($q) { return $q->where('status', 'Approved'); }
    public function scopeBulan($q, int $bulan, int $tahun)
    {
        return $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }
}


// ─────────────────────────────────────────────────────────────────
// FILE: app/Models/Master/ShiftKerja.php
// TABLE: master.shift_kerja
// KOLOM: id, nama_shift, jam_masuk, jam_pulang, toleransi_menit, is_active
// ─────────────────────────────────────────────────────────────────
namespace App\Models\Master;
use Illuminate\Database\Eloquent\Model;

class ShiftKerja extends Model
{
    protected $table    = 'master.shift_kerja';
    public    $timestamps = false;
    protected $fillable = ['nama_shift','jam_masuk','jam_pulang','toleransi_menit','is_active'];
    protected $casts    = ['is_active' => 'boolean'];
}


// ─────────────────────────────────────────────────────────────────
// FILE: app/Models/Master/HariLibur.php
// TABLE: master.hari_libur
// KOLOM: id, tanggal, keterangan, tahun (GENERATED)
// UNIQUE: tanggal
// ─────────────────────────────────────────────────────────────────
namespace App\Models\Master;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    protected $table      = 'master.hari_libur';
    public    $timestamps = false;
    protected $fillable   = ['tanggal','keterangan'];
    // tahun = GENERATED ALWAYS AS (EXTRACT(YEAR FROM tanggal)::INT) STORED
    protected $casts = ['tanggal' => 'date', 'tahun' => 'integer'];

    public function scopeTahun($q, int $tahun) { return $q->where('tahun', $tahun); }
}
