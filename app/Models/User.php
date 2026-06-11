<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'avatar',        // [FR-16] Foto profil
        'last_login_at', // [FR-16] Tracking terakhir login
        'phone',         // [FR-16] Nomor telepon (dari halaman profile)
        'address',       // [FR-16] Alamat (dari halaman profile)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_login_at' => 'datetime', // [FR-16]
    ];

    /**
     * ==========================================================
     * RELATIONSHIPS
     * ==========================================================
     */

    // Event yang dibuat oleh user
    public function events()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    // Laporan yang digenerate oleh user
    public function reports()
    {
        return $this->hasMany(Report::class, 'generated_by');
    }

    // Activity logs milik user
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    /**
     * ==========================================================
     * ROLE & PERMISSIONS (FR-01, FR-16)
     * ==========================================================
     */

    // Cek apakah user adalah superadmin
    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    // Cek apakah user adalah admin
    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'superadmin';
    }

    // Mendapatkan label role untuk UI
    public function getRoleLabelAttribute()
    {
        if ($this->role === 'superadmin') {
            return '<span class="badge bg-danger">Superadmin</span>';
        }
        
        return '<span class="badge bg-primary">Admin</span>';
    }

    /**
     * ==========================================================
     * PROFILE & AVATAR (FR-16)
     * ==========================================================
     */

    // Getter untuk URL avatar
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && file_exists(public_path('storage/avatars/' . $this->avatar))) {
            return asset('storage/avatars/' . $this->avatar);
        }
        
        // Default avatar berdasarkan inisial
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    // Getter untuk inisial (jika tidak pakai avatar)
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        
        return substr($initials, 0, 2); // Max 2 karakter
    }

    // Update last login (dipanggil dari AuthController)
    public function updateLastLogin()
    {
        $this->last_login_at = now();
        $this->save();
        
        // Log activity
        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'user_id' => $this->id,
                'activity' => 'User ' . $this->username . ' berhasil login',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * ==========================================================
     * PASSWORD VALIDATION (FR-16)
     * ==========================================================
     */

    // Validasi password saat ini
    public function validateCurrentPassword($currentPassword)
    {
        return Hash::check($currentPassword, $this->password);
    }

    // Update password dengan hash
    public function updatePassword($newPassword)
    {
        $this->password = Hash::make($newPassword);
        $this->save();
        
        // Log activity
        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'user_id' => $this->id,
                'activity' => 'Password diupdate',
                'ip_address' => request()->ip(),
            ]);
        }
    }

    /**
     * ==========================================================
     * SCOPES - FILTERING
     * ==========================================================
     */

    // Scope untuk superadmin
    public function scopeSuperadmins($query)
    {
        return $query->where('role', 'superadmin');
    }

    // Scope untuk admin biasa
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * ==========================================================
     * BOOT METHOD
     * ==========================================================
     */

    protected static function boot()
    {
        parent::boot();

        // Set default role saat create
        static::creating(function ($user) {
            if (empty($user->role)) {
                $user->role = 'admin';
            }
        });
    }
}