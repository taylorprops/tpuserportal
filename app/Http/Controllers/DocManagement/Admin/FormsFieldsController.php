<?php

namespace App\Http\Controllers\DocManagement\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FormsFieldsController extends Controller
{
    public function form_fields(Request $request) {

        return view('doc_management/admin/forms/form_fields');

    }
}
