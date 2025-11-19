<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_active'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    // Scope untuk event aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Cek apakah event masih aktif (berdasarkan tanggal dan status)
    public function getIsAccessibleAttribute()
    {
        return $this->is_active && ($this->event_date->isFuture() || $this->event_date->isToday());
    }
}