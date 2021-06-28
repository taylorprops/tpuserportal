<?php

namespace App\Http\Controllers\DocManagement\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DocManagement\Resources\FormGroups;
use App\Models\DocManagement\Resources\ChecklistGroups;
use App\Models\DocManagement\Resources\ChecklistLocations;
use App\Models\DocManagement\Admin\Checklists\AdminChecklists;
use App\Models\DocManagement\Resources\ChecklistPropertyTypes;

class ChecklistsController extends Controller
{
    public function checklists(Request $request) {

        $checklist_locations = ChecklistLocations::orderBy('display_order') -> get();

        $form_groups = FormGroups::with(['forms']) -> get();

        $checklist_groups = ChecklistGroups::with(['checklist_items']) -> get();

        return view('/doc_management/admin/checklists/checklists', compact('checklist_locations', 'form_groups', 'checklist_groups'));

    }

    public function get_checklist_locations(Request $request) {

        $checklist_locations = ChecklistLocations::orderBy('display_order') -> get();

        return view('/doc_management/admin/checklists/get_checklist_locations_html', compact('checklist_locations'));

    }

    public function get_checklists(Request $request) {


        $location_id = $request -> location_id;
        $location_details = ChecklistLocations::find($location_id, ['location', 'state']);
        $location = $location_details -> state.' | '.$location_details -> location;
        if($location_details -> state != 'MD') {
            $location = $location_details -> state;
        }

        $property_types = ChecklistPropertyTypes::with(['checklists.property_type', 'checklists.property_sub_type', 'checklists.items', 'checklists.location']) -> with(['checklists' => function($query) use ($location_id) {
            $query -> where('checklist_location_id', $location_id) -> where('active', 'yes');
        }])
        -> get();


        return view('/doc_management/admin/checklists/get_checklists_html', compact('property_types', 'location'));

    }

    public function save_checklist(Request $request) {

        $validator = $request -> validate([
            'location_id' => 'required',
            'sale_rent' => 'required',
            'property_type_id' => 'required',
            'property_sub_type_id' => 'required_if:property_sub_type_required,===,yes',
            'checklist_type' => 'required',
            'represent' => 'required',
        ],
        [
            'required' => 'Required Field',
            'required_if' => 'Required Field',
        ]);

        if($request -> id) {
            $checklist = AdminChecklists::find($request -> id);
        } else {
            $checklist = new AdminChecklists();
            $checklist -> checklist_order = 1000;
        }
        $checklist -> checklist_location_id = $request -> location_id;
        $checklist -> checklist_represent = $request -> represent;
        $checklist -> checklist_type = $request -> checklist_type;
        $checklist -> checklist_sale_rent = $request -> sale_rent;
        $checklist -> checklist_property_type_id = $request -> property_type_id;
        $checklist -> checklist_property_sub_type_id = $request -> property_sub_type_id ?? 0;
        $checklist -> checklist_state = $request -> state;

        $checklist -> save();

    }

    public function delete_checklist(Request $request) {

        if($request -> checklist_id) {
            AdminChecklists::find($request -> checklist_id) -> update([
                'active' => 'no'
            ]);
            return response() -> json(['status' => 'success']);
        }
    }

    public function update_order(Request $request) {

        foreach(json_decode($request -> checklists, true) as $key => $value) {
            AdminChecklists::find($value['id'])
            -> update([
                'checklist_order' => $value['order']
            ]);
        }

    }

}
