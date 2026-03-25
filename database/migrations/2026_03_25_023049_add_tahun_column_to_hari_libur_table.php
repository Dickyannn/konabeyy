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
        Schema::table('master_hari_libur', function (Blueprint $table) {
            // Add tahun column as generated column from tanggal
            $table->integer('tahun')->storedAs('YEAR(tanggal)')->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_hari_libur', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });
    }
};
