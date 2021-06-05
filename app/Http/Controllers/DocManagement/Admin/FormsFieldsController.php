<?php

namespace App\Http\Controllers\DocManagement\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DocManagement\Admin\Forms;
use App\Models\DocManagement\Admin\CommonFields;
use App\Models\DocManagement\Admin\CommonFieldsGroups;

class FormsFieldsController extends Controller
{
    public function form_fields(Request $request) {

        $form = Forms::with(['fields', 'pages']) -> find($request -> form_id);
        $pages = $form -> pages;

        $common_fields_people = CommonFieldsGroups::with(['sub_groups', 'sub_groups.common_fields'])
        -> where('group_name', 'People')
        -> first();

        $common_fields_property = CommonFieldsGroups::with(['common_fields'])
        -> where('group_name', 'Property')
        -> first();

        $common_fields_dates = CommonFieldsGroups::with(['common_fields'])
        -> where('group_name', 'Dates')
        -> first();

        $common_fields_prices = CommonFieldsGroups::with(['common_fields'])
        -> where('group_name', 'Prices')
        -> first();

        $common_fields_offices = CommonFieldsGroups::with(['sub_groups', 'sub_groups.common_fields'])
        -> where('group_name', 'Offices')
        -> first();

        $groups = [
            [
                'data' => $common_fields_people,
                'label' => 'People',
                'type' => 'textbox',
                'icon' => 'fa-users'
            ],
            [
                'data' => $common_fields_property,
                'label' => 'Property',
                'type' => 'textbox',
                'icon' => 'fa-home'
            ],
            [
                'data' => $common_fields_dates,
                'label' => 'Dates',
                'type' => 'date',
                'icon' => 'fa-calendar'
            ],
            [
                'data' => $common_fields_prices,
                'label' => 'Prices',
                'type' => 'price',
                'icon' => 'fa-dollar-sign'
            ],
            [
                'data' => $common_fields_offices,
                'label' => 'Offices',
                'type' => 'textbox',
                'icon' => 'fa-building'
            ],
        ];


        return view('doc_management/admin/forms/form_fields', compact('form', 'pages', 'groups'));

    }
}
