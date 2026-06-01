<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeeklyReportController;
use App\Http\Controllers\EventTaskController;
use App\Http\Controllers\DailyAttendanceController;
use App\Http\Controllers\WorkCalendarController;
use App\Http\Controllers\LeaveRequestController;
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'processLogin']);

use App\Http\Controllers\PasswordResetController;
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Daily Attendance (Web Geotagging)
    Route::post('/daily-attendance/store-luar', [DailyAttendanceController::class, 'storeLuar'])->name('attendance.storeLuar');

    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/settings/calendar', [WorkCalendarController::class, 'index'])->name('settings.calendar');
        Route::post('/settings/calendar', [WorkCalendarController::class, 'update'])->name('settings.calendar.update');
    });

    Route::middleware(['role:CEO|GM|Admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password.update');
    Route::resource('divisions', DivisionController::class);
    Route::get('/attendance-history', [DailyAttendanceController::class, 'myHistory'])->name('attendance.history');

    // Leave Requests (Izin & Cuti)
    Route::get('/leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');

    Route::middleware(['role:CEO|GM'])->group(function () {
        Route::get('/leave-approvals', [LeaveRequestController::class, 'approvals'])->name('leave-approvals.index');
        Route::post('/leave-approvals/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-approvals.approve');
        Route::post('/leave-approvals/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-approvals.reject');
    });

    Route::middleware(['role:CEO'])->group(function () {
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    });

    Route::resource('events', EventController::class)->only(['index', 'show']);

    Route::post('/events/{event}/attend',        [AttendanceController::class, 'store'])->name('attendances.store');
    Route::post('/events/{event}/attend-manual', [AttendanceController::class, 'storeManual'])->name('attendances.store.manual');
    Route::get('/weekly-report', [WeeklyReportController::class, 'index'])->name('weekly.index');
    Route::post('/weekly-report/{report}/plan', [WeeklyReportController::class, 'updatePlan'])->name('weekly.plan');
    Route::post('/weekly-report/{report}/final', [WeeklyReportController::class, 'submitFinal'])->name('weekly.final');
    Route::post('/weekly-report/autosave', [WeeklyReportController::class, 'autoSaveLog'])->name('weekly.autosave');
    Route::get('/weekly-history', [WeeklyReportController::class, 'history'])->name('weekly.history');
    Route::get('/weekly-recap/user/{user}/{week}', [WeeklyReportController::class, 'showUserReport'])->name('weekly.show_user');
    Route::middleware(['role:CEO|GM'])->group(function () {
        Route::get('/daily-attendance-recap', [DailyAttendanceController::class, 'recap'])->name('attendance.recap');
        Route::get('/daily-attendance-recap/export', [DailyAttendanceController::class, 'exportRecap'])->name('attendance.recap.export');
        Route::get('/weekly-recap', [WeeklyReportController::class, 'recap'])->name('weekly.recap');
        Route::get('/weekly-recap/export', [WeeklyReportController::class, 'exportRecap'])->name('weekly.recap.export');
    });

    Route::post('/events/{event}/tasks', [EventTaskController::class, 'store'])->name('events.tasks.store');
    Route::post('/event-tasks/{task}/toggle', [EventTaskController::class, 'toggleComplete'])->name('events.tasks.toggle');
    Route::delete('/event-tasks/{task}', [EventTaskController::class, 'destroy'])->name('events.tasks.destroy');
});
