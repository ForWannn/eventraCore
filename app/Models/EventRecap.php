<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EventRecap extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'initial_nominal',
        'expected_receipts_count',
        'status', // draft, dalam_rekap, menunggu_finance, direvisi, selesai
        'speed_percentage',
        'completed_at',
    ];

    protected $casts = [
        'initial_nominal' => 'decimal:2',
        'expected_receipts_count' => 'integer',
        'speed_percentage' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Calculate total spent from recap items
     */
    public function getTotalSpentAttribute(): float
    {
        return (float) $this->event->recapItems()->sum('nominal');
    }

    /**
     * Calculate remaining budget
     */
    public function getRemainingBudgetAttribute(): float
    {
        return (float) ($this->initial_nominal - $this->total_spent);
    }

    /**
     * Calculate completion score (percentage out of 100)
     */
    public function getCompletionScoreAttribute(): int
    {
        // 1. Completeness of Notes (Weight: 40%)
        // Since there is no target nota, if at least 1 receipt is uploaded, completeness is 40%, otherwise 0%
        $uploaded = $this->event->recapItems()->count();
        $completenessScore = $uploaded > 0 ? 40 : 0;

        // 2. Ketepatan Waktu (Weight: 30%)
        // Days elapsed since event completion
        $speedScore = 5; // default minimum
        $eventDates = $this->event->event_dates ?? [];
        if (!empty($eventDates)) {
            sort($eventDates);
            $eventEnd = Carbon::parse(end($eventDates))->endOfDay();
            if ($this->event->end_time) {
                $eventEnd = Carbon::parse(end($eventDates))->setTimeFromTimeString((string) $this->event->end_time);
            }

            $compareDate = $this->completed_at ?: now();
            
            if ($compareDate <= $eventEnd) {
                $speedScore = 30; // completed before or exactly at event end
            } else {
                $daysLate = $eventEnd->diffInDays($compareDate);
                if ($daysLate <= 1) {
                    $speedScore = 30;
                } elseif ($daysLate <= 2) {
                    $speedScore = 25;
                } elseif ($daysLate <= 3) {
                    $speedScore = 20;
                } elseif ($daysLate <= 5) {
                    $speedScore = 10;
                } else {
                    $speedScore = 5;
                }
            }
        }

        // 3. Status Validasi (Weight: 30%)
        $statusScore = 10; // draft / dalam_rekap
        if ($this->status === 'menunggu_finance') {
            $statusScore = 20;
        } elseif ($this->status === 'selesai') {
            $statusScore = 30;
        }

        return (int) round($completenessScore + $speedScore + $statusScore);
    }
}
