<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\API\APIController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Notes\NotesController;
use App\Http\Controllers\AuthNet\AuthNetController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Employees\EmployeesController;
use App\Http\Controllers\Marketing\Data\DataController;
use App\Http\Controllers\Resources\ResourcesController;
use App\Http\Controllers\HeritageFinancial\LoansController;
use App\Http\Controllers\Marketing\AgentAddressesController;
use App\Http\Controllers\DocManagement\Admin\FormsController;
use App\Http\Controllers\HeritageFinancial\LendersController;
use App\Http\Controllers\OldDB\SkySlope\OldSkySlopeController;
use App\Http\Controllers\Marketing\Schedule\ScheduleController;
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

    Route::get('/employees/loan_officer', [EmployeesController::class, 'loan_officers']) -> middleware(['mortgage']);
    Route::get('/employees/loan_officer/get_loan_officers', [EmployeesController::class, 'get_loan_officers']) -> middleware(['mortgage']);
    Route::get('/employees/loan_officer/loan_officer_view/{id?}', [EmployeesController::class, 'loan_officer_view']) -> middleware(['mortgage']);

    Route::post('/employees/docs/docs_upload', [EmployeesController::class, 'docs_upload']) -> middleware(['mortgage']);
    Route::post('/employees/docs/get_docs', [EmployeesController::class, 'get_docs']) -> middleware(['mortgage']);
    Route::post('/employees/docs/delete_doc', [EmployeesController::class, 'delete_doc']) -> middleware(['mortgage']);

    Route::get('/employees/get_licenses', [EmployeesController::class, 'get_licenses']) -> middleware(['mortgage']);

    Route::post('/employees/save_details', [EmployeesController::class, 'save_details']) -> middleware(['mortgage']);

    Route::get('/employees/get_notes', [EmployeesController::class, 'get_notes']) -> middleware(['mortgage']);
    Route::post('/employees/add_notes', [EmployeesController::class, 'add_notes']) -> middleware(['mortgage']);
    Route::post('/employees/delete_note', [EmployeesController::class, 'delete_note']) -> middleware(['mortgage']);

    // %%%% Users
    Route::get('/users', [EmployeesController::class, 'users']) -> middleware(['in_house']);
    Route::get('/users/get_users', [EmployeesController::class, 'get_users']) -> middleware(['in_house']);
    Route::post('/users/send_welcome_email', [EmployeesController::class, 'send_welcome_email']) -> middleware(['in_house']);
    Route::post('/users/reset_password', [EmployeesController::class, 'reset_password']) -> middleware(['in_house']);
    Route::get('/users/login_as_user/{id}', [EmployeesController::class, 'login_as_user']) -> middleware(['in_house']) -> name('login_as_user');

    // archived
    Route::get('/transactions_archived', [ArchivedTransactionsController::class, 'transactions_archived']) -> middleware(['in_house']);
    Route::get('/get_transactions_archived', [ArchivedTransactionsController::class, 'get_transactions_archived']) -> middleware(['in_house']);
    Route::get('/transactions_archived_view/{listingGuid}/{saleGuid}', [ArchivedTransactionsController::class, 'transactions_archived_view']) -> middleware(['in_house']);

    // Escrow
    Route::get('/transactions_archived/escrow', [EscrowController::class, 'escrow']) -> middleware(['in_house']);
    Route::get('/transactions_archived/get_escrow_html', [EscrowController::class, 'get_escrow']) -> middleware(['in_house']);

    // temp
    Route::get('/add_missing_fields', [ArchivedTransactionsController::class, 'add_missing_fields']) -> middleware(['in_house']);
    Route::get('/get_transactions', [ArchivedTransactionsController::class, 'get_transactions']) -> middleware(['in_house']);

    /////////////// %%%%%%%%%%%%%%%%%% SUPER ADMIN %%%%%%%%%%%%%%%%%% ///////////////

    // %%%% System Monitor
    Route::get('/admin/system_monitor', [AdminController::class, 'system_monitor']) -> middleware(['in_house']);
    Route::get('/admin/system_monitor/get_failed_jobs', [AdminController::class, 'get_failed_jobs']) -> middleware(['in_house']);
    Route::post('/admin/system_monitor/delete_failed_jobs', [AdminController::class, 'delete_failed_jobs']) -> middleware(['in_house']);

    // Notes
    Route::get('/notes', [NotesController::class, 'notes']) -> middleware(['in_house']);
    Route::post('/notes/save_notes', [NotesController::class, 'save_notes']) -> middleware(['in_house']);

    // %%%% Queue Monitor
    Route::get('/admin/queue_monitor', [AdminController::class, 'queue_monitor']) -> middleware(['in_house']);
    Route::prefix('jobs') -> group(function () {
        Route::queueMonitor();
    });

    // %%%% Form Elements
    Route::get('/resources/design/form_elements', [ResourcesController::class, 'form_elements']) -> middleware(['in_house']);
    Route::get('/resources/config/config_variables', [ResourcesController::class, 'config_variables']) -> middleware(['in_house']);
    Route::get('/resources/config/get_config_variables', [ResourcesController::class, 'get_config_variables']) -> middleware(['in_house']);
    Route::post('/resources/config/config_edit', [ResourcesController::class, 'config_edit']) -> middleware(['in_house']);
    Route::post('/resources/config/config_add', [ResourcesController::class, 'config_add']) -> middleware(['in_house']);
    Route::post('/resources/config/config_delete', [ResourcesController::class, 'config_delete']) -> middleware(['in_house']);

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
    Route::get('/marketing/data/address_database', [DataController::class, 'address_database']) -> middleware(['in_house']);
    Route::post('/marketing/data/location_data', [DataController::class, 'location_data']) -> middleware(['in_house']);
    Route::post('/marketing/data/search_offices', [DataController::class, 'search_offices']) -> middleware(['in_house']);
    Route::post('/marketing/data/get_results', [DataController::class, 'get_results']) -> middleware(['in_house']);
    Route::post('/marketing/data/get_recently_added', [DataController::class, 'get_recently_added']) -> middleware(['in_house']);
    Route::post('/marketing/data/get_purged', [DataController::class, 'get_purged']) -> middleware(['in_house']);
    Route::get('/marketing/data/upload_list', [DataController::class, 'upload_list']) -> middleware(['in_house']);
    Route::post('/marketing/data/add_new_list', [DataController::class, 'add_new_list']) -> middleware(['in_house']);

    Route::get('/marketing/schedule', [ScheduleController::class, 'schedule']) -> middleware(['in_house']);
    Route::post('/marketing/get_schedule', [ScheduleController::class, 'get_schedule']) -> middleware(['in_house']);
    Route::get('/marketing/schedule_settings', [ScheduleController::class, 'schedule_settings']) -> middleware(['in_house']);
    Route::post('/marketing/save_item', [ScheduleController::class, 'save_item']) -> middleware(['in_house']);
    Route::get('/marketing/show_versions', [ScheduleController::class, 'show_versions']) -> middleware(['in_house']);
    Route::post('/marketing/save_add_version', [ScheduleController::class, 'save_add_version']) -> middleware(['in_house']);
    Route::post('/marketing/calendar_get_events', [ScheduleController::class, 'calendar_get_events']) -> middleware(['in_house']);
    Route::post('/marketing/clone_event', [ScheduleController::class, 'clone_event']) -> middleware(['in_house']);
    Route::post('/marketing/delete_event', [ScheduleController::class, 'delete_event']) -> middleware(['in_house']);
    Route::post('/marketing/delete_version', [ScheduleController::class, 'delete_version']) -> middleware(['in_house']);
    Route::post('/marketing/reactivate_version', [ScheduleController::class, 'reactivate_version']) -> middleware(['in_house']);
    Route::post('/marketing/mark_version_accepted', [ScheduleController::class, 'mark_version_accepted']) -> middleware(['in_house']);
    Route::post('/marketing/send_email', [ScheduleController::class, 'send_email']) -> middleware(['in_house']);
    Route::post('/marketing/get_email_list', [ScheduleController::class, 'get_email_list']) -> middleware(['in_house']);
    Route::post('/marketing/update_status', [ScheduleController::class, 'update_status']) -> middleware(['in_house']);
    Route::get('/marketing/get_notes', [ScheduleController::class, 'get_notes']) -> middleware(['in_house']);
    Route::post('/marketing/add_notes', [ScheduleController::class, 'add_notes']) -> middleware(['in_house']);
    Route::post('/marketing/delete_note', [ScheduleController::class, 'delete_note']) -> middleware(['in_house']);
    Route::post('/marketing/mark_note_read', [ScheduleController::class, 'mark_note_read']) -> middleware(['in_house']);
    Route::get('/marketing/get_notification_count', [ScheduleController::class, 'get_notification_count']) -> middleware(['in_house']);

    Route::get('/marketing/get_schedule_settings', [ScheduleController::class, 'get_schedule_settings']) -> middleware(['in_house']);
    Route::post('/marketing/settings_save_add_item', [ScheduleController::class, 'settings_save_add_item']) -> middleware(['in_house']);
    Route::post('/marketing/settings_save_edit_item', [ScheduleController::class, 'settings_save_edit_item']) -> middleware(['in_house']);
    Route::get('/marketing/settings_get_reassign_options', [ScheduleController::class, 'settings_get_reassign_options']) -> middleware(['in_house']);
    Route::post('/marketing/settings_reassign_items', [ScheduleController::class, 'settings_reassign_items']) -> middleware(['in_house']);
    Route::post('/marketing/settings_update_order', [ScheduleController::class, 'settings_update_order']) -> middleware(['in_house']);


    // %%%% Import Loan Officers
    Route::get('/employees/loan_officer/import_los', [EmployeesController::class, 'import_los']) -> middleware(['in_house']);

    // %%%% Import Loans
    Route::get('/heritage_financial/loans/import_loans', [LoansController::class, 'import_loans']) -> middleware(['in_house']);
    Route::get('/heritage_financial/add_time_line', [LoansController::class, 'add_time_line']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/add_lock_details', [LoansController::class, 'add_lock_details']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/add_agent_details', [LoansController::class, 'add_agent_details']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/add_missing_details', [LoansController::class, 'add_missing_details']) -> middleware(['mortgage']);

    // %%%% Import Lenders
    Route::get('/heritage_financial/lenders/import_lenders', [LendersController::class, 'import_lenders']) -> middleware(['in_house']);
    Route::get('/heritage_financial/lenders/add_uuids', [LendersController::class, 'add_uuids']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/lenders/parse_address', [LendersController::class, 'parse_address']) -> middleware(['mortgage']);

    // %%%% New Skyslope to old DB
    Route::get('/old_db/archives/update_listings', [OldSkySlopeController::class, 'update_listings']) -> middleware(['in_house']);

    // %%%% add old company listings to New Skyslope
    Route::get('/old_db/company/get_transactions', [OldTransactionsController::class, 'get_transactions']) -> middleware(['in_house']);

    // %%%% get ccs
    Route::get('/old_db/company/agents', [OldTransactionsController::class, 'agents']) -> middleware(['in_house']);



    // Agent Addresses TEMP
    Route::get('/marketing/import_agent_addresses', [AgentAddressesController::class, 'import_agent_addresses']) -> middleware(['in_house']);
    Route::get('/marketing/merge_agents', [AgentAddressesController::class, 'merge_agents']) -> middleware(['in_house']);
    Route::get('/marketing/merge_multiple_matches', [AgentAddressesController::class, 'merge_multiple_matches']) -> middleware(['in_house']);

    // %%%% Tests
    Route::get('/tests/test', [TestsController::class, 'test']) -> middleware(['in_house']);
    Route::get('/tests/bright_missing_from_bright', [TestsController::class, 'bright_missing_from_bright']) -> middleware(['in_house']);
    Route::get('/tests/bright_missing_from_db', [TestsController::class, 'bright_missing_from_db']) -> middleware(['in_house']);


    Route::get('/tests/update_encrypted_fields', [TestsController::class, 'update_encrypted_fields']) -> middleware(['in_house']);
    Route::get('/tests/test_connection', [TestsController::class, 'test_connection']) -> middleware(['in_house']);
});

// Lending Pad Browser Extension
Route::get('/api/test', [APIController::class, 'test']) -> middleware(['lending_pad']);
Route::get('/api/lending_pad/update_loan', [APIController::class, 'update_loan']) -> middleware(['lending_pad']);
Route::get('/api/lending_pad/get_critical_dates', [APIController::class, 'get_critical_dates']) -> middleware(['lending_pad']);
Route::get('/api/lending_pad/search', [APIController::class, 'search']) -> middleware(['lending_pad']);

// Taylorprops.com browser extension
Route::get('/api/taylor_props/submit_recruiting_form', [APIController::class, 'submit_recruiting_form']) -> middleware(['taylor_props']);
Route::post('/api/heritage_title/submit_contact_form_title', [APIController::class, 'submit_contact_form_title']) -> middleware(['heritage_title']);
Route::post('/api/marketing/add_email_clicker_mortgage', [APIController::class, 'add_email_clicker_mortgage']) -> middleware(['heritage_financial']);
Route::post('/api/marketing/add_email_clicker_title', [APIController::class, 'add_email_clicker_title']) -> middleware(['heritage_title']);
Route::post('/api/marketing/add_email_clicker_real_estate', [APIController::class, 'add_email_clicker_real_estate']) -> middleware(['taylor_props']);

// zoho browser extension
Route::get('/api/zoho/get_medium', [APIController::class, 'get_medium']) -> middleware(['zoho']);
Route::get('/api/zoho/get_bright_agent_details', [APIController::class, 'get_bright_agent_details']) -> middleware(['zoho']);


/* Resources */
Route::get('/zoho/create_access_token', [APIController::class, 'create_access_token']);
Route::get('/zoho/get_users', [APIController::class, 'get_users']);
Route::get('/zoho/get_fields', [APIController::class, 'get_fields']);
Route::get('/zoho/get_assignment_rules', [APIController::class, 'get_assignment_rules']);

Route::get('/zoho/reports_query', [APIController::class, 'reports_query']);

/* tests */
Route::get('/zoho/check_if_user_exists', [APIController::class, 'check_if_user_exists']);
