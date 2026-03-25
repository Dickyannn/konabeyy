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
        Schema::table('master_shift_kerja', function (Blueprint $table) {
            $table->integer('toleransi_menit')->default(15)->after('jam_pulang');
            $table->boolean('is_active')->default(true)->after('toleransi_menit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_shift_kerja', function (Blueprint $table) {
            $table->dropColumn(['toleransi_menit', 'is_active']);
        });
    }
};
