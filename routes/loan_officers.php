<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeritageFinancial\LoansController;
use App\Http\Controllers\HeritageFinancial\LoanOfficerController;


Route::middleware(['auth', 'web']) -> group(function () {


    Route::get('/heritage_financial/loans', [LoansController::class, 'loans']) -> middleware(['loan_officer']);
    Route::get('/heritage_financial/loans/get_loans', [LoansController::class, 'get_loans']) -> middleware(['loan_officer']);
    Route::get('/heritage_financial/loans/view_loan/{uuid?}', [LoansController::class, 'view_loan']) -> middleware(['loan_officer']);
    Route::post('/heritage_financial/save_details', [LoansController::class, 'save_details']) -> middleware(['loan_officer']);
    Route::post('/heritage_financial/save_commission', [LoansController::class, 'save_commission']) -> middleware(['loan_officer']);

});
