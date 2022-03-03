<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GlobalController extends Controller
{
    public function get_signature(Request $request)
    {
        return auth()->user()->signature;
    }
}
