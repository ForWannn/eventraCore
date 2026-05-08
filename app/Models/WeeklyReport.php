<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/WeeklyReport.php
class WeeklyReport extends Model
{
    protected $guarded = [];
    protected $casts = [
        'week_start_date' => 'date',
        'plan_submitted_at' => 'datetime',
        'final_submitted_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(WeeklyItem::class); }
    public function dailyLogs() { return $this->hasMany(DailyLog::class); }
    
    // Helper untuk mengambil objective saja
    public function objectives() { return $this->items()->where('type', 'objective'); }
    // Helper untuk mengambil deadline saja
    public function deadlines() { return $this->items()->where('type', 'deadline'); }
    public function getCompletionRateAttribute()
{
    $total = $this->items()->count();
    if ($total == 0) return 0;
    
    $completed = $this->items()->where('is_completed', true)->count();
    return round(($completed / $total) * 100);
}
}
