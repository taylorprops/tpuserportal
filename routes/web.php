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




    // %%%% Employees
    Route::get('/employees/agents', [EmployeesController::class, 'agents']) -> middleware(['admin']);
    Route::get('/employees/agents/get_agents', [EmployeesController::class, 'get_agents']) -> middleware(['admin']);



    // %%%% Transactions
    Route::get('/transactions', [TransactionsController::class, 'transactions']) -> middleware(['agent']);
    Route::get('/transactions/create/{transaction_type}', [TransactionsController::class, 'create']) -> middleware(['agent']);
    Route::post('/transactions/save_transaction', [TransactionsController::class, 'save_transaction']) -> middleware(['agent']);
    Route::get('/transactions/get_property_info', [TransactionsController::class, 'get_property_info']) -> middleware(['agent']);
    Route::get('/transactions/get_counties/{state}', [TransactionsController::class, 'get_counties']) -> middleware(['agent']);
    Route::get('/transactions/get_location_details', [TransactionsController::class, 'get_location_details']) -> middleware(['agent']);
    Route::post('/transactions/validate_form_manual_entry', [TransactionsController::class, 'validate_form_manual_entry']) -> middleware(['agent']);
    Route::post('/transactions/validate_form_checklist_details', [TransactionsController::class, 'validate_form_checklist_details']) -> middleware(['agent']);
    Route::get('/transactions/get_property_types', [TransactionsController::class, 'get_property_types']) -> middleware(['agent']);
    Route::get('/transactions/get_property_sub_types', [TransactionsController::class, 'get_property_sub_types']) -> middleware(['agent']);
    Route::get('/transactions/get_contacts', [TransactionsController::class, 'get_contacts']) -> middleware(['agent']);
    Route::get('/transactions/agent_search', [TransactionsController::class, 'agent_search']) -> middleware(['agent']);




    /////////////// %%%%%%%%%%%%%%%%%% SUPER ADMIN %%%%%%%%%%%%%%%%%% ///////////////
    // %%%% Form Elements
    Route::get('/resources/design/form_elements', [ResourcesController::class, 'form_elements']) -> middleware(['admin']);
    Route::get('/resources/config/config_variables', [ResourcesController::class, 'config_variables']) -> middleware(['admin']);
    Route::get('/resources/config/get_config_variables', [ResourcesController::class, 'get_config_variables']) -> middleware(['admin']);
    Route::post('/resources/config/config_edit', [ResourcesController::class, 'config_edit']) -> middleware(['admin']);
    Route::post('/resources/config/config_add', [ResourcesController::class, 'config_add']) -> middleware(['admin']);




    // %%%% Tests
    Route::get('/tests/test', [TestsController::class, 'test']) -> middleware(['admin']);
    Route::get('/tests/alpine', [TestsController::class, 'alpine']) -> middleware(['admin']);


});


require __DIR__.'/auth.php';
