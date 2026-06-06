<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventPosition;
use App\Models\EventTask;
use App\Models\User;
use App\Models\DailyAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExecutiveDashboardSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch users to act as crew
        $users = User::whereDoesntHave('roles', function($q) {
            $q->whereIn('name', ['CEO', 'Superadmin']); // Don't assign CEO to regular tasks
        })->get();

        if ($users->count() < 3) {
            $this->command->warn('Not enough users to seed events. Please run UserSeeder first.');
            return;
        }

        $eventNames = [
            'Annual Corporate Gathering', 'Launching Product Brand X', 'Wedding Annisa & Faisal',
            'Gala Dinner BUMN', 'Eventra Exhibition 2026', 'Seminar Nasional Teknologi',
            'Konser Musik Indie', 'Workshop Digital Marketing', 'Pameran Otomotif Tahunan',
            'Bazar Kuliner Nusantara', 'MICE Convention 2025', 'Acara Amal Yayasan',
            'Festival Film Pendek', 'Peluncuran Aplikasi Mobile', 'Rapat Kerja Nasional'
        ];

        // Ensure we clear previous data to avoid duplicates if run multiple times
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Event::truncate();
        EventTask::truncate();
        EventPosition::truncate();
        DB::table('event_participants')->truncate();
        DB::table('event_position_members')->truncate();
        DailyAttendance::truncate();
        DB::table('attendances')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed 2025 (10-20 events per month, all completed)
        for ($month = 1; $month <= 12; $month++) {
            $numEvents = rand(10, 20);
            for ($i = 0; $i < $numEvents; $i++) {
                $day = rand(1, 28);
                $date = Carbon::create(2025, $month, $day);
                $this->createEvent($date, $users, $eventNames, true);
            }
        }

        // Seed 2026 (10-20 completed events per month for Jan-May)
        for ($month = 1; $month <= 5; $month++) {
            $numEvents = rand(10, 20);
            for ($i = 0; $i < $numEvents; $i++) {
                $day = rand(1, 28);
                $date = Carbon::create(2026, $month, $day);
                $this->createEvent($date, $users, $eventNames, true);
            }
        }

        // Seed 2026 (10-20 upcoming/ongoing events per month for Jun-Dec)
        for ($month = 6; $month <= 12; $month++) {
            $numEvents = rand(10, 20);
            for ($i = 0; $i < $numEvents; $i++) {
                $day = rand(1, 28);
                $date = Carbon::create(2026, $month, $day);
                $this->createEvent($date, $users, $eventNames, false);
            }
        }

        $this->command->info('Executive Dashboard Data Seeded Successfully!');
    }

    private function createEvent($date, $users, $eventNames, $isCompleted)
    {
        $pic = $users->random();
        
        $eventName = $eventNames[array_rand($eventNames)] . ' - ' . $date->format('M Y');
        
        // Let's decide if this event has bad crew discipline (for domino effect)
        // Roughly 1 in 5 events has bad discipline
        $hasBadDiscipline = rand(1, 5) === 1;

        $event = Event::create([
            'name' => $eventName,
            'description' => 'Dummy event for dashboard.',
            'location' => 'Jakarta Selatan',
            'category' => 'Corporate',
            'event_dates' => [$date->format('Y-m-d')],
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'attendance_start' => '07:30:00',
            'attendance_end' => '08:30:00',
            'needs_attendance' => true,
        ]);

        // Attach PIC
        $event->participants()->sync([
            $pic->id => ['is_pic' => true],
        ]);

        // Assign some positions
        $posMembers = $users->where('id', '!=', $pic->id)->random(rand(2, 4));
        $position = EventPosition::create([
            'event_id' => $event->id,
            'name' => 'Crew Operasional',
        ]);
        
        $syncData = [];
        foreach ($posMembers as $member) {
            $syncData[$member->id] = [
                'work_dates' => json_encode([$date->format('Y-m-d')]),
                'is_loading' => false,
                'is_unloading' => false,
            ];
        }
        $position->members()->sync($syncData);

        // Seed Tasks
        // If event is completed, set completion rate based on discipline
        $taskCount = rand(5, 8);
        for ($j = 0; $j < $taskCount; $j++) {
            $isTaskCompleted = false;
            
            if ($isCompleted) {
                if ($hasBadDiscipline) {
                    // Task completion around 50-70%
                    $isTaskCompleted = rand(1, 100) <= 60;
                } else {
                    // Task completion around 90-100%
                    $isTaskCompleted = rand(1, 100) <= 95;
                }
            }

            EventTask::create([
                'event_id' => $event->id,
                'task_name' => 'Task ' . ($j + 1) . ' for ' . $event->name,
                'category' => ['pre', 'dday', 'post'][rand(0, 2)],
                'type' => 'official',
                'assigned_to' => $posMembers->random()->id,
                'created_by' => $pic->id,
                'is_completed' => $isTaskCompleted,
            ]);
        }

        // Seed Attendances for Crew
        if ($isCompleted) {
            $allCrew = collect([$pic])->merge($posMembers);
            foreach ($allCrew as $crew) {
                $checkInDate = $date->copy();
                
                // Determine check in time
                // Normal: 07:15 - 07:30
                // Late: 07:31 - 07:59
                // Bad Discipline (Very Late): 08:05 - 08:45
                
                if ($hasBadDiscipline) {
                    // Most crew are late on bad discipline events
                    $minutes = rand(35, 75); // 07:50 to 08:30 (call time is 07:30, so > 20 mins late)
                    $status = 'terlambat';
                } else {
                    if (rand(1, 10) <= 8) {
                        $minutes = rand(0, 15); // 07:15 to 07:30
                        $status = 'tepat_waktu';
                    } else {
                        $minutes = rand(16, 29); // 07:31 to 07:44 (Late but not excessively)
                        $status = 'terlambat';
                    }
                }

                $checkInTime = Carbon::createFromTime(7, 15, 0)->addMinutes($minutes)->format('H:i:s');

                DailyAttendance::create([
                    'user_id' => $crew->id,
                    'date' => $checkInDate->format('Y-m-d'),
                    'check_in_time' => $checkInTime,
                    'attendance_type' => 'luar',
                    'photo_path' => 'assets/attendances/dummy.jpg',
                    'latitude' => '-6.2088',
                    'longitude' => '106.8456',
                    'status' => $status,
                ]);
            }
        }
    }
}
