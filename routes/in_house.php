<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthNet\AuthNetController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Employees\EmployeesController;
use App\Http\Controllers\Marketing\Data\DataController;
use App\Http\Controllers\Resources\ResourcesController;
use App\Http\Controllers\HeritageFinancial\LoansController;
use App\Http\Controllers\Marketing\AgentAddressesController;
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
    Route::get('/doc_management/admin/forms/forms', [FormsController::class, 'forms']) -> middleware(['in_house']);
    Route::get('/doc_management/admin/forms/get_form_groups', [FormsController::class, 'get_form_groups']) -> middleware(['in_house']);
    Route::get('/doc_management/admin/forms/get_forms', [FormsController::class, 'get_forms']) -> middleware(['in_house']);
    Route::get('/doc_management/admin/forms/search_forms', [FormsController::class, 'search_forms']) -> middleware(['in_house']);
    Route::post('/doc_management/admin/forms/get_upload_text', [FormsController::class, 'get_upload_text']) -> middleware(['in_house']);
    Route::post('/doc_management/admin/save_form', [FormsController::class, 'save_form']) -> middleware(['in_house']);
    // Form Fields
    Route::get('/doc_management/admin/forms/form_fields/{form_id}', [FormsFieldsController::class, 'form_fields']) -> middleware(['in_house']);
    Route::get('/doc_management/admin/forms/get_fields', [FormsFieldsController::class, 'get_fields']) -> middleware(['in_house']);
    Route::post('/doc_management/admin/forms/save_fields', [FormsFieldsController::class, 'save_fields']) -> middleware(['in_house']);
    Route::post('/doc_management/admin/forms/delete_page', [FormsFieldsController::class, 'delete_page']) -> middleware(['in_house']);


    // %%%% Checklists
    Route::get('/doc_management/admin/checklists/checklists', [ChecklistsController::class, 'checklists']) -> middleware(['in_house']);
    Route::get('/doc_management/admin/checklists/get_checklist_locations', [ChecklistsController::class, 'get_checklist_locations']) -> middleware(['in_house']);
    Route::get('/doc_management/admin/checklists/get_checklists', [ChecklistsController::class, 'get_checklists']) -> middleware(['in_house']);
    Route::post('/doc_management/admin/checklists/save_checklist', [ChecklistsController::class, 'save_checklist']) -> middleware(['in_house']);
    Route::post('/doc_management/admin/checklists/delete_checklist', [ChecklistsController::class, 'delete_checklist']) -> middleware(['in_house']);
    Route::post('/doc_management/admin/checklists/update_order', [ChecklistsController::class, 'update_order']) -> middleware(['in_house']);



    // %%%% Employees
    Route::get('/employees/in_house', [EmployeesController::class, 'in_house']) -> middleware(['in_house']);
    Route::get('/employees/in_house/get_in_house', [EmployeesController::class, 'get_in_house']) -> middleware(['in_house']);
    Route::get('/employees/in_house/in_house_view/{id?}', [EmployeesController::class, 'in_house_view']) -> middleware(['in_house']);

    Route::get('/employees/agent', [EmployeesController::class, 'agents']) -> middleware(['in_house']);
    Route::get('/employees/agent/get_agents', [EmployeesController::class, 'get_agents']) -> middleware(['in_house']);

    Route::get('/employees/loan_officer', [EmployeesController::class, 'loan_officers']) -> middleware(['in_house']);
    Route::get('/employees/loan_officer/get_loan_officers', [EmployeesController::class, 'get_loan_officers']) -> middleware(['in_house']);
    Route::get('/employees/loan_officer/loan_officer_view/{id?}', [EmployeesController::class, 'loan_officer_view']) -> middleware(['in_house']);


    Route::post('/employees/docs/docs_upload', [EmployeesController::class, 'docs_upload']) -> middleware(['in_house']);
    Route::post('/employees/docs/get_docs', [EmployeesController::class, 'get_docs']) -> middleware(['in_house']);
    Route::post('/employees/docs/delete_doc', [EmployeesController::class, 'delete_doc']) -> middleware(['in_house']);

    Route::get('/employees/get_licenses', [EmployeesController::class, 'get_licenses']) -> middleware(['in_house']);

    Route::post('/employees/save_details', [EmployeesController::class, 'save_details']) -> middleware(['in_house']);


    // archived
    Route::get('/transactions_archived', [ArchivedTransactionsController::class, 'transactions_archived']) -> middleware(['in_house']);
    Route::get('/get_transactions_archived', [ArchivedTransactionsController::class, 'get_transactions_archived']) -> middleware(['in_house']);
    Route::get('/transactions_archived_view/{listingGuid}/{saleGuid}', [ArchivedTransactionsController::class, 'transactions_archived_view']) -> middleware(['in_house']);

    // Escrow
    Route::get('/transactions_archived/escrow', [EscrowController::class, 'escrow']) -> middleware(['in_house']);
    Route::get('/transactions_archived/get_escrow_html', [EscrowController::class, 'get_escrow']) -> middleware(['in_house']);


    // Billing
    Route::get('/authnet/AddCreditCard', [AuthNetController::class, 'AddCreditCard']) -> middleware(['in_house']);


    // temp
    Route::get('/add_missing_fields', [ArchivedTransactionsController::class, 'add_missing_fields']) -> middleware(['in_house']);
    Route::get('/get_transactions', [ArchivedTransactionsController::class, 'get_transactions']) -> middleware(['in_house']);


    /////////////// %%%%%%%%%%%%%%%%%% SUPER ADMIN %%%%%%%%%%%%%%%%%% ///////////////
    // %%%% Form Elements
    Route::get('/resources/design/form_elements', [ResourcesController::class, 'form_elements']) -> middleware(['in_house']);
    Route::get('/resources/config/config_variables', [ResourcesController::class, 'config_variables']) -> middleware(['in_house']);
    Route::get('/resources/config/get_config_variables', [ResourcesController::class, 'get_config_variables']) -> middleware(['in_house']);
    Route::post('/resources/config/config_edit', [ResourcesController::class, 'config_edit']) -> middleware(['in_house']);
    Route::post('/resources/config/config_add', [ResourcesController::class, 'config_add']) -> middleware(['in_house']);


    // %%%% archives - import data for records
    Route::get('/archives/get_transactions', [SkySlopeController::class, 'get_transactions']) -> middleware(['in_house']);
    Route::get('/archives/get_listings', [SkySlopeController::class, 'get_listings']) -> middleware(['in_house']);
    Route::get('/archives/get_users', [SkySlopeController::class, 'get_users']) -> middleware(['in_house']);
    Route::get('/archives/get_listing/{listingGuid}', [SkySlopeController::class, 'get_listing']) -> middleware(['in_house']);
    Route::get('/archives/add_documents', [SkySlopeController::class, 'add_documents']) -> middleware(['in_house']);
    Route::get('/archives/check_documents_exists', [SkySlopeController::class, 'check_documents_exists']) -> middleware(['in_house']);
    Route::get('/archives/add_missing_documents', [SkySlopeController::class, 'add_missing_documents']) -> middleware(['in_house']);


    // %%%% escrow
    Route::get('/archives/escrow', [EscrowController::class, 'escrow']) -> middleware(['in_house']);
    Route::get('/archives/update_old_escrow', [EscrowController::class, 'update_old_escrow']) -> middleware(['in_house']);
    Route::get('/archives/get_checks', [EscrowController::class, 'get_checks']) -> middleware(['in_house']);
    Route::get('/archives/add_guids', [EscrowController::class, 'add_guids']) -> middleware(['in_house']);

    // %%%% Marketing
    Route::get('/marketing/data/agent_database', [DataController::class, 'agent_database']) -> middleware(['in_house']);
    Route::post('/marketing/data/location_data', [DataController::class, 'location_data']) -> middleware(['in_house']);
    Route::post('/marketing/data/search_offices', [DataController::class, 'search_offices']) -> middleware(['in_house']);

    // %%%% Import Loan Officers
    Route::get('/employees/loan_officer/import_los', [EmployeesController::class, 'import_los']) -> middleware(['in_house']);

    // %%%% Import Loans
    Route::get('/heritage_financial/loans/import_loans', [LoansController::class, 'import_loans']) -> middleware(['in_house']);


    // %%%% New Skyslope to old DB
    Route::get('/old_db/archives/update_listings', [OldSkySlopeController::class, 'update_listings']) -> middleware(['in_house']);

    // %%%% add old company listings to New Skyslope
    Route::get('/old_db/company/get_transactions', [OldTransactionsController::class, 'get_transactions']) -> middleware(['in_house']);

    // %%%% get ccs
    Route::get('/old_db/company/agents', [OldTransactionsController::class, 'agents']) -> middleware(['in_house']);



    Route::get('/admin/monitor', [AdminController::class, 'monitor']) -> middleware(['in_house']);
    Route::prefix('jobs') -> group(function () {
        Route::queueMonitor();
    });



    // Agent Addresses TEMP
    Route::get('/marketing/import_agent_addresses', [AgentAddressesController::class, 'import_agent_addresses']) -> middleware(['in_house']);
    Route::get('/marketing/merge_agents', [AgentAddressesController::class, 'merge_agents']) -> middleware(['in_house']);
    Route::get('/marketing/merge_multiple_matches', [AgentAddressesController::class, 'merge_multiple_matches']) -> middleware(['in_house']);



    // %%%% Tests
    Route::get('/tests/test', [TestsController::class, 'test']) -> middleware(['in_house']);
    Route::get('/tests/alpine', [TestsController::class, 'alpine']) -> middleware(['in_house']);
    Route::get('/tests/agent_data', [TestsController::class, 'agent_data']) -> middleware(['in_house']);
    Route::get('/tests/menu', [TestsController::class, 'menu']) -> middleware(['in_house']);
    Route::get('/tests/bright_update_agents', [TestsController::class, 'bright_update_agents']) -> middleware(['in_house']);
    Route::get('/tests/bright_update_offices', [TestsController::class, 'bright_update_offices']) -> middleware(['in_house']);
    Route::get('/tests/bright_remove_agents', [TestsController::class, 'bright_remove_agents']) -> middleware(['in_house']);

    Route::get('/tests/update_encrypted_fields', [TestsController::class, 'update_encrypted_fields']) -> middleware(['in_house']);





});

