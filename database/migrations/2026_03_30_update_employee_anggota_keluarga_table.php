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
        Schema::table('employee_anggota_keluarga', function (Blueprint $table) {
            // Add NIK field
            $table->string('nik', 16)->nullable()->after('nama');
            
            // Rename hubungan to status_keluarga and update values
            $table->renameColumn('hubungan', 'status_keluarga');
            
            // Add is_active and tanggal_nonaktif fields
            $table->boolean('is_active')->default(true)->after('is_tanggungan');
            $table->date('tanggal_nonaktif')->nullable()->after('is_active');
            
            // Remove tanggal_menikah as it's not in requirements
            $table->dropColumn('tanggal_menikah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_anggota_keluarga', function (Blueprint $table) {
            $table->dropColumn(['nik', 'is_active', 'tanggal_nonaktif']);
            $table->renameColumn('status_keluarga', 'hubungan');
            $table->date('tanggal_menikah')->nullable();
        });
    }
};