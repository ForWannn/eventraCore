<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Self-attendance via camera (AJAX).
     */
    public function store(Request $request, Event $event)
    {
        $user = Auth::user();

        // Must be ongoing
        if ($event->status !== 'ongoing') {
            return response()->json(['error' => 'Absensi hanya bisa dilakukan saat event berlangsung.'], 422);
        }

        // Must require attendance
        if (!$event->needs_attendance) {
            return response()->json(['error' => 'Event ini tidak memerlukan absensi.'], 422);
        }

        // Must be assigned
        if (!$this->isAssigned($user, $event)) {
            return response()->json(['error' => 'Anda tidak ditugaskan pada event ini.'], 403);
        }

        // Already attended?
        if ($event->attendances()->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Anda sudah melakukan absensi sebelumnya.'], 422);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $dir = public_path('assets/attendances');
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $filename  = 'att_' . $event->id . '_' . $user->id . '_' . time() . '.jpg';
            $request->file('photo')->move($dir, $filename);
            $photoPath = 'assets/attendances/' . $filename;
        }

        Attendance::create([
            'event_id'    => $event->id,
            'user_id'     => $user->id,
            'photo_path'  => $photoPath,
            'attended_at' => now(),
            'method'      => 'camera',
            'recorded_by' => $user->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Absensi berhasil dicatat!']);
    }

    /**
     * Manual attendance by PIC (AJAX).
     */
    public function storeManual(Request $request, Event $event)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'notes'   => 'nullable|string|max:255',
        ]);

        $pic = $event->participants->where('pivot.is_pic', true)->first();

        // Only PIC can do manual attendance
        if (!$pic || $pic->id !== Auth::id()) {
            return response()->json(['error' => 'Hanya PIC yang dapat mengabsen karyawan secara manual.'], 403);
        }

        Attendance::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $request->user_id],
            [
                'attended_at' => now(),
                'method'      => 'manual',
                'notes'       => $request->notes,
                'recorded_by' => Auth::id(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Absensi manual berhasil dicatat.']);
    }

    /**
     * Check if a user is assigned to an event (as PIC or position member).
     */
    private function isAssigned($user, Event $event): bool
    {
        if ($event->participants->contains('id', $user->id)) return true;

        return EventPosition::where('event_id', $event->id)
            ->whereHas('members', fn($q) => $q->where('user_id', $user->id))
            ->exists();
    }
}
