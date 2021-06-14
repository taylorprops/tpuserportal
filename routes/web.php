<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Employees\EmployeesController;
use App\Http\Controllers\Resources\ResourcesController;
use App\Http\Controllers\DocManagement\Admin\FormsController;
use App\Http\Controllers\DocManagement\Admin\FormsFieldsController;
use App\Http\Controllers\DocManagement\Transactions\TransactionsController;

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

    // %%%% Forms
    Route::get('/doc_management/admin/forms/forms', [FormsController::class, 'forms']) -> middleware(['admin']);
    Route::get('/doc_management/admin/forms/get_form_groups', [FormsController::class, 'get_form_groups']) -> middleware(['admin']);
    Route::get('/doc_management/admin/forms/get_forms', [FormsController::class, 'get_forms']) -> middleware(['admin']);
    Route::get('/doc_management/admin/forms/search_forms', [FormsController::class, 'search_forms']) -> middleware(['admin']);
    Route::post('/doc_management/admin/forms/get_upload_text', [FormsController::class, 'get_upload_text']) -> middleware(['admin']);
    Route::post('/doc_management/admin/save_form', [FormsController::class, 'save_form']) -> middleware(['admin']);
    // Form Fields
    Route::get('/doc_management/admin/forms/form_fields/{form_id}', [FormsFieldsController::class, 'form_fields']) -> middleware(['admin']);
    Route::get('/doc_management/admin/forms/get_fields', [FormsFieldsController::class, 'get_fields']) -> middleware(['admin']);
    Route::post('/doc_management/admin/forms/save_fields', [FormsFieldsController::class, 'save_fields']) -> middleware(['admin']);

    // %%%% Form Elements
    Route::get('/resources/design/form_elements', [ResourcesController::class, 'form_elements']) -> middleware(['admin']);


    // %%%% Employees
    Route::get('/employees/agents', [EmployeesController::class, 'agents']) -> middleware(['admin']);
    Route::get('/employees/agents/get_agents', [EmployeesController::class, 'get_agents']) -> middleware(['admin']);



    // %%%% Transactions
    Route::get('/transactions', [TransactionsController::class, 'transactions']) -> middleware(['agent']);
    Route::get('/transactions/create/{transaction_type}', [TransactionsController::class, 'create']) -> middleware(['agent']);
    Route::get('/transactions/get_property_info', [TransactionsController::class, 'get_property_info']) -> middleware(['agent']);





    // %%%% Tests
    Route::get('/tests/test', [TestsController::class, 'test']) -> middleware(['admin']);
    Route::get('/tests/alpine', [TestsController::class, 'alpine']) -> middleware(['admin']);


});


require __DIR__.'/auth.php';
