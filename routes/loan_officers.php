<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employees\EmployeesController;
use App\Http\Controllers\HeritageFinancial\LoanOfficerController;


Route::middleware(['auth', 'web']) -> group(function () {

    Route::get('/heritage_financial/loan_officers/profile', [LoanOfficerController::class, 'profile']) -> middleware(['loan_officer']);
    Route::post('/heritage_financial/loan_officers/profile/save_bio', [EmployeesController::class, 'save_bio']) -> middleware(['loan_officer']);

    Route::post('/employees/loan_officers/photos/save_cropped_upload_loan_officer', [EmployeesController::class, 'save_cropped_upload_loan_officer']) -> middleware(['loan_officer']);
    Route::post('/employees/loan_officers/photos/delete_photo_loan_officer', [EmployeesController::class, 'delete_photo_loan_officer']) -> middleware(['loan_officer']);


});
