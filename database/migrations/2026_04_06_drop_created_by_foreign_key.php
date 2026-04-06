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
            // Drop the foreign key constraint
            $table->dropForeign(['created_by']);
            
            // Keep the column but without foreign key constraint
            // This allows any user ID to be stored
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obs_master_data_keluarga', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
