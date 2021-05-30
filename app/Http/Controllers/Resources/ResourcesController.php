<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResourcesController extends Controller
{
    public function form_elements(Request $request) {

        return view('resources/design/form_elements');

    }
}
