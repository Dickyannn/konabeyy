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
        // 1. bpjs.bpjs_tk (FK to karyawan & parameter_bpjs)
        Schema::create('bpjs_bpjs_tk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('periode');
            $table->decimal('basic_salary', 15, 2);
            // Beban Perusahaan
            $table->decimal('jht_company', 15, 2);
            $table->decimal('jp_company', 15, 2);
            $table->decimal('jkk', 15, 2);
            $table->decimal('jkm', 15, 2);
            $table->decimal('total_company', 15, 2);
            // Beban Karyawan
            $table->decimal('jht_employee', 15, 2);
            $table->decimal('jp_employee', 15, 2);
            $table->decimal('total_employee', 15, 2);
            // Snapshot Parameter
            $table->foreignId('id_parameter')->constrained('master_parameter_bpjs', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();

            // Constraints & Indexes
            $table->unique(['id_karyawan', 'periode']);
            $table->index(['id_karyawan', 'periode']);
        });

        // 2. bpjs.bpjs_kesehatan (FK to karyawan & parameter_bpjs)
        Schema::create('bpjs_bpjs_kesehatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('periode');
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('beban_perusahaan', 15, 2);
            $table->decimal('beban_karyawan', 15, 2);
            $table->decimal('total', 15, 2)->storedAs('beban_perusahaan + beban_karyawan');
            $table->foreignId('id_parameter')->constrained('master_parameter_bpjs', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('created_at')->useCurrent();

            // Constraints & Indexes
            $table->unique(['id_karyawan', 'periode']);
            $table->index(['id_karyawan', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpjs_bpjs_kesehatan');
        Schema::dropIfExists('bpjs_bpjs_tk');
    }
};
