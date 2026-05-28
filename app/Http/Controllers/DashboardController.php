<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\EventPosition;
use App\Models\DailyAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $data = [];
        
        if ($user->hasRole(['CEO', 'GM'])) {
            $data = $this->getDirectorData($request);
        } else {
            $data = $this->getEmployeeData();
        }

        return view('dashboard', $data);
    }

    private function getEmployeeData(): array
    {
        $user = Auth::user();
        $now = Carbon::now();

        // ── 1. Personal Assignments ────────────────────────
        // Get all events where user is PIC or a Position Member
        $allAssignedEvents = Event::whereHas('participants', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->orWhereHas('positions.members', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['participants', 'positions.members'])->get();

        $activeAssignments = $allAssignedEvents->filter(fn($e) => in_array($e->status, ['ongoing', 'upcoming']));
        
        // ── 2. Stat Cards ─────────────────────────────────
        $totalAssignments = $allAssignedEvents->count();
        $activeCount = $activeAssignments->count();
        
        // Attendance count (total clock-ins)
        $attendanceCount = \App\Models\Attendance::where('user_id', $user->id)->count();

        // Weekly Report Status for current week
        $startOfWeek = $now->copy()->startOfWeek();
        $currentReport = \App\Models\WeeklyReport::where('user_id', $user->id)
            ->where('week_start_date', $startOfWeek->format('Y-m-d'))
            ->first();
        
        $reportStatus = 'Belum Dibuat';
        $showBanner = false;
        $bannerType = null; // 'plan' or 'final'
        
        if ($currentReport) {
            if ($currentReport->final_submitted_at) $reportStatus = 'Selesai';
            elseif ($currentReport->plan_submitted_at) $reportStatus = 'On-Progress (Plan OK)';
            else $reportStatus = 'Drafting';
        }

        // Smart Banner Logic
        $dayOfWeek = $now->dayOfWeek; // 0 (Sun) to 6 (Sat). 1=Mon, 2=Tue, 5=Fri
        if (in_array($dayOfWeek, [1, 2])) {
            if (!$currentReport || !$currentReport->plan_submitted_at) {
                $showBanner = true;
                $bannerType = 'plan';
            }
        } elseif ($dayOfWeek === 5) {
            if (!$currentReport || !$currentReport->final_submitted_at) {
                $showBanner = true;
                $bannerType = 'final';
            }
        }

        // ── 3. Personal Calendar ──────────────────────────
        $calendarEvents = [];
        foreach ($allAssignedEvents as $event) {
            $dates = $event->event_dates ?? [];
            if (empty($dates)) continue;
            sort($dates);

            $calendarEvents[] = [
                'title' => $event->name,
                'start' => $dates[0],
                'end'   => Carbon::parse(end($dates))->addDay()->format('Y-m-d'),
                'color' => match ($event->status) {
                    'ongoing'   => '#2563eb',
                    'upcoming'  => '#f59e0b',
                    'completed' => '#10b981',
                    default     => '#6b7280',
                },
                'url'   => route('events.show', $event->id),
            ];
        }

        // ── 4. Upcoming Assignments List ──────────────────
        $upcomingList = $activeAssignments
            ->sortBy(function ($e) {
                $dates = $e->event_dates ?? [];
                return empty($dates) ? '9999-12-31' : min($dates);
            })
            ->take(3)
            ->map(function ($event) use ($user) {
                // Find user's role in this event
                $role = 'Staff';
                $pic = $event->participants->where('id', $user->id)->where('pivot.is_pic', true)->first();
                if ($pic) $role = 'PIC';
                else {
                    $pos = $event->positions->filter(function($p) use ($user) {
                        return $p->members->contains($user->id);
                    })->first();
                    if ($pos) $role = $pos->name;
                }

                $dates = $event->event_dates ?? [];
                sort($dates);

                return [
                    'id'        => $event->id,
                    'name'      => $event->name,
                    'status'    => $event->status,
                    'role'      => $role,
                    'date_start' => !empty($dates) ? Carbon::parse($dates[0])->translatedFormat('d M Y') : 'TBA',
                ];
            })->values();

        // ── 5. Personal Tasks ─────────────────────────────
        // Fetch tasks from active events that are NOT completed
        // Fetch tasks from active events:
        // 1. Assigned specifically to user
        // 2. OR Assigned to NULL (Umum) AND user is part of the event
        $personalTasks = \App\Models\EventTask::whereIn('event_id', $activeAssignments->pluck('id'))
            ->where('is_completed', false)
            ->where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereNull('assigned_to');
            })
            ->with('event')
            ->latest()
            ->get();

        // Daily Attendance Status for TODAY
        $todayAttendance = DailyAttendance::where('user_id', $user->id)
            ->where('date', $now->format('Y-m-d'))
            ->first();

        // Total events assigned to the employee for the current month
        $totalEventsThisMonth = $allAssignedEvents->filter(function($event) use ($now) {
            $dates = $event->event_dates ?? [];
            foreach ($dates as $d) {
                $dt = Carbon::parse($d);
                if ($dt->month === $now->month && $dt->year === $now->year) return true;
            }
            return false;
        })->count();

        // Attendance count this month
        $attendanceCountThisMonth = DailyAttendance::where('user_id', $user->id)
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->count();

        // Calculate weekdays (Monday to Friday) in the current month
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $workDays = 0;
        $temp = $startOfMonth->copy();
        while ($temp->lte($endOfMonth)) {
            if ($temp->dayOfWeek !== Carbon::SATURDAY && $temp->dayOfWeek !== Carbon::SUNDAY) {
                $workDays++;
            }
            $temp->addDay();
        }

        // Recent 3 daily attendances
        $recentAttendances = DailyAttendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(3)
            ->get();

        return [
            'totalAssignments'         => $totalAssignments,
            'totalEventsThisMonth'     => $totalEventsThisMonth,
            'activeCount'              => $activeCount,
            'attendanceCount'          => $attendanceCount,
            'attendanceCountThisMonth' => $attendanceCountThisMonth,
            'workDays'                 => $workDays,
            'recentAttendances'        => $recentAttendances,
            'reportStatus'             => $reportStatus,
            'calendarEvents'           => json_encode($calendarEvents),
            'upcomingList'             => $upcomingList,
            'personalTasks'            => $personalTasks,
            'pendingTasksCount'        => $personalTasks->count(),
            'todayAttendance'          => $todayAttendance,
            'showBanner'               => $showBanner,
            'bannerType'               => $bannerType,
        ];
    }

    private function getDirectorData(Request $request): array
    {
        $now = Carbon::now();
        $events = Event::with(['participants', 'positions.members'])->get();

        // ── 1. Stat Cards ─────────────────────────────────
        $activeEvents = $events->filter(fn($e) => in_array($e->status, ['ongoing', 'upcoming']));
        $ongoingEventsCount = $events->filter(fn($e) => $e->status === 'ongoing')->count();

        // Unique employees currently assigned to active events
        $activeEmployeeIds = collect();
        foreach ($activeEvents as $event) {
            foreach ($event->positions as $pos) {
                foreach ($pos->members as $member) {
                    $activeEmployeeIds->push($member->id);
                }
            }
            foreach ($event->participants as $participant) {
                $activeEmployeeIds->push($participant->id);
            }
        }
        $activeEmployeesCount = $activeEmployeeIds->unique()->count();

        // Total unique employees attended today
        $todayAttendancesCount = DailyAttendance::whereDate('date', $now->toDateString())->distinct('user_id')->count();
        
        // Total unique staff (excluding CEO and GM)
        $totalStaff = User::whereHas('roles', function($q) {
            $q->whereNotIn('name', ['CEO', 'GM']);
        })->count();
        $attendanceRate = $totalStaff > 0 ? round(($todayAttendancesCount / $totalStaff) * 100) : 0;

        // ── 2. Calendar Events (JSON) ──────────────────────
        $calendarEvents = [];
        foreach ($events as $event) {
            $dates = $event->event_dates ?? [];
            if (empty($dates)) continue;
            sort($dates);

            // Main event range
            $calendarEvents[] = [
                'title' => $event->name,
                'start' => $dates[0],
                'end'   => Carbon::parse(end($dates))->addDay()->format('Y-m-d'),
                'color' => match ($event->status) {
                    'ongoing'   => '#2563eb',
                    'upcoming'  => '#f59e0b',
                    'completed' => '#10b981',
                    default     => '#6b7280',
                },
                'status'      => $event->status,
                'url'         => route('events.show', $event->id),
                'className'   => 'fc-event-' . $event->status,
                'extendedProps' => [
                    'status' => $event->status,
                    'positions' => $event->positions->count(),
                ],
            ];
        }

        // ── 3. Monthly Event Trend (Comparison of selected year vs previous year) ────────
        $filterYear = $request->query('year', date('Y'));
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $trendsCurrent = [];
        $trendsPrevious = [];
        
        for ($i = 1; $i <= 12; $i++) {
            // Current selected year
            $countCurrent = $events->filter(function ($event) use ($filterYear, $i) {
                $dates = $event->event_dates ?? [];
                foreach ($dates as $d) {
                    $dt = Carbon::parse($d);
                    if ($dt->month === $i && $dt->year == $filterYear) return true;
                }
                return false;
            })->count();
            $trendsCurrent[] = $countCurrent;

            // Previous year
            $prevYear = $filterYear - 1;
            $countPrev = $events->filter(function ($event) use ($prevYear, $i) {
                $dates = $event->event_dates ?? [];
                foreach ($dates as $d) {
                    $dt = Carbon::parse($d);
                    if ($dt->month === $i && $dt->year == $prevYear) return true;
                }
                return false;
            })->count();
            $trendsPrevious[] = $countPrev;
        }

        // ── 4. Upcoming Events List (top 3, nearest first) ─
        $upcomingEventsList = $events
            ->filter(fn($e) => $e->status !== 'completed')
            ->sortBy(function ($e) {
                $dates = $e->event_dates ?? [];
                return empty($dates) ? '9999-12-31' : min($dates);
            })
            ->take(3)
            ->map(function ($event) {
                $pic = $event->participants->where('pivot.is_pic', true)->first();
                $dates = $event->event_dates ?? [];
                sort($dates);
                
                $timeString = 'TBA';
                if ($event->start_time) {
                    $start = Carbon::parse($event->start_time)->format('H.i');
                    $end = $event->end_time ? Carbon::parse($event->end_time)->format('H.i') : 'Selesai';
                    $timeString = "{$start} — {$end} WIB";
                }
                
                // Location mock
                $location = 'Jakarta';
                if (stripos($event->name, 'launch') !== false) {
                    $location = 'Bandung';
                } elseif (stripos($event->name, 'client') !== false || stripos($event->name, 'gathering') !== false) {
                    $location = 'Surabaya';
                } elseif (stripos($event->name, 'meeting') !== false) {
                    $location = 'Malang';
                }
                
                $dayNum = 'TBA';
                $monthStr = 'TBA';
                if (!empty($dates)) {
                    $firstDate = Carbon::parse($dates[0]);
                    $dayNum = $firstDate->format('d');
                    $monthStr = strtoupper($firstDate->locale('id')->translatedFormat('M'));
                }

                return [
                    'id'        => $event->id,
                    'name'      => $event->name,
                    'status'    => $event->status,
                    'pic_name'  => $pic?->name ?? 'Belum ditentukan',
                    'pic_photo' => $pic?->photo_url ?? null,
                    'date_start' => !empty($dates) ? Carbon::parse($dates[0])->translatedFormat('d M Y') : 'TBA',
                    'date_end'   => count($dates) > 1 ? Carbon::parse(end($dates))->translatedFormat('d M Y') : null,
                    'positions_count' => $event->positions->count(),
                    'members_count'   => $event->positions->map(fn($p) => $p->members->count())->sum(),
                    'time_range' => $timeString,
                    'location' => $location,
                    'day_num' => $dayNum,
                    'month_str' => $monthStr,
                ];
            })->values();

        // ── 5. Status Distribution ─────────────────────────
        $statusCounts = [
            'upcoming'  => $events->filter(fn($e) => $e->status === 'upcoming')->count(),
            'ongoing'   => $events->filter(fn($e) => $e->status === 'ongoing')->count(),
            'completed' => $events->filter(fn($e) => $e->status === 'completed')->count(),
        ];

        // ── 6. Top 3 Employees ─────────────────────────────
        $employeeEventCounts = [];
        foreach ($events as $event) {
            $countedUsersForEvent = [];
            
            // Add PICs
            foreach ($event->participants as $participant) {
                if ($participant->pivot->is_pic && !isset($countedUsersForEvent[$participant->id])) {
                    $employeeEventCounts[$participant->id] = ($employeeEventCounts[$participant->id] ?? 0) + 1;
                    $countedUsersForEvent[$participant->id] = true;
                }
            }

            // Add role members
            foreach ($event->positions as $pos) {
                foreach ($pos->members as $member) {
                    if (!isset($countedUsersForEvent[$member->id])) {
                        $employeeEventCounts[$member->id] = ($employeeEventCounts[$member->id] ?? 0) + 1;
                        $countedUsersForEvent[$member->id] = true;
                    }
                }
            }
        }
        
        arsort($employeeEventCounts);
        $topEmployeeIds = array_slice(array_keys($employeeEventCounts), 0, 3);
        $topEmployeesRaw = User::whereIn('id', $topEmployeeIds)->get()->keyBy('id');
        
        $topEmployees = [];
        foreach ($topEmployeeIds as $id) {
            if (isset($topEmployeesRaw[$id])) {
                $topEmployees[] = [
                    'user'  => $topEmployeesRaw[$id],
                    'count' => $employeeEventCounts[$id]
                ];
            }
        }

        return [
            'totalEvents'         => $events->count(),
            'activeEventsCount'   => $activeEvents->count(),
            'ongoingEventsCount'  => $ongoingEventsCount,
            'activeEmployeesCount'=> $activeEmployeesCount,
            'todayAttendancesCount' => $todayAttendancesCount,
            'totalStaff'          => $totalStaff,
            'attendanceRate'      => $attendanceRate,
            'calendarEvents'      => json_encode($calendarEvents),
            'trendYear'           => $filterYear,
            'months'              => $months,
            'trendsCurrent'       => $trendsCurrent,
            'trendsPrevious'      => $trendsPrevious,
            'upcomingEventsList'  => $upcomingEventsList,
            'statusCounts'        => $statusCounts,
            'topEmployees'        => $topEmployees,
            'todayAttendance'     => DailyAttendance::where('user_id', Auth::id())
                                        ->where('date', Carbon::now()->format('Y-m-d'))
                                        ->first(),
        ];
    }
}
