<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FilepondUploadController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Employees\EmployeesController;


Route::middleware(['auth', 'web']) -> group(function () {

    /***** Dashboard ******/
    Route::get('/dashboard', [DashboardController::class, 'dashboard']) -> middleware(['all']);

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


});
