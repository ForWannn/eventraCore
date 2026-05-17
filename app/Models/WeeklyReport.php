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

    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(WeeklyItem::class); }
    public function dailyLogs() { return $this->hasMany(DailyLog::class); }

    // Hitung otomatis persentase objektif mingguan yang dicentang
    public function getCompletionPercentageAttribute()
    {
        $totalObjectives = $this->items()
            ->where('type', 'objective')
            ->whereNotNull('content')
            ->where('content', '!=', '')
            ->count();

        if ($totalObjectives === 0) return 0;

        $completed = $this->items()
            ->where('type', 'objective')
            ->where('is_completed', true)
            ->count();

        return round(($completed / $totalObjectives) * 100);
    }
}