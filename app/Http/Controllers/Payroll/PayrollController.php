<?php
// FILE: app/Http/Controllers/Payroll/PayrollController.php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Auth\AuditLog;
use App\Models\Bpjs\{BpjsTk, BpjsKesehatan};
use App\Models\EmployeeKaryawan;
use App\Models\EmployeeFasilitasKendaraan;
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

                // Load parameter BPJS aktif
                $paramBpjs = ParameterBpjs::whereNull('berlaku_selesai')->first();

                // Load data karyawan aktif
                $karyawans = EmployeeKaryawan::where('is_active', true)
                    ->with(['golongan', 'unit'])
                    ->orderBy('nama_karyawan')
                    ->get(['id', 'nip', 'nama_karyawan']);

                // Load payroll data untuk bulan ini
                $payrolls = PayrollRecord::with(['karyawan.golongan', 'karyawan.unit'])
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->orderBy('id')
                    ->get();

                // Filter karyawan yang belum diproses untuk form input
                $processedKaryawanIds = $payrolls->pluck('id_karyawan')->toArray();
                $karyawansBelumDiproses = $karyawans->whereNotIn('id', $processedKaryawanIds);

                // Hitung total karyawan aktif
                $totalKaryawanAktif = $karyawans->count();

                // Hitung status payroll
                $payrollDraft = $payrolls->where('status', 'draft');
                $payrollApproved = $payrolls->whereIn('status', ['approved', 'paid']);

                // Logic untuk menentukan status:
                // - adaDraft: ada record dengan status draft
                // - sudahApproved: SEMUA karyawan sudah diproses dan di-approve
                // - semuaKaryawanSudahDiproses: jumlah payroll record = jumlah karyawan aktif
                $adaDraft = $payrollDraft->count() > 0;
                $semuaKaryawanSudahDiproses = $payrolls->count() >= $totalKaryawanAktif;
                $sudahApproved = $semuaKaryawanSudahDiproses && $payrollApproved->count() >= $totalKaryawanAktif;

                return view('payroll.dashboard', [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'bulanLabel' => Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y'),

                    // Stat cards - calculate from actual data
                    'totalGajiBulanIni' => $this->rp((float) $payrollApproved->sum('total_income')),
                    'jmlKaryawanGajian' => $payrollApproved->count(),
                    'thrTerbayar' => 'Rp 0', // Will implement later
                    'bpjsTkBulanIni' => 'Rp 0', // Will implement later
                    'bpjsKesBulanIni' => 'Rp 0', // Will implement later

                    // Alert flags
                    'adaDraft' => $adaDraft,
                    'sudahApproved' => $sudahApproved,
                    'semuaKaryawanSudahDiproses' => $semuaKaryawanSudahDiproses,
                    'deadlineBpjs' => now()->day <= 14,

                    // Data collections
                    'payrolls' => $payrolls,
                    'thrs' => collect(), // Empty for now
                    'insentifs' => collect(), // Empty for now
                    'bpjsTks' => collect(), // Empty for now
                    'bpjsKess' => collect(), // Empty for now

                    // Dropdown options
                    'karyawans' => $karyawans,
                    'karyawansBelumDiproses' => $karyawansBelumDiproses,
                    'golongans' => Golongan::where('is_active', true)->orderBy('kode_golongan')->get(),
                    'costCenters' => CostCenter::where('is_active', true)->orderBy('nama_cc')->get(),
                    'paramBpjs' => $paramBpjs,

                    // Additional info
                    'totalKaryawanAktif' => $totalKaryawanAktif,
                    'jumlahSudahDiproses' => $payrolls->count(),

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
            'karyawan' => 'required|array',
            'karyawan.*.id' => 'required|integer|exists:employee_karyawan,id',
            'karyawan.*.gaji_pokok' => 'required|numeric|min:0',
            'karyawan.*.hari_wfo' => 'required|integer|min:0|max:31',
            'karyawan.*.hari_wfh' => 'required|integer|min:0|max:31',
            'karyawan.*.uang_makan' => 'required|numeric|min:0',
            'karyawan.*.uang_transport' => 'required|numeric|min:0',
            'karyawan.*.car_allowance' => 'required|numeric|min:0',
            'karyawan.*.total_income' => 'required|numeric|min:0',
            'karyawan.*.potongan_bpjs' => 'required|numeric|min:0',
            'karyawan.*.take_home_pay' => 'required|numeric|min:0',
        ]);

        if (PayrollRecord::where('bulan', $req['bulan'])->where('tahun', $req['tahun'])->exists()) {
            return back()->with('error', "Penggajian {$req['bulan']}/{$req['tahun']} sudah pernah diproses.");
        }

        $param = ParameterBpjs::whereNull('berlaku_selesai')->first();
        if (!$param) {
            return back()->with('error', 'Parameter BPJS belum dikonfigurasi di Master System.');
        }

        DB::transaction(function () use ($req, $param) {
            foreach ($req['karyawan'] as $karyawanData) {
                // Create payroll record
                $rec = PayrollRecord::create([
                    'id_karyawan'     => $karyawanData['id'],
                    'bulan'           => $req['bulan'],
                    'tahun'           => $req['tahun'],
                    'basic_salary'    => $karyawanData['gaji_pokok'],
                    'total_income'    => $karyawanData['total_income'],
                    'total_deduction' => $karyawanData['potongan_bpjs'],
                    'status'          => 'draft',
                    'created_by'      => auth()->user()->nama ?? 'system',
                ]);

                // Insert detail komponen
                $details = [];
                
                // Gaji Pokok
                if ($karyawanData['gaji_pokok'] > 0) {
                    $details[] = [
                        'id_payroll' => $rec->id, 
                        'id_component' => 1, 
                        'amount' => $karyawanData['gaji_pokok'], 
                        'keterangan' => 'Gaji Pokok'
                    ];
                }
                
                // Uang Makan
                if ($karyawanData['uang_makan'] > 0) {
                    $totalHari = $karyawanData['hari_wfo'] + $karyawanData['hari_wfh'];
                    $details[] = [
                        'id_payroll' => $rec->id, 
                        'id_component' => 2, 
                        'amount' => $karyawanData['uang_makan'], 
                        'keterangan' => "Uang Makan ({$totalHari} hari)"
                    ];
                }
                
                // Uang Transport
                if ($karyawanData['uang_transport'] > 0) {
                    $details[] = [
                        'id_payroll' => $rec->id, 
                        'id_component' => 3, 
                        'amount' => $karyawanData['uang_transport'], 
                        'keterangan' => "Uang Transport WFO ({$karyawanData['hari_wfo']} hari)"
                    ];
                }
                
                // Car Allowance
                if ($karyawanData['car_allowance'] > 0) {
                    $details[] = [
                        'id_payroll' => $rec->id, 
                        'id_component' => 4, 
                        'amount' => $karyawanData['car_allowance'], 
                        'keterangan' => 'Car Allowance'
                    ];
                }
                
                // Potongan BPJS
                if ($karyawanData['potongan_bpjs'] > 0) {
                    // Hitung detail potongan
                    $gajiPokok = $karyawanData['gaji_pokok'];
                    $potJht = round($gajiPokok * ($param->jht_karyawan_pct / 100), 2);
                    $potJp = round($gajiPokok * ($param->jp_karyawan_pct / 100), 2);
                    $potBpjsKes = round($gajiPokok * ($param->bpjs_kes_karyawan_pct / 100), 2);
                    
                    if ($potJht > 0) {
                        $details[] = [
                            'id_payroll' => $rec->id, 
                            'id_component' => 5, 
                            'amount' => -$potJht, 
                            'keterangan' => "Potongan JHT ({$param->jht_karyawan_pct}%)"
                        ];
                    }
                    
                    if ($potJp > 0) {
                        $details[] = [
                            'id_payroll' => $rec->id, 
                            'id_component' => 6, 
                            'amount' => -$potJp, 
                            'keterangan' => "Potongan JP ({$param->jp_karyawan_pct}%)"
                        ];
                    }
                    
                    if ($potBpjsKes > 0) {
                        $details[] = [
                            'id_payroll' => $rec->id, 
                            'id_component' => 7, 
                            'amount' => -$potBpjsKes, 
                            'keterangan' => "Potongan BPJS Kesehatan ({$param->bpjs_kes_karyawan_pct}%)"
                        ];
                    }
                }
                
                // Insert all details
                if (!empty($details)) {
                    PayrollDetail::insert($details);
                }
            }
        });

        AuditLog::create([
            'id_user' => auth()->id(),
            'action' => 'PROSES_PENGGAJIAN_DETAIL',
            'table_name' => 'payroll_payroll',
            'record_id' => null,
            'new_data' => json_encode(['bulan' => $req['bulan'], 'tahun' => $req['tahun'], 'jumlah_karyawan' => count($req['karyawan'])]),
        ]);
        
        $jumlahKaryawan = count($req['karyawan']);
        return back()->with('success', "Penggajian {$req['bulan']}/{$req['tahun']} berhasil diproses untuk {$jumlahKaryawan} karyawan dengan detail absensi. Status: Draft.");
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
                'approved_by' => auth()->user()->nama ?? 'system',
                'approved_at' => now(),
            ]);

        if (!$updated) return back()->with('error', 'Tidak ada penggajian draft yang bisa di-approve.');

        AuditLog::create([
            'id_user' => auth()->id(),
            'action' => 'APPROVE_PENGGAJIAN',
            'table_name' => 'payroll_payroll',
            'record_id' => null,
            'new_data' => json_encode($req),
        ]);
        
        return back()->with('success', "Penggajian {$req['bulan']}/{$req['tahun']} berhasil di-approve untuk {$updated} karyawan.");
    }
    /**
     * Reject draft penggajian (hapus semua data draft)
     */
    public function penggajianReject(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2099'
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        try {
            DB::beginTransaction();

            // Hapus semua payroll record dengan status draft untuk bulan/tahun ini
            $deleted = PayrollRecord::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->where('status', 'draft')
                ->delete();

            DB::commit();

            if ($deleted > 0) {
                return redirect()->route('payroll.dashboard')
                    ->with('success', "Draft penggajian bulan {$bulan}/{$tahun} berhasil dihapus. {$deleted} record dihapus.");
            } else {
                return redirect()->route('payroll.dashboard')
                    ->with('error', 'Tidak ada draft penggajian yang ditemukan untuk dihapus.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('payroll.dashboard')
                ->with('error', 'Gagal menghapus draft penggajian: ' . $e->getMessage());
        }
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
