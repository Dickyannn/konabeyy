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
        Schema::table('employee_riwayat_jabatan', function (Blueprint $table) {
            // Add missing columns that are needed for the new History Data Karyawan functionality
            
            // Add nip and nama if they don't exist
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'nip')) {
                $table->string('nip', 20)->nullable()->after('id_karyawan');
            }
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'nama')) {
                $table->string('nama', 150)->nullable()->after('nip');
            }
            
            // Add tipe_perubahan if it doesn't exist (this is different from jenis_perubahan)
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'tipe_perubahan')) {
                $table->string('tipe_perubahan', 50)->nullable()->after('nama');
            }
            
            // Add detail_perubahan if it doesn't exist
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'detail_perubahan')) {
                $table->string('detail_perubahan', 200)->nullable()->after('tipe_perubahan');
            }
            
            // Add current_data and proposed_data JSON columns if they don't exist
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'current_data')) {
                $table->json('current_data')->nullable()->after('detail_perubahan');
            }
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'proposed_data')) {
                $table->json('proposed_data')->nullable()->after('current_data');
            }
            
            // Add tanggal_efektif if it doesn't exist (this is different from tgl_efektif)
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'tanggal_efektif')) {
                $table->date('tanggal_efektif')->nullable()->after('proposed_data');
            }
            
            // Add end_date if it doesn't exist
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'end_date')) {
                $table->date('end_date')->default('9999-12-31')->after('tanggal_efektif');
            }
            
            // Add unit fields for tracking unit changes
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'unit_lama')) {
                $table->foreignId('unit_lama')->nullable()->constrained('master_unit_pt', 'id')->cascadeOnUpdate()->nullOnDelete()->after('golongan_baru');
            }
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'unit_baru')) {
                $table->foreignId('unit_baru')->nullable()->constrained('master_unit_pt', 'id')->cascadeOnUpdate()->nullOnDelete()->after('unit_lama');
            }
            
            // Add status karyawan fields for tracking status changes
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'status_karyawan_lama')) {
                $table->foreignId('status_karyawan_lama')->nullable()->constrained('master_status_karyawan', 'id')->cascadeOnUpdate()->nullOnDelete()->after('unit_baru');
            }
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'status_karyawan_baru')) {
                $table->foreignId('status_karyawan_baru')->nullable()->constrained('master_status_karyawan', 'id')->cascadeOnUpdate()->nullOnDelete()->after('status_karyawan_lama');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_riwayat_jabatan', function (Blueprint $table) {
            $table->dropForeign(['unit_lama']);
            $table->dropForeign(['unit_baru']);
            $table->dropForeign(['status_karyawan_lama']);
            $table->dropForeign(['status_karyawan_baru']);
            
            $table->dropColumn([
                'nip',
                'nama',
                'tipe_perubahan',
                'detail_perubahan',
                'current_data',
                'proposed_data',
                'tanggal_efektif',
                'end_date',
                'unit_lama',
                'unit_baru',
                'status_karyawan_lama',
                'status_karyawan_baru'
            ]);
        });
    }
};