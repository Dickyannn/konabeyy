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
use App\Models\MasterStatusKaryawan;
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
        $karyawan->load(['golongan', 'unit', 'statusKaryawan', 'statusKawin']);
        
        // Format the data properly for the edit form
        $data = $karyawan->toArray();
        
        // Format tanggal_lahir for HTML date input (YYYY-MM-DD format)
        if ($karyawan->tanggal_lahir) {
            $data['tanggal_lahir'] = $karyawan->tanggal_lahir->format('Y-m-d');
            \Log::info('karyawanShow: Formatted tanggal_lahir', [
                'original' => $karyawan->tanggal_lahir,
                'formatted' => $data['tanggal_lahir']
            ]);
        }
        
        // Format tanggal_masuk for HTML date input
        if ($karyawan->tanggal_masuk) {
            $data['tanggal_masuk'] = $karyawan->tanggal_masuk->format('Y-m-d');
        }
        
        \Log::info('karyawanShow: Returning data', [
            'id' => $karyawan->id,
            'tanggal_lahir' => $data['tanggal_lahir'] ?? null
        ]);
        
        return response()->json([
            'data' => $data
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

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Karyawan {$karyawan->nama_karyawan} berhasil ditambahkan."
            ]);
        }

        return back()->with('success', "Karyawan {$karyawan->nama_karyawan} berhasil ditambahkan.");
    }

    public function karyawanUpdate(Request $request, EmployeeKaryawan $karyawan)
    {
        \Log::info('karyawanUpdate: Starting update process', [
            'karyawan_id' => $karyawan->id,
            'request_data' => $request->all()
        ]);
        
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

        // STEP 1: Save snapshot of old data (CURRENT DATA ONLY) to obs_master_data_karyawan BEFORE making changes
        $oldKaryawanData = $karyawan->toArray();
        
        \Log::info('karyawanUpdate: Saving CURRENT data to obs_master_data_karyawan', [
            'action_type' => 'before_update',
            'karyawan_id' => $karyawan->id,
            'current_nip' => $oldKaryawanData['nip'],
            'current_nama' => $oldKaryawanData['nama_karyawan']
        ]);
        
        ObsMasterDataKaryawan::create([
            'id_karyawan'          => $karyawan->id,
            'action_type'          => 'before_update',
            'change_reason'        => 'Data Karyawan Update - Current Data Backup',
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
            'created_by'           => auth()->id(),
            'notes'                => 'Snapshot data sebelum perubahan identitas karyawan',
        ]);

        \Log::info('karyawanUpdate: obs_master_data_karyawan record created successfully');

        // STEP 2: Update employee_karyawan with new data
        $karyawan->update($data);

        \Log::info('karyawanUpdate: employee_karyawan updated successfully', [
            'updated_data' => $data
        ]);

        // STEP 3: Log audit (no need for after_update in obs_master_data_karyawan since we only track current data)
        AuditLog::create([
            'id_user'    => auth()->id(),
            'action'     => 'UPDATE_KARYAWAN_IDENTITY',
            'table_name' => 'employee_karyawan',
            'record_id'  => $karyawan->id,
            'old_data'   => json_encode($oldKaryawanData),
            'new_data'   => json_encode($data),
        ]);

        \Log::info('karyawanUpdate: Process completed successfully');

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data karyawan {$karyawan->nama_karyawan} berhasil diupdate."
            ]);
        }

        return back()->with('success', "Data karyawan {$karyawan->nama_karyawan} berhasil diupdate.");
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
        try {
            \Log::info('Riwayat Store Request Data:', $request->all());
            
            $data = $request->validate([
                'id_karyawan'         => 'required|integer|exists:employee_karyawan,id',
                'jenis_perubahan'     => 'required|in:Actual Conversion,Change of Status,Contract Extension,Demotion,New Hire,Pass Probation,Promotion,Service Extension,Termination,Transfer',
                'detail_perubahan'    => 'required|string|max:200',
                'jabatan_lama'        => 'nullable|string|max:150',
                'jabatan_baru'        => 'nullable|string|max:150',
                'golongan_lama'       => 'nullable|integer|exists:master_golongan,id',
                'golongan_baru'       => 'nullable|integer|exists:master_golongan,id',
                'unit_lama'           => 'nullable|integer|exists:master_unit_pt,id',
                'unit_baru'           => 'nullable|integer|exists:master_unit_pt,id',
                'status_karyawan_lama' => 'nullable|integer|exists:master_status_karyawan,id',
                'status_karyawan_baru' => 'nullable|integer|exists:master_status_karyawan,id',
                'tgl_efektif'         => 'required|date',
                'nomor_sk'            => 'nullable|string|max:100',
                'catatan'             => 'nullable|string',
            ]);

            \Log::info('Riwayat Validation Passed:', $data);

            // Get the employee
            $karyawan = EmployeeKaryawan::find($data['id_karyawan']);
            
            if (!$karyawan) {
                throw new \Exception('Employee not found');
            }

            \Log::info('Employee Found:', ['id' => $karyawan->id, 'name' => $karyawan->nama_karyawan]);
            
            // STEP 1: Prepare current_data from current employee state
            $currentData = [
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
            ];

            // STEP 2: Prepare proposed_data (updated values) - only update fields that have new values
            $proposedData = $currentData;
            
            if (!empty($data['jabatan_baru'])) {
                $proposedData['jabatan'] = $data['jabatan_baru'];
            }
            if (!empty($data['golongan_baru'])) {
                $proposedData['id_golongan'] = $data['golongan_baru'];
            }
            if (!empty($data['unit_baru'])) {
                $proposedData['id_unit'] = $data['unit_baru'];
            }
            if (!empty($data['status_karyawan_baru'])) {
                $proposedData['id_status_karyawan'] = $data['status_karyawan_baru'];
            }

            \Log::info('Current Data:', $currentData);
            \Log::info('Proposed Data:', $proposedData);

            // Update end_date of previous riwayat jabatan records for this employee
            EmployeeRiwayatJabatan::where('id_karyawan', $data['id_karyawan'])
                ->where('end_date', '9999-12-31')
                ->update(['end_date' => \Carbon\Carbon::parse($data['tgl_efektif'])->subDay()]);

            // STEP 3: Create new riwayat jabatan record with current_data and proposed_data
            $riwayatData = [
                'id_karyawan' => $data['id_karyawan'],
                'nip' => $karyawan->nip,
                'nama' => $karyawan->nama_karyawan,
                'tipe_perubahan' => $data['jenis_perubahan'], // Map jenis_perubahan to tipe_perubahan
                'jenis_perubahan' => $data['jenis_perubahan'],
                'detail_perubahan' => $data['detail_perubahan'],
                'jabatan_lama' => $karyawan->jabatan,
                'jabatan_baru' => $data['jabatan_baru'] ?? $karyawan->jabatan,
                'golongan_lama' => $karyawan->id_golongan,
                'golongan_baru' => $data['golongan_baru'] ?? $karyawan->id_golongan,
                'unit_lama' => $karyawan->id_unit,
                'unit_baru' => $data['unit_baru'] ?? $karyawan->id_unit,
                'status_karyawan_lama' => $karyawan->id_status_karyawan,
                'status_karyawan_baru' => $data['status_karyawan_baru'] ?? $karyawan->id_status_karyawan,
                'tgl_efektif' => $data['tgl_efektif'],
                'tanggal_efektif' => $data['tgl_efektif'], // Store in both fields for compatibility
                'nomor_sk' => $data['nomor_sk'],
                'catatan' => $data['catatan'],
                'current_data' => $currentData,
                'proposed_data' => $proposedData,
                'end_date' => '9999-12-31',
                'created_by' => auth()->user()->nama ?? 'system'
            ];

            \Log::info('Riwayat Data to Create:', $riwayatData);

            $riwayat = EmployeeRiwayatJabatan::create($riwayatData);

            \Log::info('Riwayat Created Successfully:', ['id' => $riwayat->id]);

            // STEP 4: Save to obs_master_data_karyawan for audit trail
            ObsMasterDataKaryawan::create([
                'id_karyawan'          => $karyawan->id,
                'action_type'          => 'history_update',
                'change_reason'        => $data['detail_perubahan'],
                'nip'                  => $karyawan->nip,
                'nik'                  => $karyawan->nik,
                'npwp'                 => $karyawan->npwp,
                'nama_karyawan'        => $karyawan->nama_karyawan,
                'tanggal_lahir'        => $karyawan->tanggal_lahir,
                'jenis_kelamin'        => $karyawan->jenis_kelamin,
                'alamat'               => $karyawan->alamat,
                'email'                => $karyawan->email,
                'nomor_telepon'        => $karyawan->nomor_telepon,
                'bpjs_kesehatan_number' => $karyawan->bpjs_kesehatan_number,
                'bpjs_tk_number'       => $karyawan->bpjs_tk_number,
                'tanggal_masuk'        => $karyawan->tanggal_masuk,
                'id_status_karyawan'   => $karyawan->id_status_karyawan,
                'id_status_kawin'      => $karyawan->id_status_kawin,
                'id_golongan'          => $karyawan->id_golongan,
                'jabatan'              => $karyawan->jabatan,
                'id_unit'              => $karyawan->id_unit,
                'id_atasan'            => $karyawan->id_atasan,
                'foto_path'            => $karyawan->foto_path,
                'is_active'            => $karyawan->is_active,
                'created_by'           => auth()->id(),
                'notes'                => "History Update: {$data['detail_perubahan']}",
            ]);

            // STEP 5: Update employee_karyawan table with proposed_data (only if there are changes)
            $updateData = [];
            if (!empty($data['jabatan_baru'])) {
                $updateData['jabatan'] = $data['jabatan_baru'];
            }
            if (!empty($data['golongan_baru'])) {
                $updateData['id_golongan'] = $data['golongan_baru'];
            }
            if (!empty($data['unit_baru'])) {
                $updateData['id_unit'] = $data['unit_baru'];
            }
            if (!empty($data['status_karyawan_baru'])) {
                $updateData['id_status_karyawan'] = $data['status_karyawan_baru'];
            }
            
            if (!empty($updateData)) {
                $karyawan->update($updateData);
                \Log::info('Employee Updated:', $updateData);
            }

            AuditLog::catat(
                'RIWAYAT_JABATAN_' . strtoupper(str_replace(' ', '_', $data['jenis_perubahan'])),
                'employee_riwayat_jabatan',
                $riwayat->id,
                $data
            );

            \Log::info('Riwayat Store Completed Successfully');

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Riwayat perubahan data berhasil dicatat dan data karyawan diupdate."
                ]);
            }

            return back()->with('success', "Riwayat perubahan data berhasil dicatat dan data karyawan diupdate.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Riwayat Validation Error:', $e->errors());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Riwayat Store Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'errors' => [
                        'general' => ['Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()]
                    ]
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function riwayatShow(EmployeeRiwayatJabatan $riwayat)
    {
        // Load all required relationships
        $riwayat->load(['karyawan', 'golonganLamaRelation', 'golonganBaruRelation']);
        
        // Prepare the response data with proper field mapping
        $data = [
            'id' => $riwayat->id,
            'nip' => $riwayat->nip ?? $riwayat->karyawan?->nip,
            'nama' => $riwayat->nama ?? $riwayat->karyawan?->nama_karyawan,
            'tipe_perubahan' => $riwayat->tipe_perubahan ?? $riwayat->jenis_perubahan,
            'jenis_perubahan' => $riwayat->jenis_perubahan,
            'detail_perubahan' => $riwayat->detail_perubahan,
            'tanggal_efektif' => $riwayat->tanggal_efektif ?? $riwayat->tgl_efektif,
            'tgl_efektif' => $riwayat->tgl_efektif,
            'nomor_sk' => $riwayat->nomor_sk,
            'catatan' => $riwayat->catatan,
            'current_data' => $riwayat->current_data,
            'proposed_data' => $riwayat->proposed_data,
            'jabatan_lama' => $riwayat->jabatan_lama,
            'jabatan_baru' => $riwayat->jabatan_baru,
            'golongan_lama' => $riwayat->golongan_lama,
            'golongan_baru' => $riwayat->golongan_baru,
        ];
        
        return response()->json(['data' => $data]);
    }

    /**
     * Get employee current data for riwayat history form
     * Returns current employee data to be used as current_data in the form
     */
    public function riwayatGetKaryawanData(EmployeeKaryawan $karyawan)
    {
        // Load all required relationships
        $karyawan->load(['golongan', 'unit', 'statusKaryawan', 'statusKawin']);
        
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
            'golongan_nama' => $karyawan->golongan?->kode_golongan . ' — ' . $karyawan->golongan?->nama_golongan ?? '-',
            'unit_nama' => $karyawan->unit?->nama_pt ?? '-',
            'status_karyawan_nama' => $karyawan->statusKaryawan?->nama_status ?? '-',
            'status_kawin_nama' => $karyawan->statusKawin?->deskripsi ?? '-',
        ];

        return response()->json(['data' => $data]);
    }

    public function riwayatUpdate(Request $request, EmployeeRiwayatJabatan $riwayat)
    {
        $data = $request->validate([
            'jenis_perubahan'  => 'required|in:promosi,mutasi,demosi,rotasi',
            'jabatan_baru'     => 'required|string|max:150',
            'golongan_baru'    => 'required|integer|exists:master_golongan,id',
            'tgl_efektif'      => 'required|date',
            'nomor_sk'         => 'nullable|string|max:100',
            'catatan'          => 'nullable|string',
        ]);

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
        
        // Update employee_karyawan table with new golongan if this is the latest record
        $latestRiwayat = EmployeeRiwayatJabatan::where('id_karyawan', $riwayat->id_karyawan)
            ->where('end_date', '9999-12-31')
            ->first();
            
        if ($latestRiwayat && $latestRiwayat->id === $riwayat->id) {
            $riwayat->karyawan->update([
                'id_golongan' => $data['golongan_baru'],
                'jabatan' => $data['jabatan_baru']
            ]);
        }
        
        return back()->with('success', 'Riwayat jabatan berhasil diupdate.');
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
