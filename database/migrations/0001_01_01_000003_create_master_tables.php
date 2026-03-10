<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. master.golongan
        Schema::create('master_golongan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_golongan', 20)->unique();
            $table->string('nama_golongan', 100);
            $table->decimal('gaji_pokok_min', 15, 2)->nullable();
            $table->decimal('gaji_pokok_max', 15, 2)->nullable();
            $table->string('deskripsi', 200)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. master.unit_pt
        Schema::create('master_unit_pt', function (Blueprint $table) {
            $table->id();
            $table->string('kode_unit', 20)->unique();
            $table->string('nama_pt', 100);
            $table->string('lokasi', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        // 3. master.cost_center (FK to master_unit_pt)
        Schema::create('master_cost_center', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_unit')->constrained('master_unit_pt', 'id')->restrictOnDelete();
            $table->string('kode_cc', 20);
            $table->string('nama_cc', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['id_unit', 'kode_cc']);
        });

        // 4. master.status_karyawan
        Schema::create('master_status_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_status', 50)->unique();
            $table->timestamp('created_at')->useCurrent();
        });

        // 5. master.status_kawin
        Schema::create('master_status_kawin', function (Blueprint $table) {
            $table->id();
            $table->string('kode_status', 10)->unique();
            $table->string('deskripsi', 50);
            $table->timestamp('created_at')->useCurrent();
        });

        // 6. master.parameter_bpjs
        Schema::create('master_parameter_bpjs', function (Blueprint $table) {
            $table->id();
            $table->date('berlaku_mulai');
            $table->date('berlaku_selesai')->nullable();
            $table->decimal('jht_perusahaan_pct', 5, 3)->default(3.700);
            $table->decimal('jp_perusahaan_pct', 5, 3)->default(2.000);
            $table->decimal('jkk_pct', 5, 3)->default(0.240);
            $table->decimal('jkm_pct', 5, 3)->default(0.300);
            $table->decimal('jht_karyawan_pct', 5, 3)->default(2.000);
            $table->decimal('jp_karyawan_pct', 5, 3)->default(1.000);
            $table->decimal('bpjs_kes_perusahaan_pct', 5, 3)->default(4.000);
            $table->decimal('bpjs_kes_karyawan_pct', 5, 3)->default(1.000);
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 100)->nullable();
        });

        // 7. master.payroll_component
        Schema::create('master_payroll_component', function (Blueprint $table) {
            $table->id();
            $table->string('nama_component', 100);
            $table->string('component_type', 20);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        // 8. master.car_allowance (FK to master_golongan)
        Schema::create('master_car_allowance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_golongan')->constrained('master_golongan', 'id')->restrictOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->date('berlaku_mulai');
            $table->date('berlaku_selesai')->nullable();
            $table->timestamps();
        });

        // 9. master.komponen_tunjangan (FK to master_golongan)
        Schema::create('master_komponen_tunjangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_golongan')->constrained('master_golongan', 'id')->restrictOnDelete();
            $table->decimal('uang_makan', 12, 2)->default(0);
            $table->decimal('uang_transport', 12, 2)->default(0);
            $table->date('berlaku_mulai');
            $table->date('berlaku_selesai')->nullable();
            $table->timestamps();
        });

        // 10. master.shift_kerja
        Schema::create('master_shift_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama_shift', 50);
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->timestamp('created_at')->useCurrent();
        });

        // 11. master.hari_libur
        Schema::create('master_hari_libur', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->string('keterangan', 150);
            $table->string('tipe', 30);
            $table->timestamp('created_at')->useCurrent();
        });

        // Seed data
        $this->seedMasterData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_hari_libur');
        Schema::dropIfExists('master_shift_kerja');
        Schema::dropIfExists('master_komponen_tunjangan');
        Schema::dropIfExists('master_car_allowance');
        Schema::dropIfExists('master_payroll_component');
        Schema::dropIfExists('master_parameter_bpjs');
        Schema::dropIfExists('master_status_kawin');
        Schema::dropIfExists('master_status_karyawan');
        Schema::dropIfExists('master_cost_center');
        Schema::dropIfExists('master_unit_pt');
        Schema::dropIfExists('master_golongan');
    }

    private function seedMasterData(): void
    {
        // Status Karyawan
        \DB::table('master_status_karyawan')->insert([
            ['nama_status' => 'Permanent'],
            ['nama_status' => 'Kontrak'],
            ['nama_status' => 'Probation'],
            ['nama_status' => 'PKWT'],
        ]);

        // Status Kawin
        \DB::table('master_status_kawin')->insert([
            ['kode_status' => 'TK', 'deskripsi' => 'Tidak Kawin'],
            ['kode_status' => 'K0', 'deskripsi' => 'Kawin tanpa anak'],
            ['kode_status' => 'K1', 'deskripsi' => 'Kawin 1 anak'],
            ['kode_status' => 'K2', 'deskripsi' => 'Kawin 2 anak'],
            ['kode_status' => 'K3', 'deskripsi' => 'Kawin 3 anak'],
        ]);

        // Golongan
        \DB::table('master_golongan')->insert([
            ['kode_golongan' => 'H-11', 'nama_golongan' => 'Manager', 'gaji_pokok_min' => 15000000, 'gaji_pokok_max' => 25000000, 'deskripsi' => 'Manager Level'],
            ['kode_golongan' => 'H-10', 'nama_golongan' => 'Senior Supervisor', 'gaji_pokok_min' => 10000000, 'gaji_pokok_max' => 15000000, 'deskripsi' => 'Senior Supervisor'],
            ['kode_golongan' => 'H-9', 'nama_golongan' => 'Supervisor', 'gaji_pokok_min' => 6000000, 'gaji_pokok_max' => 10000000, 'deskripsi' => 'Supervisor Level'],
            ['kode_golongan' => 'H-8', 'nama_golongan' => 'Staff', 'gaji_pokok_min' => 3500000, 'gaji_pokok_max' => 5000000, 'deskripsi' => 'Staff Junior'],
        ]);

        // Unit PT
        \DB::table('master_unit_pt')->insert([
            ['kode_unit' => 'STP-PWK', 'nama_pt' => 'PT Suri Tani Pemuka', 'lokasi' => 'Purwakarta'],
            ['kode_unit' => 'KBI-TJK', 'nama_pt' => 'PT Kona Bay Indonesia', 'lokasi' => 'Tejakula'],
        ]);

        // Cost Center
        \DB::table('master_cost_center')->insert([
            ['id_unit' => 1, 'kode_cc' => 'UMUM', 'nama_cc' => 'Umum'],
            ['id_unit' => 1, 'kode_cc' => 'FAC', 'nama_cc' => 'Finance & Accounting'],
            ['id_unit' => 1, 'kode_cc' => 'OPS', 'nama_cc' => 'Operations'],
            ['id_unit' => 2, 'kode_cc' => 'SALES', 'nama_cc' => 'Sales'],
        ]);

        // Parameter BPJS
        \DB::table('master_parameter_bpjs')->insert([
            ['berlaku_mulai' => '2024-01-01'],
        ]);

        // Payroll Components
        \DB::table('master_payroll_component')->insert([
            ['nama_component' => 'Basic Salary', 'component_type' => 'income'],
            ['nama_component' => 'Tunjangan Hari Raya', 'component_type' => 'benefit'],
            ['nama_component' => 'Tunjangan Kesehatan', 'component_type' => 'benefit'],
            ['nama_component' => 'Car Allowance', 'component_type' => 'income'],
            ['nama_component' => 'Uang Makan', 'component_type' => 'benefit'],
            ['nama_component' => 'Potongan BPJS TK', 'component_type' => 'deduction'],
            ['nama_component' => 'Potongan BPJS Kes.', 'component_type' => 'deduction'],
        ]);

        // Car Allowance
        \DB::table('master_car_allowance')->insert([
            ['id_golongan' => 1, 'nominal' => 3500000, 'berlaku_mulai' => '2024-01-01'],
            ['id_golongan' => 2, 'nominal' => 2500000, 'berlaku_mulai' => '2024-01-01'],
            ['id_golongan' => 3, 'nominal' => 1500000, 'berlaku_mulai' => '2024-01-01'],
            ['id_golongan' => 4, 'nominal' => 1000000, 'berlaku_mulai' => '2024-01-01'],
        ]);

        // Komponen Tunjangan
        \DB::table('master_komponen_tunjangan')->insert([
            ['id_golongan' => 1, 'uang_makan' => 35000, 'uang_transport' => 30000, 'berlaku_mulai' => '2024-01-01'],
            ['id_golongan' => 2, 'uang_makan' => 30000, 'uang_transport' => 25000, 'berlaku_mulai' => '2024-01-01'],
            ['id_golongan' => 3, 'uang_makan' => 25000, 'uang_transport' => 20000, 'berlaku_mulai' => '2024-01-01'],
            ['id_golongan' => 4, 'uang_makan' => 20000, 'uang_transport' => 15000, 'berlaku_mulai' => '2024-01-01'],
        ]);

        // Shift Kerja
        \DB::table('master_shift_kerja')->insert([
            ['nama_shift' => 'Normal', 'jam_masuk' => '08:00', 'jam_pulang' => '17:00'],
            ['nama_shift' => 'Shift Pagi', 'jam_masuk' => '06:00', 'jam_pulang' => '14:00'],
            ['nama_shift' => 'Shift Siang', 'jam_masuk' => '14:00', 'jam_pulang' => '22:00'],
            ['nama_shift' => 'Shift Malam', 'jam_masuk' => '22:00', 'jam_pulang' => '06:00'],
        ]);
    }
};
