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
        // Update existing employees with null or empty jabatan to have default 'Staff'
        DB::table('employee_karyawan')
            ->whereNull('jabatan')
            ->orWhere('jabatan', '')
            ->update(['jabatan' => 'Staff']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this data migration
    }
};