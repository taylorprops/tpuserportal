<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Marketing\Schedule\ScheduleController;


Route::middleware(['auth', 'web']) -> group(function () {
    Route::get('/marketing/schedule_review', [ScheduleController::class, 'schedule_review']) -> middleware(['owner']);
    Route::get('/marketing/get_schedule_review', [ScheduleController::class, 'get_schedule_review']) -> middleware(['owner']);
});
