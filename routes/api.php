<?php

use App\Models\Attendance\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/attendance', function (Request $request) {
    Attendance::handleAttendanceFromDevice($request);
})->middleware('auth-attendance-device');
