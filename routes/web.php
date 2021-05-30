<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Resources\ResourcesController;
use App\Http\Controllers\DocManagement\Admin\FormsController;
use App\Http\Controllers\DocManagement\Admin\FormsFieldsController;

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

    // Forms
    Route::get('/doc_management/admin/forms/forms', [FormsController::class, 'forms']) -> middleware(['admin']);
    Route::get('/doc_management/admin/forms/get_form_groups', [FormsController::class, 'get_form_groups']) -> middleware(['admin']);
    Route::get('/doc_management/admin/forms/get_forms', [FormsController::class, 'get_forms']) -> middleware(['admin']);
    Route::get('/doc_management/admin/forms/search_forms', [FormsController::class, 'search_forms']) -> middleware(['admin']);
    Route::post('/doc_management/admin/forms/get_upload_text', [FormsController::class, 'get_upload_text']) -> middleware(['admin']);
    Route::post('/doc_management/admin/save_form', [FormsController::class, 'save_form']) -> middleware(['admin']);
    // Form Fields
    Route::get('/doc_management/admin/forms/form_fields/{form_id}', [FormsFieldsController::class, 'form_fields']) -> middleware(['admin']);

    // Form Elements
    Route::get('/resources/design/form_elements', [ResourcesController::class, 'form_elements']) -> middleware(['admin']);

});


require __DIR__.'/auth.php';
