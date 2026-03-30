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
        Schema::table('auth_audit_log', function (Blueprint $table) {
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->text('details')->nullable()->after('user_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auth_audit_log', function (Blueprint $table) {
            $table->dropColumn(['user_agent', 'details']);
        });
    }
};