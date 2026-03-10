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
        // 1. transaction.attendance (FK to karyawan & shift_kerja)
        Schema::create('transaction_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('tanggal');
            $table->foreignId('id_shift')->nullable()->constrained('master_shift_kerja', 'id')->cascadeOnUpdate()->nullOnDelete();
            $table->string('status', 20);
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('terlambat_menit')->default(0);
            $table->text('keterangan')->nullable();
            $table->string('source', 20)->default('manual');
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 100)->nullable();

            // Constraints & Indexes
            $table->unique(['id_karyawan', 'tanggal']);
            $table->index(['id_karyawan', 'tanggal']);
            $table->index(['status', 'tanggal']);
        });

        // 2. transaction.cuti (FK to karyawan untuk pemohon & approver)
        Schema::create('transaction_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('jenis_cuti', 50);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('jumlah_hari');
            $table->text('alasan')->nullable();
            $table->string('status', 20)->default('Pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('catatan_approver')->nullable();
            $table->string('dokumen_path', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Approver FK to karyawan
            $table->foreign('approved_by')
                ->references('id')
                ->on('employee_karyawan')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Indexes
            $table->index(['id_karyawan', 'status']);
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });

        // 3. transaction.saldo_cuti (FK to karyawan)
        Schema::create('transaction_saldo_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('tahun');
            $table->integer('kuota')->default(12);
            $table->integer('terpakai')->default(0);
            $table->integer('sisa');
            $table->timestamp('updated_at')->useCurrent();

            // Constraints
            $table->unique(['id_karyawan', 'tahun']);
        });

        // 4. transaction.lembur (FK to karyawan untuk pemohon & approver)
        Schema::create('transaction_lembur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('total_jam', 4, 2);
            $table->text('keterangan')->nullable();
            $table->string('status', 20)->default('Pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->decimal('nominal_lembur', 15, 2)->nullable();
            $table->boolean('sudah_dibayar')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 100)->nullable();

            // Approver FK to karyawan
            $table->foreign('approved_by')
                ->references('id')
                ->on('employee_karyawan')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            // Indexes
            $table->index(['id_karyawan', 'status']);
            $table->index(['sudah_dibayar']);
        });

        // 5. transaction.kontrak_karyawan (FK to karyawan & status_karyawan)
        Schema::create('transaction_kontrak_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_status_lama')->nullable()->constrained('master_status_karyawan', 'id')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_status_baru')->constrained('master_status_karyawan', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('jenis_perubahan', 50);
            $table->date('tanggal_berlaku');
            $table->date('tanggal_berakhir')->nullable();
            $table->string('nomor_dokumen', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->string('dokumen_path', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 100)->nullable();

            // Indexes
            $table->index(['id_karyawan', 'tanggal_berlaku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_kontrak_karyawan');
        Schema::dropIfExists('transaction_lembur');
        Schema::dropIfExists('transaction_saldo_cuti');
        Schema::dropIfExists('transaction_cuti');
        Schema::dropIfExists('transaction_attendance');
    }
};
