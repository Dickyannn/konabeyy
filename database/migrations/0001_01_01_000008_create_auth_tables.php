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
        // 1. auth.roles
        Schema::create('auth_roles', function (Blueprint $table) {
            $table->id();
            $table->string('kode_role', 50)->unique();
            $table->string('nama_role', 100);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 2. auth.users (FK to roles, unit_pt, karyawan)
        Schema::create('auth_users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('email', 150)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->rememberToken();
            $table->foreignId('id_role')->constrained('auth_roles', 'id')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('id_unit')->nullable()->constrained('master_unit_pt', 'id')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('id_karyawan')->nullable()->constrained('employee_karyawan', 'id')->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('id_role');
            $table->index('id_unit');
            $table->index('is_active');
        });

        // 3. auth.password_reset_tokens
        Schema::create('auth_password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 150)->primary();
            $table->string('token', 255);
            $table->timestamp('created_at')->nullable();
        });

        // 4. auth.sessions (FK to users)
        // Modified to use string ID for Laravel sessions
        Schema::create('auth_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('auth_users', 'id')->cascadeOnUpdate()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 5. auth.audit_log (FK to users)
        Schema::create('auth_audit_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('id_user')->nullable()->constrained('auth_users', 'id')->cascadeOnUpdate()->nullOnDelete();
            $table->string('action', 50);
            $table->string('table_name', 100)->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index(['id_user', 'created_at']);
            $table->index(['table_name', 'record_id']);
            $table->index(['action', 'created_at']);
        });

        // Seed data
        $this->seedAuthData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_audit_log');
        Schema::dropIfExists('auth_sessions');
        Schema::dropIfExists('auth_password_reset_tokens');
        Schema::dropIfExists('auth_users');
        Schema::dropIfExists('auth_roles');
    }

    private function seedAuthData(): void
    {
        // Insert roles
        \DB::table('auth_roles')->insert([
            [
                'kode_role' => 'master_system',
                'nama_role' => 'Master System',
                'deskripsi' => 'Admin sistem — konfigurasi parameter, kelola user',
            ],
            [
                'kode_role' => 'personal_admin',
                'nama_role' => 'Personal Administration',
                'deskripsi' => 'Akses penuh data kepegawaian (CRUD)',
            ],
            [
                'kode_role' => 'payroll',
                'nama_role' => 'Payroll',
                'deskripsi' => 'Proses penggajian, THR, insentif, laporan BPJS',
            ],
            [
                'kode_role' => 'personalia',
                'nama_role' => 'Personalia',
                'deskripsi' => 'Data karyawan (read-only) + kelola absensi & cuti',
            ],
        ]);

        // Insert superadmin - WARNING: Ganti password ini setelah deploy!
        // Password default: Admin@12345 (bcrypt hash)
        \DB::table('auth_users')->insert([
            [
                'nama' => 'Super Admin',
                'email' => 'admin@hrms.local',
                'email_verified_at' => now(),
                'password' => bcrypt('Admin@12345'),
                'id_role' => 1,
                'id_unit' => null,
                'id_karyawan' => null,
                'is_active' => true,
            ],
        ]);
    }
};
