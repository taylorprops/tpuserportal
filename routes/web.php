<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\DocManagement\Admin\FormsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'web']) -> group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard']);

    Route::get('/doc_management/admin/forms', [FormsController::class, 'forms']) -> middleware(['admin']);
    Route::post('/doc_management/admin/forms/get_upload_text', [FormsController::class, 'get_upload_text']) -> middleware(['admin']);

});


require __DIR__.'/auth.php';
