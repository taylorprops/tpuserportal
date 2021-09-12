<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilepondUploadController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Employees\EmployeesController;


Route::get('/', function () {
    return view('/auth/login');
});


Route::get('/dashboard', [DashboardController::class, 'dashboard']);

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/agents.php';
require __DIR__.'/loan_officers.php';


/***** file upload ******/
Route::post('/filepond_upload', [FilepondUploadController::class, 'upload']);

