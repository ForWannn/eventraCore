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
        $request->validate([
            'photo' => 'required', // Base64 image
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $user = Auth::user();
        $now = Carbon::now();
        $date = $now->format('Y-m-d');

        // Prevent double entry
        $exists = DailyAttendance::where('user_id', $user->id)
                                  ->where('date', $date)
                                  ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah melakukan absensi hari ini.'], 400);
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

        // Get all weekdays in this range
        $workdays = [];
        $temp = $startDate->copy();
        while ($temp->lte($endDate)) {
            // Check if weekday (Monday to Friday)
            if ($temp->dayOfWeek !== Carbon::SATURDAY && $temp->dayOfWeek !== Carbon::SUNDAY) {
                $workdays[] = $temp->format('Y-m-d');
            }
            $temp->addDay();
        }

        // Fetch actual attendance records in this range
        $attendanceRecords = DailyAttendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy('date');

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
                $historyData[] = [
                    'date' => $dateStr,
                    'day_name' => $dateObj->locale('id')->translatedFormat('l, d M Y'),
                    'check_in' => '-',
                    'status' => 'Tidak Hadir',
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

        foreach ($historyData as $item) {
            if ($item['status'] === 'Hadir') {
                $statsHadir++;
            } elseif ($item['status'] === 'Terlambat') {
                $statsTerlambat++;
            } elseif ($item['status'] === 'Tidak Hadir') {
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
                'hadir_pct' => $statsWorkdays > 0 ? round(($statsHadir / $statsWorkdays) * 100, 2) : 0,
                'terlambat_pct' => $statsWorkdays > 0 ? round(($statsTerlambat / $statsWorkdays) * 100, 2) : 0,
                'tidak_hadir_pct' => $statsWorkdays > 0 ? round(($statsTidakHadir / $statsWorkdays) * 100, 2) : 0,
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

        // Query ALL active users with their division & attendance for the selected date
        $users = \App\Models\User::with(['division'])
            ->with(['dailyAttendances' => function ($q) use ($date) {
                $q->where('date', $date);
            }])
            ->orderBy('name', 'asc')
            ->get();

        // Compute summary statistics
        $totalStaff = $users->count();
        $presentCount = $users->filter(fn($u) => $u->dailyAttendances->isNotEmpty())->count();
        $lateCount = $users->filter(fn($u) => $u->dailyAttendances->where('status', 'terlambat')->isNotEmpty())->count();
        $remoteCount = $users->filter(fn($u) => $u->dailyAttendances->where('attendance_type', 'luar')->isNotEmpty())->count();

        return view('daily-attendances.index', compact(
            'users', 'date', 'totalStaff', 'presentCount', 'lateCount', 'remoteCount'
        ));
    }
}
