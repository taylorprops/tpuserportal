<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FilepondUploadController;
use App\Http\Controllers\Dashboard\DashboardController;


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
/***** tinymce file upload ******/
Route::post('/text_editor/file_upload', [FileUploadController::class, 'file_upload']);
