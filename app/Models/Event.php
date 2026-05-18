<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'event_dates',
        'start_time',
        'end_time',
        'attendance_start',
        'attendance_end',
        'status',
        'pic_fee',
        'loading_fee',
        'unloading_fee',
        'needs_attendance',
    ];

    protected $casts = [
        'event_dates' => 'array',
        'needs_attendance' => 'boolean',
    ];

    /**
     * Override status with real-time computed value based on current time.
     */
    public function getStatusAttribute(): string
    {
        $now = now();
        $dates = $this->event_dates ?? [];
        if (empty($dates)) {
            return 'upcoming';
        }
        
        sort($dates);
        $firstDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
        if ($this->start_time) {
            $firstDate->setTimeFromTimeString((string) $this->start_time);
        }

        $lastDate = \Carbon\Carbon::parse(end($dates))->endOfDay();
        if ($this->end_time) {
            $lastDate = \Carbon\Carbon::parse(end($dates))->setTimeFromTimeString((string) $this->end_time);
        }
        
        if ($now < $firstDate) return 'upcoming';
        if ($now <= $lastDate) return 'ongoing';
        return 'completed';
    }

    /** PIC + other participants via event_participants */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants')
            ->withPivot('is_pic')
            ->withTimestamps();
    }

    /** Dynamic positions (Runner, MC, etc.) */
    public function positions()
    {
        return $this->hasMany(EventPosition::class);
    }

    /** Attendance records for this event */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
