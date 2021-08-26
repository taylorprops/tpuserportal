<?php
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'web']) -> group(function () {

    // // %%%% Transactions
    //Route::get('/heritage_financial/loans', [LoansController::class, 'loans']) -> middleware(['loan_officer']);


});
