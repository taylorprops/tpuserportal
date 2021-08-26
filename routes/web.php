<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;


Route::get('/', function () {
    return view('/auth/login');
});


Route::get('/dashboard', [DashboardController::class, 'dashboard']);

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/agents.php';
require __DIR__.'/loan_officers.php';
