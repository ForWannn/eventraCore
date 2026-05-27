<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AttendanceApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Hikvision Biometric Push Endpoint
Route::post('/attendance/hikvision-push', [AttendanceApiController::class, 'hikvisionPush']);
