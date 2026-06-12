<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventPosition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        $activeEvents = Event::with(['participants', 'positions.members'])->get()->filter(function ($event) {
            return $event->status !== 'completed';
        });

        $usersSchedules = $users->map(function ($u) use ($activeEvents) {
            $uEvents = $activeEvents->filter(function ($event) use ($u) {
                $isPic = $event->participants->contains('id', $u->id);
                $isPosMember = $event->positions->some(fn($pos) => $pos->members->contains('id', $u->id));
                return $isPic || $isPosMember;
            });

            // Sort events by starting date
            $sortedEvents = $uEvents->sortBy(function ($event) {
                $dates = $event->event_dates ?? [];
                if (empty($dates)) return now()->addYears(100);
                sort($dates);
                $firstDate = \Carbon\Carbon::parse($dates[0])->startOfDay();
                if ($event->start_time) {
                    $firstDate->setTimeFromTimeString((string) $event->start_time);
                }
                return $firstDate;
            });

            $nextEvent = $sortedEvents->first();
            $nextEventDetails = null;

            if ($nextEvent) {
                $dates = $nextEvent->event_dates ?? [];
                sort($dates);
                $formattedDate = '';
                if (!empty($dates)) {
                    $first = \Carbon\Carbon::parse($dates[0])->translatedFormat('d M Y');
                    if (count($dates) > 1) {
                        $last = \Carbon\Carbon::parse(end($dates))->translatedFormat('d M Y');
                        $formattedDate = $first . ' - ' . $last;
                    } else {
                        $formattedDate = $first;
                    }
                }
                
                $timeStr = '';
                if ($nextEvent->start_time && $nextEvent->end_time) {
                    $timeStr = substr($nextEvent->start_time, 0, 5) . ' - ' . substr($nextEvent->end_time, 0, 5);
                } elseif ($nextEvent->start_time) {
                    $timeStr = substr($nextEvent->start_time, 0, 5);
                }

                $nextEventDetails = [
                    'id' => $nextEvent->id,
                    'name' => $nextEvent->name,
                    'date' => $formattedDate,
                    'time' => $timeStr,
                    'status' => $nextEvent->status,
                ];
            }

            return [
                'id' => $u->id,
                'name' => $u->name,
                'division' => optional($u->division)->name ?? '-',
                'photo' => $u->photo_url,
                'active_events_count' => $uEvents->count(),
                'next_event' => $nextEventDetails,
                'all_events' => $uEvents->map(fn($ev) => [
                    'id' => $ev->id,
                    'name' => $ev->name,
                    'status' => $ev->status,
                ])->values()->all(),
            ];
        });

        $usersJson = $users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'division' => optional($u->division)->name ?? '-',
            'photo' => $u->photo_url,
        ]);

        return view('events.create', compact('users', 'usersJson', 'usersSchedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'event_dates' => 'required|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'attendance_start' => 'nullable|date_format:H:i',
            'attendance_end' => 'nullable|date_format:H:i',
            'pic_id' => 'required|exists:users,id',
            'positions' => 'nullable|array',
            'positions.*.name' => 'required_with:positions|string|max:100',
            'positions.*.members' => 'nullable|array',
            'positions.*.member_dates' => 'nullable|array',
        ]);

        $eventDates = explode(', ', $request->event_dates);

        $event = Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'event_dates' => $eventDates,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'attendance_start' => $request->attendance_start,
            'attendance_end' => $request->attendance_end,
            'needs_attendance' => $request->has('needs_attendance'),
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

        // Get all crew assigned
        $crewIds = [];
        if ($request->pic_id) {
            $crewIds[] = $request->pic_id;
        }
        if ($request->has('positions')) {
            foreach ($request->positions as $posData) {
                if (!empty($posData['members'])) {
                    foreach ($posData['members'] as $userId) {
                        $crewIds[] = $userId;
                    }
                }
            }
        }
        $crewIds = array_unique($crewIds);
        
        $this->notifyAssignedCrew($event, $crewIds);

        return redirect()->route('events.index')->with('success', "Event \"{$event->name}\" berhasil dibuat.");
    }

    public function edit(Event $event)
    {
        // Ambil data user kecuali level top management untuk pilihan panitia/anggota
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['CEO', 'Direktur']);
        })->with(['roles', 'division'])->orderBy('name')->get();

        // Load relasi yang diperlukan agar bisa ditampilkan di form edit
        $event->load(['participants', 'positions.members']);

        $usersJson = $users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'division' => optional($u->division)->name ?? '-',
            'photo' => $u->photo_url,
        ]);

        return view('events.edit', compact('event', 'users', 'usersJson'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'event_dates' => 'required|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'attendance_start' => 'nullable|date_format:H:i',
            'attendance_end' => 'nullable|date_format:H:i',
            'pic_id' => 'required|exists:users,id',
            'positions' => 'nullable|array',
            'positions.*.id' => 'nullable|exists:event_positions,id',
            'positions.*.name' => 'required_with:positions|string|max:100',
            'positions.*.members' => 'nullable|array',
            'positions.*.member_dates' => 'nullable|array',
        ]);

        // Capture old crew IDs before update
        $oldCrewIds = [];
        $oldPic = $event->participants->where('pivot.is_pic', true)->first();
        if ($oldPic) {
            $oldCrewIds[] = $oldPic->id;
        }
        foreach ($event->positions as $pos) {
            foreach ($pos->members as $m) {
                $oldCrewIds[] = $m->id;
            }
        }
        $oldCrewIds = array_unique($oldCrewIds);

        $eventDates = explode(', ', $request->event_dates);

        // 1. Update Data Utama Event
        $event->update([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'event_dates' => $eventDates,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'attendance_start' => $request->attendance_start,
            'attendance_end' => $request->attendance_end,
            'needs_attendance' => $request->has('needs_attendance'),
        ]);

        // 2. Kelola Pergantian PIC
        $oldPic = $event->participants->where('pivot.is_pic', true)->first();
        
        $event->participants()->sync([
            $request->pic_id => ['is_pic' => true],
        ]);

        // Berikan role ke PIC baru jika belum punya
        $newPic = User::find($request->pic_id);
        if ($newPic && !$newPic->hasRole('PIC Event')) {
            $newPic->assignRole('PIC Event');
        }

        // Cabut role dari PIC lama jika dia sudah tidak memegang event apapun
        if ($oldPic && $oldPic->id != $request->pic_id) {
            $otherPicCount = \DB::table('event_participants')
                ->where('user_id', $oldPic->id)
                ->where('is_pic', true)
                ->where('event_id', '!=', $event->id)
                ->count();
            if ($otherPicCount === 0) {
                $oldPic->removeRole('PIC Event');
            }
        }

        // 3. Kelola Posisi dan Anggota (Disinilah histori pergantian disimpan)
        if ($request->has('positions')) {
            $providedPositionIds = [];

            foreach ($request->positions as $posData) {
                if (empty($posData['name'])) continue;

                // Update posisi jika ada ID, buat baru jika tidak ada (untuk penambahan divisi baru di tengah event)
                $position = EventPosition::updateOrCreate(
                    ['id' => $posData['id'] ?? null, 'event_id' => $event->id],
                    ['name' => $posData['name']]
                );
                
                $providedPositionIds[] = $position->id;

                if (!empty($posData['members'])) {
                    $syncData = [];
                    foreach ($posData['members'] as $userId) {
                        $dateData = $posData['member_dates'][$userId] ?? [];
                        
                        // LOGIKA HISTORI: 
                        // Jika User A diganti di hari ke-3, array work_dates yang dikirim dari form
                        // hanya berisi hari ke-1 dan ke-2. Data User A tetap ada di database.
                        // User B (pengganti) dikirim dengan array work_dates hari ke-3 dst.
                        $workDates = !empty($dateData['work_dates']) ? explode(', ', $dateData['work_dates']) : [];
                        
                        $syncData[$userId] = [
                            'work_dates' => json_encode($workDates),
                            'is_loading' => isset($posData['member_loading'][$userId]),
                            'is_unloading' => isset($posData['member_unloading'][$userId]),
                        ];
                    }
                    // Sync akan menimpa data pivot. Anggota yang tidak ada di form (di-remove total) akan terhapus dari posisi ini, 
                    // tapi anggota yang jadwalnya dikurangi tetap tersimpan sesuai work_dates terbarunya.
                    $position->members()->sync($syncData);
                } else {
                    // Jika posisi dikosongkan dari anggota
                    $position->members()->detach();
                }
            }

            // Hapus posisi yang benar-benar dihapus dari form UI
            EventPosition::where('event_id', $event->id)
                ->whereNotIn('id', $providedPositionIds)
                ->delete();
        } else {
            // Jika form tidak mengirim posisi sama sekali, hapus semua
            foreach($event->positions as $pos) {
                $pos->members()->detach();
                $pos->delete();
            }
        }

        // Get new crew assigned
        $newCrewIds = [];
        if ($request->pic_id) {
            $newCrewIds[] = $request->pic_id;
        }
        if ($request->has('positions')) {
            foreach ($request->positions as $posData) {
                if (!empty($posData['members'])) {
                    foreach ($posData['members'] as $userId) {
                        $newCrewIds[] = $userId;
                    }
                }
            }
        }
        $newCrewIds = array_unique($newCrewIds);

        // Find newly added crew
        $addedCrewIds = array_diff($newCrewIds, $oldCrewIds);

        if (!empty($addedCrewIds)) {
            $this->notifyAssignedCrew($event, $addedCrewIds);
        }

        return redirect()->route('events.index')->with('success', "Event \"{$event->name}\" berhasil diperbarui.");
    }

    public function show(Event $event)
    {
        $event->load([
            'participants.division',
            'positions.members.division',
        ]);

        $authUser = Auth::user();
        $pic = $event->participants->where('pivot.is_pic', true)->first();
        $isPic = $pic && $pic->id === $authUser->id;
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

        return view('events.show', compact(
            'event',
            'pic',
            'isPic',
            'isLeader',
            'assignedUsers',
            'isAssigned'
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

    /**
     * Send WhatsApp notification to crew members assigned to an event.
     */
    private function notifyAssignedCrew(Event $event, array $userIds)
    {
        // Load event relationships
        $event->load(['participants', 'positions.members']);

        // Find PIC
        $pic = $event->participants->where('pivot.is_pic', true)->first();

        // Format event dates
        $eventDates = $event->event_dates ?? [];
        sort($eventDates);
        $eventDatesStr = '';
        if (!empty($eventDates)) {
            $first = Carbon::parse($eventDates[0])->translatedFormat('d M Y');
            if (count($eventDates) > 1) {
                $last = Carbon::parse(end($eventDates))->translatedFormat('d M Y');
                $eventDatesStr = $first . ' - ' . $last;
            } else {
                $eventDatesStr = $first;
            }
        }

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if (!$user || empty($user->phone)) continue;

            $positions = [];
            $datesList = [];

            // Check if they are PIC
            if ($pic && $pic->id === $userId) {
                $positions[] = 'PIC Event';
                $datesList[] = $eventDatesStr;
            }

            // Check if they are in any position
            foreach ($event->positions as $position) {
                $member = $position->members->where('id', $userId)->first();
                if ($member) {
                    $positions[] = $position->name;
                    
                    // Decode work dates
                    $workDates = json_decode($member->pivot->work_dates, true) ?: [];
                    sort($workDates);
                    $workDatesStr = '';
                    if (!empty($workDates)) {
                        $first = Carbon::parse($workDates[0])->translatedFormat('d M Y');
                        if (count($workDates) > 1) {
                            $last = Carbon::parse(end($workDates))->translatedFormat('d M Y');
                            $workDatesStr = $first . ' - ' . $last;
                        } else {
                            $workDatesStr = $first;
                        }
                    } else {
                        $workDatesStr = $eventDatesStr;
                    }
                    $datesList[] = $workDatesStr;
                }
            }

            if (empty($positions)) continue;

            $posName = implode(', ', $positions);
            $posDates = implode(', ', array_unique($datesList));

            // 📢 [INFO PENUGASAN EVENT]
            $url = url('/events/' . $event->id);
            $message = "📢 [INFO PENUGASAN EVENT]\n\n"
                     . "Halo {$user->name},\n"
                     . "Kamu telah ditugaskan untuk bergabung dalam tim! Berikut rinciannya:\n\n"
                     . "Event: {$event->name}\n"
                     . "Posisi: {$posName}\n"
                     . "Tanggal: {$posDates}\n\n"
                     . "Silakan cek detail jadwal dan lokasi secara lengkap melalui tautan berikut:\n"
                     . "{$url}\n\n"
                     . "Semangat bertugas!";

            \App\Services\FonnteService::send($user->phone, $message);
        }
    }
}
