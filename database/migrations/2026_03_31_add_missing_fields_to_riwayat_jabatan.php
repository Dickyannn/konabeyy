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
            // Add missing fields for the refactored History Data Karyawan
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'nip')) {
                $table->string('nip', 20)->after('id_karyawan');
            }
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'nama')) {
                $table->string('nama', 150)->after('nip');
            }
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'tipe_perubahan')) {
                $table->string('tipe_perubahan', 50)->after('nama'); // Rotasi, Promosi, Terminasi
            }
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'tanggal_efektif')) {
                $table->date('tanggal_efektif')->nullable()->after('proposed_data'); // Make nullable first
            }
            
            // Add indexes for better performance
            if (!Schema::hasIndex('employee_riwayat_jabatan', 'employee_riwayat_jabatan_nip_tanggal_efektif_index')) {
                $table->index(['nip', 'tanggal_efektif']);
            }
            if (!Schema::hasIndex('employee_riwayat_jabatan', 'employee_riwayat_jabatan_tipe_perubahan_index')) {
                $table->index('tipe_perubahan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_riwayat_jabatan', function (Blueprint $table) {
            $table->dropIndex(['nip', 'tanggal_efektif']);
            $table->dropIndex(['tipe_perubahan']);
            
            $table->dropColumn([
                'nip',
                'nama', 
                'tipe_perubahan',
                'tanggal_efektif'
            ]);
        });
    }
};