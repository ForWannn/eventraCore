<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventPosition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $month = request('month', date('m'));
        $year = request('year', date('Y'));
        
        $searchPattern = '%"' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-%';

        if ($user->hasRole(['CEO', 'GM'])) {
            $events = Event::with(['participants', 'positions'])
                ->where('event_dates', 'like', $searchPattern)
                ->orderBy('id', 'desc')->get();
        } else {
            $assignedEventIds = collect();

            $picEventIds = $user->events()->pluck('events.id');
            $assignedEventIds = $assignedEventIds->merge($picEventIds);

            $positionEventIds = EventPosition::whereHas('members', fn($q) => $q->where('user_id', $user->id))
                ->pluck('event_id');
            $assignedEventIds = $assignedEventIds->merge($positionEventIds)->unique();

            $events = Event::with(['participants', 'positions'])
                ->whereIn('id', $assignedEventIds)
                ->where('event_dates', 'like', $searchPattern)
                ->orderBy('id', 'desc')->get();
        }

        return view('events.index', compact('events', 'month', 'year'));
    }

    public function create()
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['CEO', 'Direktur']);
        })->with(['roles', 'division'])->orderBy('name')->get();

        $usersJson = $users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'division' => optional($u->division)->name ?? '-',
            'photo' => $u->photo_url,
        ]);

        return view('events.create', compact('users', 'usersJson'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_dates' => 'required|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'attendance_start' => 'nullable|date_format:H:i',
            'attendance_end' => 'nullable|date_format:H:i',
            'pic_id' => 'required|exists:users,id',
            'pic_fee' => 'nullable|numeric|min:0',
            'loading_fee' => 'nullable|numeric|min:0',
            'unloading_fee' => 'nullable|numeric|min:0',
            'positions' => 'nullable|array',
            'positions.*.name' => 'required_with:positions|string|max:100',
            'positions.*.fee' => 'nullable|numeric|min:0',
            'positions.*.members' => 'nullable|array',
            'positions.*.member_dates' => 'nullable|array',
        ]);

        $eventDates = explode(', ', $request->event_dates);

        $event = Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'event_dates' => $eventDates,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'attendance_start' => $request->attendance_start,
            'attendance_end' => $request->attendance_end,
            'pic_fee' => $request->pic_fee ?? 0,
            'loading_fee' => $request->loading_fee ?? 0,
            'unloading_fee' => $request->unloading_fee ?? 0,
        ]);

        // Attach PIC
        $event->participants()->sync([
            $request->pic_id => ['is_pic' => true],
        ]);

        // Auto-assign PIC Event role
        $picUser = User::find($request->pic_id);
        if ($picUser && !$picUser->hasRole('PIC Event')) {
            $picUser->assignRole('PIC Event');
        }

        // Create positions with member date assignments
        if ($request->has('positions')) {
            foreach ($request->positions as $posData) {
                if (empty($posData['name']))
                    continue;

                $position = EventPosition::create([
                    'event_id' => $event->id,
                    'name' => $posData['name'],
                    'fee' => $posData['fee'] ?? 0,
                ]);

                if (!empty($posData['members'])) {
                    $syncData = [];
                    foreach ($posData['members'] as $userId) {
                        $dateData = $posData['member_dates'][$userId] ?? [];
                        $workDates = !empty($dateData['work_dates']) ? explode(', ', $dateData['work_dates']) : [];
                        $syncData[$userId] = [
                            'work_dates' => json_encode($workDates),
                            'is_loading' => isset($posData['member_loading'][$userId]),
                            'is_unloading' => isset($posData['member_unloading'][$userId]),
                        ];
                    }
                    $position->members()->sync($syncData);
                }
            }
        }

        return redirect()->route('events.index')->with('success', "Event \"{$event->name}\" berhasil dibuat.");
    }

    public function show(Event $event)
    {
        $event->load([
            'participants.division',
            'positions.members.division',
            'attendances.user',
            'attendances.recorder',
        ]);

        $authUser = Auth::user();
        $pic = $event->participants->where('pivot.is_pic', true)->first();
        $isPic = $pic && $pic->id === $authUser->id;
        $myAttendance = $event->attendances->where('user_id', $authUser->id)
            ->filter(fn($att) => $att->attended_at->isToday())
            ->first();
        $isLeader = $authUser->hasAnyRole(['CEO', 'GM']);

        // All users assigned to this event (PIC + position members)
        $assignedUsers = collect();
        if ($pic)
            $assignedUsers->push($pic);
        foreach ($event->positions as $pos) {
            foreach ($pos->members as $m) {
                if (!$assignedUsers->contains('id', $m->id))
                    $assignedUsers->push($m);
            }
        }

        $isAssigned = $assignedUsers->contains('id', $authUser->id);

        $now = now();
        $attendanceOpen = true;
        if ($event->attendance_start && $event->attendance_end) {
            $startTime = \Carbon\Carbon::parse($event->attendance_start);
            $endTime = \Carbon\Carbon::parse($event->attendance_end);
            $currentTime = \Carbon\Carbon::createFromTime($now->hour, $now->minute, 0);

            if ($currentTime < $startTime || $currentTime > $endTime) {
                $attendanceOpen = false;
            }
        }

        return view('events.show', compact(
            'event',
            'pic',
            'isPic',
            'myAttendance',
            'isLeader',
            'assignedUsers',
            'isAssigned',
            'attendanceOpen'
        ));
    }

    public function destroy(Event $event)
    {
        // Revoke PIC Event role from PIC if no other events
        $pic = $event->participants->where('pivot.is_pic', true)->first();
        if ($pic) {
            $otherPic = \DB::table('event_participants')
                ->where('user_id', $pic->id)
                ->where('is_pic', true)
                ->where('event_id', '!=', $event->id)
                ->count();
            if ($otherPic === 0) {
                $pic->removeRole('PIC Event');
            }
        }

        $name = $event->name;
        $event->delete(); // cascades to positions, members, attendances

        return redirect()->route('events.index')->with('success', "Event \"{$name}\" berhasil dihapus.");
    }
}
