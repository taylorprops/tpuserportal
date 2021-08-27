<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Employees\EmployeesController;
use App\Http\Controllers\Resources\ResourcesController;
use App\Http\Controllers\DocManagement\Admin\FormsController;
use App\Http\Controllers\OldDB\SkySlope\OldSkySlopeController;
use App\Http\Controllers\DocManagement\Admin\SkySlopeController;
use App\Http\Controllers\DocManagement\Archives\EscrowController;
use App\Http\Controllers\OldDB\Company\OldTransactionsController;
use App\Http\Controllers\DocManagement\Admin\ChecklistsController;
use App\Http\Controllers\DocManagement\Admin\FormsFieldsController;
use App\Http\Controllers\DocManagement\Archives\ArchivedTransactionsController;


Route::middleware(['auth', 'web']) -> group(function () {

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


    // archived
    Route::get('/transactions_archived', [ArchivedTransactionsController::class, 'transactions_archived']) -> middleware(['admin']);
    Route::get('/get_transactions_archived', [ArchivedTransactionsController::class, 'get_transactions_archived']) -> middleware(['admin']);
    Route::get('/transactions_archived_view/{listingGuid}/{saleGuid}', [ArchivedTransactionsController::class, 'transactions_archived_view']) -> middleware(['admin']);

    // Escrow
    Route::get('/transactions_archived/escrow', [EscrowController::class, 'escrow']) -> middleware(['admin']);
    Route::get('/transactions_archived/get_escrow_html', [EscrowController::class, 'get_escrow']) -> middleware(['admin']);


    // temp
    Route::get('/add_missing_fields', [ArchivedTransactionsController::class, 'add_missing_fields']) -> middleware(['admin']);


    /////////////// %%%%%%%%%%%%%%%%%% SUPER ADMIN %%%%%%%%%%%%%%%%%% ///////////////
    // %%%% Form Elements
    Route::get('/resources/design/form_elements', [ResourcesController::class, 'form_elements']) -> middleware(['admin']);
    Route::get('/resources/config/config_variables', [ResourcesController::class, 'config_variables']) -> middleware(['admin']);
    Route::get('/resources/config/get_config_variables', [ResourcesController::class, 'get_config_variables']) -> middleware(['admin']);
    Route::post('/resources/config/config_edit', [ResourcesController::class, 'config_edit']) -> middleware(['admin']);
    Route::post('/resources/config/config_add', [ResourcesController::class, 'config_add']) -> middleware(['admin']);


    // %%%% archives - import data for records
    Route::get('/archives/get_transactions', [SkySlopeController::class, 'get_transactions']) -> middleware(['admin']);
    Route::get('/archives/get_listings', [SkySlopeController::class, 'get_listings']) -> middleware(['admin']);
    Route::get('/archives/get_users', [SkySlopeController::class, 'get_users']) -> middleware(['admin']);
    Route::get('/archives/get_listing/{listingGuid}', [SkySlopeController::class, 'get_listing']) -> middleware(['admin']);
    Route::get('/archives/add_documents', [SkySlopeController::class, 'add_documents']) -> middleware(['admin']);
    Route::get('/archives/check_documents_exists', [SkySlopeController::class, 'check_documents_exists']) -> middleware(['admin']);
    Route::get('/archives/add_missing_documents', [SkySlopeController::class, 'add_missing_documents']) -> middleware(['admin']);

    // %%%% escrow
    Route::get('/archives/escrow', [EscrowController::class, 'escrow']) -> middleware(['admin']);
    Route::get('/archives/update_old_escrow', [EscrowController::class, 'update_old_escrow']) -> middleware(['admin']);
    Route::get('/archives/get_checks', [EscrowController::class, 'get_checks']) -> middleware(['admin']);


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
    Route::get('/tests/menu', [TestsController::class, 'menu']) -> middleware(['admin']);





});

