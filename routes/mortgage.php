<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeritageFinancial\LoansController;
use App\Http\Controllers\HeritageFinancial\LoanOfficerController;


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

    Route::post('/heritage_financial/save_details', [LoansController::class, 'save_details']) -> middleware(['mortgage']);

    Route::post('/heritage_financial/save_commission', [LoansController::class, 'save_commission']) -> middleware(['mortgage']);

    Route::get('/heritage_financial/get_notes', [LoansController::class, 'get_notes']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/add_notes', [LoansController::class, 'add_notes']) -> middleware(['mortgage']);
    Route::post('/heritage_financial/delete_note', [LoansController::class, 'delete_note']) -> middleware(['mortgage']);

    Route::get('/heritage_financial/loan_software', function() {
        return view('/heritage_financial/loan_software/loan_software');
    });


});
