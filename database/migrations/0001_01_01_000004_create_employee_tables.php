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
        // 1. employee.karyawan (self-join + multiple FKs)
        Schema::create('employee_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique();
            $table->string('nik', 20)->unique()->nullable();
            $table->string('npwp', 30)->nullable();
            $table->string('nama_karyawan', 150);
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->string('email', 150)->unique()->nullable();
            $table->string('nomor_telepon', 25)->nullable();
            $table->string('bpjs_kesehatan_number', 50)->nullable();
            $table->string('bpjs_tk_number', 50)->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->foreignId('id_status_karyawan')->constrained('master_status_karyawan', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_status_kawin')->nullable()->constrained('master_status_kawin', 'id')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_golongan')->constrained('master_golongan', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_unit')->constrained('master_unit_pt', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('id_atasan')->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Self-join foreign key
            $table->foreign('id_atasan')
                ->references('id')
                ->on('employee_karyawan')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Indexes
            $table->index('nip');
            $table->index('id_unit');
            $table->index('id_golongan');
            $table->index('id_atasan');
            $table->index('is_active');
        });

        // 2. employee.position (FK to karyawan & cost_center)
        Schema::create('employee_position', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_cost_center')->constrained('master_cost_center', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama_jabatan', 150);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('is_current')->default(true);
            $table->timestamp('created_at')->useCurrent();

            // Index
            $table->index(['id_karyawan', 'is_current']);
        });

        // 3. employee.anggota_keluarga (FK to karyawan)
        Schema::create('employee_anggota_keluarga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('nama', 150);
            $table->string('hubungan', 50);
            $table->date('tanggal_lahir')->nullable();
            $table->date('tanggal_menikah')->nullable();
            $table->boolean('is_tanggungan')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        // 4. employee.fasilitas_kendaraan (FK to karyawan)
        Schema::create('employee_fasilitas_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('jenis_fasilitas', 50);
            $table->decimal('nominal_allowance', 15, 2)->nullable();
            $table->string('nomor_polisi', 20)->nullable();
            $table->date('tgl_berlaku');
            $table->date('tgl_berakhir')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 100)->nullable();
        });

        // 5. employee.riwayat_jabatan (FK to karyawan & golongan)
        Schema::create('employee_riwayat_jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('jabatan_lama', 150)->nullable();
            $table->string('jabatan_baru', 150);
            $table->foreignId('golongan_lama')->nullable()->constrained('master_golongan', 'id')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('golongan_baru')->constrained('master_golongan', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('jenis_perubahan', 50);
            $table->date('tgl_efektif');
            $table->string('nomor_sk', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 100)->nullable();
        });

        // 6. employee.dokumen_karyawan (FK to karyawan)
        Schema::create('employee_dokumen_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('jenis_dokumen', 100);
            $table->string('nama_file', 255);
            $table->string('file_path', 500);
            $table->timestamp('tgl_upload')->useCurrent();
            $table->string('uploaded_by', 100)->nullable();
            $table->text('keterangan')->nullable();
        });

        // Seed data
        $this->seedEmployeeData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_dokumen_karyawan');
        Schema::dropIfExists('employee_riwayat_jabatan');
        Schema::dropIfExists('employee_fasilitas_kendaraan');
        Schema::dropIfExists('employee_anggota_keluarga');
        Schema::dropIfExists('employee_position');
        Schema::dropIfExists('employee_karyawan');
    }

    private function seedEmployeeData(): void
    {
        // Insert sample karyawan
        $karyawan = \DB::table('employee_karyawan')->insertGetId([
            'nip' => '21000001',
            'nik' => '327601',
            'npwp' => '09.888',
            'nama_karyawan' => 'Bambang Suryanto',
            'tanggal_lahir' => '1985-06-15',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jl. Merdeka 123, Purwakarta',
            'email' => 'bambang@example.com',
            'nomor_telepon' => '08123456789',
            'tanggal_masuk' => '2021-01-01',
            'id_status_karyawan' => 1,
            'id_status_kawin' => 2,
            'id_golongan' => 1,
            'id_unit' => 1,
            'is_active' => true,
        ]);

        // Insert position
        \DB::table('employee_position')->insert([
            'id_karyawan' => $karyawan,
            'id_cost_center' => 2,
            'nama_jabatan' => 'F&A Manager',
            'tanggal_mulai' => '2021-01-01',
            'is_current' => true,
        ]);

        // Insert family member
        \DB::table('employee_anggota_keluarga')->insert([
            'id_karyawan' => $karyawan,
            'nama' => 'Siti Nurhaliza',
            'hubungan' => 'Istri',
            'tanggal_lahir' => '1987-02-10',
            'tanggal_menikah' => '2010-05-20',
            'is_tanggungan' => true,
        ]);
    }
};
