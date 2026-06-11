<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // [NEW] Boot method untuk validasi otomatis
    protected static function boot()
    {
        parent::boot();

        // Validasi sebelum create/update
        static::saving(function ($participant) {
            // Cek duplikasi nomor telepon (case insensitive)
            $query = self::where('phone', $participant->phone);
            
            // Jika update, exclude ID sendiri
            if ($participant->id) {
                $query->where('id', '!=', $participant->id);
            }
            
            // Include soft deleted? Default: tidak
            if ($query->exists()) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], [], [])->errors()->add(
                        'phone', 
                        'Nomor telepon sudah terdaftar atas nama: ' . 
                        $query->first()->name
                    )
                );
            }
        });
    }

    // Relasi ke attendances
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // [NEW] Scope untuk pencarian duplikat
    public function scopeDuplicates($query, $field = 'phone')
    {
        return $query->select('*')
            ->whereIn('id', function ($q) use ($field) {
                $q->selectRaw('MIN(id)')
                  ->from('participants')
                  ->groupBy($field)
                  ->havingRaw('COUNT(*) > 1');
            })
            ->orderBy($field);
    }

    // [NEW] Cek apakah peserta sudah absen di acara tertentu
    public function hasAttended($eventId)
    {
        return $this->attendances()
            ->where('event_id', $eventId)
            ->exists();
    }

    // [NEW] Get total kehadiran unik per acara
    public function getUniqueAttendanceCountAttribute()
    {
        return $this->attendances()
            ->select('event_id')
            ->distinct()
            ->count('event_id');
    }

    // [NEW] Format nomor telepon
    public function getFormattedPhoneAttribute()
    {
        $phone = $this->phone;
        // Format: 0812-3456-7890
        if (strlen($phone) >= 10) {
            return substr($phone, 0, 4) . '-' . 
                   substr($phone, 4, 4) . '-' . 
                   substr($phone, 8);
        }
        return $phone;
    }

    // [NEW] Validator untuk request
    public static function validate($data, $id = null)
    {
        $rules = [
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[0-9]+$/',
                'unique:participants,phone,' . $id . ',id,deleted_at,NULL' // Ignore soft deleted
            ]
        ];

        $messages = [
            'phone.unique' => 'Nomor telepon :input sudah digunakan oleh peserta lain.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka 0-9.',
            'phone.min' => 'Nomor telepon minimal 10 digit.',
            'phone.max' => 'Nomor telepon maksimal 15 digit.',
        ];

        return validator($data, $rules, $messages);
    }
}