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
        Schema::table('master_cost_center', function (Blueprint $table) {
            // Add id_unit_pt column if it doesn't exist
            if (!Schema::hasColumn('master_cost_center', 'id_unit_pt')) {
                $table->unsignedBigInteger('id_unit_pt')->nullable()->after('id');
                
                // Add foreign key constraint
                $table->foreign('id_unit_pt')
                    ->references('id')
                    ->on('master_unit_pt')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_cost_center', function (Blueprint $table) {
            // Drop foreign key and column
            if (Schema::hasColumn('master_cost_center', 'id_unit_pt')) {
                $table->dropForeign(['id_unit_pt']);
                $table->dropColumn('id_unit_pt');
            }
        });
    }
};
