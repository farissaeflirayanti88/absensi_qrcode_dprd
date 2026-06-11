<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // ⚠️⚠️⚠️ INI HARUS DIAKTIFKAN! ⚠️⚠️⚠️
    // Karena tabel di database Anda namanya "attendance" (tunggal)
    // BUKAN "attendances" (jamak) yang dicari Laravel
    protected $table = 'attendance';

    protected $fillable = [
        'event_id',
        'participant_id',
        'unique_code',
        'attendance_time',
        'notes',
        'is_duplicate',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'attendance_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_duplicate' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
        // Tambah parameter ke-2 'event_id' untuk lebih spesifik
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
        // Tambah parameter ke-2 'participant_id'
    }

    /**
     * Scope untuk query data hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('attendance_time', today());
    }

    /**
     * Scope untuk query 7 hari terakhir
     */
    public function scopeLast7Days($query)
    {
        return $query->where('attendance_time', '>=', now()->subDays(7));
    }
}