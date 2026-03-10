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
        // 1. payroll.payroll (FK to karyawan)
        Schema::create('payroll_payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->smallInteger('bulan');
            $table->smallInteger('tahun');
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('total_income', 15, 2)->default(0);
            $table->decimal('total_deduction', 15, 2)->default(0);
            $table->decimal('take_home_pay', 15, 2)->storedAs('total_income - total_deduction');
            $table->string('status', 20)->default('draft');
            $table->string('approved_by', 100)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 100)->nullable();

            // Constraints & Indexes
            $table->unique(['id_karyawan', 'bulan', 'tahun']);
            $table->index('status');
            $table->index(['id_karyawan', 'tahun', 'bulan']);
        });

        // 2. payroll.payroll_detail (FK to payroll & payroll_component)
        Schema::create('payroll_payroll_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_payroll')->constrained('payroll_payroll', 'id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_component')->constrained('master_payroll_component', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('keterangan', 200)->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Constraints & Indexes
            $table->unique(['id_payroll', 'id_component']);
            $table->index('id_payroll');
        });

        // 3. payroll.uang_makan_transport (FK to karyawan)
        Schema::create('payroll_uang_makan_transport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->smallInteger('bulan');
            $table->smallInteger('tahun');
            $table->integer('hari_kerja')->default(0);
            $table->decimal('uang_makan', 12, 2)->default(0);
            $table->decimal('uang_transport', 12, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 100)->nullable();

            // Constraints & Indexes
            $table->unique(['id_karyawan', 'bulan', 'tahun']);
        });

        // 4. payroll.thr (FK to karyawan)
        Schema::create('payroll_thr', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->smallInteger('tahun');
            $table->decimal('basic_salary', 15, 2);
            $table->integer('masa_kerja_bulan');
            $table->decimal('jumlah_thr', 15, 2);
            $table->smallInteger('bulan_proses');
            $table->string('status', 20)->default('draft');
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 100)->nullable();

            // Constraints & Indexes
            $table->unique(['id_karyawan', 'tahun']);
            $table->index(['tahun', 'status']);
        });

        // 5. payroll.insentif (FK to karyawan)
        Schema::create('payroll_insentif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->smallInteger('bulan');
            $table->smallInteger('tahun');
            $table->decimal('jumlah', 15, 2);
            $table->string('deskripsi', 200)->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('created_at')->useCurrent();
            $table->string('created_by', 100)->nullable();

            // Indexes
            $table->index(['tahun', 'bulan', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_insentif');
        Schema::dropIfExists('payroll_thr');
        Schema::dropIfExists('payroll_uang_makan_transport');
        Schema::dropIfExists('payroll_payroll_detail');
        Schema::dropIfExists('payroll_payroll');
    }
};
