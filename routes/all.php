<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\Email\EmailController;
use App\Http\Controllers\Search\SearchController;
use App\Http\Controllers\FilepondUploadController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Employees\EmployeesController;


Route::middleware(['auth', 'web']) -> group(function () {

    /***** Dashboard ******/
    Route::get('/dashboard', [DashboardController::class, 'dashboard']) -> name('dashboard') -> middleware(['all']);

    /***** Search ******/
    Route::get('/search', [SearchController::class, 'search']) -> middleware(['all']);

    /***** file upload ******/
    Route::post('/filepond_upload', [FilepondUploadController::class, 'upload']) -> middleware(['all']);
    /***** tinymce file upload ******/
    Route::post('/text_editor/file_upload', [FileUploadController::class, 'file_upload']) -> middleware(['all']);

    /***** Employee Profile ******/
    Route::get('/employees/profile', [EmployeesController::class, 'profile']) -> middleware(['all']);
    Route::post('/employees/profile/save_bio', [EmployeesController::class, 'save_bio']) -> middleware(['all']);
    Route::post('/employees/profile/save_signature', [EmployeesController::class, 'save_signature']) -> middleware(['all']);

    /***** Employee Photos ******/
    Route::post('/employees/photos/save_cropped_upload', [EmployeesController::class, 'save_cropped_upload']) -> middleware(['all']);
    Route::post('/employees/photos/delete_photo', [EmployeesController::class, 'delete_photo']) -> middleware(['all']);

    /***** Billing ******/
    Route::get('/employees/billing/get_credit_cards', [EmployeesController::class, 'get_credit_cards']) -> middleware(['all']);
    Route::post('/employees/billing/add_credit_card', [EmployeesController::class, 'add_credit_card']) -> middleware(['all']);
    Route::post('/employees/billing/delete_credit_card', [EmployeesController::class, 'delete_credit_card']) -> middleware(['all']);
    Route::post('/employees/billing/set_default_credit_card', [EmployeesController::class, 'set_default_credit_card']) -> middleware(['all']);

    Route::get('/global/get_signature', [GlobalController::class, 'get_signature']) -> middleware(['all']);

    /***** Email lists ******/
    Route::post('/email/email_list', [EmailController::class, 'email_list']) -> middleware(['all']);


    /***** Reports ******/
    Route::get('/reports', [ReportsController::class, 'reports']) -> middleware(['all']);

    Route::get('/reports/mortgage/loans_in_process', [ReportsController::class, 'loans_in_process']) -> middleware(['all']);
    Route::get('/reports/mortgage/closed_loans_by_month', [ReportsController::class, 'closed_loans_by_month']) -> middleware(['all']);
    Route::get('/reports/mortgage/closed_loans_by_month_detailed', [ReportsController::class, 'closed_loans_by_month_detailed']) -> middleware(['all']);
    Route::get('/reports/mortgage/closed_loans_by_loan_officer', [ReportsController::class, 'closed_loans_by_loan_officer']) -> middleware(['all']);
    Route::get('/reports/mortgage/closed_loans_by_loan_officer_summary', [ReportsController::class, 'closed_loans_by_loan_officer_summary']) -> middleware(['all']);

    Route::post('/reports/mortgage/get_detailed_report', [ReportsController::class, 'get_detailed_report']) -> middleware(['all']);
    Route::get('/reports/mortgage/get_detailed_report_data', [ReportsController::class, 'get_detailed_report_data']) -> middleware(['all']);
    Route::post('/reports/mortgage/get_detailed_report_details', [ReportsController::class, 'get_detailed_report_data']) -> middleware(['all']);

    Route::get('/reports/print', [ReportsController::class, 'print']) -> middleware(['all']);


});
