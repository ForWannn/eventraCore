<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeeklyReportController;
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'processLogin']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('divisions', DivisionController::class);

    Route::resource('events', EventController::class);

    Route::post('/events/{event}/attend',        [AttendanceController::class, 'store'])->name('attendances.store');
    Route::post('/events/{event}/attend-manual', [AttendanceController::class, 'storeManual'])->name('attendances.store.manual');
    Route::get('/weekly-report', [WeeklyReportController::class, 'index'])->name('weekly.index');
    Route::post('/weekly-report/{report}/plan', [WeeklyReportController::class, 'updatePlan'])->name('weekly.plan');
    Route::post('/weekly-report/{report}/final', [WeeklyReportController::class, 'submitFinal'])->name('weekly.final');
    Route::post('/weekly-report/autosave', [WeeklyReportController::class, 'autoSaveLog'])->name('weekly.autosave');
    Route::middleware(['role:CEO|GM'])->group(function () {
        Route::get('/weekly-recap', [WeeklyReportController::class, 'recap'])->name('weekly.recap');
        Route::get('/weekly-recap/user/{user}/{week}', [WeeklyReportController::class, 'showUserReport'])->name('weekly.show_user');
    });
});
