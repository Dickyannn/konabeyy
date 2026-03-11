<?php

namespace App\Http\Controllers\PersonalAdmin;

use App\Http\Controllers\Controller;
use App\Models\Employee\Karyawan;
use App\Models\Employee\Position;
use App\Models\Employee\AnggotaKeluarga;
use App\Models\Employee\FasilitasKendaraan;
use App\Models\Employee\RiwayatJabatan;
use App\Models\Transaction\KontrakKaryawan;
use App\Models\Bpjs\BpjsTk;
use App\Models\Bpjs\BpjsKesehatan;
use App\Models\Master\Golongan;
use App\Models\Master\UnitPt;
use App\Models\Master\CostCenter;
use App\Models\Auth\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PersonalAdminController extends Controller
{
    // ══════════════════════════════════════════════════════
    //  DASHBOARD
    // ══════════════════════════════════════════════════════
    public function index()
    {
        $now = now();

        return view('personal-admin.dashboard', [

            // ── Stat cards ────────────────────────────────
            // Total karyawan aktif bulan ini
            'totalKaryawan' => Karyawan::where('status_aktif', true)->count(),

            // Karyawan bergabung bulan ini
            'karyawanBaru' => Karyawan::whereMonth('tanggal_bergabung', $now->month)
                                ->whereYear('tanggal_bergabung', $now->year)
                                ->count(),

            // Kontrak berakhir dalam 30 hari ke depan
            'kontrakBerakhir' => KontrakKaryawan::whereNotNull('tanggal_akhir_kontrak')
                                    ->where('is_aktif', true)
                                    ->whereBetween('tanggal_akhir_kontrak', [
                                        $now->toDateString(),
                                        $now->addDays(30)->toDateString(),
                                    ])->count(),

            // Karyawan di golongan H-11
            'golonganH11' => Karyawan::whereHas('golongan', fn($q) => $q->where('kode_golongan', 'H-11'))
                                ->where('status_aktif', true)->count(),

            // Alerts
            'kontrakAlertCount' => KontrakKaryawan::whereNotNull('tanggal_akhir_kontrak')
                                    ->where('is_aktif', true)
                                    ->whereBetween('tanggal_akhir_kontrak', [
                                        now()->toDateString(),
                                        now()->addDays(30)->toDateString(),
                                    ])->count(),

            'bpjsBelumUpload' => Karyawan::where('status_aktif', true)
                                    ->whereDoesntHave('bpjsTk')->count(),

            // ── Data untuk modal ──────────────────────────
            'golongans'   => Golongan::where('is_active', true)->orderBy('kode_golongan')->get(),
            'units'       => UnitPt::where('is_active', true)->orderBy('nama_pt')->get(),
            'costCenters' => CostCenter::where('is_active', true)->orderBy('nama_cc')->get(),
        ]);
    }

    // ══════════════════════════════════════════════════════
    //  DATA KARYAWAN
    // ══════════════════════════════════════════════════════
    public function karyawanIndex(Request $request)
    {
        $query = Karyawan::with(['golongan', 'unit', 'costCenter'])
            ->orderBy('nama_lengkap');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nip', 'ilike', "%$s%")
                  ->orWhere('nama_lengkap', 'ilike', "%$s%")
                  ->orWhere('jabatan', 'ilike', "%$s%")
            );
        }
        if ($request->filled('golongan')) {
            $query->whereHas('golongan', fn($q) => $q->where('kode_golongan', $request->golongan));
        }
        if ($request->filled('unit')) {
            $query->where('id_unit', $request->unit);
        }

        return response()->json([
            'data'  => $query->paginate(20),
            'stats' => [
                'total'   => Karyawan::where('status_aktif', true)->count(),
                'probasi' => KontrakKaryawan::where('tipe_kontrak', 'probation')->where('is_aktif', true)->count(),
            ],
        ]);
    }

    public function karyawanStore(Request $request)
    {
        $data = $request->validate([
            'nip'              => 'required|string|max:30|unique:employee.karyawan,nip',
            'nama_lengkap'     => 'required|string|max:150',
            'nik'              => 'nullable|string|size:16',
            'tanggal_lahir'    => 'nullable|date',
            'jenis_kelamin'    => 'nullable|in:L,P',
            'no_hp'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'alamat'           => 'nullable|string',
            'id_golongan'      => 'required|integer|exists:master.golongan,id',
            'jabatan'          => 'required|string|max:100',
            'id_unit'          => 'required|integer|exists:master.unit_pt,id',
            'id_cost_center'   => 'required|integer|exists:master.cost_center,id',
            'status_kawin'     => 'nullable|in:lajang,menikah,cerai',
            'status_aktif'     => 'boolean',
            'tanggal_bergabung'=> 'required|date',
            'gaji_pokok'       => 'nullable|numeric|min:0',
        ]);

        $karyawan = Karyawan::create($data);

        AuditLog::create([
            'id_user'    => auth()->id(),
            'action'     => 'CREATE_KARYAWAN',
            'table_name' => 'employee.karyawan',
            'record_id'  => $karyawan->id,
            'new_data'   => json_encode($data),
        ]);

        return back()->with('success', "Karyawan {$karyawan->nama_lengkap} berhasil ditambahkan.");
    }

    public function karyawanUpdate(Request $request, Karyawan $karyawan)
    {
        $data = $request->validate([
            'nip'              => "required|string|max:30|unique:employee.karyawan,nip,{$karyawan->id}",
            'nama_lengkap'     => 'required|string|max:150',
            'nik'              => 'nullable|string|size:16',
            'tanggal_lahir'    => 'nullable|date',
            'jenis_kelamin'    => 'nullable|in:L,P',
            'no_hp'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'alamat'           => 'nullable|string',
            'id_golongan'      => 'required|integer|exists:master.golongan,id',
            'jabatan'          => 'required|string|max:100',
            'id_unit'          => 'required|integer|exists:master.unit_pt,id',
            'id_cost_center'   => 'required|integer|exists:master.cost_center,id',
            'status_kawin'     => 'nullable|in:lajang,menikah,cerai',
            'status_aktif'     => 'boolean',
            'tanggal_bergabung'=> 'required|date',
            'gaji_pokok'       => 'nullable|numeric|min:0',
        ]);

        $old = $karyawan->toArray();
        $karyawan->update($data);

        AuditLog::create([
            'id_user'    => auth()->id(),
            'action'     => 'UPDATE_KARYAWAN',
            'table_name' => 'employee.karyawan',
            'record_id'  => $karyawan->id,
            'old_data'   => json_encode($old),
            'new_data'   => json_encode($data),
        ]);

        return back()->with('success', "Data karyawan {$karyawan->nama_lengkap} berhasil diupdate.");
    }

    public function karyawanDestroy(Karyawan $karyawan)
    {
        // Soft delete — nonaktifkan
        $karyawan->update(['status_aktif' => false]);

        AuditLog::create([
            'id_user'    => auth()->id(),
            'action'     => 'DEACTIVATE_KARYAWAN',
            'table_name' => 'employee.karyawan',
            'record_id'  => $karyawan->id,
            'old_data'   => json_encode(['status_aktif' => true]),
            'new_data'   => json_encode(['status_aktif' => false]),
        ]);

        return back()->with('success', "Karyawan {$karyawan->nama_lengkap} dinonaktifkan.");
    }

    // ══════════════════════════════════════════════════════
    //  KONTRAK KARYAWAN
    // ══════════════════════════════════════════════════════
    public function kontrakStore(Request $request)
    {
        $data = $request->validate([
            'id_karyawan'          => 'required|integer|exists:employee.karyawan,id',
            'tipe_kontrak'         => 'required|in:permanent,probation,pkwt',
            'tanggal_masuk'        => 'required|date',
            'tanggal_akhir_kontrak'=> 'nullable|date|after:tanggal_masuk',
            'no_sk'                => 'nullable|string|max:100',
            'catatan'              => 'nullable|string',
        ]);

        // Nonaktifkan kontrak lama
        KontrakKaryawan::where('id_karyawan', $data['id_karyawan'])
            ->where('is_aktif', true)
            ->update(['is_aktif' => false]);

        $kontrak = KontrakKaryawan::create(array_merge($data, ['is_aktif' => true]));

        return back()->with('success', 'Kontrak berhasil disimpan.');
    }

    public function kontrakUpdate(Request $request, KontrakKaryawan $kontrak)
    {
        $data = $request->validate([
            'tipe_kontrak'         => 'required|in:permanent,probation,pkwt',
            'tanggal_masuk'        => 'required|date',
            'tanggal_akhir_kontrak'=> 'nullable|date',
            'no_sk'                => 'nullable|string|max:100',
            'catatan'              => 'nullable|string',
        ]);

        $kontrak->update($data);
        return back()->with('success', 'Kontrak berhasil diupdate.');
    }

    public function kontrakDestroy(KontrakKaryawan $kontrak)
    {
        $kontrak->update(['is_aktif' => false]);
        return back()->with('success', 'Kontrak dinonaktifkan.');
    }

    // ══════════════════════════════════════════════════════
    //  POSISI / STRUKTUR ORGANISASI
    // ══════════════════════════════════════════════════════
    public function posisiStore(Request $request)
    {
        $data = $request->validate([
            'id_karyawan'         => 'required|integer|exists:employee.karyawan,id',
            'jabatan'             => 'required|string|max:100',
            'id_atasan'           => 'nullable|integer|exists:employee.karyawan,id',
            'departemen'          => 'required|string|max:100',
            'id_unit'             => 'required|integer|exists:master.unit_pt,id',
            'berlaku_mulai'       => 'nullable|date',
        ]);

        // Nonaktifkan posisi lama
        Position::where('id_karyawan', $data['id_karyawan'])
            ->whereNull('berlaku_selesai')
            ->update(['berlaku_selesai' => now()]);

        Position::create($data);
        return back()->with('success', 'Posisi berhasil disimpan.');
    }

    public function posisiDestroy(Position $posisi)
    {
        $posisi->update(['berlaku_selesai' => now()]);
        return back()->with('success', 'Posisi dinonaktifkan.');
    }

    // ══════════════════════════════════════════════════════
    //  FASILITAS KENDARAAN
    // ══════════════════════════════════════════════════════
    public function kendaraanStore(Request $request)
    {
        $data = $request->validate([
            'id_karyawan'     => 'required|integer|exists:employee.karyawan,id',
            'tipe_fasilitas'  => 'required|in:car_allowance,kendaraan_dinas',
            'nominal'         => 'nullable|numeric|min:0',
            'berlaku_mulai'   => 'required|date',
            'no_polisi'       => 'nullable|string|max:20',
        ]);

        // Nonaktifkan fasilitas lama
        FasilitasKendaraan::where('id_karyawan', $data['id_karyawan'])
            ->where('is_active', true)
            ->update(['is_active' => false, 'berlaku_selesai' => now()]);

        FasilitasKendaraan::create(array_merge($data, ['is_active' => true]));
        return back()->with('success', 'Fasilitas kendaraan berhasil disimpan.');
    }

    public function kendaraanDestroy(FasilitasKendaraan $fasilitas)
    {
        $fasilitas->update(['is_active' => false, 'berlaku_selesai' => now()]);
        return back()->with('success', 'Fasilitas kendaraan dinonaktifkan.');
    }

    // ══════════════════════════════════════════════════════
    //  DATA BPJS (View Only — no CUD)
    // ══════════════════════════════════════════════════════
    public function bpjsIndex(Request $request)
    {
        // Hanya read — data BPJS dikelola Payroll
        $query = Karyawan::with(['bpjsTk', 'bpjsKesehatan'])
            ->where('status_aktif', true)
            ->orderBy('nama_lengkap');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nama_lengkap', 'ilike', "%$s%")
                  ->orWhereHas('bpjsTk', fn($bq) => $bq->where('no_bpjs_tk', 'ilike', "%$s%"))
            );
        }

        return response()->json($query->paginate(20));
    }

    // ══════════════════════════════════════════════════════
    //  RIWAYAT JABATAN
    // ══════════════════════════════════════════════════════
    public function riwayatStore(Request $request)
    {
        $data = $request->validate([
            'id_karyawan'      => 'required|integer|exists:employee.karyawan,id',
            'tipe_perubahan'   => 'required|in:promosi,mutasi,demosi,rotasi',
            'jabatan_lama'     => 'nullable|string|max:100',
            'jabatan_baru'     => 'required|string|max:100',
            'id_golongan_baru' => 'required|integer|exists:master.golongan,id',
            'id_unit_baru'     => 'nullable|integer|exists:master.unit_pt,id',
            'tanggal_efektif'  => 'required|date',
            'no_sk'            => 'nullable|string|max:100',
            'keterangan'       => 'nullable|string',
        ]);

        $riwayat = RiwayatJabatan::create($data);

        // Update jabatan & golongan karyawan sesuai data terbaru
        $karyawan = Karyawan::find($data['id_karyawan']);
        $karyawan->update([
            'jabatan'      => $data['jabatan_baru'],
            'id_golongan'  => $data['id_golongan_baru'],
            'id_unit'      => $data['id_unit_baru'] ?? $karyawan->id_unit,
        ]);

        AuditLog::create([
            'id_user'    => auth()->id(),
            'action'     => 'RIWAYAT_JABATAN_' . strtoupper($data['tipe_perubahan']),
            'table_name' => 'employee.riwayat_jabatan',
            'record_id'  => $riwayat->id,
            'new_data'   => json_encode($data),
        ]);

        return back()->with('success', "Riwayat jabatan berhasil dicatat. Jabatan karyawan otomatis diupdate.");
    }

    public function riwayatUpdate(Request $request, RiwayatJabatan $riwayat)
    {
        $data = $request->validate([
            'tipe_perubahan'   => 'required|in:promosi,mutasi,demosi,rotasi',
            'jabatan_baru'     => 'required|string|max:100',
            'id_golongan_baru' => 'required|integer|exists:master.golongan,id',
            'id_unit_baru'     => 'nullable|integer|exists:master.unit_pt,id',
            'tanggal_efektif'  => 'required|date',
            'no_sk'            => 'nullable|string|max:100',
            'keterangan'       => 'nullable|string',
        ]);

        $riwayat->update($data);
        return back()->with('success', 'Riwayat jabatan berhasil diupdate.');
    }

    public function riwayatDestroy(RiwayatJabatan $riwayat)
    {
        $riwayat->delete();
        return back()->with('success', 'Riwayat jabatan dihapus.');
    }
}
