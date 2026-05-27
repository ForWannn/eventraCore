<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAttendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in_time',
        'attendance_type',
        'photo_path',
        'latitude',
        'longitude',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
