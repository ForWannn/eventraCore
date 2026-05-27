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

    Route::resource('users', UserController::class);
    Route::resource('divisions', DivisionController::class);

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
    Route::middleware(['role:CEO|GM'])->group(function () {
        Route::get('/daily-attendance-recap', [DailyAttendanceController::class, 'recap'])->name('attendance.recap');
        Route::get('/weekly-recap', [WeeklyReportController::class, 'recap'])->name('weekly.recap');
        Route::get('/weekly-history', [WeeklyReportController::class, 'history'])->name('weekly.history');
        Route::get('/weekly-recap/user/{user}/{week}', [WeeklyReportController::class, 'showUserReport'])->name('weekly.show_user');
    });

    Route::post('/events/{event}/tasks', [EventTaskController::class, 'store'])->name('events.tasks.store');
    Route::post('/event-tasks/{task}/toggle', [EventTaskController::class, 'toggleComplete'])->name('events.tasks.toggle');
    Route::delete('/event-tasks/{task}', [EventTaskController::class, 'destroy'])->name('events.tasks.destroy');
});
