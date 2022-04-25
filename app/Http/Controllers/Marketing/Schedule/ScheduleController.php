<?php

namespace App\Http\Controllers\Marketing\Schedule;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Marketing\Schedule\Schedule;
use App\Models\Marketing\Schedule\ScheduleMediums;
use App\Models\DocManagement\Resources\LocationData;
use App\Models\Marketing\Schedule\ScheduleCompanies;
use App\Models\Marketing\Schedule\ScheduleCategories;

class ScheduleController extends Controller
{

    public function schedule(Request $request) {

        $categories = ScheduleCategories::orderBy('category') -> get();
        $companies = ScheduleCompanies::orderBy('company') -> get();
        $mediums = ScheduleMediums::with(['descriptions']) -> orderBy('medium') -> get();
        $states = LocationData::activeStates();

        return view('marketing/schedule/schedule', compact('categories', 'companies', 'mediums', 'states'));

    }

    public function get_schedule(Request $request) {

        $items = Schedule::orderBy('deploy_date', 'desc') -> get();


        return view('marketing/schedule/get_schedule_html', compact('items'));

    }

    public function save_add_item(Request $request) {

        dd($request -> all());

    }


    public function schedule_settings(Request $request) {

        return view('marketing/schedule/schedule_settings');

    }

    public function get_schedule_settings(Request $request) {

        $type = $request -> type;

        if($type == 'categories') {
            $settings = ScheduleCategories::orderBy('category') -> get();
        } else if($type == 'mediums') {
            $settings = ScheduleMediums::with(['descriptions']) -> orderBy('medium') -> get();
        } else if($type == 'companies') {
            $settings = ScheduleCompanies::orderBy('company') -> get();
        }

        return view('marketing/schedule/get_schedule_settings_html', compact('type', 'settings'));

    }

    public function settings_get_reassign_options(Request $request) {

        $type = $request -> type;
        $id = $request -> id;

        if($type == 'categories') {
            $in_use = Schedule::where('category_id', $id) -> first();
            if(!$in_use) {
                ScheduleCategories::find($id) -> delete();
                return response() -> json(['deleted' => true]);
            }
            $settings = ScheduleCategories::where('id', '!=', $id) -> orderBy('category') -> get();
        } else if($type == 'mediums') {
            $in_use = Schedule::where('medium_id', $id) -> first();
            if(!$in_use) {
                ScheduleMediums::find($id) -> delete();
                return response() -> json(['deleted' => true]);
            }
            $settings = ScheduleMediums::where('id', '!=', $id) -> with(['descriptions']) -> orderBy('medium') -> get();
        } else if($type == 'companies') {
            $in_use = Schedule::where('company_id', $id) -> first();
            if(!$in_use) {
                ScheduleCompanies::find($id) -> delete();
                return response() -> json(['deleted' => true]);
            }
            $settings = ScheduleCompanies::where('id', '!=', $id) -> orderBy('company') -> get();
        }

        return compact('settings');

    }

    public function settings_save_add_item(Request $request) {

        $type = $request -> type;
        $value = $request -> value;

        if($type == 'categories') {
            ScheduleCategories::create([
                'category' => $value
            ]);
        } else if($type == 'mediums') {
            ScheduleMediums::create([
                'medium' => $value
            ]);
        } else if($type == 'companies') {
            ScheduleCompanies::create([
                'company' => $value
            ]);
        }

        return response() -> json(['status' => 'success']);

    }

    public function settings_save_edit_item(Request $request) {

        $type = $request -> type;
        $id = $request -> id;
        $value = $request -> value;

        if($type == 'categories') {
            ScheduleCategories::find($id) -> update([
                'category' => $value
            ]);
        } else if($type == 'mediums') {
            ScheduleMediums::find($id) -> update([
                'medium' => $value
            ]);
        } else if($type == 'companies') {
            ScheduleCompanies::find($id) -> update([
                'company' => $value
            ]);
        }

        return response() -> json(['status' => 'success']);

    }

    public function settings_save_delete_item(Request $request) {

        dd($request -> all());

    }




}
