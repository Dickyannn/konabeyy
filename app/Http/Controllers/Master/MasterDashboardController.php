<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Golongan;
use App\Models\Master\CostCenter;
use App\Models\Master\CarAllowance;
use App\Models\Master\KomponenTunjangan;
use App\Models\Master\ParameterBpjs;
use App\Models\Master\UnitPt;
use App\Models\Auth\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterDashboardController extends Controller
{
    // ── DASHBOARD ────────────────────────────────────────────
    public function index()
    {
        return view('master.dashboard', [
            // Stat cards — diambil langsung dari DB
            'totalGolongan'   => Golongan::where('is_active', true)->count(),
            'totalCostCenter' => CostCenter::where('is_active', true)->count(),
            'userAktif'       => User::where('is_active', true)->count(),

            // Log perubahan = semua aksi di audit_log bulan & tahun ini
            // (LOGIN, CREATE_USER, UPDATE_USER, dll)
            'logBulanIni'     => AuditLog::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),

            // Data untuk modal-modal CRUD (tampilkan semua, baik aktif maupun nonaktif)
            'golongans'       => Golongan::orderBy('kode_golongan')->get(),
            'carAllowances'   => CarAllowance::with('golongan')
                                    ->whereNull('berlaku_selesai')
                                    ->orderBy('id_golongan')->get(),
            'komponenGajis'   => KomponenTunjangan::with('golongan')
                                    ->whereNull('berlaku_selesai')
                                    ->orderBy('id_golongan')->get(),
            'paramBpjs'       => ParameterBpjs::whereNull('berlaku_selesai')
                                    ->latest()->first(),
            'units'           => UnitPt::with('costCenters')
                                    ->orderBy('kode_unit')->get(),
            'users'           => User::with(['role', 'unit'])
                                    ->orderBy('nama')->get(),
            'roles'           => Role::orderBy('nama_role')->get(),
        ]);
    }

    // ══════════════════════════════════════════════════════
    //  GOLONGAN
    // ══════════════════════════════════════════════════════
    public function golonganStore(Request $request)
    {
        $data = $request->validate([
            'kode_golongan' => 'required|string|max:20|unique:master_golongan,kode_golongan',
            'nama_golongan' => 'required|string|max:100',
            'gaji_pokok_min'=> 'nullable|numeric|min:0',
            'gaji_pokok_max'=> 'nullable|numeric|min:0',
            'deskripsi'     => 'nullable|string|max:200',
        ]);

        Golongan::create($data);
        return back()->with('success', 'Golongan berhasil ditambahkan.');
    }

    public function golonganUpdate(Request $request, Golongan $golongan)
    {
        $data = $request->validate([
            'kode_golongan' => 'required|string|max:20|unique:master_golongan,kode_golongan,'.$golongan->id,
            'nama_golongan' => 'required|string|max:100',
            'gaji_pokok_min'=> 'nullable|numeric|min:0',
            'gaji_pokok_max'=> 'nullable|numeric|min:0',
            'deskripsi'     => 'nullable|string|max:200',
            'is_active'     => 'boolean',
        ]);

        $golongan->update(array_merge($data, [
            'is_active' => $request->boolean('is_active', true),
        ]));
        return back()->with('success', 'Golongan berhasil diupdate.');
    }

    public function golonganDestroy(Golongan $golongan)
    {
        // Soft delete — nonaktifkan saja, jangan hapus beneran
        // karena golongan direferens banyak tabel
        $golongan->update(['is_active' => false]);
        return back()->with('success', 'Golongan dinonaktifkan.');
    }

    // ══════════════════════════════════════════════════════
    //  CAR ALLOWANCE
    // ══════════════════════════════════════════════════════
    public function carAllowanceStore(Request $request)
    {
        $data = $request->validate([
            'id_golongan'  => 'required|integer|exists:master_golongan,id',
            'nominal'      => 'required|numeric|min:0',
            'berlaku_mulai'=> 'required|date',
        ]);

        // Tutup record lama golongan yang sama
        CarAllowance::where('id_golongan', $data['id_golongan'])
            ->whereNull('berlaku_selesai')
            ->update(['berlaku_selesai' => now()->subDay()->toDateString()]);

        CarAllowance::create($data);
        return back()->with('success', 'Car allowance disimpan.');
    }

    public function carAllowanceUpdate(Request $request, CarAllowance $carAllowance)
    {
        $data = $request->validate([
            'id_golongan'  => 'required|integer|exists:master_golongan,id',
            'nominal'      => 'required|numeric|min:0',
            'berlaku_mulai'=> 'required|date',
        ]);

        $carAllowance->update($data);
        return back()->with('success', 'Car allowance berhasil diupdate.');
    }

    public function carAllowanceDestroy(CarAllowance $carAllowance)
    {
        $carAllowance->update(['berlaku_selesai' => now()->toDateString()]);
        return back()->with('success', 'Car allowance dinonaktifkan.');
    }

    // ══════════════════════════════════════════════════════
    //  PARAMETER BPJS
    // ══════════════════════════════════════════════════════
    public function parameterBpjsUpdate(Request $request, ParameterBpjs $parameterBpjs)
    {
        $data = $request->validate([
            'jht_perusahaan_pct'      => 'required|numeric|min:0|max:100',
            'jp_perusahaan_pct'       => 'required|numeric|min:0|max:100',
            'jkk_pct'                 => 'required|numeric|min:0|max:100',
            'jkm_pct'                 => 'required|numeric|min:0|max:100',
            'jht_karyawan_pct'        => 'required|numeric|min:0|max:100',
            'jp_karyawan_pct'         => 'required|numeric|min:0|max:100',
            'bpjs_kes_perusahaan_pct' => 'required|numeric|min:0|max:100',
            'bpjs_kes_karyawan_pct'   => 'required|numeric|min:0|max:100',
            'berlaku_mulai'           => 'required|date',
        ]);

        // Buat record baru (tidak timpa history lama)
        ParameterBpjs::whereNull('berlaku_selesai')
            ->update(['berlaku_selesai' => now()->subDay()]);

        ParameterBpjs::create(array_merge($data, [
            'created_by' => Auth::user()->nama,
        ]));

        return back()->with('success', 'Parameter BPJS diupdate.');
    }

    // ══════════════════════════════════════════════════════
    //  KOMPONEN GAJI
    // ══════════════════════════════════════════════════════
    public function komponenGajiStore(Request $request)
    {
        $data = $request->validate([
            'id_golongan'   => 'required|integer|exists:master_golongan,id',
            'uang_makan'    => 'nullable|numeric|min:0',
            'uang_transport'=> 'nullable|numeric|min:0',
            'tunjangan_lain'=> 'nullable|numeric|min:0',
            'berlaku_mulai' => 'required|date',
        ]);

        // Tutup record lama
        KomponenTunjangan::where('id_golongan', $data['id_golongan'])
            ->whereNull('berlaku_selesai')
            ->update(['berlaku_selesai' => now()->subDay()->toDateString()]);

        KomponenTunjangan::create($data);
        return back()->with('success', 'Komponen gaji disimpan.');
    }

    public function komponenGajiUpdate(Request $request, KomponenTunjangan $komponenGaji)
    {
        $data = $request->validate([
            'id_golongan'   => 'required|integer|exists:master_golongan,id',
            'uang_makan'    => 'nullable|numeric|min:0',
            'uang_transport'=> 'nullable|numeric|min:0',
            'tunjangan_lain'=> 'nullable|numeric|min:0',
            'berlaku_mulai' => 'required|date',
        ]);

        $komponenGaji->update($data);
        return back()->with('success', 'Komponen gaji berhasil diupdate.');
    }

    public function komponenGajiDestroy(KomponenTunjangan $komponenGaji)
    {
        $komponenGaji->update(['is_active' => false]);
        return back()->with('success', 'Komponen gaji berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════
    //  USER MANAGEMENT  (sudah ada di UserManagementController)
    //  Di sini hanya proxy ke controller yang sama
    // ══════════════════════════════════════════════════════

    // ══════════════════════════════════════════════════════
    //  MASTER UNIT / PT
    // ══════════════════════════════════════════════════════
    public function unitPtStore(Request $request)
    {
        $data = $request->validate([
            'kode_unit'    => 'required|string|max:20|unique:master_unit_pt,kode_unit',
            'nama_pt'      => 'required|string|max:100',
            'lokasi'       => 'nullable|string|max:100',
            'is_active'    => 'boolean',
            'cost_centers' => 'required|array|min:1',
            'cost_centers.*'=> 'required|string|max:100',
        ]);

        $unit = UnitPt::create([
            'kode_unit' => $data['kode_unit'],
            'nama_pt'   => $data['nama_pt'],
            'lokasi'    => $data['lokasi'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Simpan cost center
        foreach (array_filter($data['cost_centers'] ?? []) as $cc) {
            $unit->costCenters()->create([
                'kode_cc' => strtoupper($cc),
                'nama_cc' => $cc,
                'is_active' => true,
            ]);
        }

        return back()->with('success', 'Unit/PT berhasil ditambahkan.');
    }

    public function unitPtUpdate(Request $request, UnitPt $unitPt)
    {
        $data = $request->validate([
            'kode_unit' => 'required|string|max:20|unique:master_unit_pt,kode_unit,'.$unitPt->id,
            'nama_pt'   => 'required|string|max:100',
            'lokasi'    => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $unitPt->update($data);
        return back()->with('success', 'Unit/PT berhasil diupdate.');
    }

    public function unitPtDestroy(UnitPt $unitPt)
    {
        $unitPt->update(['is_active' => false]);
        return back()->with('success', 'Unit/PT dinonaktifkan.');
    }

    // ══════════════════════════════════════════════════════
    //  USER MANAGEMENT
    // ══════════════════════════════════════════════════════
    public function usersStore(Request $request)
    {
        $data = $request->validate([
    'nama'      => 'required|string|max:100',
    'email'     => 'required|email|max:100|unique:auth_users,email',
    'id_role'   => 'required|integer|exists:auth_roles,id',
    'id_unit'   => 'nullable|integer|exists:master_unit_pt,id',
    'password'  => 'required|string|min:6|confirmed',
]);

        User::create([
            'nama'      => $data['nama'],
            'email'     => $data['email'],
            'id_role'   => $data['id_role'],
            'id_unit'   => $data['id_unit'] ?? null,
            'password'  => bcrypt($data['password']),
            'is_active' => true,
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function usersUpdate(Request $request, User $user)
    {
        $data = $request->validate([
    'nama'    => 'required|string|max:100',
    'email'   => 'required|email|max:100|unique:auth_users,email,' . $user->id,
    'id_role' => 'required|integer|exists:auth_roles,id',
    'id_unit' => 'nullable|integer|exists:master_unit_pt,id',
    'password'=> 'nullable|string|min:6|confirmed',
    'is_active' => 'boolean',
]);

        $user->update([
            'nama'    => $data['nama'],
            'email'   => $data['email'],
            'id_role' => $data['id_role'],
            'id_unit' => $data['id_unit'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (!empty($data['password']) && !empty($data['password'])) {
            $user->update(['password' => bcrypt($data['password'])]);
        }

        return back()->with('success', 'User berhasil diupdate.');
    }

    public function usersToggleActive(Request $request, User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'Diaktifkan' : 'Dinonaktifkan';
        return back()->with('success', "User $status.");
    }

    public function usersDestroy(User $user)
    {
        // Soft delete - nonaktifkan, jangan hapus
        $user->update(['is_active' => false]);
        return back()->with('success', 'User dinonaktifkan.');
    }
}
