<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        // Redirect ke dashboard sesuai role jika sudah login
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        // Cek apakah user aktif
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !$user->is_active) {
            return back()->withErrors([
                'email' => 'Akun belum diaktifkan atau tidak ditemukan.',
            ])->withInput($request->except('password'));
        }

        // Attempt login
        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            // Update last login time
            $user = Auth::user();
            $user->update(['last_login' => now()]);

            // Log audit
            $this->logAudit('LOGIN', null, null);

            // Redirect sesuai role user
            return $this->redirectByRole($user)
                ->with('success', 'Selamat datang ' . $user->nama . '!');
        }

        // Failed login
        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai.',
        ])->withInput($request->except('password'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Log audit
        $this->logAudit('LOGOUT', null, null);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'))
            ->with('status', 'Anda telah berhasil logout.');
    }

    /**
     * Log activity ke audit_log
     */
    private function logAudit($action, $tableName = null, $recordId = null)
    {
        try {
            DB::table('auth_audit_log')->insert([
                'id_user' => Auth::id(),
                'action' => $action,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silent fail - jangan ganggu flow login
            \Log::error('Audit log gagal: ' . $e->getMessage());
        }
    }

    /**
     * Redirect user ke dashboard sesuai dengan rolenya
     */
    private function redirectByRole(User $user)
    {
        // Load role relationship
        $user->load('role');

        // Tentukan redirect berdasarkan kode role
        $roleCode = $user->role->kode_role ?? null;

        switch ($roleCode) {
            case 'master_system':
                return redirect()->route('master.dashboard');
            case 'personal_admin':
                return redirect()->route('dashboard');
            case 'payroll_admin':
                return redirect()->route('dashboard');
            case 'personalia':
                return redirect()->route('dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }
}

