<?php
// FILE: app/Http/Controllers/Payroll/PayrollController.php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Auth\AuditLog;
use App\Models\Bpjs\{BpjsTk, BpjsKesehatan};
use App\Models\EmployeeKaryawan;
use App\Models\Master\{CostCenter, Golongan, ParameterBpjs, UnitPt};
use App\Models\Payroll\{Insentif, PayrollDetail, PayrollRecord, Thr};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    // ─────────────────────────────────────────────────────
    //  Helper: format nominal ke singkatan Rupiah
    //  contoh: 4_200_000 → "Rp 4,2jt"   1_800_000_000 → "Rp 1,8M"
    // ─────────────────────────────────────────────────────
    private function rp(float $v): string
    {
        if ($v >= 1_000_000_000) return 'Rp ' . number_format($v / 1_000_000_000, 1, ',', '.') . 'M';
        if ($v >= 1_000_000) {
            $x = round($v / 1_000_000, 1);
            return 'Rp ' . ($x == (int)$x ? (int)$x : $x) . 'jt';
        }
        if ($v >= 1_000) return 'Rp ' . number_format($v / 1_000, 0, ',', '.') . 'rb';
        return 'Rp ' . number_format($v, 0, ',', '.');
    }

    // ─────────────────────────────────────────────────────
    //  DASHBOARD — GET /payroll/dashboard
    // ─────────────────────────────────────────────────────
    public function index()
    {
        try {
            $bulan = now()->month;
            $tahun = now()->year;
            
            // Simple data untuk test
            return view('payroll.dashboard', [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'bulanLabel' => Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y'),
                
                // Stat cards - hardcoded untuk test
                'totalGajiBulanIni' => 'Rp 0',
                'jmlKaryawanGajian' => 0,
                'thrTerbayar' => 'Rp 0',
                'bpjsTkBulanIni' => 'Rp 0',
                'bpjsKesBulanIni' => 'Rp 0',
                
                // Alert flags
                'adaDraft' => false,
                'sudahApproved' => false,
                'deadlineBpjs' => true,
                
                // Empty collections untuk test
                'payrolls' => collect(),
                'thrs' => collect(),
                'insentifs' => collect(),
                'bpjsTks' => collect(),
                'bpjsKess' => collect(),
                
                // Empty options
                'karyawans' => collect(),
                'golongans' => collect(),
                'costCenters' => collect(),
                'paramBpjs' => null,
                
                // Flags
                'thrSudahAda' => false,
                'bpjsTkSudahAda' => false,
                'bpjsKesSudahAda' => false,
            ]);
        } catch (\Exception $e) {
            \Log::error('Payroll Dashboard Error: ' . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
    }

    // ─────────────────────────────────────────────────────
    //  PENGGAJIAN: Generate slip gaji
    //  POST /payroll/penggajian/proses
    // ─────────────────────────────────────────────────────
    public function penggajianProses(Request $request)
    {
        $req = $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
        ]);

        if (PayrollRecord::where('bulan', $req['bulan'])->where('tahun', $req['tahun'])->exists()) {
            return back()->with('error', "Penggajian {$req['bulan']}/{$req['tahun']} sudah pernah diproses.");
        }

        $param = ParameterBpjs::aktif();
        if (!$param) {
            return back()->with('error', 'Parameter BPJS belum dikonfigurasi di Master System.');
        }

        DB::transaction(function () use ($req, $param) {
            Karyawan::aktif()->with(['golongan', 'fasilitas'])->chunk(50, function ($list) use ($req, $param) {
                foreach ($list as $k) {
                    // basic_salary dari gaji_min golongan
                    $basicSalary = (float) ($k->golongan->gaji_min ?? 0);

                    // Cek apakah punya Car Allowance
                    $carAllow = 0;
                    if ($k->fasilitas && $k->fasilitas->jenis_fasilitas === 'Car Allowance') {
                        $carAllow = (float) ($k->fasilitas->nominal_allowance ?? 0);
                    }

                    // total_income = basic + tunjangan
                    $totalIncome = $basicSalary + $carAllow;

                    // total_deduction = potongan JHT + JP + BPJS Kes karyawan
                    $potJht   = round($basicSalary * ($param->jht_karyawan_pct / 100), 2);
                    $potJp    = round($basicSalary * ($param->jp_karyawan_pct  / 100), 2);
                    $potBpjsK = round($basicSalary * ($param->bpjs_kes_karyawan_pct / 100), 2);
                    $totalDed = $potJht + $potJp + $potBpjsK;

                    $rec = PayrollRecord::create([
                        'id_karyawan'     => $k->id,
                        'bulan'           => $req['bulan'],
                        'tahun'           => $req['tahun'],
                        'basic_salary'    => $basicSalary,
                        'total_income'    => $totalIncome,
                        'total_deduction' => $totalDed,
                        // take_home_pay = GENERATED COLUMN, jangan diisi
                        'status'          => 'draft',
                        'created_by'      => auth()->user()->nama,
                    ]);

                    // Insert detail komponen ke payroll.payroll_detail
                    $details = [
                        ['id_payroll' => $rec->id, 'id_component' => 1, 'amount' => $basicSalary, 'keterangan' => 'Gaji Pokok'],
                    ];
                    if ($carAllow > 0) {
                        $details[] = ['id_payroll' => $rec->id, 'id_component' => 2, 'amount' => $carAllow, 'keterangan' => 'Car Allowance'];
                    }
                    if ($potJht > 0) {
                        $details[] = ['id_payroll' => $rec->id, 'id_component' => 3, 'amount' => -$potJht, 'keterangan' => 'Potongan JHT Karyawan'];
                    }
                    if ($potBpjsK > 0) {
                        $details[] = ['id_payroll' => $rec->id, 'id_component' => 4, 'amount' => -$potBpjsK, 'keterangan' => 'Potongan BPJS Kesehatan'];
                    }
                    PayrollDetail::insert($details);
                }
            });
        });

        AuditLog::catat('PROSES_PENGGAJIAN', 'payroll.payroll', null, $req);
        return back()->with('success', "Penggajian {$req['bulan']}/{$req['tahun']} berhasil di-generate. Status: Draft.");
    }

    // POST /payroll/penggajian/approve
    public function penggajianApprove(Request $request)
    {
        $req = $request->validate(['bulan' => 'required|integer', 'tahun' => 'required|integer']);

        $updated = PayrollRecord::where('bulan', $req['bulan'])
            ->where('tahun', $req['tahun'])
            ->where('status', 'draft')
            ->update([
                'status'      => 'approved',
                'approved_by' => auth()->user()->nama,
                'approved_at' => now(),
            ]);

        if (!$updated) return back()->with('error', 'Tidak ada penggajian draft yang bisa di-approve.');

        AuditLog::catat('APPROVE_PENGGAJIAN', 'payroll.payroll', null, $req);
        return back()->with('success', "Penggajian {$req['bulan']}/{$req['tahun']} berhasil di-approve.");
    }

    // ─────────────────────────────────────────────────────
    //  THR: Hitung / Bayar / Hapus
    //  Kolom DB: jumlah_thr, masa_kerja_bulan, basic_salary, bulan_proses
    // ─────────────────────────────────────────────────────

    // POST /payroll/thr/hitung
    public function thrHitung(Request $request)
    {
        $req = $request->validate(['tahun' => 'required|integer|min:2020']);

        if (Thr::where('tahun', $req['tahun'])->exists()) {
            return back()->with('error', "THR tahun {$req['tahun']} sudah dihitung.");
        }

        DB::transaction(function () use ($req) {
            Karyawan::aktif()->with('golongan')->each(function (Karyawan $k) use ($req) {
                $masaBulan   = Carbon::parse($k->tanggal_masuk)->diffInMonths(now());
                $basicSalary = (float) ($k->golongan->gaji_min ?? 0);

                // >= 12 bulan = 1x gaji, < 12 bulan = proporsional
                $jumlahThr = $masaBulan >= 12
                    ? $basicSalary
                    : round(($masaBulan / 12) * $basicSalary, 2);

                Thr::create([
                    'id_karyawan'     => $k->id,
                    'tahun'           => $req['tahun'],
                    'basic_salary'    => $basicSalary,
                    'masa_kerja_bulan'=> $masaBulan,
                    'jumlah_thr'      => $jumlahThr,
                    'bulan_proses'    => now()->month,
                    'status'          => 'draft',
                    'created_by'      => auth()->user()->nama,
                ]);
            });
        });

        AuditLog::catat('HITUNG_THR', 'payroll.thr', null, $req);
        return back()->with('success', "THR tahun {$req['tahun']} berhasil dihitung untuk semua karyawan aktif.");
    }

    // POST /payroll/thr/{thr}/bayar
    public function thrBayar(Thr $thr)
    {
        $thr->update(['status' => 'paid']);
        return back()->with('success', "THR {$thr->karyawan->nama_karyawan} ditandai terbayar.");
    }

    // DELETE /payroll/thr/{thr}
    public function thrDestroy(Thr $thr)
    {
        if ($thr->status === 'paid') {
            return back()->with('error', 'THR yang sudah dibayar tidak bisa dihapus.');
        }
        $thr->delete();
        return back()->with('success', 'Data THR dihapus.');
    }

    // ─────────────────────────────────────────────────────
    //  INSENTIF: CRUD
    //  Kolom DB: bulan, tahun, jumlah, deskripsi  (BUKAN nominal/keterangan/periode_*)
    // ─────────────────────────────────────────────────────

    // POST /payroll/insentif
    public function insentifStore(Request $request)
    {
        $req = $request->validate([
            'id_karyawan' => 'required|integer|exists:employee.karyawan,id',
            'bulan'       => 'required|integer|min:1|max:12',
            'tahun'       => 'required|integer|min:2020',
            'jumlah'      => 'required|numeric|min:0',
            'deskripsi'   => 'nullable|string|max:200',
        ]);

        $ins = Insentif::create(array_merge($req, [
            'status'     => 'draft',
            'created_by' => auth()->user()->nama,
        ]));

        AuditLog::catat('CREATE_INSENTIF', 'payroll.insentif', $ins->id, $req);
        return back()->with('success', 'Insentif berhasil disimpan.');
    }

    // PUT /payroll/insentif/{insentif}
    public function insentifUpdate(Request $request, Insentif $insentif)
    {
        if ($insentif->status === 'paid') {
            return back()->with('error', 'Insentif yang sudah paid tidak bisa diubah.');
        }
        $req = $request->validate([
            'jumlah'    => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:200',
            'status'    => 'required|in:draft,approved,paid',
        ]);
        $insentif->update($req);
        return back()->with('success', 'Insentif diperbarui.');
    }

    // DELETE /payroll/insentif/{insentif}
    public function insentifDestroy(Insentif $insentif)
    {
        if ($insentif->status === 'paid') {
            return back()->with('error', 'Insentif yang sudah paid tidak bisa dihapus.');
        }
        $insentif->delete();
        return back()->with('success', 'Insentif dihapus.');
    }

    // ─────────────────────────────────────────────────────
    //  BPJS KETENAGAKERJAAN
    //  Kolom DB: periode (DATE), basic_salary,
    //            jht_company, jp_company, jkk, jkm, total_company,
    //            jht_employee, jp_employee, total_employee, id_parameter
    //  BUKAN: bulan, tahun, beban_*, dasar_upah
    // ─────────────────────────────────────────────────────

    // POST /payroll/bpjs-tk/generate
    public function bpjsTkGenerate(Request $request)
    {
        $req = $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
        ]);

        $periode = BpjsTk::toPeriode((int)$req['bulan'], (int)$req['tahun']);

        if (BpjsTk::where('periode', $periode)->exists()) {
            return back()->with('error', "BPJS TK periode {$periode} sudah digenerate.");
        }

        $param = ParameterBpjs::aktif();
        if (!$param) return back()->with('error', 'Parameter BPJS belum dikonfigurasi.');

        DB::transaction(function () use ($periode, $param) {
            Karyawan::aktif()->with('golongan')->chunk(100, function ($list) use ($periode, $param) {
                $rows = [];
                foreach ($list as $k) {
                    $gaji = (float) ($k->golongan->gaji_min ?? 0);

                    $jhtC = round($gaji * ($param->jht_perusahaan_pct / 100), 2);
                    $jpC  = round($gaji * ($param->jp_perusahaan_pct  / 100), 2);
                    $jkk  = round($gaji * ($param->jkk_pct            / 100), 2);
                    $jkm  = round($gaji * ($param->jkm_pct            / 100), 2);
                    $jhtE = round($gaji * ($param->jht_karyawan_pct   / 100), 2);
                    $jpE  = round($gaji * ($param->jp_karyawan_pct    / 100), 2);

                    $rows[] = [
                        'id_karyawan'   => $k->id,
                        'periode'       => $periode,
                        'basic_salary'  => $gaji,
                        'jht_company'   => $jhtC,
                        'jp_company'    => $jpC,
                        'jkk'           => $jkk,
                        'jkm'           => $jkm,
                        'total_company' => $jhtC + $jpC + $jkk + $jkm,
                        'jht_employee'  => $jhtE,
                        'jp_employee'   => $jpE,
                        'total_employee'=> $jhtE + $jpE,
                        'id_parameter'  => $param->id,
                        'created_at'    => now(),
                    ];
                }
                if ($rows) BpjsTk::insert($rows);
            });
        });

        AuditLog::catat('GENERATE_BPJS_TK', 'bpjs.bpjs_tk', null, ['periode' => $periode]);
        return back()->with('success', "BPJS Ketenagakerjaan periode {$periode} berhasil digenerate.");
    }

    // DELETE /payroll/bpjs-tk/{bpjsTk}
    public function bpjsTkDestroy(BpjsTk $bpjsTk)
    {
        $bpjsTk->delete();
        return back()->with('success', 'Record BPJS TK dihapus.');
    }

    // ─────────────────────────────────────────────────────
    //  BPJS KESEHATAN
    //  Kolom DB: periode (DATE), basic_salary,
    //            beban_perusahaan, beban_karyawan,
    //            total (GENERATED = beban_perusahaan + beban_karyawan), id_parameter
    //  BUKAN: bulan, tahun, dasar_upah, jumlah_tanggungan
    // ─────────────────────────────────────────────────────

    // POST /payroll/bpjs-kes/generate
    public function bpjsKesGenerate(Request $request)
    {
        $req = $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
        ]);

        $periode = BpjsKesehatan::toPeriode((int)$req['bulan'], (int)$req['tahun']);

        if (BpjsKesehatan::where('periode', $periode)->exists()) {
            return back()->with('error', "BPJS Kesehatan periode {$periode} sudah digenerate.");
        }

        $param = ParameterBpjs::aktif();
        if (!$param) return back()->with('error', 'Parameter BPJS belum dikonfigurasi.');

        DB::transaction(function () use ($periode, $param) {
            Karyawan::aktif()->with('golongan')->chunk(100, function ($list) use ($periode, $param) {
                $rows = [];
                foreach ($list as $k) {
                    $gaji  = (float) ($k->golongan->gaji_min ?? 0);
                    $bPrsh = round($gaji * ($param->bpjs_kes_perusahaan_pct / 100), 2);
                    $bKary = round($gaji * ($param->bpjs_kes_karyawan_pct   / 100), 2);
                    // 'total' = GENERATED COLUMN, jangan dimasukkan

                    $rows[] = [
                        'id_karyawan'      => $k->id,
                        'periode'          => $periode,
                        'basic_salary'     => $gaji,
                        'beban_perusahaan' => $bPrsh,
                        'beban_karyawan'   => $bKary,
                        'id_parameter'     => $param->id,
                        'created_at'       => now(),
                    ];
                }
                if ($rows) BpjsKesehatan::insert($rows);
            });
        });

        AuditLog::catat('GENERATE_BPJS_KES', 'bpjs.bpjs_kesehatan', null, ['periode' => $periode]);
        return back()->with('success', "BPJS Kesehatan periode {$periode} berhasil digenerate.");
    }

    // DELETE /payroll/bpjs-kes/{bpjsKesehatan}
    public function bpjsKesDestroy(BpjsKesehatan $bpjsKesehatan)
    {
        $bpjsKesehatan->delete();
        return back()->with('success', 'Record BPJS Kesehatan dihapus.');
    }
}
