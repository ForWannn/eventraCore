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
        $data = $request->all();

        // Check if payload is wrapped inside event_log (which can be a JSON string)
        if ($request->has('event_log')) {
            $eventLog = $request->input('event_log');
            if (is_string($eventLog)) {
                $decoded = json_decode($eventLog, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data = $decoded;
                }
            } elseif (is_array($eventLog)) {
                $data = $eventLog;
            }
        }

        // Extract employeeNo
        $employeeNo = $data['employeeNo'] ?? 
                      $data['EmployeeNo'] ?? 
                      ($data['AccessControllerEvent']['employeeNoString'] ?? null) ?? 
                      ($data['AccessControllerEvent']['employeeNo'] ?? null);

        // Extract timeString
        $timeString = $data['time'] ?? 
                      $data['dateTime'] ?? 
                      ($data['AccessControllerEvent']['dateTime'] ?? null);

        if (!$employeeNo) {
            return response()->json([
                'status' => 'success', 
                'message' => 'Event acknowledged but no employeeNo found (heartbeat/system event)',
                'statusCode' => 1,
                'statusString' => 'OK',
                'errorCode' => 0,
                'errorMsg' => 'OK'
            ], 200);
        }

        $user = User::where('employee_id', $employeeNo)
                    ->orWhere('nik', $employeeNo)
                    ->first();

        if (!$user) {
            Log::warning("Hikvision Push: User not found with ID: " . $employeeNo);
            return response()->json([
                'status' => 'success', 
                'message' => 'Event acknowledged but User not found with ID: ' . $employeeNo,
                'statusCode' => 1,
                'statusString' => 'OK',
                'errorCode' => 0,
                'errorMsg' => 'OK'
            ], 200);
        }

        $checkInTime = $timeString ? Carbon::parse($timeString) : Carbon::now();
        $date = $checkInTime->format('Y-m-d');

        // Only accept attendance from today onwards
        $today = Carbon::today()->format('Y-m-d');
        if ($date < $today) {
            Log::warning("Hikvision Push: Event date {$date} is in the past (Today is {$today}). ID: {$employeeNo}");
            return response()->json([
                'status' => 'success', 
                'message' => 'Event acknowledged but skipped because it is in the past',
                'statusCode' => 1,
                'statusString' => 'OK',
                'errorCode' => 0,
                'errorMsg' => 'OK'
            ], 200);
        }

        $exists = DailyAttendance::where('user_id', $user->id)
                                  ->where('date', $date)
                                  ->exists();

        if ($exists) {
            Log::info("Hikvision Swipe Acknowledged (Already Recorded Today) - Name: {$user->name}, ID: {$employeeNo}, Time: " . $checkInTime->format('H:i:s'));
            return response()->json([
                'status' => 'success', 
                'message' => 'Attendance already recorded for today',
                'statusCode' => 1,
                'statusString' => 'OK',
                'errorCode' => 0,
                'errorMsg' => 'OK'
            ], 200);
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

        // Only log when attendance is successfully recorded
        Log::info("Hikvision Attendance Recorded - Name: {$user->name}, ID: {$employeeNo}, Time: {$attendance->check_in_time}, Status: {$attendance->status}");

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance recorded successfully',
            'statusCode' => 1,
            'statusString' => 'OK',
            'errorCode' => 0,
            'errorMsg' => 'OK',
            'data' => [
                'name' => $user->name,
                'time' => $attendance->check_in_time,
                'status' => $attendance->status
            ]
        ], 200);
    }
}
