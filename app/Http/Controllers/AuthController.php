<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ActivityLog;
use Carbon\Carbon;

class AuthController extends Controller
{
    // 1. RATE LIMITING SETTINGS
    protected $maxAttempts = 5;
    protected $decayMinutes = 15;
    
    // 2. JAM KERJA DPRD
    protected $workingHours = [
        'start' => 8,   // 08:00 WIB
        'end' => 17,    // 17:00 WIB
    ];

    // 3. Cache status
    private $cacheAvailable = false;

    public function __construct()
    {
        // Cek jika cache tersedia
        $this->cacheAvailable = function_exists('cache') && 
                               config('cache.default') !== 'database';
    }

    public function showLogin()
    {
        // Redirect jika sudah login
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // ========== SIMPLE VALIDATION ==========
        $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string|min:3' // Ubah min:8 ke min:3 untuk testing
        ]);

        // ========== FIND USER ==========
        $user = User::where('username', $request->username)->first();

        // ========== PASSWORD VERIFICATION (FIXED) ==========
        if (!$user) {
            // FR-01 Alt-2: Catat gagal login — username tidak ditemukan
            try {
                ActivityLog::create([
                    'user_id'    => null,
                    'activity'   => 'Gagal login: username "' . $request->username . '" tidak ditemukan',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Exception $e) {
                // Ignore logging error
            }

            return back()->withErrors([
                'username' => 'Username tidak terdaftar.'
            ])->withInput($request->except('password'));
        }

        // VERIFIKASI PASSWORD DENGAN MULTI-ALGORITHM
        $passwordVerified = $this->verifyPasswordEnhanced($request->password, $user->password, $user);
        
        if (!$passwordVerified) {
            // FR-01 Alt-1: Catat failed login attempt ke log
            try {
                ActivityLog::create([
                    'user_id'    => $user->id,
                    'activity'   => 'Gagal login: password salah untuk username "' . $request->username . '"',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Exception $e) {
                // Ignore logging error
            }

            return back()->withErrors([
                'username' => 'Password salah.'
            ])->withInput($request->except('password'));
        }

        // ========== ACCOUNT CHECKS ==========
        // Cek jika akun terkunci
        if (isset($user->is_locked) && $user->is_locked) {
            return back()->withErrors([
                'username' => 'Akun terkunci. Hubungi administrator.'
            ]);
        }
        
        // ========== LOGIN USER ==========
        try {
            // Login user
            Auth::login($user, $request->boolean('remember'));
            
            // Regenerate session ID
            $request->session()->regenerate();

            // Update last login
            $user->last_login_at = Carbon::now();
            $user->last_login_ip = $request->ip();
            
            // Reset login attempts jika ada kolomnya
            if (isset($user->login_attempts)) {
                $user->login_attempts = 0;
            }
            
            $user->save();

            // ========== LOG ACTIVITY ==========
            try {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'activity' => 'Login berhasil',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            } catch (\Exception $e) {
                // Ignore jika logging gagal
            }

            // ========== REDIRECT ==========
            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang, ' . $user->name . '!');
                
        } catch (\Exception $e) {
            return back()->withErrors([
                'username' => 'Terjadi kesalahan saat login: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Enhanced password verification dengan multiple algorithm support
     */
    private function verifyPasswordEnhanced($inputPassword, $storedPassword, $user = null)
    {
        // 1. Cek jika password kosong
        if (empty($storedPassword)) {
            return false;
        }

        // 2. Cek format hash untuk menentukan algoritma
        $hashType = $this->detectHashAlgorithm($storedPassword);
        
        switch ($hashType) {
            case 'bcrypt':
                // Gunakan Hash::check untuk bcrypt
                try {
                    if (Hash::check($inputPassword, $storedPassword)) {
                        return true;
                    }
                } catch (\Exception $e) {
                    // Jika Hash::check error, coba manual
                    return $this->manualBcryptCheck($inputPassword, $storedPassword);
                }
                break;
                
            case 'md5':
                // Cek MD5 hash
                if (md5($inputPassword) === $storedPassword) {
                    // Auto-upgrade ke bcrypt jika ada user object
                    if ($user) {
                        $this->upgradePasswordToBcrypt($user, $inputPassword);
                    }
                    return true;
                }
                break;
                
            case 'plain':
                // Password plain text (hanya untuk development/testing)
                if ($inputPassword === $storedPassword) {
                    // Auto-upgrade ke bcrypt
                    if ($user) {
                        $this->upgradePasswordToBcrypt($user, $inputPassword);
                    }
                    return true;
                }
                break;
                
            default:
                // Coba semua kemungkinan
                return $this->tryAllHashMethods($inputPassword, $storedPassword, $user);
        }
        
        return false;
    }

    /**
     * Deteksi algoritma hash berdasarkan format
     */
    private function detectHashAlgorithm($hash)
    {
        // 1. Cek bcrypt (format: $2y$...)
        if (substr($hash, 0, 4) === '$2y$' || 
            substr($hash, 0, 4) === '$2a$' || 
            substr($hash, 0, 4) === '$2b$') {
            return 'bcrypt';
        }
        
        // 2. Cek MD5 (32 karakter hex)
        if (strlen($hash) === 32 && preg_match('/^[a-f0-9]{32}$/i', $hash)) {
            return 'md5';
        }
        
        // 3. Jika pendek, mungkin plain text (hanya untuk debug)
        if (strlen($hash) < 60) {
            return 'plain';
        }
        
        // 4. Unknown - coba tebak
        return 'unknown';
    }

    /**
     * Manual bcrypt check jika Hash::check error
     */
    private function manualBcryptCheck($password, $hash)
    {
        try {
            // Coba menggunakan password_verify native PHP
            if (function_exists('password_verify')) {
                return password_verify($password, $hash);
            }
            
            // Fallback: extract salt dari hash bcrypt
            if (substr($hash, 0, 4) === '$2y$') {
                $parts = explode('$', $hash);
                if (count($parts) >= 4) {
                    $salt = '$2y$' . $parts[2] . '$' . $parts[3];
                    $newHash = crypt($password, $salt);
                    return hash_equals($hash, $newHash);
                }
            }
        } catch (\Exception $e) {
            return false;
        }
        
        return false;
    }

    /**
     * Coba semua metode hash
     */
    private function tryAllHashMethods($inputPassword, $storedPassword, $user = null)
    {
        // 1. Coba bcrypt dengan Hash::check
        try {
            if (Hash::check($inputPassword, $storedPassword)) {
                return true;
            }
        } catch (\Exception $e) {
            // Ignore
        }
        
        // 2. Cek MD5
        if (md5($inputPassword) === $storedPassword) {
            if ($user) {
                $this->upgradePasswordToBcrypt($user, $inputPassword);
            }
            return true;
        }
        
        // 3. Cek SHA1 (jika ada)
        if (sha1($inputPassword) === $storedPassword) {
            if ($user) {
                $this->upgradePasswordToBcrypt($user, $inputPassword);
            }
            return true;
        }
        
        // 4. Cek plain text (hanya untuk emergency)
        if ($inputPassword === $storedPassword) {
            if ($user) {
                $this->upgradePasswordToBcrypt($user, $inputPassword);
            }
            return true;
        }
        
        // 5. Cek dengan password_verify langsung
        if (function_exists('password_verify') && password_verify($inputPassword, $storedPassword)) {
            return true;
        }
        
        return false;
    }

    /**
     * Upgrade password ke bcrypt
     */
    private function upgradePasswordToBcrypt($user, $plainPassword)
    {
        try {
            $user->password = Hash::make($plainPassword);
            
            // Tambahkan timestamp jika ada kolomnya
            if (isset($user->password_changed_at)) {
                $user->password_changed_at = Carbon::now();
            }
            
            $user->save();
            
            // Log upgrade
            try {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'activity' => 'Password diupgrade ke Bcrypt',
                    'ip_address' => request()->ip()
                ]);
            } catch (\Exception $e) {
                // Ignore logging error
            }
        } catch (\Exception $e) {
            // Log error upgrade
            error_log('Password upgrade failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique device ID
     */
    private function generateDeviceId(Request $request)
    {
        $components = [
            $request->userAgent(),
            $request->ip(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
        ];
        
        return md5(implode('|', $components));
    }
    
    /**
     * Get device info for display
     */
    private function getDeviceInfo(Request $request)
    {
        $agent = new \Jenssegers\Agent\Agent();
        $agent->setUserAgent($request->userAgent());
        
        return [
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'device' => $agent->device(),
            'ip' => $request->ip(),
            'time' => now()->format('d/m/Y H:i')
        ];
    }
    
    /**
     * Rate limiting throttle key
     */
    private function throttleKey(Request $request)
    {
        return strtolower($request->username) . '|' . $request->ip();
    }

    /**
     * Logout method
     */
    public function logout(Request $request)
    {
        try {
            // Log activity sebelum logout
            if (Auth::check()) {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'activity' => 'Logout',
                    'ip_address' => $request->ip()
                ]);
            }
        } catch (\Exception $e) {
            // Ignore jika tidak bisa log
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Emergency password reset (untuk admin)
     */
    public function showEmergencyReset()
    {
        return view('auth.emergency-reset');
    }

    public function emergencyReset(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:users,username',
            'new_password' => 'required|min:3|confirmed'
        ]);

        try {
            $user = User::where('username', $request->username)->first();
            
            if (!$user) {
                return back()->withErrors(['username' => 'User tidak ditemukan']);
            }

            // Reset password ke bcrypt
            $user->password = Hash::make($request->new_password);
            
            if (isset($user->password_changed_at)) {
                $user->password_changed_at = Carbon::now();
            }
            
            // Reset lock status jika ada
            if (isset($user->is_locked)) {
                $user->is_locked = false;
            }
            
            if (isset($user->login_attempts)) {
                $user->login_attempts = 0;
            }
            
            $user->save();

            // Log activity
            ActivityLog::create([
                'user_id' => null,
                'activity' => 'Emergency password reset untuk: ' . $user->username,
                'ip_address' => $request->ip()
            ]);

            return redirect('/login')
                ->with('success', 'Password berhasil direset. Silakan login dengan password baru.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal reset password: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Debug password hash
     */
    public function debugPassword(Request $request)
    {
        // Hanya untuk development
        if (!app()->environment('local', 'development')) {
            abort(404);
        }

        $username = $request->get('username', 'admin');
        $password = $request->get('password', '123456');
        
        $user = User::where('username', $username)->first();
        
        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan']);
        }

        return response()->json([
            'stored_hash' => $user->password,
            'hash_type' => $this->detectHashAlgorithm($user->password),
            'hash_length' => strlen($user->password),
            'test_md5' => md5($password),
            'md5_match' => md5($password) === $user->password,
            'bcrypt_match' => Hash::check($password, $user->password),
            'manual_check' => $this->verifyPasswordEnhanced($password, $user->password, $user)
        ]);
    }
}