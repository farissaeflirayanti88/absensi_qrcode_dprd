<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'report_name',
        'report_type',
        'file_path',
        'generated_by',
    ];

    // Nonaktifkan timestamps karena tidak ada created_at/updated_at
    public $timestamps = false;

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}