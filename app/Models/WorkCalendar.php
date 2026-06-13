<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'is_working_day',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'is_working_day' => 'boolean',
    ];

    private static $holidaysCache = [];

    /**
     * Clear in-memory holidays cache (primarily for tests).
     */
    public static function clearRuntimeCache()
    {
        self::$holidaysCache = [];
    }

    /**
     * Get holidays list for a given year (cached).
     */
    public static function getHolidaysForYear($year)
    {
        if (isset(self::$holidaysCache[$year])) {
            return self::$holidaysCache[$year];
        }

        $holidays = \Illuminate\Support\Facades\Cache::remember("indonesian_holidays_{$year}", 86400 * 30, function () use ($year) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)->get("https://libur.deno.dev/api?year={$year}");
                if ($response->successful()) {
                    $data = $response->json();
                    $list = [];
                    foreach ($data as $item) {
                        if (isset($item['date'])) {
                            $list[$item['date']] = [
                                'name' => $item['name'] ?? 'Hari Libur Nasional',
                                'is_national_holiday' => $item['is_national_holiday'] ?? true,
                            ];
                        }
                    }
                    return $list;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to fetch holidays: " . $e->getMessage());
            }
            return [];
        });

        self::$holidaysCache[$year] = $holidays;
        return $holidays;
    }

    /**
     * Check if a given date is a working day.
     */
    public static function isWorkingDay($date)
    {
        $dateObj = \Carbon\Carbon::parse($date);
        $dateStr = $dateObj->format('Y-m-d');

        // Check database override
        $override = self::where('date', $dateObj->startOfDay())->first();
        if ($override) {
            return (bool)$override->is_working_day;
        }

        // Weekend check
        if ($dateObj->dayOfWeek === \Carbon\Carbon::SATURDAY || $dateObj->dayOfWeek === \Carbon\Carbon::SUNDAY) {
            return false;
        }

        // National Holiday check
        $holidays = self::getHolidaysForYear($dateObj->year);
        if (isset($holidays[$dateStr])) {
            return false;
        }

        return true;
    }

    /**
     * Get holiday description for a given date.
     */
    public static function getHolidayDescription($date)
    {
        $dateObj = \Carbon\Carbon::parse($date);
        $dateStr = $dateObj->format('Y-m-d');

        // Check database override
        $override = self::where('date', $dateObj->startOfDay())->first();
        if ($override) {
            return $override->description;
        }

        // National Holiday check
        $holidays = self::getHolidaysForYear($dateObj->year);
        if (isset($holidays[$dateStr])) {
            return $holidays[$dateStr]['name'];
        }

        return null;
    }
}
