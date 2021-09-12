<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocManagement\Transactions\TransactionsController;


Route::middleware(['auth', 'web']) -> group(function () {

    // // %%%% Transactions
    Route::get('/transactions', [TransactionsController::class, 'transactions']) -> middleware(['agent']);
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



});
