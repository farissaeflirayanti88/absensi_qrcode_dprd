<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'event_name', 
        'location',
        'event_date',
        'description',
        'qr_code_hash',
        'qr_code_url',
        'qr_code_generated_at',
        'is_active',
        'status',
        'max_participants',
        'requires_registration',
        'category'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_active' => 'boolean',
        'requires_registration' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'qr_code_generated_at' => 'datetime',
    ];

    /**
     * Boot method untuk memastikan event_date selalu punya waktu
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($event) {
            // Pastikan event_date selalu dalam format datetime lengkap
            if ($event->event_date) {
                // Jika hanya berisi tanggal (Y-m-d), tambah waktu default 09:00:00
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $event->event_date)) {
                    $event->event_date = $event->event_date . ' 09:00:00';
                }
            }
        });
    }

    // Relasi dengan creator (user)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi dengan attendances
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // ==========================================================
    // ACCESSORS & MUTATORS UNTUK TANGGAL
    // ==========================================================
    
    /**
     * Get formatted event date for display (d/m/Y H:i)
     */
    public function getFormattedEventDateAttribute()
    {
        if (!$this->event_date) {
            return '-';
        }
        return $this->event_date->format('d/m/Y H:i');
    }

    /**
     * Get formatted event date for display with day name (l, d F Y H:i)
     */
    public function getFormattedEventDateFullAttribute()
    {
        if (!$this->event_date) {
            return '-';
        }
        return $this->event_date->translatedFormat('l, d F Y H:i');
    }

    /**
     * Get event date for input field (Y-m-d)
     */
    public function getInputDateAttribute()
    {
        return $this->event_date ? $this->event_date->format('Y-m-d') : now()->format('Y-m-d');
    }

    /**
     * Get event time for input field (H:i)
     */
    public function getInputTimeAttribute()
    {
        if (!$this->event_date) {
            return '09:00';
        }
        return $this->event_date->format('H:i');
    }

    /**
     * Check if event has custom time (not default 09:00)
     */
    public function getHasCustomTimeAttribute()
    {
        if (!$this->event_date) {
            return false;
        }
        return $this->event_date->format('H:i') !== '09:00';
    }

    /**
     * Get event date only (without time)
     */
    public function getDateOnlyAttribute()
    {
        return $this->event_date ? $this->event_date->format('Y-m-d') : null;
    }

    /**
     * Get event time only
     */
    public function getTimeOnlyAttribute()
    {
        return $this->event_date ? $this->event_date->format('H:i') : '09:00';
    }

    // ==========================================================
    // FR-04: BUKA/TUTUP AKSES ABSENSI
    // ==========================================================
    
    // Scope untuk event yang aksesnya aktif (peserta bisa absen)
    public function scopeActiveAccess($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk event yang aksesnya ditutup
    public function scopeInactiveAccess($query)
    {
        return $query->where('is_active', false);
    }

    // Cek apakah akses absensi sedang dibuka
    public function isAccessOpen()
    {
        return $this->is_active == 1;
    }

    // ==========================================================
    // FR-14: ARSIP ACARA
    // ==========================================================
    
    // Scope untuk event yang belum diarsip (tampil di kelola acara)
    public function scopeNotArchived($query)
    {
        return $query->where('status', 'active');
    }

    // Scope untuk event yang sudah diarsip
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    // Cek apakah event sudah diarsipkan
    public function isArchived()
    {
        return $this->status === 'archived';
    }

    // Cek apakah event bisa diarsipkan
    public function canBeArchived()
    {
        return $this->attendances()->exists() && !$this->isArchived();
    }

    // ==========================================================
    // SCOPE: UPCOMING & PAST
    // ==========================================================
    
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('event_date', '<', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('event_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('event_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('event_date', now()->month)
                     ->whereYear('event_date', now()->year);
    }

    // ==========================================================
    // FR-15: READ-ONLY UNTUK ARSIP
    // ==========================================================
    
    public function canBeEdited()
    {
        return $this->status !== 'archived';
    }

    public function canBeDeleted()
    {
        return !$this->attendances()->exists() && !$this->isArchived();
    }

    // ==========================================================
    // HELPER: STATUS UNTUK UI
    // ==========================================================
    
    public function getStatusLabelAttribute()
    {
        if ($this->isArchived()) {
            return 'Diarsipkan';
        }
        
        if ($this->is_active) {
            return 'Aktif';
        }
        
        return 'Tidak Aktif';
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->isArchived()) {
            return '<span class="px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs">Diarsipkan</span>';
        }
        
        if ($this->is_active) {
            return '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs"><i class="fas fa-circle text-xs mr-1"></i>Aktif</span>';
        }
        
        return '<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs"><i class="fas fa-circle text-xs mr-1"></i>Tidak Aktif</span>';
    }

    public function getIsAccessibleAttribute()
    {
        return $this->status === 'active' 
            && $this->is_active 
            && ($this->event_date->isFuture() || $this->event_date->isToday());
    }
}