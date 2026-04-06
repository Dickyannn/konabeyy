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
        Schema::create('obs_master_data_keluarga', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->enum('action_type', ['create', 'before_update', 'after_update', 'delete']);
            $table->json('data_before')->nullable();
            $table->json('data_after')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('created_at')->useCurrent();
            
            // Foreign keys
            $table->foreign('employee_id')->references('id')->on('employee_karyawan')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['employee_id', 'action_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obs_master_data_keluarga');
    }
};
