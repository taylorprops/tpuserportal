<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees\Agents;
use App\Models\DocManagement\Admin\Forms\Forms;
use App\Models\DocManagement\Resources\CommonFieldsGroups;

class TestsController extends Controller
{

    public function agent_data(Request $request) {

        $agents = Agents::select(['id', 'first', 'last', 'email1'])
        -> where('active', 'yes')
        -> with(['docs', 'licenses'])
        -> get()
        -> toJson();

        dd($agents);
    }

    public function alpine(Request $request) {

        return view('/tests/alpine');

    }

    public function test(Request $request) {

        $form = Forms::with(['fields', 'pages']) -> find(185);
        $pages = $form -> pages;

        $common_fields_people = CommonFieldsGroups::with(['sub_groups', 'sub_groups.common_fields'])
        -> where('group_name', 'People')
        -> first();

        return view('/tests/test', compact('form', 'pages', 'common_fields_people'));

    }
}
