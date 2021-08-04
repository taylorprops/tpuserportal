<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Employees\EmployeesController;
use App\Http\Controllers\Resources\ResourcesController;
use App\Http\Controllers\DocManagement\Admin\FormsController;
use App\Http\Controllers\OldDB\SkySlope\OldSkySlopeController;
use App\Http\Controllers\DocManagement\Admin\ArchivesController;
use App\Http\Controllers\OldDB\Company\OldTransactionsController;
use App\Http\Controllers\DocManagement\Admin\ChecklistsController;
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
    return view('/auth/login');
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
    Route::post('/doc_management/admin/forms/delete_page', [FormsFieldsController::class, 'delete_page']) -> middleware(['admin']);


    // %%%% Checklists
    Route::get('/doc_management/admin/checklists/checklists', [ChecklistsController::class, 'checklists']) -> middleware(['admin']);
    Route::get('/doc_management/admin/checklists/get_checklist_locations', [ChecklistsController::class, 'get_checklist_locations']) -> middleware(['admin']);
    Route::get('/doc_management/admin/checklists/get_checklists', [ChecklistsController::class, 'get_checklists']) -> middleware(['admin']);
    Route::post('/doc_management/admin/checklists/save_checklist', [ChecklistsController::class, 'save_checklist']) -> middleware(['admin']);
    Route::post('/doc_management/admin/checklists/delete_checklist', [ChecklistsController::class, 'delete_checklist']) -> middleware(['admin']);
    Route::post('/doc_management/admin/checklists/update_order', [ChecklistsController::class, 'update_order']) -> middleware(['admin']);



    // %%%% Employees
    Route::get('/employees/agents', [EmployeesController::class, 'agents']) -> middleware(['admin']);
    Route::get('/employees/agents/get_agents', [EmployeesController::class, 'get_agents']) -> middleware(['admin']);



    // %%%% Transactions
    Route::get('/transactions', [TransactionsController::class, 'transactions']) -> middleware(['agent']);
    Route::get('/transactions_archived', [TransactionsController::class, 'transactions_archived']) -> middleware(['agent']);
    Route::get('/transactions/create/{transaction_type}', [TransactionsController::class, 'create']) -> middleware(['agent']);
    Route::post('/transactions/save_transaction', [TransactionsController::class, 'save_transaction']) -> middleware(['agent']);
    Route::get('/transactions/get_property_info', [TransactionsController::class, 'get_property_info']) -> middleware(['agent']);
    Route::get('/transactions/get_counties/{state}', [TransactionsController::class, 'get_counties']) -> middleware(['agent']);
    Route::get('/transactions/get_location_details', [TransactionsController::class, 'get_location_details']) -> middleware(['agent']);
    Route::post('/transactions/validate_form_manual_entry', [TransactionsController::class, 'validate_form_manual_entry']) -> middleware(['agent']);
    Route::post('/transactions/validate_form_checklist_details', [TransactionsController::class, 'validate_form_checklist_details']) -> middleware(['agent']);
    Route::get('/transactions/get_locations', [TransactionsController::class, 'get_locations']) -> middleware(['agent']);
    Route::get('/transactions/get_property_types', [TransactionsController::class, 'get_property_types']) -> middleware(['agent']);
    Route::get('/transactions/get_property_sub_types', [TransactionsController::class, 'get_property_sub_types']) -> middleware(['agent']);
    Route::get('/transactions/get_contacts', [TransactionsController::class, 'get_contacts']) -> middleware(['agent']);
    Route::get('/transactions/get_form_groups', [TransactionsController::class, 'get_form_groups']) -> middleware(['agent']);
    Route::get('/transactions/agent_search', [TransactionsController::class, 'agent_search']) -> middleware(['agent']);




    /////////////// %%%%%%%%%%%%%%%%%% SUPER ADMIN %%%%%%%%%%%%%%%%%% ///////////////
    // %%%% Form Elements
    Route::get('/resources/design/form_elements', [ResourcesController::class, 'form_elements']) -> middleware(['admin']);
    Route::get('/resources/config/config_variables', [ResourcesController::class, 'config_variables']) -> middleware(['admin']);
    Route::get('/resources/config/get_config_variables', [ResourcesController::class, 'get_config_variables']) -> middleware(['admin']);
    Route::post('/resources/config/config_edit', [ResourcesController::class, 'config_edit']) -> middleware(['admin']);
    Route::post('/resources/config/config_add', [ResourcesController::class, 'config_add']) -> middleware(['admin']);


    // %%%% Skyslope - import data for records
    Route::get('/archives/get_transactions', [SkySlopeController::class, 'get_transactions']) -> middleware(['admin']);
    Route::get('/archives/get_listings', [SkySlopeController::class, 'get_listings']) -> middleware(['admin']);
    Route::get('/archives/get_users', [SkySlopeController::class, 'get_users']) -> middleware(['admin']);
    Route::get('/archives/get_listing/{listingGuid}', [SkySlopeController::class, 'get_listing']) -> middleware(['admin']);
    Route::get('/archives/add_documents', [SkySlopeController::class, 'add_documents']) -> middleware(['admin']);
    Route::get('/archives/check_documents_exists', [SkySlopeController::class, 'check_documents_exists']) -> middleware(['admin']);
    Route::get('/archives/add_missing_documents', [SkySlopeController::class, 'add_missing_documents']) -> middleware(['admin']);


    // %%%% New Skyslope to old DB
    Route::get('/old_db/archives/update_listings', [OldSkySlopeController::class, 'update_listings']) -> middleware(['admin']);

    // %%%% add old company listings to New Skyslope
    Route::get('/old_db/company/get_transactions', [OldTransactionsController::class, 'get_transactions']) -> middleware(['admin']);



    Route::get('/admin/monitor', [AdminController::class, 'monitor']) -> middleware(['admin']);
    Route::prefix('jobs') -> group(function () {
        Route::queueMonitor();
    });



    // %%%% Tests
    Route::get('/tests/test', [TestsController::class, 'test']) -> middleware(['admin']);
    Route::get('/tests/alpine', [TestsController::class, 'alpine']) -> middleware(['admin']);
    Route::get('/tests/agent_data', [TestsController::class, 'agent_data']) -> middleware(['admin']);


});


require __DIR__.'/auth.php';
