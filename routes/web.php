<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('/auth/login');
});




require __DIR__.'/auth.php';
require __DIR__.'/all.php';
require __DIR__.'/admin.php';
require __DIR__.'/agents.php';
require __DIR__.'/loan_officers.php';


