<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_riwayat_jabatan', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'detail_perubahan')) {
                $table->string('detail_perubahan', 200)->nullable()->after('jenis_perubahan');
            }
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'current_data')) {
                $table->json('current_data')->nullable()->after('detail_perubahan');
            }
            if (!Schema::hasColumn('employee_riwayat_jabatan', 'proposed_data')) {
                $table->json('proposed_data')->nullable()->after('current_data');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_riwayat_jabatan', function (Blueprint $table) {
            $table->dropColumn([
                'nip',
                'nama',
                'detail_perubahan',
                'current_data',
                'proposed_data',
            ]);
        });
    }
};
