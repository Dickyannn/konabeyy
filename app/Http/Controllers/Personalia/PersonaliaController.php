<?php
// FILE: app/Http/Controllers/Personalia/PersonaliaController.php

namespace App\Http\Controllers\Personalia;

use App\Http\Controllers\Controller;
use App\Models\Auth\AuditLog;
use App\Models\{EmployeeKaryawan, EmployeePosition, MasterCostCenter, MasterGolongan, MasterUnitPt};
use App\Models\Master\{HariLibur, ShiftKerja};
use App\Models\Transaction\{Attendance, Cuti, Lembur, SaldoCuti};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonaliaController extends Controller
{
    // ════════════════════════════════════════════════════════
    //  DASHBOARD
    // ════════════════════════════════════════════════════════
    public function index()
    {
        $bulan = now()->month;
        $tahun = now()->year;

        // ── Stat 1: Hadir Hari Ini ──────────────────────────
        // Diambil dari transaction.attendance WHERE tanggal = today AND status = 'Hadir'
        $hadirHariIni = Attendance::where('tanggal', today())
            ->where('status', 'Hadir')
            ->count();

        // ── Stat 2: Belum absen hari ini ────────────────────
        $totalKaryawan = EmployeeKaryawan::where('is_active', true)->whereNull('tanggal_keluar')->count();
        $sudahAbsenHariIni = Attendance::where('tanggal', today())->count();
        $belumAbsen = max(0, $totalKaryawan - $sudahAbsenHariIni);

        // ── Stat 3: Lembur Bulan Ini ────────────────────────
        // SUM(total_jam) dari transaction.lembur status Approved bulan ini
        $lemburBulanIni = Lembur::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'Approved')
            ->sum('total_jam');

        // ── Stat 4: Rata-rata sisa cuti ──────────────────────
        // AVG(sisa) dari transaction.saldo_cuti tahun ini
        $rataRataCuti = SaldoCuti::where('tahun', $tahun)->avg('sisa') ?? 0;

        // ── Alert: Pengajuan cuti pending ───────────────────
        $cutiPending = Cuti::where('status', 'Pending')->count();

        // ── Alert: Lembur pending ───────────────────────────
        $lemburPending = Lembur::where('status', 'Pending')->count();

        // ── Data untuk rekap absensi (chart) ────────────────
        // Status breakdown hari ini
        $statusToday = Attendance::where('tanggal', today())
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Tren 7 hari terakhir: jumlah hadir per hari
        $trenHadir = Attendance::where('tanggal', '>=', now()->subDays(6)->toDateString())
            ->where('status', 'Hadir')
            ->select('tanggal', DB::raw('count(*) as total'))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('total', 'tanggal');

        // Rekap bulan ini per status
        $rekapBulan = Attendance::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('personalia.dashboard', [
            'bulan'           => $bulan,
            'tahun'           => $tahun,
            'bulanLabel'      => Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y'),
            'totalKaryawan'   => $totalKaryawan,

            // Stat cards
            'hadirHariIni'    => $hadirHariIni,
            'belumAbsen'      => $belumAbsen,
            'lemburBulanIni'  => round((float)$lemburBulanIni, 1),
            'rataRataCuti'    => round((float)$rataRataCuti, 0),

            // Alerts
            'cutiPending'     => $cutiPending,
            'lemburPending'   => $lemburPending,

            // Chart data (JSON untuk JS)
            'statusTodayJson' => $statusToday->toJson(),
            'rekapBulanJson'  => $rekapBulan->toJson(),
            'trenHadirJson'   => $trenHadir->toJson(),

            // Tabel data
            'karyawans'       => EmployeeKaryawan::with(['golongan', 'unit', 'currentPosition.costCenter'])
                                    ->where('is_active', true)->whereNull('tanggal_keluar')
                                    ->orderBy('nama_karyawan')->paginate(30, ['*'], 'pKar'),

            'absensiHariIni'  => Attendance::with(['karyawan.golongan', 'shift'])
                                    ->where('tanggal', today())
                                    ->orderByDesc('created_at')->paginate(30, ['*'], 'pAbs'),

            'cutis'           => Cuti::with('karyawan.golongan')
                                    ->orderByDesc('created_at')->paginate(30, ['*'], 'pCut'),

            'lemburs'         => Lembur::with('karyawan.golongan')
                                    ->orderByDesc('tanggal')->paginate(30, ['*'], 'pLem'),

            'shifts'          => ShiftKerja::where('is_active', true)->get(),
            'golongans'       => MasterGolongan::where('is_active', true)->orderBy('kode_golongan')->get(),
            'costCenters'     => MasterCostCenter::where('is_active', true)->orderBy('nama_cc')->get(),
            'unitPts'         => MasterUnitPt::where('is_active', true)->get(),

            // Rekap absensi detail bulan ini
            'rekapPerDept'    => Attendance::with('karyawan.currentPosition.costCenter')
                                    ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                                    ->select('id_karyawan', DB::raw('count(*) as total_hadir'),
                                             DB::raw("sum(terlambat_menit) as total_terlambat"),
                                             DB::raw("sum(case when status='Alpha' then 1 else 0 end) as total_alpha"))
                                    ->groupBy('id_karyawan')
                                    ->with('karyawan.golongan', 'karyawan.unit')
                                    ->paginate(30, ['*'], 'pRek'),

            // Kalender: hari libur + shifts untuk jadwal kerja
            'hariLiburs'      => HariLibur::where('tahun', $tahun)->orderBy('tanggal')->get(),
        ]);
    }

    // ════════════════════════════════════════════════════════
    //  ABSENSI HARIAN
    // ════════════════════════════════════════════════════════

    // POST /personalia/absensi
    public function absensiStore(Request $request)
    {
        $req = $request->validate([
            'id_karyawan'    => 'required|integer|exists:employee_karyawan,id',
            'tanggal'        => 'required|date',
            'id_shift'       => 'nullable|integer|exists:master_shift_kerja,id',
            'status'         => 'required|in:Hadir,Izin,Sakit,Alpha,Cuti,Libur',
            'check_in'       => 'nullable|date_format:H:i',
            'check_out'      => 'nullable|date_format:H:i',
            'terlambat_menit'=> 'nullable|integer|min:0',
            'keterangan'     => 'nullable|string|max:500',
            'source'         => 'in:manual,fingerprint,mobile',
        ]);

        // Hitung keterlambatan otomatis jika ada shift & check_in
        if (!empty($req['id_shift']) && !empty($req['check_in'])) {
            $shift = ShiftKerja::find($req['id_shift']);
            if ($shift) {
                $masuk   = Carbon::parse($req['tanggal'] . ' ' . $shift->jam_masuk);
                $checkIn = Carbon::parse($req['tanggal'] . ' ' . $req['check_in']);
                $selisih = $checkIn->diffInMinutes($masuk, false);
                if ($selisih < -($shift->toleransi_menit ?? 0)) {
                    $req['terlambat_menit'] = abs($selisih);
                }
            }
        }

        $abs = Attendance::updateOrCreate(
            ['id_karyawan' => $req['id_karyawan'], 'tanggal' => $req['tanggal']],
            array_merge($req, [
                'source'     => $req['source'] ?? 'manual',
                'created_by' => auth()->user()->nama,
            ])
        );

        AuditLog::catat('CREATE_ABSENSI', 'transaction_attendance', $abs->id, $req);
        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    // PUT /personalia/absensi/{attendance}
    public function absensiUpdate(Request $request, Attendance $attendance)
    {
        $req = $request->validate([
            'status'         => 'required|in:Hadir,Izin,Sakit,Alpha,Cuti,Libur',
            'check_in'       => 'nullable|date_format:H:i',
            'check_out'      => 'nullable|date_format:H:i',
            'terlambat_menit'=> 'nullable|integer|min:0',
            'keterangan'     => 'nullable|string|max:500',
        ]);
        $attendance->update($req);
        AuditLog::catat('UPDATE_ABSENSI', 'transaction_attendance', $attendance->id, $req);
        return back()->with('success', 'Absensi diperbarui.');
    }

    // DELETE /personalia/absensi/{attendance}
    public function absensiDestroy(Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('success', 'Record absensi dihapus.');
    }

    // ════════════════════════════════════════════════════════
    //  CUTI & IZIN
    // ════════════════════════════════════════════════════════

    // POST /personalia/cuti
    public function cutiStore(Request $request)
    {
        $req = $request->validate([
            'id_karyawan'    => 'required|integer|exists:employee_karyawan,id',
            'jenis_cuti'     => 'required|in:Tahunan,Sakit,Melahirkan,Khusus,Besar',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'required|date|after_or_equal:tanggal_mulai',
            'alasan'         => 'nullable|string|max:500',
        ]);
        // jumlah_hari = GENERATED COLUMN, tidak perlu diisi

        $cuti = Cuti::create(array_merge($req, ['status' => 'Pending']));
        AuditLog::catat('CREATE_CUTI', 'transaction_cuti', $cuti->id, $req);
        return back()->with('success', 'Pengajuan cuti berhasil disimpan.');
    }

    // PUT /personalia/cuti/{cuti}/approve
    public function cutiApprove(Request $request, Cuti $cuti)
    {
        $req = $request->validate([
            'status'           => 'required|in:Approved,Rejected',
            'catatan_approver' => 'nullable|string|max:500',
        ]);

        $cuti->update([
            'status'           => $req['status'],
            'catatan_approver' => $req['catatan_approver'] ?? null,
            'approved_by'      => auth()->user()->id_karyawan,
            'approved_at'      => now(),
        ]);

        // Jika approved, update saldo_cuti
        if ($req['status'] === 'Approved' && $cuti->jenis_cuti === 'Tahunan') {
            SaldoCuti::where('id_karyawan', $cuti->id_karyawan)
                ->where('tahun', $cuti->tanggal_mulai->year)
                ->increment('terpakai', $cuti->jumlah_hari);
        }

        AuditLog::catat('APPROVE_CUTI', 'transaction_cuti', $cuti->id, $req);
        return back()->with('success', 'Status cuti diperbarui.');
    }

    // DELETE /personalia/cuti/{cuti}
    public function cutiDestroy(Cuti $cuti)
    {
        if ($cuti->status === 'Approved') {
            return back()->with('error', 'Cuti yang sudah Approved tidak bisa dihapus.');
        }
        $cuti->delete();
        return back()->with('success', 'Pengajuan cuti dihapus.');
    }

    // ════════════════════════════════════════════════════════
    //  LEMBUR
    // ════════════════════════════════════════════════════════

    // POST /personalia/lembur
    public function lemburStore(Request $request)
    {
        $req = $request->validate([
            'id_karyawan' => 'required|integer|exists:employee_karyawan,id',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan'  => 'nullable|string|max:500',
        ]);

        // Hitung total_jam di app layer
        $mulai   = Carbon::parse($req['tanggal'] . ' ' . $req['jam_mulai']);
        $selesai = Carbon::parse($req['tanggal'] . ' ' . $req['jam_selesai']);
        $totalJam = round($mulai->floatDiffInHours($selesai), 2);

        $lembur = Lembur::create(array_merge($req, [
            'total_jam'  => $totalJam,
            'status'     => 'Pending',
            'created_by' => auth()->user()->nama,
        ]));

        AuditLog::catat('CREATE_LEMBUR', 'transaction_lembur', $lembur->id, $req);
        return back()->with('success', "Lembur {$totalJam} jam berhasil dicatat.");
    }

    // PUT /personalia/lembur/{lembur}/approve
    public function lemburApprove(Request $request, Lembur $lembur)
    {
        $req = $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);

        $lembur->update([
            'status'      => $req['status'],
            'approved_by' => auth()->user()->id_karyawan,
            'approved_at' => now(),
        ]);

        AuditLog::catat('APPROVE_LEMBUR', 'transaction_lembur', $lembur->id, $req);
        return back()->with('success', 'Status lembur diperbarui.');
    }

    // DELETE /personalia/lembur/{lembur}
    public function lemburDestroy(Lembur $lembur)
    {
        if ($lembur->status === 'Approved') {
            return back()->with('error', 'Lembur yang sudah Approved tidak bisa dihapus.');
        }
        $lembur->delete();
        return back()->with('success', 'Data lembur dihapus.');
    }
}
