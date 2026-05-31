<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceApiController extends Controller
{
    /**
     * Handle Hikvision Push Event
     */
    public function hikvisionPush(Request $request)
    {
        Log::info('Hikvision Attendance Push Received:', $request->all());

        $employeeNo = $request->input('employeeNo') ?? $request->input('EmployeeNo');
        $timeString = $request->input('time') ?? $request->input('dateTime');

        if (!$employeeNo) {
            return response()->json(['status' => 'error', 'message' => 'employeeNo is required'], 400);
        }

        $user = User::where('employee_id', $employeeNo)
                    ->orWhere('nik', $employeeNo)
                    ->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found with ID: ' . $employeeNo], 404);
        }

        $checkInTime = $timeString ? Carbon::parse($timeString) : Carbon::now();
        $date = $checkInTime->format('Y-m-d');

        $exists = DailyAttendance::where('user_id', $user->id)
                                  ->where('date', $date)
                                  ->exists();

        if ($exists) {
            return response()->json(['status' => 'success', 'message' => 'Attendance already recorded for today'], 200);
        }

        $limit = config('attendance.checkout_threshold', '09:00:00');
        $threshold = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $limit);
        $status = $checkInTime->gt($threshold) ? 'terlambat' : 'tepat_waktu';

        $attendance = DailyAttendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'check_in_time' => $checkInTime->format('H:i:s'),
            'attendance_type' => 'kantor',
            'status' => $status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance recorded successfully',
            'data' => [
                'name' => $user->name,
                'time' => $attendance->check_in_time,
                'status' => $attendance->status
            ]
        ], 201);
    }
}
