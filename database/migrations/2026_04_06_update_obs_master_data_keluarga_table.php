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
        Schema::table('obs_master_data_keluarga', function (Blueprint $table) {
            // Add new columns for better tracking
            $table->unsignedBigInteger('id_anggota_keluarga')->nullable()->after('employee_id');
            $table->string('action_update', 50)->default('proposed')->after('action_type'); // before, after, proposed
            $table->string('change_reason', 255)->nullable()->after('action_update');
            
            // Add individual columns for snapshot (like obs_master_data_karyawan)
            $table->string('nik', 16)->nullable()->after('change_reason');
            $table->string('nama', 150)->nullable();
            $table->enum('status_keluarga', ['spouse', 'child', 'father', 'mother', 'father-in-law', 'mother-in-law'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->boolean('is_tanggungan')->default(false);
            $table->boolean('is_active')->default(true);
            $table->date('tanggal_nonaktif')->nullable();
            
            // Add notes for additional info
            $table->text('notes')->nullable();
            
            // Add index for better queries
            $table->index(['employee_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obs_master_data_keluarga', function (Blueprint $table) {
            $table->dropColumn([
                'id_anggota_keluarga',
                'action_update',
                'change_reason',
                'nik',
                'nama',
                'status_keluarga',
                'tanggal_lahir',
                'is_tanggungan',
                'is_active',
                'tanggal_nonaktif',
                'notes',
            ]);
            
            $table->dropIndex(['employee_id', 'created_at']);
        });
    }
};
