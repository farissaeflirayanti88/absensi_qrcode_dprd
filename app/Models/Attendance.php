<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara eksplisit
    protected $table = 'attendance';

    protected $fillable = [
        'event_id',
        'participant_id',
        'unique_code',
        'attendance_time',
        'notes',
    ];

    protected $casts = [
        'attendance_time' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}