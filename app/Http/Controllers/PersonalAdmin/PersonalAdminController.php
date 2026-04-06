<?php

namespace App\Http\Controllers\PersonalAdmin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeKaryawan;
use App\Models\EmployeePosition;
use App\Models\EmployeeFasilitasKendaraan;
use App\Models\EmployeeRiwayatJabatan;
use App\Models\ObsMasterDataKaryawan;
use App\Models\MasterGolongan;
use App\Models\MasterUnitPt;
use App\Models\MasterCostCenter;
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

        // Total karyawan aktif
        $totalKaryawan = EmployeeKaryawan::where('is_active', true)->count();

        // Karyawan bergabung bulan ini
        $karyawanBaru = EmployeeKaryawan::whereMonth('tanggal_masuk', $now->month)
                            ->whereYear('tanggal_masuk', $now->year)
                            ->where('is_active', true)
                            ->count();

        // Kontrak berakhir dalam 30 hari (dari employee_position)
        $kontrakBerakhir = EmployeePosition::where('is_current', true)
                            ->whereNotNull('tanggal_selesai')
                            ->whereBetween('tanggal_selesai', [$now, $now->copy()->addDays(30)])
                            ->count();

        // Karyawan golongan H-11
        $golonganH11 = EmployeeKaryawan::where('is_active', true)
                        ->whereHas('golongan', fn($q) => $q->where('kode_golongan', 'H-11'))
                        ->count();

        // Data karyawan untuk tabel dengan eager loading
        $karyawans = EmployeeKaryawan::with([
                        'golongan', 
                        'unit', 
                        'statusKaryawan',
                        'currentPosition.costCenter'
                    ])
                    ->where('is_active', true)
                    ->orderBy('nama_karyawan')
                    ->get();

        // Data untuk modal Kontrak (menggunakan employee_position dengan tanggal_selesai)
        $kontrakData = EmployeePosition::with(['karyawan.golongan', 'karyawan.unit'])
                    ->where('is_current', true)
                    ->orderBy('tanggal_mulai', 'desc')
                    ->get();

        // Data untuk modal Struktur Organisasi
        $positions = EmployeePosition::with(['karyawan', 'costCenter'])
                    ->where('is_current', true)
                    ->orderBy('id')
                    ->get();

        // Data untuk modal Fasilitas Kendaraan
        $fasilitasKendaraans = EmployeeFasilitasKendaraan::with(['karyawan.golongan'])
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->get();

        // Data untuk modal Riwayat Jabatan
        $riwayatJabatans = EmployeeRiwayatJabatan::with(['karyawan', 'golonganBaruRelation'])
                    ->orderBy('tgl_efektif', 'desc')
                    ->get();

        // Data master untuk dropdown
        $golongans = MasterGolongan::where('is_active', true)->orderBy('kode_golongan')->get();
        $units = MasterUnitPt::where('is_active', true)->orderBy('nama_pt')->get();
        $costCenters = MasterCostCenter::where('is_active', true)->orderBy('nama_cc')->get();
        $statusKaryawans = \App\Models\MasterStatusKaryawan::all();
        $statusKawins = \App\Models\MasterStatusKawin::all();

        return view('personal-admin.dashboard', compact(
            'totalKaryawan',
            'karyawanBaru',
            'kontrakBerakhir',
            'golonganH11',
            'karyawans',
            'kontrakData',
            'positions',
            'fasilitasKendaraans',
            'riwayatJabatans',
            'golongans',
            'units',
            'costCenters',
            'statusKaryawans',
            'statusKawins'
        ));
    }

    // ══════════════════════════════════════════════════════
    //  DATA KARYAWAN
    // ══════════════════════════════════════════════════════
    public function karyawanIndex(Request $request)
    {
        $query = EmployeeKaryawan::with(['golongan', 'unit'])
            ->orderBy('nama_karyawan');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nip', 'ilike', "%$s%")
                  ->orWhere('nama_karyawan', 'ilike', "%$s%")
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
        ]);
    }

    public function karyawanShow(EmployeeKaryawan $karyawan)
    {
        return response()->json([
            'data' => $karyawan->load(['golongan', 'unit', 'statusKaryawan', 'statusKawin'])
        ]);
    }

    public function karyawanView(EmployeeKaryawan $karyawan)
    {
        $data = $karyawan->load(['golongan', 'unit', 'statusKaryawan', 'statusKawin', 'atasan']);
        
        // Build golongan string with proper null handling
        $golonganStr = '';
        if ($data->golongan) {
            $golonganStr = $data->golongan->kode_golongan . ' — ' . $data->golongan->nama_golongan;
        }
        
        // Handle jenis_kelamin
        $jenisKelaminStr = '';
        if ($data->jenis_kelamin) {
            $jenisKelaminStr = $data->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
        }
        
        return response()->json([
            'data' => [
                // Identitas Karyawan
                'nip'              => $data->nip ?: '-',
                'nama_karyawan'    => $data->nama_karyawan ?: '-',
                'nik'              => $data->nik ?: '-',
                'tanggal_lahir'    => $data->tanggal_lahir ? $data->tanggal_lahir->format('d/m/Y') : '-',
                'jenis_kelamin'    => $jenisKelaminStr ?: '-',
                'nomor_telepon'    => $data->nomor_telepon ?: '-',
                'email'            => $data->email ?: '-',
                'alamat'           => $data->alamat ?: '-',
                
                // Data Kepegawaian
                'golongan'         => $golonganStr ?: '-',
                'golongan_kode'    => $data->golongan?->kode_golongan ?: '-',
                'jabatan'          => $data->jabatan ?: '-',
                'unit'             => $data->unit?->nama_pt ?: '-',
                'status_kawin'     => $data->statusKawin?->deskripsi ?: '-',
                'status_karyawan'  => $data->statusKaryawan?->nama_status ?: '-',
                'tanggal_masuk'    => $data->tanggal_masuk ? $data->tanggal_masuk->format('d/m/Y') : '-',
                'is_active'        => $data->is_active ? 'Aktif' : 'Nonaktif',
                
                // For edit/delete buttons
                'id'               => $data->id,
            ]
        ]);
    }

    public function karyawanStore(Request $request)
    {
        $data = $request->validate([
            'nip'              => 'required|string|max:20|unique:employee_karyawan,nip',
            'nama_karyawan'    => 'required|string|max:150',
            'nik'              => 'nullable|string|size:16',
            'tanggal_lahir'    => 'nullable|date',
            'jenis_kelamin'    => 'nullable|in:L,P',
            'nomor_telepon'    => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100|unique:employee_karyawan,email',
            'alamat'           => 'nullable|string',
            'id_golongan'      => 'required|integer|exists:master_golongan,id',
            'jabatan'          => 'required|string|max:150',
            'id_unit'          => 'required|integer|exists:master_unit_pt,id',
            'id_status_kawin'  => 'nullable|integer|exists:master_status_kawin,id',
            'id_status_karyawan' => 'required|integer|exists:master_status_karyawan,id',
            'tanggal_masuk'    => 'required|date',
            'is_active'        => 'boolean',
        ]);

        $karyawan = EmployeeKaryawan::create(array_merge($data, [
            'is_active' => $request->boolean('is_active', true),
        ]));

        AuditLog::catat(
            'CREATE_KARYAWAN',
            'employee_karyawan',
            $karyawan->id,
            $data
        );

        return back()->with('success', "Karyawan {$karyawan->nama_karyawan} berhasil ditambahkan.");
    }

    public function karyawanUpdate(Request $request, EmployeeKaryawan $karyawan)
    {
        try {
            // Only validate identity fields during update - kepegawaian fields are not editable
            $data = $request->validate([
                'nip'              => "required|string|max:20|unique:employee_karyawan,nip,{$karyawan->id}",
                'nama_karyawan'    => 'required|string|max:150',
                'nik'              => 'nullable|string|size:16',
                'tanggal_lahir'    => 'nullable|date',
                'jenis_kelamin'    => 'nullable|in:L,P',
                'nomor_telepon'    => 'nullable|string|max:20',
                'email'            => "nullable|email|max:100|unique:employee_karyawan,email,{$karyawan->id}",
                'alamat'           => 'nullable|string',
            ]);

            // Save old data snapshot before updating
            $oldKaryawanData = $karyawan->toArray();
            
            try {
                ObsMasterDataKaryawan::create([
                    'id_karyawan'          => $karyawan->id,
                    'action_type'          => 'before_update',
                    'change_reason'        => 'Perubahan data identitas karyawan',
                    'nip'                  => $oldKaryawanData['nip'],
                    'nik'                  => $oldKaryawanData['nik'],
                    'npwp'                 => $oldKaryawanData['npwp'],
                    'nama_karyawan'        => $oldKaryawanData['nama_karyawan'],
                    'tanggal_lahir'        => $oldKaryawanData['tanggal_lahir'],
                    'jenis_kelamin'        => $oldKaryawanData['jenis_kelamin'],
                    'alamat'               => $oldKaryawanData['alamat'],
                    'email'                => $oldKaryawanData['email'],
                    'nomor_telepon'        => $oldKaryawanData['nomor_telepon'],
                    'bpjs_kesehatan_number' => $oldKaryawanData['bpjs_kesehatan_number'],
                    'bpjs_tk_number'       => $oldKaryawanData['bpjs_tk_number'],
                    'tanggal_masuk'        => $oldKaryawanData['tanggal_masuk'],
                    'tanggal_keluar'       => $oldKaryawanData['tanggal_keluar'],
                    'id_status_karyawan'   => $oldKaryawanData['id_status_karyawan'],
                    'id_status_kawin'      => $oldKaryawanData['id_status_kawin'],
                    'id_golongan'          => $oldKaryawanData['id_golongan'],
                    'jabatan'              => $oldKaryawanData['jabatan'],
                    'id_unit'              => $oldKaryawanData['id_unit'],
                    'id_atasan'            => $oldKaryawanData['id_atasan'],
                    'foto_path'            => $oldKaryawanData['foto_path'],
                    'is_active'            => $oldKaryawanData['is_active'],
                    'created_by'           => auth()->id() ?? 1,
                    'notes'                => 'Snapshot data sebelum perubahan identitas',
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to save before_update snapshot: ' . $e->getMessage());
            }

            // Update employee_karyawan with new identity data
            $karyawan->update($data);

            return back()->with('success', "Data karyawan {$karyawan->nama_karyawan} berhasil diupdate.");
        } catch (\Exception $e) {
            \Log::error('Error updating karyawan: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengupdate data karyawan: ' . $e->getMessage());
        }
    }

    public function karyawanDestroy(EmployeeKaryawan $karyawan)
    {
        // Save snapshot data to obs_master_data_karyawan before delete
        $karyawanData = $karyawan->toArray();
        
        ObsMasterDataKaryawan::create([
            'id_karyawan'          => $karyawan->id,
            'action_type'          => 'delete',
            'change_reason'        => 'Penghapusan data karyawan',
            'nip'                  => $karyawanData['nip'],
            'nik'                  => $karyawanData['nik'],
            'npwp'                 => $karyawanData['npwp'],
            'nama_karyawan'        => $karyawanData['nama_karyawan'],
            'tanggal_lahir'        => $karyawanData['tanggal_lahir'],
            'jenis_kelamin'        => $karyawanData['jenis_kelamin'],
            'alamat'               => $karyawanData['alamat'],
            'email'                => $karyawanData['email'],
            'nomor_telepon'        => $karyawanData['nomor_telepon'],
            'bpjs_kesehatan_number' => $karyawanData['bpjs_kesehatan_number'],
            'bpjs_tk_number'       => $karyawanData['bpjs_tk_number'],
            'tanggal_masuk'        => $karyawanData['tanggal_masuk'],
            'tanggal_keluar'       => $karyawanData['tanggal_keluar'],
            'id_status_karyawan'   => $karyawanData['id_status_karyawan'],
            'id_status_kawin'      => $karyawanData['id_status_kawin'],
            'id_golongan'          => $karyawanData['id_golongan'],
            'jabatan'              => $karyawanData['jabatan'],
            'id_unit'              => $karyawanData['id_unit'],
            'id_atasan'            => $karyawanData['id_atasan'],
            'foto_path'            => $karyawanData['foto_path'],
            'is_active'            => $karyawanData['is_active'],
            'created_by'           => auth()->id(),
            'notes'                => 'Snapshot data sebelum penghapusan',
        ]);

        // Soft delete: set is_active = false
        $karyawan->update(['is_active' => false]);

        AuditLog::create([
            'id_user'    => auth()->id(),
            'action'     => 'DEACTIVATE_KARYAWAN',
            'table_name' => 'employee_karyawan',
            'record_id'  => $karyawan->id,
            'old_data'   => json_encode(['is_active' => true]),
            'new_data'   => json_encode(['is_active' => false]),
        ]);

        return back()->with('success', "Karyawan {$karyawan->nama_karyawan} dinonaktifkan.");
    }

    // ══════════════════════════════════════════════════════
    //  POSISI / STRUKTUR ORGANISASI
    // ══════════════════════════════════════════════════════
    public function posisiStore(Request $request)
    {
        $data = $request->validate([
            'id_karyawan'         => 'required|integer|exists:employee_karyawan,id',
            'id_cost_center'      => 'required|integer|exists:master_cost_center,id',
            'nama_jabatan'        => 'required|string|max:150',
            'tanggal_mulai'       => 'required|date',
        ]);

        // Nonaktifkan posisi lama
        EmployeePosition::where('id_karyawan', $data['id_karyawan'])
            ->where('is_current', true)
            ->update(['is_current' => false, 'tanggal_selesai' => now()]);

        EmployeePosition::create(array_merge($data, ['is_current' => true]));
        
        return back()->with('success', 'Posisi berhasil disimpan.');
    }

    public function posisiShow(EmployeePosition $posisi)
    {
        return response()->json([
            'data' => $posisi->load(['karyawan', 'costCenter'])
        ]);
    }

    public function posisiUpdate(Request $request, EmployeePosition $posisi)
    {
        $data = $request->validate([
            'id_cost_center'      => 'required|integer|exists:master_cost_center,id',
            'nama_jabatan'        => 'required|string|max:150',
            'tanggal_mulai'       => 'required|date',
            'tanggal_selesai'     => 'nullable|date',
        ]);

        $posisi->update($data);
        return back()->with('success', 'Posisi berhasil diupdate.');
    }

    public function posisiDestroy(EmployeePosition $posisi)
    {
        $posisi->update(['is_current' => false, 'tanggal_selesai' => now()]);
        return back()->with('success', 'Posisi dinonaktifkan.');
    }

    // ══════════════════════════════════════════════════════
    //  FASILITAS KENDARAAN
    // ══════════════════════════════════════════════════════
    public function kendaraanStore(Request $request)
    {
        $data = $request->validate([
            'id_karyawan'       => 'required|integer|exists:employee_karyawan,id',
            'jenis_fasilitas'   => 'required|string|max:50',
            'nominal_allowance' => 'nullable|numeric|min:0',
            'tgl_berlaku'       => 'required|date',
            'nomor_polisi'      => 'nullable|string|max:20',
        ]);

        // Nonaktifkan fasilitas lama
        EmployeeFasilitasKendaraan::where('id_karyawan', $data['id_karyawan'])
            ->where('is_active', true)
            ->update(['is_active' => false, 'tgl_berakhir' => now()]);

        EmployeeFasilitasKendaraan::create(array_merge($data, [
            'is_active' => true,
            'created_by' => auth()->user()->name ?? 'system'
        ]));
        
        return back()->with('success', 'Fasilitas kendaraan berhasil disimpan.');
    }

    public function kendaraanShow(EmployeeFasilitasKendaraan $kendaraan)
    {
        return response()->json([
            'data' => $kendaraan->load(['karyawan'])
        ]);
    }

    public function kendaraanUpdate(Request $request, EmployeeFasilitasKendaraan $kendaraan)
    {
        $data = $request->validate([
            'jenis_fasilitas'   => 'required|string|max:50',
            'nominal_allowance' => 'nullable|numeric|min:0',
            'tgl_berlaku'       => 'required|date',
            'nomor_polisi'      => 'nullable|string|max:20',
        ]);

        $kendaraan->update($data);
        return back()->with('success', 'Fasilitas kendaraan berhasil diupdate.');
    }

    public function kendaraanDestroy(EmployeeFasilitasKendaraan $kendaraan)
    {
        $kendaraan->update(['is_active' => false, 'tgl_berakhir' => now()]);
        return back()->with('success', 'Fasilitas kendaraan dinonaktifkan.');
    }

    // ══════════════════════════════════════════════════════
    //  RIWAYAT JABATAN
    // ══════════════════════════════════════════════════════
    public function riwayatStore(Request $request)
    {
        $data = $request->validate([
            'id_karyawan'         => 'required|integer|exists:employee_karyawan,id',
            'jenis_perubahan'     => 'required|in:promotion,demotion,transfer,termination,new_hire,contract_extension,actual_conversion,change_of_status,pass_probation,service_extension',
            'detail_perubahan'    => 'required|string|max:200',
            'jabatan_lama'        => 'nullable|string|max:150',
            'jabatan_baru'        => 'required|string|max:150',
            'golongan_lama'       => 'nullable|integer|exists:master_golongan,id',
            'golongan_baru'       => 'required|integer|exists:master_golongan,id',
            'tgl_efektif'         => 'required|date',
            'nomor_sk'            => 'nullable|string|max:100',
            'catatan'             => 'nullable|string',
        ]);

        // Get the employee
        $karyawan = EmployeeKaryawan::find($data['id_karyawan']);
        
        // Capture before state
        $beforeData = $karyawan->toArray();
        
        // Get valid user ID
        $userId = auth()->id();
        if (!$userId) {
            $userId = \App\Models\User::first()?->id ?? 1;
        }

        try {
            // STEP 1: Save before_update snapshot to obs_master_data_karyawan
            $obsRecord = ObsMasterDataKaryawan::create([
                'id_karyawan'          => $karyawan->id,
                'action_type'          => 'before_update',
                'change_reason'        => $data['detail_perubahan'],
                'nip'                  => $beforeData['nip'],
                'nik'                  => $beforeData['nik'],
                'npwp'                 => $beforeData['npwp'],
                'nama_karyawan'        => $beforeData['nama_karyawan'],
                'tanggal_lahir'        => $beforeData['tanggal_lahir'],
                'jenis_kelamin'        => $beforeData['jenis_kelamin'],
                'alamat'               => $beforeData['alamat'],
                'email'                => $beforeData['email'],
                'nomor_telepon'        => $beforeData['nomor_telepon'],
                'bpjs_kesehatan_number' => $beforeData['bpjs_kesehatan_number'],
                'bpjs_tk_number'       => $beforeData['bpjs_tk_number'],
                'tanggal_masuk'        => $beforeData['tanggal_masuk'],
                'id_status_karyawan'   => $beforeData['id_status_karyawan'],
                'id_status_kawin'      => $beforeData['id_status_kawin'],
                'id_golongan'          => $beforeData['id_golongan'],
                'jabatan'              => $beforeData['jabatan'],
                'id_unit'              => $beforeData['id_unit'],
                'id_atasan'            => $beforeData['id_atasan'],
                'foto_path'            => $beforeData['foto_path'],
                'is_active'            => $beforeData['is_active'],
                'created_by'           => $userId,
                'notes'                => "Snapshot sebelum {$data['jenis_perubahan']} - {$data['jabatan_baru']}",
            ]);
            \Log::info("Before_update snapshot saved to obs: obsRecord ID={$obsRecord->id}");
        } catch (\Exception $e) {
            \Log::error('Failed to save before_update snapshot: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
        }

        // STEP 2: Update end_date of previous riwayat jabatan records
        EmployeeRiwayatJabatan::where('id_karyawan', $data['id_karyawan'])
            ->where('end_date', '9999-12-31')
            ->update(['end_date' => \Carbon\Carbon::parse($data['tgl_efektif'])->subDay()]);

        // STEP 3: Create riwayat jabatan record with proposed data
        $riwayat = EmployeeRiwayatJabatan::create([
            'id_karyawan'      => $data['id_karyawan'],
            'nip'              => $karyawan->nip,
            'nama'             => $karyawan->nama_karyawan,
            'jenis_perubahan'  => $data['jenis_perubahan'],
            'tipe_perubahan'   => $data['jenis_perubahan'],
            'detail_perubahan' => $data['detail_perubahan'],
            'jabatan_lama'     => $beforeData['jabatan'],
            'jabatan_baru'     => $data['jabatan_baru'],
            'golongan_lama'    => $beforeData['id_golongan'],
            'golongan_baru'    => $data['golongan_baru'],
            'tgl_efektif'      => $data['tgl_efektif'],
            'end_date'         => '9999-12-31',
            'nomor_sk'         => $data['nomor_sk'] ?? null,
            'catatan'          => $data['catatan'] ?? null,
            'created_by'       => $userId,
        ]);

        // STEP 4: Update employee_karyawan with proposed data
        $karyawan->update([
            'jabatan' => $data['jabatan_baru'],
            'id_golongan' => $data['golongan_baru'],
        ]);

        return back()->with('success', "✓ Riwayat {$data['jenis_perubahan']} dicatat. Old state di obs_master_data_karyawan, new state di employee_riwayat_jabatan + employee_karyawan.");
    }

    public function riwayatShow(EmployeeRiwayatJabatan $riwayat)
    {
        $riwayat->load(['karyawan', 'golonganLamaRelation', 'golonganBaruRelation']);
        
        // Get before_update snapshot from obs_master_data_karyawan
        $beforeSnapshot = ObsMasterDataKaryawan::where('id_karyawan', $riwayat->id_karyawan)
            ->where('action_type', 'before_update')
            ->where('change_reason', $riwayat->detail_perubahan)
            ->latest('id')
            ->first();
        
        // Get current employee state
        $employee = $riwayat->karyawan;
        
        // Helper to get golongan name
        $getGolonganName = function($id) {
            if (!$id) return '-';
            $golongan = MasterGolongan::find($id);
            return $golongan?->nama_golongan ?? '-';
        };
        
        // Build current_data (before state from obs snapshot)
        $currentData = [];
        if ($beforeSnapshot) {
            $currentData = [
                'nip' => $beforeSnapshot->nip,
                'nama' => $beforeSnapshot->nama_karyawan,
                'jabatan' => $beforeSnapshot->jabatan,
                'golongan' => $getGolonganName($beforeSnapshot->id_golongan),
                'unit' => $beforeSnapshot->unit_nama ?? 'N/A',
                'status_karyawan' => $beforeSnapshot->status_karyawan ?? 'N/A',
            ];
        } else {
            // Fallback: if no snapshot, show old data from riwayat table
            $currentData = [
                'nip' => $riwayat->nip,
                'nama' => $riwayat->nama,
                'jabatan' => $riwayat->jabatan_lama,
                'golongan' => $riwayat->golonganLamaRelation?->nama_golongan ?? '-',
                'unit' => 'N/A',
                'status_karyawan' => 'N/A',
            ];
        }
        
        // Build proposed_data (after state - what will be set)
        $proposedData = [
            'nip' => $riwayat->nip,
            'nama' => $riwayat->nama,
            'jabatan' => $riwayat->jabatan_baru,
            'golongan' => $riwayat->golonganBaruRelation?->nama_golongan ?? '-',
            'unit' => $employee?->unit?->nama_pt ?? 'N/A',
            'status_karyawan' => $employee?->statusKaryawan?->nama_status ?? 'N/A',
        ];
        
        $riwayat->current_data = $currentData;
        $riwayat->proposed_data = $proposedData;
        
        return response()->json([
            'data' => $riwayat
        ]);
    }

    /**
     * Get employee current data for riwayat history form
     * Returns current employee data to be used as current_data in the form
     */
    public function riwayatGetKaryawanData(EmployeeKaryawan $karyawan)
    {
        $data = [
            'id' => $karyawan->id,
            'nip' => $karyawan->nip,
            'nama_karyawan' => $karyawan->nama_karyawan,
            'nik' => $karyawan->nik,
            'tanggal_lahir' => $karyawan->tanggal_lahir,
            'jenis_kelamin' => $karyawan->jenis_kelamin,
            'alamat' => $karyawan->alamat,
            'email' => $karyawan->email,
            'nomor_telepon' => $karyawan->nomor_telepon,
            'jabatan' => $karyawan->jabatan,
            'id_golongan' => $karyawan->id_golongan,
            'id_unit' => $karyawan->id_unit,
            'id_status_kawin' => $karyawan->id_status_kawin,
            'id_status_karyawan' => $karyawan->id_status_karyawan,
            'tanggal_masuk' => $karyawan->tanggal_masuk,
            'is_active' => $karyawan->is_active,
            'golongan_nama' => $karyawan->golongan?->nama_golongan ?? '-',
            'unit_nama' => $karyawan->unit?->nama_pt ?? '-',
        ];

        return response()->json(['data' => $data]);
    }

    public function riwayatUpdate(Request $request, EmployeeRiwayatJabatan $riwayat)
    {
        $data = $request->validate([
            'jenis_perubahan'  => 'required|in:promotion,demotion,transfer,termination,new_hire,contract_extension,actual_conversion,change_of_status,pass_probation,service_extension',
            'jabatan_baru'     => 'required|string|max:150',
            'golongan_baru'    => 'required|integer|exists:master_golongan,id',
            'tgl_efektif'      => 'required|date',
            'nomor_sk'         => 'nullable|string|max:100',
            'catatan'          => 'nullable|string',
        ]);

        // User ID with fallback
        $userId = auth()->id();
        if (!$userId) {
            $userId = \App\Models\User::first()?->id ?? 1;
        }

        // STEP 1: Capture before state before any update
        $oldRiwayatData = $riwayat->toArray();
        $employee = $riwayat->karyawan;
        $employeeBeforeData = $employee->toArray();

        // STEP 2: Save before_update snapshot to obs_master_data_karyawan
        try {
            ObsMasterDataKaryawan::create([
                'id_karyawan'          => $employee->id,
                'action_type'          => 'before_update',
                'change_reason'        => "Edit Riwayat: Perubahan {$data['jenis_perubahan']}",
                'nip'                  => $employeeBeforeData['nip'],
                'nik'                  => $employeeBeforeData['nik'],
                'npwp'                 => $employeeBeforeData['npwp'],
                'nama_karyawan'        => $employeeBeforeData['nama_karyawan'],
                'tanggal_lahir'        => $employeeBeforeData['tanggal_lahir'],
                'jenis_kelamin'        => $employeeBeforeData['jenis_kelamin'],
                'alamat'               => $employeeBeforeData['alamat'],
                'email'                => $employeeBeforeData['email'],
                'nomor_telepon'        => $employeeBeforeData['nomor_telepon'],
                'bpjs_kesehatan_number' => $employeeBeforeData['bpjs_kesehatan_number'],
                'bpjs_tk_number'       => $employeeBeforeData['bpjs_tk_number'],
                'tanggal_masuk'        => $employeeBeforeData['tanggal_masuk'],
                'id_status_karyawan'   => $employeeBeforeData['id_status_karyawan'],
                'id_status_kawin'      => $employeeBeforeData['id_status_kawin'],
                'id_golongan'          => $employeeBeforeData['id_golongan'],
                'jabatan'              => $employeeBeforeData['jabatan'],
                'id_unit'              => $employeeBeforeData['id_unit'],
                'id_atasan'            => $employeeBeforeData['id_atasan'],
                'foto_path'            => $employeeBeforeData['foto_path'],
                'is_active'            => $employeeBeforeData['is_active'],
                'created_by'           => $userId,
                'notes'                => "Before edit riwayat {$riwayat->id}: {$oldRiwayatData['jabatan_lama']} → {$oldRiwayatData['jabatan_baru']}",
            ]);
            \Log::info("Before_update snapshot saved to obs for riwayat edit ID={$riwayat->id}");
        } catch (\Exception $e) {
            \Log::error("Failed to save before_update snapshot for riwayat edit: " . $e->getMessage() . " | Stack: " . $e->getTraceAsString());
        }

        // STEP 3: Update riwayat record
        $oldTglEfektif = $riwayat->tgl_efektif;
        $newTglEfektif = \Carbon\Carbon::parse($data['tgl_efektif']);
        
        // If effective date changed, update end_date of previous records
        if ($oldTglEfektif->format('Y-m-d') !== $newTglEfektif->format('Y-m-d')) {
            // Update end_date of previous riwayat jabatan records for this employee
            EmployeeRiwayatJabatan::where('id_karyawan', $riwayat->id_karyawan)
                ->where('id', '<', $riwayat->id)
                ->where('end_date', $oldTglEfektif->subDay())
                ->update(['end_date' => $newTglEfektif->copy()->subDay()]);
        }

        $riwayat->update($data);
        
        // STEP 4: Update employee_karyawan table with new data if this is the latest record
        $latestRiwayat = EmployeeRiwayatJabatan::where('id_karyawan', $riwayat->id_karyawan)
            ->where('end_date', '9999-12-31')
            ->first();
            
        if ($latestRiwayat && $latestRiwayat->id === $riwayat->id) {
            $employee->update([
                'id_golongan' => $data['golongan_baru'],
                'jabatan' => $data['jabatan_baru']
            ]);
            \Log::info("Employee updated: {$employee->nip} - Jabatan: {$data['jabatan_baru']}, Golongan: {$data['golongan_baru']}");
        }
        
        return back()->with('success', "✓ Riwayat {$data['jenis_perubahan']} diupdate. Data lama tersimpan di transcation audit, data baru di riwayat & employee.");
    }

    public function riwayatDestroy(EmployeeRiwayatJabatan $riwayat)
    {
        $riwayat->delete();
        return back()->with('success', 'Riwayat jabatan dihapus.');
    }

    /**
     * Get employee current data for riwayat jabatan form
     */
    public function getEmployeeCurrentData(EmployeeKaryawan $karyawan)
    {
        // Get latest riwayat jabatan for this employee
        $latestRiwayat = EmployeeRiwayatJabatan::where('id_karyawan', $karyawan->id)
            ->where('end_date', '9999-12-31')
            ->orderBy('tgl_efektif', 'desc')
            ->first();

        return response()->json([
            'data' => [
                'id' => $karyawan->id,
                'nama_karyawan' => $karyawan->nama_karyawan,
                'current_golongan_id' => $karyawan->id_golongan,
                'current_golongan_name' => $karyawan->golongan->nama_golongan ?? '',
                'current_jabatan' => $karyawan->jabatan ?? 'Belum ada jabatan',
            ]
        ]);
    }
}
