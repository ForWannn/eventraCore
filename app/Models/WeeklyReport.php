<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyReport extends Model
{
    protected $guarded = [];

    protected $casts = [
        'week_start_date' => 'date',
        'plan_submitted_at' => 'datetime',
        'final_submitted_at' => 'datetime',
    ];

    public function user() { 
        return $this->belongsTo(User::class);
    }
    public function items() {
        return $this->hasMany(WeeklyItem::class);
    }
    public function dailyLogs() {
        return $this->hasMany(DailyLog::class);
    }
}