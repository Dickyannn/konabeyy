<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing family status values from Indonesian to English
        DB::table('employee_anggota_keluarga')
            ->where('status_keluarga', 'Istri')
            ->orWhere('status_keluarga', 'Suami')
            ->update(['status_keluarga' => 'spouse']);
            
        DB::table('employee_anggota_keluarga')
            ->where('status_keluarga', 'Anak')
            ->update(['status_keluarga' => 'child']);
            
        DB::table('employee_anggota_keluarga')
            ->where('status_keluarga', 'Ayah')
            ->update(['status_keluarga' => 'father']);
            
        DB::table('employee_anggota_keluarga')
            ->where('status_keluarga', 'Ibu')
            ->update(['status_keluarga' => 'mother']);
            
        DB::table('employee_anggota_keluarga')
            ->where('status_keluarga', 'Mertua')
            ->update(['status_keluarga' => 'in-law']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to Indonesian values
        DB::table('employee_anggota_keluarga')
            ->where('status_keluarga', 'spouse')
            ->update(['status_keluarga' => 'Istri']);
            
        DB::table('employee_anggota_keluarga')
            ->where('status_keluarga', 'child')
            ->update(['status_keluarga' => 'Anak']);
            
        DB::table('employee_anggota_keluarga')
            ->where('status_keluarga', 'father')
            ->update(['status_keluarga' => 'Ayah']);
            
        DB::table('employee_anggota_keluarga')
            ->where('status_keluarga', 'mother')
            ->update(['status_keluarga' => 'Ibu']);
            
        DB::table('employee_anggota_keluarga')
            ->where('status_keluarga', 'in-law')
            ->update(['status_keluarga' => 'Mertua']);
    }
};
