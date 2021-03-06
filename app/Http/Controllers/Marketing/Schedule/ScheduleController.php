<?php

namespace App\Http\Controllers\Marketing\Schedule;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Mail\General\EmailGeneral;
use App\Models\BrightMLS\BrightOffices;
use App\Models\DocManagement\Resources\LocationData;
use App\Models\Marketing\InHouseAddresses;
use App\Models\Marketing\LoanOfficerAddresses;
use App\Models\Marketing\Schedule\Schedule;
use App\Models\Marketing\Schedule\ScheduleChecklist;
use App\Models\Marketing\Schedule\ScheduleNotes;
use App\Models\Marketing\Schedule\ScheduleSettings;
use App\Models\Marketing\Schedule\ScheduleUploads;
use App\Models\Marketing\TestCenterAddresses;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ScheduleController extends Controller
{

    private $agent_columns_mail_chimp = ['MemberEmail', 'MemberFirstName', 'MemberLastName', 'MemberCity', 'MemberState', 'OfficeKey', 'OfficeMlsId', 'OfficeName'];
    private $agent_columns_send_in_blue = ['MemberEmail', 'MemberFirstName', 'MemberLastName', 'OfficeKey', 'OfficeMlsId', 'OfficeName'];
    private $agent_columns_omni_send = ['MemberEmail', 'MemberFirstName', 'MemberLastName', 'MemberCity', 'MemberCountry', 'MemberState', 'OfficeKey', 'OfficeMlsId', 'OfficeName'];
    private $agent_columns_addresses = ['MemberFirstName', 'MemberLastName', 'MemberAddress1', 'MemberCity', 'MemberState', 'MemberPostalCode', 'MemberType', 'OfficeKey', 'OfficeName'];
    private $agent_columns_in_house = ['first_name', 'last_name', 'street', 'city', 'state', 'zip', 'email', 'cell_phone', 'company', 'start_date'];
    private $agent_columns_psi = ['email', 'first_name', 'last_name'];
    private $loan_officer_columns = ['email', 'first_name', 'last_name', 'county', 'city', 'state'];

    public function schedule(Request $request)
    {

        $settings = ScheduleSettings::orderBy('order') -> get();
        $states = LocationData::activeStates();

        return view('marketing/schedule/schedule', compact('settings', 'states'));
    }

    public function get_schedule(Request $request)
    {

        $search_event_id = $request -> search_event_id;
        $company_id = $request -> company_id;
        $recipient_id = $request -> recipient_id;
        $medium_id = $request -> medium_id;
        $status_id = $request -> status_id;
        $show_completed = $request -> show_completed;

        $events = Schedule::where('active', true)
            -> where(function ($query) use ($search_event_id, $company_id, $recipient_id, $medium_id, $status_id, $show_completed) {

                if ($search_event_id) {
                    $query -> where('id', 'like', '%'.$search_event_id.'%');
                } else {
                    if ($company_id) {
                        $query -> where('company_id', $company_id);
                    }
                    if ($recipient_id) {
                        $query -> where('recipient_id', $recipient_id);
                    }
                    if ($medium_id) {
                        $query -> where('medium_id', $medium_id);
                    }
                    if ($status_id) {
                        $query -> where('status_id', $status_id);
                    } else {
                        if ($show_completed == 'false') {
                            $query -> whereNot('status_id', '24');
                        }
                    }
                }
            })
            -> with(['company', 'notes', 'medium', 'recipient', 'status', 'uploads' => function ($query) {
                $query -> where('active', true);
            }])
            -> orderBy('event_date', 'desc')
            -> get();

        $settings = ScheduleSettings::orderBy('order') -> get();

        if ($events) {

            return view('marketing/schedule/get_schedule_html', compact('events', 'settings'));
        }

        return response() -> json(['status' => 'no event']);
    }

    public function schedule_review(Request $request)
    {

        $settings = ScheduleSettings::orderBy('order') -> get();
        $states = LocationData::activeStates();

        return view('marketing/schedule/schedule_review', compact('settings', 'states'));
    }

    public function get_schedule_review(Request $request)
    {

        $company_id = $request -> company_id;
        $recipient_id = $request -> recipient_id;

        $events = Schedule::where('active', true)
        // -> where(function($query) {
        //         $query -> whereIn('status_id', [38])
        //         -> orWhere(function($query) {
        //             $query -> where('status_id', 37) -> where('status_changed_at', date('Y-m-d'));
        //         })
        //         -> orWhere(function($query) {
        //             $query -> where('status_id', 26) -> where('status_changed_at', date('Y-m-d'));
        //         })
        //         -> orWhereHas('notes', function($query) {
        //             $query -> where('read', false) -> where('user_id', auth() -> user() -> id);
        //         });
        // })
            -> where(function ($query) use ($company_id, $recipient_id) {
                if ($company_id) {
                    $query -> where('company_id', $company_id);
                }
                if ($recipient_id) {
                    $query -> where('recipient_id', $recipient_id);
                }
            })
            -> with(['company', 'notes', 'medium', 'recipient', 'status', 'uploads' => function ($query) {
                $query -> where('active', true);
            }])
            -> orderBy('event_date', 'asc')
            -> get();

        $settings = ScheduleSettings::whereIn('id', [26, 37, 38]) -> orderBy('order') -> get();

        if ($events) {

            return view('marketing/schedule/get_schedule_review_html', compact('events', 'settings'));
        }

        return response() -> json(['status' => 'no event']);

    }

    public function save_item(Request $request)
    {

        $validated = $request -> validate(
            [
                'event_date' => 'required',
                'status_id' => 'required',
                'recipient_id' => 'required',
                'states' => 'required|numeric|min:0|not_in:0',
                'company_id' => 'required',
                'medium_id' => 'required',
                'description' => 'required',
            ],
            [
                'required' => 'Required',
                'states.not_in' => 'State is required',
            ]
        );

        $id = $request -> id;
        $event_date = $request -> event_date;
        $status_id = $request -> status_id;
        $recipient_id = $request -> recipient_id;
        $state = implode(',', $request -> state);
        $company_id = $request -> company_id;
        $medium_id = $request -> medium_id;
        $description = $request -> description;
        $upload_html = $request -> upload_html;
        $upload_file = $request -> file('upload_file');
        $subject_line_a = $request -> subject_line_a;
        $subject_line_b = $request -> subject_line_b;
        $preview_text = $request -> preview_text;
        $tracking_code = $request -> tracking_code;
        $focus_id = $request -> focus_id;
        $goal_id = $request -> goal_id;

        $company = ScheduleSettings::where('id', $company_id) -> first();
        $company = $company -> item;
        $recipient = ScheduleSettings::where('id', $recipient_id) -> first();
        $recipient = $recipient -> item;

        if ($id) {
            $event = Schedule::find($id);
            if ($event -> status_id != $request -> status_id) {
                $event -> status_changed_at = date('Y-m-d H:i:s');
            }
        } else {
            $event = new Schedule();
        }

        $event -> event_date = $event_date;
        $event -> recipient_id = $recipient_id;
        $event -> status_id = $status_id;
        $event -> state = $state;
        $event -> company_id = $company_id;
        $event -> medium_id = $medium_id;
        $event -> description = $description;
        $event -> subject_line_a = $subject_line_a;
        $event -> subject_line_b = $subject_line_b;
        $event -> preview_text = $preview_text;
        $event -> tracking_code = $tracking_code;
        $event -> focus_id = $focus_id;
        $event -> goal_id = $goal_id;

        $event -> save();
        $event_id = $event -> id;

        $uuid = date("Y", strtotime($event_date)).'-'.date("m", strtotime($event_date)).'_'.Helper::get_initials($company).'_'.$event_id;
        $event -> uuid = $uuid;
        $event -> save();

        if ($upload_file) {

            $upload_file_name = Helper::clean_file_name($upload_file, '', false, true);
            $ext = $upload_file -> getClientOriginalExtension();
            $upload_file_type = strtolower($ext) == 'pdf' ? 'pdf' : 'image';

            $dir = 'marketing/'.$event_id;
            if (!is_dir($dir)) {
                Storage::makeDirectory($dir);
            }
            $upload_file -> storeAs($dir, $upload_file_name);
            $upload_file_location = $dir.'/'.$upload_file_name;
            $upload_file_url = Storage::url($dir.'/'.$upload_file_name);

            $upload = new ScheduleUploads();
            $upload -> file_name = $upload_file_name;
            $upload -> file_type = $upload_file_type;
            $upload -> file_location = $upload_file_location;
            $upload -> file_url = $upload_file_url;
            $upload -> event_id = $event_id;
            $upload -> save();
        }

        if ($upload_html) {

            $upload = new ScheduleUploads();
            $upload -> html = $upload_html;
            $upload -> event_id = $event_id;
            $upload -> save();
        }
    }

    public function get_checklist(Request $request)
    {

        $company_id = $request -> company_id;
        $recipient_id = $request -> recipient_id;
        $states = $request -> states;

        $checklists = ScheduleChecklist::where('active', true)
            -> where('company_id', $company_id)
            -> orderBy('order')
            -> get();

        return view('/marketing/schedule/get_event_checklist_html', compact('checklists', 'recipient_id', 'states'));
    }

    public function update_status(Request $request)
    {

        if ($request -> event_id) {
            Schedule::find($request -> event_id) -> update([
                'status_id' => $request -> status_id,
                'status_changed_at' => date('Y-m-d H:i:s'),
            ]);

            return response() -> json(['status' => 'success']);
        }
    }

    public function show_versions(Request $request)
    {

        $event_id = $request -> id;
        $versions = ScheduleUploads::where('event_id', $event_id) -> get();

        return view('marketing/schedule/get_versions_html', compact('event_id', 'versions'));
    }

    public function save_add_version(Request $request)
    {

        $validated = $request -> validate(
            [
                'upload_version_html' => 'required_without:upload_version_file',
                'upload_version_file' => 'required_without:upload_version_html',
            ],
            [
                'upload_version_html.required_without' => 'You must paste HTML or upload a file',
                'upload_version_file.required_without' => 'You must paste HTML or upload a file',
            ]
        );

        $event_id = $request -> event_id;

        $upload_file = $request -> file('upload_version_file');
        $upload_html = $request -> upload_version_html;

        ScheduleUploads::where('event_id', $event_id) -> update([
            'accepted_version' => false,
        ]);

        if ($upload_file) {

            $upload_file_name = Helper::clean_file_name($upload_file, '', false, true);
            $ext = $upload_file -> getClientOriginalExtension();
            $upload_file_type = strtolower($ext) == 'pdf' ? 'pdf' : 'image';

            $dir = 'marketing/'.$event_id;
            if (!is_dir($dir)) {
                Storage::makeDirectory($dir);
            }
            $upload_file -> storeAs($dir, $upload_file_name);
            $upload_file_location = $dir.'/'.$upload_file_name;
            $upload_file_url = Storage::url($dir.'/'.$upload_file_name);

            $upload = new ScheduleUploads();
            $upload -> file_name = $upload_file_name;
            $upload -> file_type = $upload_file_type;
            $upload -> file_location = $upload_file_location;
            $upload -> file_url = $upload_file_url;
            $upload -> event_id = $event_id;
            $upload -> accepted_version = true;
            $upload -> save();
        }

        if ($upload_html) {

            $upload = new ScheduleUploads();
            $upload -> html = $upload_html;
            $upload -> event_id = $event_id;
            $upload -> accepted_version = true;
            $upload -> save();
        }

        return response() -> json(['status' => 'success']);
    }

    public function delete_version(Request $request)
    {

        $version_id = $request -> version_id;

        $delete = ScheduleUploads::find($version_id) -> update([
            'active' => false,
            'accepted_version' => false,
        ]);

        return response() -> json(['status' => 'success']);
    }

    public function reactivate_version(Request $request)
    {

        $version_id = $request -> version_id;

        $reactivate_version = ScheduleUploads::find($version_id) -> update([
            'active' => true,
        ]);

        return response() -> json(['status' => 'success']);
    }

    public function mark_version_accepted(Request $request)
    {

        $version_id = $request -> version_id;
        $event_id = $request -> event_id;

        $update = ScheduleUploads::where('event_id', $event_id) -> update([
            'accepted_version' => false,
        ]);

        $mark_accepted = ScheduleUploads::find($version_id) -> update([
            'accepted_version' => true,
        ]);

        return response() -> json(['status' => 'success']);
    }

    public function send_email(Request $request)
    {

        $event_id = $request -> email_event_id;

        $event = Schedule::with(['recipient', 'company', 'uploads' => function ($query) {
            $query -> where('accepted_version', true);
        }])
            -> find($event_id);

        $tos = explode(',', $request -> email_to);
        $subject = 'Email to review | '.$request -> email_subject;
        $preview_text = $request -> email_preview_text;

        $body = null;
        $attachment = null;

        $upload = $event -> uploads -> first();

        $html = $upload -> html;

        if ($html) {
            $preview_html = '
            <div style="display: none; max-height: 0px; overflow: hidden;">
                '.$preview_text.'
            </div>
            <div style="display: none; max-height: 0px; overflow: hidden;">
                &#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;
            </div>';
            $details = 'Send Date: '.$event -> event_date.'<br>
            From '.$event -> company -> item.' to '.$event -> recipient -> item.' | ID: '.$event_id;

            $body = preg_replace('/(<body\s.*>)/', '$1'.$preview_html.$details, $html);
        } else {
            $attachment = [Storage::path($upload -> file_location)];
            $body = 'See Attached';
        }

        $message = [
            'company' => 'Taylor Properties',
            'subject' => $subject,
            'from' => ['email' => 'internal@taylorprops.com', 'name' => 'Taylor Properties'],
            'body' => $body,
            'attachments' => $attachment,
            'bcc' => config('global.contact_email_title_bcc_addresses'),
        ];

        Mail::to($tos)
            -> send(new EmailGeneral($message));
    }

    public function get_list_hubspot_agents(Request $request)
    {

        $sender = $request -> sender;
        $recipient_id = $request -> recipient_id;
        $states = explode(',', $request -> states);

        $columns = ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Company', 'Office Address', 'Office City', 'Office State', 'Office Zip'];

        $offices = BrightOffices::select(['OfficeKey', 'OfficeMlsId', 'OfficeName', 'OfficeAddress1', 'OfficeCity', 'OfficeStateOrProvince', 'OfficePostalCode'])
            -> whereIn('OfficeStateOrProvince', $states)
            -> whereHas('agents', function (Builder $query) {
                $query -> where('MemberType', 'Agent')
                    -> where('active', 'yes')
                    -> whereNotNull('MemberEmail');
            })
            -> with(['agents' => function ($query) {
                $query -> where('MemberType', 'Agent')
                    -> where('active', 'yes')
                    -> whereNotNull('MemberEmail')
                    -> select(['MemberKey', 'MemberFirstName', 'MemberLastName', 'MemberEmail', 'MemberPreferredPhone', 'OfficeKey']);
            }])
            -> get();

        $file_name = 'agent_list_'.time().'.csv';
        $file = Storage::path('/tmp/'.$file_name);
        $handle = fopen($file, 'w');

        fputcsv($handle, $columns, ',');

        foreach ($offices as $office) {
            foreach ($office -> agents as $agent) {
                $data = $agent -> makeHidden(['OfficeKey']) -> toArray();
                $data['OfficeName'] = $office -> OfficeName;
                $data['OfficeAddress1'] = $office -> OfficeAddress1;
                $data['OfficeCity'] = $office -> OfficeCity;
                $data['OfficeStateOrProvince'] = $office -> OfficeStateOrProvince;
                $data['OfficePostalCode'] = $office -> OfficePostalCode;
                fputcsv($handle, $data, ',');
            }
        }

        $employees = [
            ['1', 'Delia', 'Abrams', 'delia@taylorprops.com', '', '', '', '', '', '', ''],
            ['2', 'Kyle', 'Abrams', 'kyle@taylorprops.com', '', '', '', '', '', '', ''],
            ['3', 'Robb', 'Taylor', 'senorrobb@yahoo.com', '', '', '', '', '', '', ''],
            ['4', 'Mike', 'Taylor', 'miketaylor0101@gmail.com', '', '', '', '', '', '', ''],
        ];

        foreach ($employees as $employee) {
            fputcsv($handle, $employee, ',');
        }

    }

    public function get_list(Request $request)
    {

        $sender = $request -> sender;
        $recipient_id = $request -> recipient_id;
        $type = $request -> type;
        $states = explode(',', $request -> states);

        if ($type == 'emails') {

            if ($sender == 'mailchimp') {
                $this -> agent_columns = $this -> agent_columns_mail_chimp;
                $employees = [
                    ['delia@taylorprops.com', 'Delia', 'Abrams', '', '', '', ''],
                    ['kyle@taylorprops.com', 'Kyle', 'Abrams', '', '', '', ''],
                    ['senorrobb@yahoo.com', 'Robb', 'Taylor', '', '', '', ''],
                    ['miketaylor0101@gmail.com', 'Mike', 'Taylor', '', '', '', ''],
                ];
            } else if ($sender == 'sendinblue') {
                $this -> agent_columns = $this -> agent_columns_send_in_blue;
                $employees = [
                    ['delia@taylorprops.com', 'Delia', 'Abrams', '', ''],
                    ['kyle@taylorprops.com', 'Kyle', 'Abrams', '', ''],
                    ['senorrobb@yahoo.com', 'Robb', 'Taylor', '', ''],
                    ['miketaylor0101@gmail.com', 'Mike', 'Taylor', '', ''],
                ];
            } else if ($sender == 'omnisend') {
                $this -> agent_columns = $this -> agent_columns_omni_send;
                $employees = [
                    ['delia@taylorprops.com', 'Delia', 'Abrams', '', '', '', '', ''],
                    ['kyle@taylorprops.com', 'Kyle', 'Abrams', '', '', '', '', ''],
                    ['senorrobb@yahoo.com', 'Robb', 'Taylor', '', '', '', '', ''],
                    ['miketaylor0101@gmail.com', 'Mike', 'Taylor', '', '', '', '', ''],
                ];
            }

            $file_name = 'agent_list_'.time().'.csv';
            if ($recipient_id == '42' || $recipient_id == '43') {
                $file_name = 'loan_officer_list_'.time().'.csv';
            }
            $file = Storage::path('/tmp/'.$file_name);
            $handle = fopen($file, 'w');

            if ($recipient_id == '13' /* 'In-House Agents' */) {

                $this -> agent_columns = $this -> agent_columns_in_house;
                $agents = InHouseAddresses::select($this -> agent_columns_in_house) -> get();

                $header_columns = $this -> rename_columns($this -> agent_columns);
                fputcsv($handle, $header_columns, ',');

                foreach ($agents as $agent) {
                    fputcsv($handle, $agent -> toArray(), ',');
                }

                $employees = [
                    ['Delia', 'Abrams', '', '', '', '', 'delia@taylorprops.com', '', '', ''],
                    ['Kyle', 'Abrams', '', '', '', '', 'kyle@taylorprops.com', '', '', ''],
                    ['Robb', 'Taylor', '', '', '', '', 'senorrobb@yahoo.com', '', '', ''],
                    ['Mike', 'Taylor', '', '', '', '', 'miketaylor0101@gmail.com', '', '', ''],
                ];

                foreach ($employees as $employee) {
                    fputcsv($handle, $employee, ',');
                }
            } else if ($recipient_id == '12' /* PSI */) {

                $agents = TestCenterAddresses::select($this -> agent_columns_psi) -> get();

                $header_columns = $this -> rename_columns($this -> agent_columns_psi);
                fputcsv($handle, $header_columns, ',');

                foreach ($agents as $agent) {
                    fputcsv($handle, $agent -> toArray(), ',');
                }

                $employees = [
                    ['Delia', 'Abrams', '', '', '', '', 'delia@taylorprops.com', '', '', ''],
                    ['Kyle', 'Abrams', '', '', '', '', 'kyle@taylorprops.com', '', '', ''],
                    ['Robb', 'Taylor', '', '', '', '', 'senorrobb@yahoo.com', '', '', ''],
                    ['Mike', 'Taylor', '', '', '', '', 'miketaylor0101@gmail.com', '', '', ''],
                ];

                foreach ($employees as $employee) {
                    fputcsv($handle, $employee, ',');
                }

            } else if ($recipient_id == '42' || $recipient_id == '43' /* Loan Officers */) {

                $loan_officers = LoanOfficerAddresses::select($this -> loan_officer_columns) -> where('active', 'yes')
                    -> get();

                $header_columns = $this -> rename_columns($this -> loan_officer_columns);
                fputcsv($handle, $header_columns, ',');

                foreach ($loan_officers as $loan_officer) {
                    fputcsv($handle, $loan_officer -> toArray(), ',');
                }

                $employees = [
                    ['delia@taylorprops.com', 'Delia', 'Abrams', '', '', ''],
                    ['kyle@taylorprops.com', 'Kyle', 'Abrams', '', '', ''],
                    ['senorrobb@yahoo.com', 'Robb', 'Taylor', '', '', ''],
                    ['miketaylor0101@gmail.com', 'Mike', 'Taylor', '', '', ''],
                ];

                foreach ($employees as $employee) {
                    fputcsv($handle, $employee, ',');
                }

            } else {

                $offices = BrightOffices::select(['OfficeKey', 'OfficeMlsId', 'OfficeName', 'OfficeAddress1', 'OfficeCity', 'OfficeStateOrProvince', 'OfficePostalCode'])
                    -> whereIn('OfficeStateOrProvince', $states)
                    -> whereHas('agents', function (Builder $query) {
                        $query -> where('MemberType', 'Agent')
                            -> where('active', 'yes')
                            -> where('MemberEmail', '!=', '')
                            -> whereNotNull('MemberEmail');
                    })
                    -> with(['agents' => function ($query) {
                        $query -> where('MemberType', 'Agent')
                            -> where('active', 'yes')
                            -> where('MemberEmail', '!=', '')
                            -> whereNotNull('MemberEmail')
                            -> select($this -> agent_columns);
                    }])
                    -> get();

                $header_columns = $this -> rename_columns($this -> agent_columns);
                fputcsv($handle, $header_columns, ',');

                foreach ($offices as $office) {
                    foreach ($office -> agents as $agent) {
                        fputcsv($handle, $agent -> toArray(), ',');
                    }
                }

                $employees = [
                    ['delia@taylorprops.com', 'Delia', 'Abrams', '', '', ''],
                    ['kyle@taylorprops.com', 'Kyle', 'Abrams', '', '', ''],
                    ['senorrobb@yahoo.com', 'Robb', 'Taylor', '', '', ''],
                    ['miketaylor0101@gmail.com', 'Mike', 'Taylor', '', '', ''],
                ];

                foreach ($employees as $employee) {
                    fputcsv($handle, $employee, ',');
                }
            }

        } else if ($type == 'addresses') {

            $file_name = 'agent_address_list_'.time().'.csv';
            $file = Storage::path('/tmp/'.$file_name);
            $handle = fopen($file, 'w');

            $offices = BrightOffices::select(['OfficeKey', 'OfficeMlsId', 'OfficeName', 'OfficeAddress1', 'OfficeCity', 'OfficeStateOrProvince', 'OfficePostalCode'])
                -> whereIn('OfficeStateOrProvince', $states)
                -> with(['agents' => function ($query) {
                    $query -> where('MemberType', 'Agent')
                        -> where('active', 'yes')
                        -> select($this -> agent_columns_addresses);
                }])
                -> get();

            $header_columns = $this -> rename_columns($this -> agent_columns_addresses);
            fputcsv($handle, $header_columns, ',');

            foreach ($offices as $office) {
                if (count($office -> agents) > 0) {
                    foreach ($office -> agents as $agent) {
                        if ($agent -> MemberAddress1 != '') {
                            fputcsv($handle, $agent -> toArray(), ',');
                        }
                    }
                }
            }

        }

        $file_location = '/storage/tmp/'.$file_name;
        $count = count(file(Storage::path('/tmp/'.$file_name))) - 1;

        return response() -> json([
            'count' => $count,
            'location' => $file_location,
        ]);
    }

    public function rename_columns($columns)
    {

        $new_columns = [];
        foreach ($columns as $column) {
            if ($column == 'MemberFirstName' || $column == 'first_name') {
                $new_columns[] = 'First_Name';
            } else if ($column == 'MemberLastName' || $column == 'last_name') {
                $new_columns[] = 'Last_Name';
            } else if ($column == 'MemberEmail' || $column == 'email') {
                $new_columns[] = 'Email';
            } else if ($column == 'OfficeName') {
                $new_columns[] = 'Company';
            } else {
                $new_columns[] = $column;
            }
        }
        return $new_columns;

    }

    public function rename_dynamic_fields_and_text($text)
    {

        // $text = str_replace('{{ contact.EMAIL }}', '%%Email%%', $text);
        // $text = str_replace('{{contact.EMAIL}}', '%%Email%%', $text);
        // $text = str_replace('{{%20contact.EMAIL%20}}', '%%Email%%', $text);
        $text = preg_replace('/\{\{[\s]*contact.EMAIL[\s]*\}\}/', '%%Email%%', $text);
        $text = str_replace('[[contact.email]]', '%%Email%%', $text);
        $text = str_replace('[First Name]', '%%First_Name%%', $text);
        $text = str_replace('{First Name}', '%%First_Name%%', $text);

        $text = preg_replace('/[(]*866[)]*(\s|\-|\.)913[-\s\.]*4095/', '(800) 544-0286', $text); // HT
        $text = preg_replace('/[(]*800[)]*(\s|\-|\.)351[-\s\.]*5755/', '(800) 498-3199', $text); // HF
        $text = preg_replace('/[(]*301[)]*(\s|\-|\.)970[-\s\.]*2447/', '(800) 277-3588', $text); // TP
        $text = preg_replace('/[(]*800[)]*(\s|\-|\.)590[-\s\.]*0925/', '(800) 277-3588', $text); // TP

        return $text;

    }

    public function calendar_get_events(Request $request)
    {

        $company_id = $request -> company_id;
        $recipient_id = $request -> recipient_id;
        $medium_id = $request -> medium_id;

        $events = Schedule::select(['id', 'company_id', 'recipient_id', 'event_date as start']) -> where('active', true)
            -> where(function ($query) use ($company_id, $recipient_id, $medium_id) {
                if ($company_id) {
                    $query -> where('company_id', $company_id);
                }
                if ($recipient_id) {
                    $query -> where('recipient_id', $recipient_id);
                }
                if ($medium_id) {
                    $query -> where('medium_id', $medium_id);
                }
            })
            -> with(['company', 'recipient'])
            -> orderBy('event_date', 'desc')
            -> get();

        foreach ($events as $event) {
            $event -> className = [
                'text-'.$event -> company -> color.'-700',
                'bg-'.$event -> company -> color.'-50',
                'border-'.$event -> company -> color.'-200',
                'rounded',
                'flex',
                'items-center',
                'overflow-hidden',
                'pt-1',
            ];
            $event -> textColor = 'inherit';
            // $event -> backgroundColor = 'inherit';
            // $event -> borderColor = 'inherit';
            $event -> title = Helper::get_initials($event -> company -> item).' -> '.$event -> recipient -> item;
        }

        return response() -> json($events);
    }

    public function delete_event(Request $request)
    {

        $delete = Schedule::find($request -> id) -> update([
            'active' => 0,
        ]);

        return response() -> json(['status' => 'success']);
    }

    public function clone_event(Request $request)
    {

        $event_id = $request -> id;

        $event = Schedule::find($event_id);
        $clone = $event -> replicate();
        $clone -> save();
        $clone_id = $clone -> id;

        // $uploads = ScheduleUploads::where('event_id', $event_id) -> get();

        // foreach($uploads as $upload) {

        //     $upload_clone = $upload -> replicate();
        //     $upload_clone -> event_id = $clone_id;

        //     if($upload -> html == '') {
        //         $dir = 'marketing/'.$clone_id;
        //         if (! is_dir($dir)) {
        //             Storage::makeDirectory($dir);
        //         }

        //         $upload_file_name = basename(Storage::path($upload -> file_location));

        //         Storage::copy($upload -> file_location, $dir.'/'.$upload_file_name);

        //         $upload_file_location = $dir.'/'.$upload_file_name;
        //         $upload_file_url = Storage::url($dir.'/'.$upload_file_name);

        //         $upload_clone -> file_location = $upload_file_location;
        //         $upload_clone -> file_url = $upload_file_url;
        //     }

        //     $upload_clone -> save();
        // }

        return response() -> json(['id' => $clone_id]);
    }

    public function export_medium(Request $request)
    {

        $event = Schedule::with(['uploads' => function ($query) {
            $query -> where('accepted_version', true);
        }])
            -> find($request -> id);

        $html = $event -> uploads -> first() -> html;
        $file_url = $event -> uploads -> first() -> file_url;

        if ($html != '') {

            $html = $this -> rename_dynamic_fields_and_text($html);

            $preview_text = $this -> rename_dynamic_fields_and_text($event -> preview_text);

            $preview_html = '<div style="display: none; max-height: 0px; overflow: hidden;">
            '.$preview_text.'
            </div>
            <div style="display: none; max-height: 0px; overflow: hidden;">
            &#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;
            &#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;
            &#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;
            </div>';

            $html = preg_replace('/(<body\s.*>)/', '$1'.$preview_html, $html);

            $unsubscribe = '';
            if ($event -> recipient_id != '13') {
                $unsubscribe = '
                <table style="width: 600px; margin-left: auto; margin-right: auto">
                    <tr>
                        <td style="padding: 10px; text-align:center;"><a href="tag://%%unsubscribe%%" style="color: #717171; font-size: 11px; font-family: Arial">Unsubscribe</td>
                    </tr>
                </table>';
            }

            $tracking_code = $event -> tracking_code;
            $html = preg_replace('/(<\/body>)/', $unsubscribe.$tracking_code.'$1', $html);

            return compact('html');

        } else if ($file_url != '') {
            return compact('file_url');
        }

    }

    public function get_view_div_details(Request $request)
    {

        $event = Schedule::with(['uploads' => function ($query) {
            $query -> where('accepted_version', true) -> first();
        }])
            -> find($request -> id) -> toJson();

        return $event;

    }

    public function get_notes(Request $request)
    {
        $notes = ScheduleNotes::where('event_id', $request -> event_id)
            -> with(['user'])
            -> orderBy('created_at', 'desc')
            -> get();

        return view('marketing/schedule/get_notes_html', compact('notes'));
    }

    public function add_notes(Request $request)
    {

        ScheduleNotes::create([
            'event_id' => $request -> event_id,
            'notes' => $request -> notes,
            'user_id' => auth() -> user() -> id,
        ]);
    }

    public function delete_note(Request $request)
    {
        ScheduleNotes::find($request -> id) -> delete();
    }

    public function mark_note_read(Request $request)
    {
        ScheduleNotes::find($request -> note_id) -> update([
            'read' => true,
        ]);
    }

    public function get_notification_count(Request $request)
    {

        if (auth() -> user() -> level != 'owner') {
            $status_count = 0;
            if (auth() -> user() -> level == 'marketing') {
                $status_count = Schedule::whereIn('status_id', ['37', '26']) -> where('active', true) -> count();
            } else if (auth() -> user() -> level == 'super_admin') {
                $status_count = Schedule::whereIn('status_id', ['25']) -> where('active', true) -> count();
            }
            $notes_count = ScheduleNotes::where('user_id', '!=', auth() -> user() -> id) -> where('read', false) -> count();
            $count = $status_count + $notes_count;
            return response() -> json(['count' => $count]);
        }
        return 0;
    }

    public function marketing_notes(Request $request)
    {

        return view('marketing/schedule/marketing_notes');

    }

    public function schedule_settings(Request $request)
    {

        return view('marketing/schedule/schedule_settings');
    }

    public function get_schedule_settings(Request $request)
    {

        $settings = ScheduleSettings::orderBy('category') -> orderBy('order') -> get();

        $categories = [];
        foreach ($settings as $setting) {
            $categories[] = $setting -> category;
        }
        $categories = array_unique($categories);

        return view('marketing/schedule/get_schedule_settings_html', compact('categories', 'settings'));
    }

    public function settings_get_reassign_options(Request $request)
    {

        $category = $request -> category;
        $id = $request -> id;

        if ($category == 'users') {
            ScheduleSettings::find($id) -> delete();
            return response() -> json(['deleted' => true]);
        }

        $in_use = Schedule::where($category.'_id', $id) -> first();

        if (!$in_use) {
            ScheduleSettings::find($id) -> delete();
            return response() -> json(['deleted' => true]);
        }

        $settings = ScheduleSettings::where('id', '!=', $id) -> where('category', $category) -> orderBy('order') -> get();

        return compact('settings');
    }

    public function settings_save_add_item(Request $request)
    {

        $category = $request -> type;
        $value = $request -> value;

        $setting = ScheduleSettings::where('category', $category) -> first();
        $has_color = $setting -> has_color;
        $has_email = $setting -> has_email;

        ScheduleSettings::create([
            'category' => $category,
            'item' => $value,
            'has_color' => $has_color,
            'has_email' => $has_email,
        ]);

        return response() -> json(['status' => 'success']);
    }

    public function settings_save_edit_item(Request $request)
    {
        $id = $request -> id;
        $value = $request -> value;
        $field = $request -> field;

        ScheduleSettings::find($id) -> update([
            $field => $value,
        ]);

        return response() -> json(['status' => 'success']);
    }

    public function settings_update_order(Request $request)
    {

        foreach (json_decode($request -> settings, true) as $key => $value) {
            ScheduleSettings::find($value['id'])
                -> update([
                    'order' => $value['order'],
                ]);
        }

        return response() -> json(['status' => 'success']);
    }

    public function settings_reassign_items(Request $request)
    {

        $new_setting_id = $request -> new_setting_id;
        $deleted_setting_id = $request -> deleted_setting_id;
        $category = $request -> category;

        $reassign = Schedule::where($category.'_id', $deleted_setting_id) -> update([
            $category.'_id' => $new_setting_id,
        ]);

        ScheduleSettings::find($deleted_setting_id) -> delete();

        return response() -> json(['status' => 'success']);
    }
}
