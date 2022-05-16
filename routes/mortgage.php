<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Marketing\Data\DataController;
use App\Http\Controllers\HeritageFinancial\LoansController;
use App\Http\Controllers\HeritageFinancial\LendersController;
use App\Http\Controllers\HeritageFinancial\LoanOfficerController;
use App\Http\Controllers\HeritageFinancial\AgentDatabaseController;

Route::middleware(['auth', 'web']) -> group(function () {
    Route::get('/heritage_financial/loans', [LoansController::class, 'loans']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/loans/get_loans', [LoansController::class, 'get_loans']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/loans/view_loan/{uuid?}', [LoansController::class, 'view_loan']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/loans/docs_upload', [LoansController::class, 'docs_upload']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/loans/get_docs', [LoansController::class, 'get_docs']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/loans/delete_docs', [LoansController::class, 'delete_docs']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/loans/restore_docs', [LoansController::class, 'restore_docs']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/loans/commission_reports', [LoansController::class, 'commission_reports']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/loans/get_commission_reports', [LoansController::class, 'get_commission_reports']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/loans/get_changes', [LoansController::class, 'get_changes']) -> middleware(['mortgage']);

    Route::post('/heritage_financial/save_details', [LoansController::class, 'save_details']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/save_time_line', [LoansController::class, 'save_time_line']) -> middleware(['mortgage']);

    Route::post('/heritage_financial/save_commission', [LoansController::class, 'save_commission']) -> middleware(['mortgage']);

    Route::get('/heritage_financial/get_notes', [LoansController::class, 'get_notes']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/add_notes', [LoansController::class, 'add_notes']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/delete_note', [LoansController::class, 'delete_note']) -> middleware(['mortgage']);

    Route::get('/heritage_financial/manager_bonuses', [LoansController::class, 'manager_bonuses']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/email_manager_bonuses', [LoansController::class, 'email_manager_bonuses']) -> middleware(['mortgage']);

    Route::get('/heritage_financial/lenders', [LendersController::class, 'lenders']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/lenders/get_lenders', [LendersController::class, 'get_lenders']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/lenders/view_lender/{uuid?}', [LendersController::class, 'view_lender']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/lenders/save_details', [LendersController::class, 'save_details']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/lenders/docs_upload', [LendersController::class, 'docs_upload']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/lenders/get_docs', [LendersController::class, 'get_docs']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/lenders/delete_doc', [LendersController::class, 'delete_doc']) -> middleware(['mortgage']);

    Route::get('/heritage_financial/loan_software', function () {
        return view('/heritage_financial/loan_software/loan_software');
    });

    Route::get('/heritage_financial/agent_database', [AgentDatabaseController::class, 'agent_database']) -> middleware(['mortgage']);
    Route::get('/heritage_financial/agent_database/get_agent_database', [AgentDatabaseController::class, 'get_agent_database']) -> middleware(['mortgage']);
});
