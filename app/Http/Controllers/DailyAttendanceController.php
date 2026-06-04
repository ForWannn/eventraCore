<?php

namespace App\Http\Controllers;

use App\Models\DailyAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DailyAttendanceController extends Controller
{
    public function storeLuar(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('Intern')) {
            return response()->json(['message' => 'Intern tidak diperbolehkan melakukan absensi.'], 403);
        }

        $request->validate([
            'photo' => 'required', // Base64 image
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $now = Carbon::now();
        $date = $now->format('Y-m-d');

        // Block check-in on non-working days
        $calendar = \App\Models\WorkCalendar::where('date', $date)->first();
        $isWorkDay = true;
        if ($calendar) {
            $isWorkDay = (bool)$calendar->is_working_day;
        } else {
            if ($now->dayOfWeek === Carbon::SATURDAY || $now->dayOfWeek === Carbon::SUNDAY) {
                $isWorkDay = false;
            }
        }

        if (!$isWorkDay) {
            return response()->json(['message' => 'Hari ini libur.'], 400);
        }

        // Block check-in after attendance close time (12:00)
        $closeTime = config('attendance.attendance_close_time', '12:00:00');
        $closeThreshold = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $closeTime);
        if ($now->gte($closeThreshold)) {
            return response()->json(['message' => 'Absensi sudah ditutup. Absensi dibuka kembali pada pukul 00:00.'], 400);
        }

        // Prevent double entry
        $exists = DailyAttendance::where('user_id', $user->id)
                                  ->where('date', $date)
                                  ->exists();

        if ($exists) {
            return response()->json(['message' => 'Kamu sudah melakukan absensi hari ini.'], 400);
        }

        // Process Base64 Photo
        $image = $request->photo;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = 'attendance_' . $user->id . '_' . time() . '.png';
        Storage::disk('public')->put('attendances/' . $imageName, base64_decode($image));

        // Logic Status: Terlambat jika lewat threshold
        $limit = config('attendance.checkout_threshold');
        $threshold = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $limit);
        $status = $now->gt($threshold) ? 'terlambat' : 'tepat_waktu';

        DailyAttendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'check_in_time' => $now->format('H:i:s'),
            'attendance_type' => 'luar',
            'photo_path' => 'attendances/' . $imageName,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Absensi luar kantor berhasil dikirim!',
            'status' => $status,
            'time' => $now->format('H:i:s')
        ]);
    }

    public function myHistory(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('Intern')) {
            abort(403, 'Intern tidak memiliki akses ke riwayat absensi.');
        }
        $now = Carbon::now();

        // Date range parse
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        if ($startDateInput && $endDateInput) {
            $startDate = Carbon::parse($startDateInput)->startOfDay();
            $endDate = Carbon::parse($endDateInput)->endOfDay();
        } else {
            // Default to current month
            $startDate = $now->copy()->startOfMonth()->startOfDay();
            $endDate = $now->copy()->endOfMonth()->endOfDay();
        }

        // Fetch calendar overrides in range
        $overrides = \App\Models\WorkCalendar::whereBetween('date', [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        ])->get()->keyBy(fn($item) => $item->date->format('Y-m-d'));

        // Get all weekdays or overridden dates in this range
        $workdays = [];
        $temp = $startDate->copy();
        while ($temp->lte($endDate)) {
            $dateStr = $temp->format('Y-m-d');
            $isWork = true;
            if (isset($overrides[$dateStr])) {
                $isWork = $overrides[$dateStr]->is_working_day;
            } else {
                if ($temp->dayOfWeek === Carbon::SATURDAY || $temp->dayOfWeek === Carbon::SUNDAY) {
                    $isWork = false;
                }
            }

            if ($isWork) {
                $workdays[] = $dateStr;
            }
            $temp->addDay();
        }

        // Fetch actual attendance records in this range
        $attendanceRecords = DailyAttendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy('date');

        // Fetch approved leaves covering this range
        $approvedLeaves = \App\Models\LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->where('start_date', '<=', $startDate->format('Y-m-d'))
                          ->where('end_date', '>=', $endDate->format('Y-m-d'));
                  });
            })
            ->get();

        // Build the full list of entries
        $historyData = [];
        foreach ($workdays as $dateStr) {
            $dateObj = Carbon::parse($dateStr);
            if ($dateObj->gt($now)) {
                // Ignore future weekdays
                continue;
            }

            if (isset($attendanceRecords[$dateStr])) {
                $record = $attendanceRecords[$dateStr];
                // Late duration calculation
                $checkIn = Carbon::parse($record->check_in_time);
                $schedule = Carbon::parse($dateStr . ' 08:00:00');
                $lateDiff = $schedule->diffInMinutes($checkIn, false);
                $lateString = '-';
                if ($record->status === 'terlambat' && $lateDiff > 0) {
                    $hours = floor($lateDiff / 60);
                    $minutes = $lateDiff % 60;
                    $lateString = ($hours > 0 ? "$hours jam " : "") . "$minutes menit";
                }

                $historyData[] = [
                    'date' => $dateStr,
                    'day_name' => $dateObj->locale('id')->translatedFormat('l, d M Y'),
                    'check_in' => Carbon::parse($record->check_in_time)->format('H:i'),
                    'status' => $record->status === 'tepat_waktu' ? 'Hadir' : 'Terlambat',
                    'attendance_type' => $record->attendance_type,
                    'photo_path' => $record->photo_path,
                    'latitude' => $record->latitude,
                    'longitude' => $record->longitude,
                    'lateness' => $lateString,
                    'is_present' => true
                ];
            } else {
                // Check if has approved leave request for this date
                $leave = $approvedLeaves->first(fn($l) => $l->start_date->format('Y-m-d') <= $dateStr && $l->end_date->format('Y-m-d') >= $dateStr);

                $status = 'Tidak Hadir';
                if ($leave) {
                    $status = $leave->type === 'izin' ? 'Izin' : 'Cuti';
                }

                $historyData[] = [
                    'date' => $dateStr,
                    'day_name' => $dateObj->locale('id')->translatedFormat('l, d M Y'),
                    'check_in' => '-',
                    'status' => $status,
                    'attendance_type' => null,
                    'photo_path' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'lateness' => '-',
                    'is_present' => false
                ];
            }
        }

        // Sort historyData desc by date
        usort($historyData, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        // Calculate statistics for the full range (before filtering status/search)
        $statsWorkdays = count($historyData);
        $statsHadir = 0;
        $statsTerlambat = 0;
        $statsTidakHadir = 0;
        $statsIzin = 0;
        $statsCuti = 0;

        foreach ($historyData as $item) {
            if ($item['status'] === 'Hadir') {
                $statsHadir++;
            } elseif ($item['status'] === 'Terlambat') {
                $statsTerlambat++;
            } elseif ($item['status'] === 'Izin') {
                $statsIzin++;
            } elseif ($item['status'] === 'Cuti') {
                $statsCuti++;
            } else {
                $statsTidakHadir++;
            }
        }

        // Filter by Status if requested
        $statusFilter = $request->input('status', 'all');
        if ($statusFilter !== 'all') {
            $historyData = array_filter($historyData, function($item) use ($statusFilter) {
                if ($statusFilter === 'hadir') {
                    return $item['status'] === 'Hadir';
                } elseif ($statusFilter === 'terlambat') {
                    return $item['status'] === 'Terlambat';
                } elseif ($statusFilter === 'tidak_hadir') {
                    return $item['status'] === 'Tidak Hadir';
                } elseif ($statusFilter === 'izin') {
                    return $item['status'] === 'Izin';
                } elseif ($statusFilter === 'cuti') {
                    return $item['status'] === 'Cuti';
                }
                return true;
            });
        }

        // Filter by Search if requested
        $searchQuery = $request->input('search');
        if ($searchQuery) {
            $historyData = array_filter($historyData, function($item) use ($searchQuery) {
                return (strpos(strtolower($item['day_name']), strtolower($searchQuery)) !== false) ||
                       (strpos(strtolower($item['status']), strtolower($searchQuery)) !== false);
            });
        }

        // Paginate the array
        $perPage = (int) $request->input('per_page', 7);
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $currentItems = array_slice($historyData, ($currentPage - 1) * $perPage, $perPage);
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            count($historyData),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $dateRangeString = $startDate->locale('id')->translatedFormat('d M Y') . ' - ' . $endDate->locale('id')->translatedFormat('d M Y');

        return view('daily-attendances.history', [
            'attendances' => $paginatedItems,
            'stats' => [
                'workdays' => $statsWorkdays,
                'hadir' => $statsHadir,
                'terlambat' => $statsTerlambat,
                'tidak_hadir' => $statsTidakHadir,
                'izin' => $statsIzin,
                'cuti' => $statsCuti,
                'hadir_pct' => $statsWorkdays > 0 ? round(($statsHadir / $statsWorkdays) * 100, 2) : 0,
                'terlambat_pct' => $statsWorkdays > 0 ? round(($statsTerlambat / $statsWorkdays) * 100, 2) : 0,
                'tidak_hadir_pct' => $statsWorkdays > 0 ? round(($statsTidakHadir / $statsWorkdays) * 100, 2) : 0,
                'izin_pct' => $statsWorkdays > 0 ? round(($statsIzin / $statsWorkdays) * 100, 2) : 0,
                'cuti_pct' => $statsWorkdays > 0 ? round(($statsCuti / $statsWorkdays) * 100, 2) : 0,
            ],
            'filters' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'status' => $statusFilter,
                'search' => $searchQuery,
                'per_page' => $perPage,
            ],
            'dateRangeString' => $dateRangeString
        ]);
    }

    public function recap(Request $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $search = $request->input('search');
        $divisionId = $request->input('division_id');
        $statusFilter = $request->input('status', 'all');

        // Query ALL active users with their division & attendance for the selected date
        $users = \App\Models\User::with(['division'])
            ->with(['dailyAttendances' => function ($q) use ($date) {
                $q->where('date', $date);
            }])
            ->orderBy('name', 'asc')
            ->get();

        // Query approved leave requests covering the selected date
        $leaves = \App\Models\LeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->get()
            ->keyBy('user_id');

        $now = Carbon::now();
        $dateObj = Carbon::parse($date);
        
        $limit = config('attendance.attendance_close_time', '12:00:00');
        $threshold = Carbon::parse($date . ' ' . $limit);
        
        $isClosed = false;
        if ($dateObj->isPast() && !$dateObj->isToday()) {
            $isClosed = true;
        } elseif ($dateObj->isToday() && $now->gt($threshold)) {
            $isClosed = true;
        }

        // Check if the recap date is a working day
        $calendar = \App\Models\WorkCalendar::where('date', $date)->first();
        $isWorkDay = true;
        if ($calendar) {
            $isWorkDay = (bool)$calendar->is_working_day;
        } else {
            if ($dateObj->dayOfWeek === Carbon::SATURDAY || $dateObj->dayOfWeek === Carbon::SUNDAY) {
                $isWorkDay = false;
            }
        }

        // Map and compute status details for all users
        $usersData = $users->map(function ($u) use ($leaves, $isClosed, $date, $isWorkDay) {
            $attendance = $u->dailyAttendances->first();
            $leave = $leaves->get($u->id);
            
            $status = 'belum_hadir';
            $checkInTime = null;
            $lateness = null;
            $method = null;
            $photoPath = null;
            $latitude = null;
            $longitude = null;
            $reason = null;
            
            if ($attendance) {
                $status = $attendance->status === 'tepat_waktu' ? 'hadir' : 'terlambat';
                $checkInTime = $attendance->check_in_time;
                $method = $attendance->attendance_type;
                $photoPath = $attendance->photo_path;
                $latitude = $attendance->latitude;
                $longitude = $attendance->longitude;
                
                // Calculate lateness minutes
                if ($attendance->status === 'terlambat') {
                    $checkIn = Carbon::parse($attendance->check_in_time);
                    $schedule = Carbon::parse($date . ' 08:00:00');
                    $diff = $schedule->diffInMinutes($checkIn, false);
                    if ($diff > 0) {
                        $hours = floor($diff / 60);
                        $minutes = $diff % 60;
                        $lateness = ($hours > 0 ? "$hours jam " : "") . "$minutes menit";
                    }
                }
            } elseif ($leave) {
                $status = 'izin_cuti';
                $reason = $leave->type === 'izin' ? 'Izin: ' . ($leave->reason ?? 'Izin pribadi') : 'Cuti: ' . ($leave->reason ?? 'Cuti tahunan');
            } else {
                if (!$isWorkDay) {
                    $status = 'libur';
                } else {
                    $status = $isClosed ? 'absen' : 'belum_hadir';
                }
            }
            
            return [
                'user' => $u,
                'status' => $status,
                'check_in_time' => $checkInTime,
                'lateness' => $lateness,
                'method' => $method,
                'photo_path' => $photoPath,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'reason' => $reason,
                'leave' => $leave,
            ];
        });

        // Compute summary statistics BEFORE filters are applied
        $totalStaff = $usersData->count();
        $hadirCount = $usersData->where('status', 'hadir')->count();
        $lateCount = $usersData->where('status', 'terlambat')->count();
        $absenCount = $usersData->where('status', 'absen')->count();
        $leaveCount = $usersData->where('status', 'izin_cuti')->count();
        $notPresentCount = $usersData->where('status', 'belum_hadir')->count();
        $liburCount = $usersData->where('status', 'libur')->count();

        $hadirPct = $totalStaff > 0 ? round(($hadirCount / $totalStaff) * 100, 1) : 0;
        $latePct = $totalStaff > 0 ? round(($lateCount / $totalStaff) * 100, 1) : 0;
        $absenPct = $totalStaff > 0 ? round(($absenCount / $totalStaff) * 100, 1) : 0;
        $leavePct = $totalStaff > 0 ? round(($leaveCount / $totalStaff) * 100, 1) : 0;
        $notPresentPct = $totalStaff > 0 ? round(($notPresentCount / $totalStaff) * 100, 1) : 0;

        // Apply filters
        if ($search) {
            $usersData = $usersData->filter(function ($item) use ($search) {
                return strpos(strtolower($item['user']->name), strtolower($search)) !== false;
            });
        }

        if ($divisionId && $divisionId !== 'all') {
            $usersData = $usersData->filter(function ($item) use ($divisionId) {
                return (string)$item['user']->division_id === (string)$divisionId;
            });
        }

        if ($statusFilter && $statusFilter !== 'all') {
            $usersData = $usersData->filter(function ($item) use ($statusFilter) {
                return $item['status'] === $statusFilter;
            });
        }

        // Paginate results
        $perPage = (int) $request->input('per_page', 10);
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $currentItems = array_slice($usersData->all(), ($currentPage - 1) * $perPage, $perPage);
        
        $paginatedUsers = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $usersData->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $divisions = \App\Models\Division::orderBy('name', 'asc')->get();

        return view('daily-attendances.index', [
            'users' => $paginatedUsers,
            'date' => $date,
            'divisions' => $divisions,
            'totalStaff' => $totalStaff,
            'hadirCount' => $hadirCount,
            'lateCount' => $lateCount,
            'absenCount' => $absenCount,
            'leaveCount' => $leaveCount,
            'notPresentCount' => $notPresentCount,
            'liburCount' => $liburCount,
            'isWorkDay' => $isWorkDay,
            'stats' => [
                'hadir_pct' => $hadirPct,
                'terlambat_pct' => $latePct,
                'absen_pct' => $absenPct,
                'leave_pct' => $leavePct,
                'not_present_pct' => $notPresentPct,
            ],
            'filters' => [
                'search' => $search,
                'division_id' => $divisionId,
                'status' => $statusFilter,
                'per_page' => $perPage,
            ]
        ]);
    }

    public function exportRecap(Request $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $search = $request->input('search');
        $divisionId = $request->input('division_id');
        $statusFilter = $request->input('status', 'all');

        $users = \App\Models\User::with(['division'])
            ->with(['dailyAttendances' => function ($q) use ($date) {
                $q->where('date', $date);
            }])
            ->orderBy('name', 'asc')
            ->get();

        $leaves = \App\Models\LeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->get()
            ->keyBy('user_id');

        $now = Carbon::now();
        $dateObj = Carbon::parse($date);
        
        $limit = config('attendance.attendance_close_time', '12:00:00');
        $threshold = Carbon::parse($date . ' ' . $limit);
        
        $isClosed = false;
        if ($dateObj->isPast() && !$dateObj->isToday()) {
            $isClosed = true;
        } elseif ($dateObj->isToday() && $now->gt($threshold)) {
            $isClosed = true;
        }

        // Check if the recap date is a working day
        $calendar = \App\Models\WorkCalendar::where('date', $date)->first();
        $isWorkDay = true;
        if ($calendar) {
            $isWorkDay = (bool)$calendar->is_working_day;
        } else {
            if ($dateObj->dayOfWeek === Carbon::SATURDAY || $dateObj->dayOfWeek === Carbon::SUNDAY) {
                $isWorkDay = false;
            }
        }

        $usersData = $users->map(function ($u) use ($leaves, $isClosed, $date, $isWorkDay) {
            $attendance = $u->dailyAttendances->first();
            $leave = $leaves->get($u->id);
            
            $status = 'belum_hadir';
            $checkInTime = null;
            $lateness = null;
            $method = null;
            $reason = null;
            
            if ($attendance) {
                $status = $attendance->status === 'tepat_waktu' ? 'hadir' : 'terlambat';
                $checkInTime = $attendance->check_in_time;
                $method = $attendance->attendance_type === 'kantor' ? 'Website' : 'Website';
                
                if ($attendance->status === 'terlambat') {
                    $checkIn = Carbon::parse($attendance->check_in_time);
                    $schedule = Carbon::parse($date . ' 08:00:00');
                    $diff = $schedule->diffInMinutes($checkIn, false);
                    if ($diff > 0) {
                        $hours = floor($diff / 60);
                        $minutes = $diff % 60;
                        $lateness = ($hours > 0 ? "$hours jam " : "") . "$minutes menit";
                    }
                }
            } elseif ($leave) {
                $status = 'izin_cuti';
                $reason = $leave->type === 'izin' ? 'Izin: ' . ($leave->reason ?? 'Izin pribadi') : 'Cuti: ' . ($leave->reason ?? 'Cuti tahunan');
            } else {
                if (!$isWorkDay) {
                    $status = 'libur';
                } else {
                    $status = $isClosed ? 'absen' : 'belum_hadir';
                }
            }
            
            return [
                'user' => $u,
                'name' => $u->name,
                'division' => $u->division->name ?? 'Tanpa Divisi',
                'status' => $status,
                'check_in_time' => $checkInTime,
                'lateness' => $lateness,
                'method' => $method,
                'reason' => $reason,
            ];
        });

        // Apply filters
        if ($search) {
            $usersData = $usersData->filter(function ($item) use ($search) {
                return strpos(strtolower($item['name']), strtolower($search)) !== false;
            });
        }

        if ($divisionId && $divisionId !== 'all') {
            $usersData = $usersData->filter(function ($item) use ($divisionId) {
                return (string)$item['user']->division_id === (string)$divisionId;
            });
        }

        if ($statusFilter && $statusFilter !== 'all') {
            $usersData = $usersData->filter(function ($item) use ($statusFilter) {
                return $item['status'] === $statusFilter;
            });
        }

        $fileName = 'rekap_absensi_' . $date . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($usersData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Nama Karyawan', 'Departemen', 'Status', 'Jam Masuk', 'Keterlambatan', 'Metode', 'Keterangan']);

            foreach ($usersData as $row) {
                $statusLabel = '-';
                if ($row['status'] === 'hadir') $statusLabel = 'Hadir';
                elseif ($row['status'] === 'terlambat') $statusLabel = 'Terlambat';
                elseif ($row['status'] === 'absen') $statusLabel = '-';
                elseif ($row['status'] === 'izin_cuti') $statusLabel = 'Izin/Cuti';
                elseif ($row['status'] === 'belum_hadir') $statusLabel = 'Belum Hadir';
                elseif ($row['status'] === 'libur') $statusLabel = 'Libur';

                fputcsv($file, [
                    $row['name'],
                    $row['division'],
                    $statusLabel,
                    $row['check_in_time'] ?? '-',
                    $row['lateness'] ?? '-',
                    $row['method'] ?? '-',
                    $row['reason'] ?? ($row['status'] === 'hadir' ? 'Tepat waktu' : ($row['status'] === 'terlambat' ? 'Terlambat' : ($row['status'] === 'absen' ? '-' : ($row['status'] === 'libur' ? 'Hari libur' : '-')))),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdfMonthly(Request $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $search = $request->input('search');
        $divisionId = $request->input('division_id');
        $statusFilter = $request->input('status', 'all');

        $dateObj = Carbon::parse($date);
        $startOfMonth = $dateObj->copy()->startOfMonth();
        $endOfMonth = $dateObj->copy()->endOfMonth();
        $year = $dateObj->year;

        // Get month name in Indonesian
        $monthName = $startOfMonth->locale('id')->translatedFormat('F');

        // Fetch users matching search and division filters, excluding Admin and Superadmin roles
        $usersQuery = \App\Models\User::with(['division', 'roles'])
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['Admin', 'Superadmin']);
            })
            ->orderBy('name', 'asc');

        if ($search) {
            $usersQuery->where('name', 'like', '%' . $search . '%');
        }

        if ($divisionId && $divisionId !== 'all') {
            $usersQuery->where('division_id', $divisionId);
        }

        $users = $usersQuery->get();

        // If status filter is active, filter users by their status on the selected $date
        if ($statusFilter && $statusFilter !== 'all') {
            $dateAttendances = \App\Models\DailyAttendance::where('date', $date)
                ->get()
                ->keyBy('user_id');

            $dateLeaves = \App\Models\LeaveRequest::where('status', 'approved')
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->get()
                ->keyBy('user_id');

            $calendar = \App\Models\WorkCalendar::where('date', $date)->first();
            $isWorkDay = true;
            if ($calendar) {
                $isWorkDay = (bool)$calendar->is_working_day;
            } else {
                if ($dateObj->dayOfWeek === Carbon::SATURDAY || $dateObj->dayOfWeek === Carbon::SUNDAY) {
                    $isWorkDay = false;
                }
            }

            $limit = config('attendance.attendance_close_time', '12:00:00');
            $threshold = Carbon::parse($date . ' ' . $limit);
            
            $now = Carbon::now();
            $isClosed = false;
            if ($dateObj->isPast() && !$dateObj->isToday()) {
                $isClosed = true;
            } elseif ($dateObj->isToday() && $now->gt($threshold)) {
                $isClosed = true;
            }

            $users = $users->filter(function ($u) use ($dateAttendances, $dateLeaves, $isWorkDay, $isClosed, $statusFilter) {
                $att = $dateAttendances->get($u->id);
                $leave = $dateLeaves->get($u->id);

                $status = 'belum_hadir';
                if ($att) {
                    $status = $att->status === 'tepat_waktu' ? 'hadir' : 'terlambat';
                } elseif ($leave) {
                    $status = 'izin_cuti';
                } else {
                    if (!$isWorkDay) {
                        $status = 'libur';
                    } else {
                        $status = $isClosed ? 'absen' : 'belum_hadir';
                    }
                }

                return $status === $statusFilter;
            });
        }

        // Fetch WorkCalendar overrides for the entire month
        $overrides = \App\Models\WorkCalendar::whereBetween('date', [
            $startOfMonth->format('Y-m-d'),
            $endOfMonth->format('Y-m-d')
        ])->get()->keyBy(fn($item) => $item->date->format('Y-m-d'));

        // Fetch all approved leaves for these users during the month
        $userIds = $users->pluck('id');
        $approvedLeaves = \App\Models\LeaveRequest::whereIn('user_id', $userIds)
            ->where('status', 'approved')
            ->where(function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('start_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                  ->orWhereBetween('end_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                  ->orWhere(function($sub) use ($startOfMonth, $endOfMonth) {
                      $sub->where('start_date', '<=', $startOfMonth->format('Y-m-d'))
                          ->where('end_date', '>=', $endOfMonth->format('Y-m-d'));
                  });
            })
            ->get();

        $leavesMap = [];
        foreach ($approvedLeaves as $leave) {
            $temp = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);
            while ($temp->lte($end)) {
                $leavesMap[$leave->user_id][$temp->format('Y-m-d')] = $leave;
                $temp->addDay();
            }
        }

        // Fetch all attendance records for these users during the month
        $attendances = \App\Models\DailyAttendance::whereIn('user_id', $userIds)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get();

        $attendanceMap = [];
        foreach ($attendances as $att) {
            $attendanceMap[$att->user_id][$att->date] = $att;
        }

        $recapData = [];

        foreach ($users as $user) {
            // Generate short name (first word with length > 2)
            $words = explode(' ', trim($user->name));
            $shortName = '';
            foreach ($words as $word) {
                $cleanWord = preg_replace('/[^\w]/', '', $word);
                if (strlen($cleanWord) > 2) {
                    $shortName = $cleanWord;
                    break;
                }
            }
            if (!$shortName) {
                $shortName = $words[0] ?? $user->name;
            }

            // Loop through each calendar day of the month
            $tempDay = $startOfMonth->copy();
            while ($tempDay->lte($endOfMonth)) {
                $dateStr = $tempDay->format('Y-m-d');
                $tanggalLabel = $tempDay->locale('id')->translatedFormat('l, d F Y');
                
                $isWeekend = ($tempDay->dayOfWeek === Carbon::SATURDAY || $tempDay->dayOfWeek === Carbon::SUNDAY);
                
                $workCalendar = $overrides->get($dateStr);
                if ($workCalendar) {
                    $isWorkDay = (bool)$workCalendar->is_working_day;
                } else {
                    $isWorkDay = !$isWeekend;
                }
                
                $leave = $leavesMap[$user->id][$dateStr] ?? null;
                $attendance = $attendanceMap[$user->id][$dateStr] ?? null;

                $rowClass = '';
                $checkInTimeStr = '';
                $terlambatStr = '0 Menit';
                $catatan = '';

                if ($isWeekend) {
                    // Weekend days (Saturday & Sunday) must display blank check-in times and empty notes (white)
                    $rowClass = '';
                    $checkInTimeStr = '';
                    $terlambatStr = '0 Menit';
                    $catatan = '';
                } elseif (!$isWorkDay) {
                    // Weekday holidays: white, note LIBUR
                    $rowClass = '';
                    $checkInTimeStr = '';
                    $terlambatStr = '0 Menit';
                    $catatan = 'LIBUR';
                } elseif ($leave) {
                    // Approved leave: white, note IZIN/CUTI
                    $rowClass = '';
                    $checkInTimeStr = '';
                    $terlambatStr = '0 Menit';
                    $catatan = $leave->type === 'izin' ? 'IZIN' : 'CUTI';
                } else {
                    // Regular working weekday
                    if ($attendance) {
                        $checkInObj = Carbon::parse($attendance->check_in_time);
                        $checkInTimeStr = $checkInObj->format('H:i');
                        $checkInTimeOnly = $checkInObj->format('H:i:s');
                        
                        // Weekdays where check-in occurred before 07:00 AM or after 12:00 PM: LUPA ABSEN
                        $isLupaAbsen = ($checkInTimeOnly < '00:00:00' || $checkInTimeOnly > '12:00:00');
                        $isLate = ($checkInTimeOnly > '09:00:00');

                        if ($isLupaAbsen) {
                            $rowClass = 'lupa-absen-row'; // yellow
                            $catatan = '';
                        } elseif ($isLate) {
                            $rowClass = 'lupa-absen-row'; // yellow (telat kuning)
                            $catatan = '';
                        } else {
                            $rowClass = 'tepat-waktu-row'; // green
                            $catatan = '';
                        }

                        // Lateness calculation against 09:00:00
                        if ($isLate) {
                            $thresholdObj = Carbon::parse($dateStr . ' 09:00:00');
                            $diff = $thresholdObj->diffInMinutes($checkInObj, false);
                            
                            if ($diff > 0) {
                                $hours = intval(floor($diff / 60));
                                $minutes = intval($diff % 60);
                                $terlambatStr = $hours . ' Jam ' . $minutes . ' Menit';
                            }
                        }
                    } else {
                        // Working weekday, no check-in, no leave: ALPA (merah)
                        $rowClass = 'mangkir-row';
                        $checkInTimeStr = '';
                        $terlambatStr = '0 Menit';
                        $catatan = 'ALPA';
                    }
                }

                $recapData[] = [
                    'tanggal' => $tanggalLabel,
                    'nama' => $shortName,
                    'jam_masuk' => $checkInTimeStr,
                    'terlambat' => $terlambatStr,
                    'catatan' => $catatan,
                    'row_class' => $rowClass,
                ];

                $tempDay->addDay();
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('daily-attendances.pdf_monthly', [
            'monthName' => $monthName,
            'year' => $year,
            'threshold' => '9:00',
            'recapData' => $recapData,
        ]);

        return $pdf->stream('data_absensi_bulan_' . strtolower($monthName) . '_' . $year . '.pdf');
    }
}
