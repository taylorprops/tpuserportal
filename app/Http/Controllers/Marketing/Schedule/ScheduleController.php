<?php

namespace App\Http\Controllers\Marketing\Schedule;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Marketing\Schedule\Schedule;
use App\Models\Marketing\Schedule\ScheduleMediums;
use App\Models\DocManagement\Resources\LocationData;
use App\Models\Marketing\Schedule\ScheduleCompanies;
use App\Models\Marketing\Schedule\ScheduleRecipients;

class ScheduleController extends Controller
{

    public function schedule(Request $request) {

        $recipients = ScheduleRecipients::orderBy('recipient') -> get();
        $companies = ScheduleCompanies::orderBy('company') -> get();
        $mediums = ScheduleMediums::orderBy('medium') -> get();
        $states = LocationData::activeStates();

        return view('marketing/schedule/schedule', compact('recipients', 'companies', 'mediums', 'states'));

    }

    public function get_schedule(Request $request) {

        $items = Schedule::where('active', TRUE)
        -> with(['company', 'medium', 'recipient'])
        -> orderBy('event_date', 'desc')
        -> get();

        return view('marketing/schedule/get_schedule_html', compact('items'));

    }

    public function save_add_item(Request $request) {

        $validated = $request -> validate([
            'event_date' => 'required',
            'recipient_id' => 'required',
            'states' => 'required|numeric|min:0|not_in:0',
            'company_id' => 'required',
            'medium_id' => 'required',
            'description' => 'required',
            'upload_html' => 'required_without:upload_file',
            'upload_file' => 'required_without:upload_html',
        ],
        [
            'required' => 'Required',
            'states.not_in' => 'State is required',
            'upload_html.required_without' => 'You must paste HTML or upload a file',
            'upload_file.required_without' => 'You must paste HTML or upload a file',
        ]);

        $event_date = $request -> event_date;
        $recipient_id = $request -> recipient_id;
        $state = implode(',', $request -> state);
        $company_id = $request -> company_id;
        $medium_id = $request -> medium_id;
        $description = $request -> description;
        $upload_html = $request -> upload_html;
        $upload_file = $request -> file('upload_file');

        $event = new Schedule();
        $event -> event_date = $event_date;
        $event -> recipient_id = $recipient_id;
        $event -> state = $state;
        $event -> company_id = $company_id;
        $event -> medium_id = $medium_id;
        $event -> description = $description;
        $event -> upload_html = $upload_html ?? null;

        $event -> save();
        $event_id = $event -> id;

        if($upload_file) {

            $upload_file_name = Helper::clean_file_name($upload_file, '', false, true);
            $ext = $upload_file -> getClientOriginalExtension();
            $upload_file_type = strtolower($ext) == 'pdf' ? 'pdf' : 'image';

            $dir = 'marketing/'.$event_id;
            if (! is_dir($dir)) {
                Storage::makeDirectory($dir);
            }
            $upload_file -> storeAs($dir, $upload_file_name);
            $upload_file_location = $dir.'/'.$upload_file_name;
            $upload_file_url = Storage::url($dir.'/'.$upload_file_name);

            $event -> upload_file_name = $upload_file_name;
            $event -> upload_file_type = $upload_file_type;
            $event -> upload_file_location = $upload_file_location;
            $event -> upload_file_url = $upload_file_url;
            $event -> save();

        }




    }


    public function schedule_settings(Request $request) {

        return view('marketing/schedule/schedule_settings');

    }

    public function get_schedule_settings(Request $request) {

        $type = $request -> type;

        if($type == 'recipients') {
            $settings = ScheduleRecipients::orderBy('recipient') -> get();
        } else if($type == 'mediums') {
            $settings = ScheduleMediums::orderBy('medium') -> get();
        } else if($type == 'companies') {
            $settings = ScheduleCompanies::orderBy('company') -> get();
        }

        return view('marketing/schedule/get_schedule_settings_html', compact('type', 'settings'));

    }

    public function settings_get_reassign_options(Request $request) {

        $type = $request -> type;
        $id = $request -> id;

        if($type == 'recipients') {
            $in_use = Schedule::where('recipient_id', $id) -> first();
            if(!$in_use) {
                ScheduleRecipients::find($id) -> delete();
                return response() -> json(['deleted' => true]);
            }
            $settings = ScheduleRecipients::where('id', '!=', $id) -> orderBy('recipient') -> get();
        } else if($type == 'mediums') {
            $in_use = Schedule::where('medium_id', $id) -> first();
            if(!$in_use) {
                ScheduleMediums::find($id) -> delete();
                return response() -> json(['deleted' => true]);
            }
            $settings = ScheduleMediums::where('id', '!=', $id) -> orderBy('medium') -> get();
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

        if($type == 'recipients') {
            ScheduleRecipients::create([
                'recipient' => $value
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

        if($type == 'recipients') {
            ScheduleRecipients::find($id) -> update([
                'recipient' => $value
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
