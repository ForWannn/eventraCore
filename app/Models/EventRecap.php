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
        'status', 
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
        return (float) $this->event->recapItems()
            ->whereNotIn('category', ['Tambahan Ops', 'Penambahan Saldo', 'Pemasukan', 'Refund', 'Pengurangan Anggaran'])
            ->sum('nominal');
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
        $uploaded = $this->event->recapItems()->count();
        $completenessScore = $uploaded > 0 ? 40 : 0;

        $speedScore = 5; 
        $eventDates = $this->event->event_dates ?? [];
        if (!empty($eventDates)) {
            sort($eventDates);
            $eventEnd = Carbon::parse(end($eventDates))->endOfDay();
            if ($this->event->end_time) {
                $eventEnd = Carbon::parse(end($eventDates))->setTimeFromTimeString((string) $this->event->end_time);
            }

            $compareDate = $this->completed_at ?: now();
            
            if ($compareDate <= $eventEnd) {
                $speedScore = 30;
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

        $statusScore = 10; 
        if ($this->status === 'menunggu_finance') {
            $statusScore = 20;
        } elseif ($this->status === 'selesai') {
            $statusScore = 30;
        }

        return (int) round($completenessScore + $speedScore + $statusScore);
    }
}
