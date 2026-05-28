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

    public function myHistory()
    {
        $user = Auth::user();
        $attendances = DailyAttendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('daily-attendances.history', compact('attendances'));
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
