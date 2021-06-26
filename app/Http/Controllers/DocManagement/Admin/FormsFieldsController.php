<?php

namespace App\Http\Controllers\DocManagement\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DocManagement\Admin\Forms\Forms;
use App\Models\DocManagement\Admin\Forms\FormsFields;
use App\Models\DocManagement\Resources\CommonFields;
use App\Models\DocManagement\Resources\CommonFieldsGroups;

class FormsFieldsController extends Controller
{

    public function form_fields(Request $request) {

        $form = Forms::with(['fields', 'pages']) -> find($request -> form_id);
        $pages = $form -> pages;
        $form_id = $request -> form_id;

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
                'type' => 'number',
                'icon' => 'fa-dollar-sign'
            ],
            [
                'data' => $common_fields_offices,
                'label' => 'Offices',
                'type' => 'textbox',
                'icon' => 'fa-building'
            ],
        ];


        return view('doc_management/admin/forms/form_fields', compact('form', 'form_id', 'pages', 'groups'));

    }

    public function get_fields(Request $request) {

        $fields = FormsFields::where('form_id', $request -> form_id) -> get();

        return $fields -> toJson();

    }

    public function save_fields(Request $request) {

        $fields = json_decode($request -> fields, true);

        // delete all fields for this document
        $form_id = $request -> form_id;
        $delete_fields = FormsFields::where('form_id', $form_id) -> delete();

        if($fields) {

            foreach ($fields as $field) {

                $new_field = new FormsFields();

                $new_field -> form_id = $form_id;
                $new_field -> field_id = $field['id'];
                $new_field -> group_id = $field['group_id'];
                $new_field -> page = $field['page'];
                $new_field -> field_category = $field['category']; // textline, date, number, checkbox, radio
                $new_field -> field_type = $field['field_type']; // address, date, name, number, phone, text
                $new_field -> field_name = $field['field_name'];
                $new_field -> common_field_id = $field['common_field_id'];
                $new_field -> common_field_group_id = $field['common_field_group_id'];
                $new_field -> common_field_sub_group_id = $field['common_field_sub_group_id'];
                $new_field -> db_column_name = $field['db_column_name'];
                $new_field -> number_type = $field['number_type']; // numeric or written
                $new_field -> top_perc = $field['top_perc'];
                $new_field -> left_perc = $field['left_perc'];
                $new_field -> width_perc = $field['width_perc'];
                $new_field -> height_perc = $field['height_perc'];
                $new_field -> height_px = $field['height_px'];
                $new_field -> x = $field['x'];
                $new_field -> y = $field['y'];

                $new_field -> save();

            }

        }

    }

}
