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
            $table->date('end_date')->default('9999-12-31')->after('tgl_efektif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_riwayat_jabatan', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
};