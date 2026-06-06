<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DailyAttendance;
use Carbon\Carbon;

class DailyOfficeAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        $startDate = Carbon::create(2026, 1, 1);
        $endDate = Carbon::create(2026, 6, 30);
        
        $currentDate = $startDate->copy();
        
        $totalRecords = 0;
        
        while ($currentDate->lte($endDate)) {
            // Kita lewati hari Minggu agar lebih realistis (libur kerja)
            if ($currentDate->isSunday()) {
                $currentDate->addDay();
                continue;
            }
            
            foreach ($users as $user) {
                // Tentukan jam kedatangan
                // Asumsi jam masuk normal adalah 08:30 - 09:00
                // Terlambat adalah jika di atas jam 09:00
                
                $isLate = rand(1, 100) <= 25; // 25% kemungkinan terlambat
                
                if ($isLate) {
                    $minutes = rand(1, 60); // 09:01 - 10:00
                    $checkInTime = Carbon::createFromTime(9, 0, 0)->addMinutes($minutes)->format('H:i:s');
                    $status = 'terlambat';
                } else {
                    $minutes = rand(0, 30); // 08:30 - 09:00
                    $checkInTime = Carbon::createFromTime(8, 30, 0)->addMinutes($minutes)->format('H:i:s');
                    $status = 'tepat_waktu';
                }
                
                // Pastikan belum ada absensi pada hari ini untuk user ini agar tidak bentrok dengan absensi event
                $exists = DailyAttendance::where('user_id', $user->id)
                                         ->where('date', $currentDate->format('Y-m-d'))
                                         ->exists();
                
                if (!$exists) {
                    DailyAttendance::create([
                        'user_id' => $user->id,
                        'date' => $currentDate->format('Y-m-d'),
                        'check_in_time' => $checkInTime,
                        'attendance_type' => 'kantor',
                        'photo_path' => 'assets/attendances/dummy.jpg',
                        'latitude' => '-6.2088',
                        'longitude' => '106.8456',
                        'status' => $status,
                    ]);
                    $totalRecords++;
                }
            }
            
            $currentDate->addDay();
        }
        
        $this->command->info("Berhasil menambahkan {$totalRecords} data absensi harian kantor untuk Jan - Jun 2026!");
    }
}
