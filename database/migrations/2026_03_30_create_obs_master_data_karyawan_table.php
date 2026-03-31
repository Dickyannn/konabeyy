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
        Schema::create('obs_master_data_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('action_type', 50); // CREATE, UPDATE, DELETE
            $table->string('change_reason', 100)->nullable(); // Reason for change
            
            // Snapshot of employee data before change
            $table->string('nip', 20);
            $table->string('nik', 16)->nullable();
            $table->string('npwp', 20)->nullable();
            $table->string('nama_karyawan', 150);
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->string('bpjs_kesehatan_number', 50)->nullable();
            $table->string('bpjs_tk_number', 50)->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->foreignId('id_status_karyawan')->constrained('master_status_karyawan', 'id');
            $table->foreignId('id_status_kawin')->nullable()->constrained('master_status_kawin', 'id');
            $table->foreignId('id_golongan')->constrained('master_golongan', 'id');
            $table->string('jabatan', 150);
            $table->foreignId('id_unit')->constrained('master_unit_pt', 'id');
            $table->foreignId('id_atasan')->nullable()->constrained('employee_karyawan', 'id');
            $table->string('foto_path', 500)->nullable();
            $table->boolean('is_active')->default(true);
            
            // Audit fields
            $table->foreignId('created_by')->constrained('auth_users', 'id');
            $table->timestamp('created_at')->useCurrent();
            $table->text('notes')->nullable();
            
            // Indexes
            $table->index(['id_karyawan', 'created_at']);
            $table->index(['action_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obs_master_data_karyawan');
    }
};