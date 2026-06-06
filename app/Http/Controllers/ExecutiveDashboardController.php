<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventTask;
use App\Models\DailyAttendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExecutiveDashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));

        // 1. Fetch Events for the Year
        $allEvents = Event::where('event_dates', 'like', "%{$year}-%")->get();
        $completedEvents = $allEvents->filter(fn($e) => $e->status === 'completed');
        $completedEventIds = $completedEvents->pluck('id');

        // Total Completed Events
        $totalCompletedEvents = $completedEvents->count();
        
        // Month over Month Growth (Optional, for visual trend on KPI)
        $lastYearEvents = Event::where('event_dates', 'like', "%" . ($year - 1) . "-%")->get()
                                ->filter(fn($e) => $e->status === 'completed')->count();
        $eventGrowth = $lastYearEvents > 0 ? round((($totalCompletedEvents - $lastYearEvents) / $lastYearEvents) * 100, 1) : 0;

        // 2. Task Completion Rate (only for completed events)
        $tasksQuery = EventTask::whereIn('event_id', $completedEventIds);
        $totalTasks = (clone $tasksQuery)->count();
        $completedTasks = (clone $tasksQuery)->where('is_completed', true)->count();
        $taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        // 3. Crew Discipline Rate (Attendance On-Time Rate for the year)
        $totalYearlyAttendances = DailyAttendance::whereYear('date', $year)->count();
        $onTimeYearlyAttendances = DailyAttendance::whereYear('date', $year)->where('status', 'tepat_waktu')->count();
        $disciplineRate = $totalYearlyAttendances > 0 ? round(($onTimeYearlyAttendances / $totalYearlyAttendances) * 100, 1) : 0;

        // 4. Weekly Report Compliance Rate
        $totalWeeklyReports = \App\Models\WeeklyReport::whereYear('week_start_date', $year)->count();
        $completedWeeklyReports = \App\Models\WeeklyReport::whereYear('week_start_date', $year)->whereNotNull('final_submitted_at')->count();
        $reportComplianceRate = $totalWeeklyReports > 0 ? round(($completedWeeklyReports / $totalWeeklyReports) * 100, 1) : 0;

        // 5. Monthly Chart Data (Event Volume vs Productivity)
        $chartLabels = [];
        $eventsCountData = [];
        $productivityData = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthStr = sprintf('%04d-%02d', $year, $m);
            $monthlyEvents = $allEvents->filter(function($e) use ($monthStr) {
                $dates = $e->event_dates ?? [];
                return collect($dates)->contains(fn($d) => str_starts_with($d, $monthStr));
            });
            
            $completedMonthlyEvents = $monthlyEvents->filter(fn($e) => $e->status === 'completed');
            $monthlyCompletedEventIds = $completedMonthlyEvents->pluck('id');
            
            $mTotalTasks = EventTask::whereIn('event_id', $monthlyCompletedEventIds)->count();
            $mCompletedTasks = EventTask::whereIn('event_id', $monthlyCompletedEventIds)->where('is_completed', true)->count();
            
            $chartLabels[] = date('M', mktime(0, 0, 0, $m, 1));
            $eventsCountData[] = $monthlyEvents->count(); // all planned/completed events this month
            $productivityData[] = $mTotalTasks > 0 ? round(($mCompletedTasks / $mTotalTasks) * 100, 1) : 0;
        }

        $chartData = [
            'labels' => $chartLabels,
            'events_count' => $eventsCountData,
            'productivity' => $productivityData,
        ];

        // 5. Top 5 Karyawan Terajin vs Terlambat
        $topOnTimeUsers = DailyAttendance::select('user_id', DB::raw('count(*) as total'))
            ->whereYear('date', $year)
            ->where('status', 'tepat_waktu')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('user')
            ->get();

        $topLateUsers = DailyAttendance::select('user_id', DB::raw('count(*) as total'))
            ->whereYear('date', $year)
            ->where('status', 'terlambat')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('user')
            ->get();

        // Pass to View
        return view('executive-dashboard', compact(
            'year',
            'totalCompletedEvents',
            'eventGrowth',
            'taskCompletionRate',
            'disciplineRate',
            'reportComplianceRate',
            'chartData',
            'topOnTimeUsers',
            'topLateUsers'
        ));
    }
}
