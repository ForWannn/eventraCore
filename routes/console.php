<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\User;
use App\Models\WeeklyReport;
use App\Models\DailyAttendance;
use App\Models\LeaveRequest;
use App\Models\WorkCalendar;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schedule;

Artisan::command('weekly:sunday-plan-reminder', function () {
    $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
    
    $users = User::whereDoesntHave('roles', function($q) {
        $q->whereIn('name', ['Director', 'GM', 'Admin', 'Superadmin']);
    })->get();

    foreach ($users as $user) {
        if (empty($user->phone)) continue;

        $report = WeeklyReport::where('user_id', $user->id)
            ->where('week_start_date', $nextMonday)
            ->first();

        if (!$report || !$report->plan_submitted_at) {
            $url = url('/weekly-reports');
            $message = "🗓️ [PENGINGAT WEEKLY PLAN]\n\n"
                     . "Selamat pagi, {$user->name}! ☀️\n"
                     . "Kamu belum membuat Weekly Plan untuk minggu ini.\n\n"
                     . "Yuk, segera buat dan susun rencanamu sekarang untuk menghindari keterlambatan batas waktu pengumpulan.\n"
                     . "🔗 {$url}";
            FonnteService::send($user->phone, $message);
        }
    }
})->purpose('Send Sunday reminder for Weekly Plan');

Artisan::command('weekly:monday-plan-warning', function () {
    $thisMonday = Carbon::today()->toDateString();
    
    $users = User::whereDoesntHave('roles', function($q) {
        $q->whereIn('name', ['Director', 'GM', 'Admin', 'Superadmin']);
    })->get();

    foreach ($users as $user) {
        if (empty($user->phone)) continue;

        $report = WeeklyReport::where('user_id', $user->id)
            ->where('week_start_date', $thisMonday)
            ->first();

        if (!$report || !$report->plan_submitted_at) {
            $url = url('/weekly-reports');
            $message = "⏳ [PERINGATAN SUBMIT DRAFT]\n\n"
                     . "Pagi {$user->name},\n"
                     . "Sistem mencatat kamu belum mengirimkan (submit) draf Weekly Plan minggu ini.\n\n"
                     . "Silakan kirim draf tersebut sebelum jam 09:00 WIB hari ini agar pekerjaanmu bisa segera di-review.\n"
                     . "🔗 {$url}";
            FonnteService::send($user->phone, $message);
        }
    }
})->purpose('Send Monday warning for Weekly Plan');

Artisan::command('weekly:friday-report-reminder', function () {
    $thisMonday = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
    
    $users = User::whereDoesntHave('roles', function($q) {
        $q->whereIn('name', ['Director', 'GM', 'Admin', 'Superadmin']);
    })->get();

    foreach ($users as $user) {
        if (empty($user->phone)) continue;

        $report = WeeklyReport::where('user_id', $user->id)
            ->where('week_start_date', $thisMonday)
            ->first();

        if (!$report || $report->status !== 'submitted') {
            $url = url('/weekly-reports');
            $message = "📝 [PENGINGAT WEEKLY REPORT]\n\n"
                     . "Halo {$user->name},\n"
                     . "Waktu akhir pekan hampir tiba! Namun, kamu belum mengirimkan Weekly Report untuk minggu ini.\n\n"
                     . "Mohon segera lengkapi dan kirimkan laporanmu sebelum jam 23:59 WIB malam ini ya.\n"
                     . "🔗 {$url}\n\n"
                     . "Selamat beristirahat setelahnya! 🌙";
            FonnteService::send($user->phone, $message);
        }
    }
})->purpose('Send Friday reminder for Weekly Report');

Artisan::command('attendance:daily-reminder', function () {
    $today = Carbon::today();
    $dateStr = $today->toDateString();
    
    $isWorkDay = WorkCalendar::isWorkingDay($dateStr);
    
    if (!$isWorkDay) return;

    $users = User::whereDoesntHave('roles', function($q) {
        $q->whereIn('name', ['Intern', 'Admin', 'Superadmin']);
    })->get();

    foreach ($users as $user) {
        if (empty($user->phone)) continue;

        $attendance = DailyAttendance::where('user_id', $user->id)
            ->where('date', $dateStr)
            ->whereNotNull('check_in_time')
            ->first();

        if (!$attendance) {
            $hasApprovedLeave = LeaveRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $dateStr)
                ->where('end_date', '>=', $dateStr)
                ->exists();

            if (!$hasApprovedLeave) {
                $message = "⏰ [PENGINGAT PRESENSI]\n\n"
                         . "Selamat Pagi, {$user->name}! ☀️\n"
                         . "Waktu saat ini menunjukkan pukul 08:50 WIB.\n\n"
                         . "Jangan lupa untuk melakukan absen masuk (check-in) sebelum pukul 09:00 WIB di mesin atau aplikasi. Semangat menjalani hari!";
                FonnteService::send($user->phone, $message);
            }
        }
    }
})->purpose('Send daily attendance check-in reminder');

Artisan::command('attendance:daily-warning', function () {
    $today = Carbon::today();
    $dateStr = $today->toDateString();
    
    $isWorkDay = WorkCalendar::isWorkingDay($dateStr);
    
    if (!$isWorkDay) return;

    $users = User::whereDoesntHave('roles', function($q) {
        $q->whereIn('name', ['Intern', 'Admin', 'Superadmin']);
    })->get();

    foreach ($users as $user) {
        if (empty($user->phone)) continue;

        $attendance = DailyAttendance::where('user_id', $user->id)
            ->where('date', $dateStr)
            ->whereNotNull('check_in_time')
            ->first();

        if (!$attendance) {
            $hasApprovedLeave = LeaveRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $dateStr)
                ->where('end_date', '>=', $dateStr)
                ->exists();

            if (!$hasApprovedLeave) {
                $url = url('/leave-requests');
                $message = "🚨 [PERINGATAN KETERLAMBATAN]\n\n"
                         . "Halo {$user->name},\n"
                         . "Saat ini sudah pukul 09:10 WIB dan sistem belum mencatat kehadiranmu hari ini.\n\n"
                         . "Jika kamu sudah berada di lokasi, mohon segera lakukan absensi. Jika berhalangan hadir, harap ajukan izin di sistem:\n"
                         . "🔗 {$url}";
                FonnteService::send($user->phone, $message);
            }
        }
    }
})->purpose('Send daily attendance check-in warning');

// Scheduling
Schedule::command('weekly:sunday-plan-reminder')->sundays()->at('07:00');
Schedule::command('weekly:monday-plan-warning')->mondays()->at('07:00');
Schedule::command('weekly:friday-report-reminder')->fridays()->at('21:00');
Schedule::command('attendance:daily-reminder')->weekdays()->at('08:50');
Schedule::command('attendance:daily-warning')->weekdays()->at('09:10');
