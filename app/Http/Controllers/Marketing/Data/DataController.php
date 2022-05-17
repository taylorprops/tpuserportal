<?php

namespace App\Http\Controllers\Marketing\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InHouseAddressesImport;
use App\Models\BrightMLS\BrightOffices;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Imports\TestCenterAddressesImport;
use App\Models\Marketing\InHouseAddresses;
use App\Models\BrightMLS\BrightAgentRoster;
use App\Models\Marketing\TestCenterAddresses;
use App\Models\Marketing\LoanOfficerAddresses;
use App\Models\Marketing\TestCenterAddressesTemp;
use App\Models\DocManagement\Resources\LocationData;

class DataController extends Controller
{

    private $agent_columns = ['MemberFullName', 'MemberFirstName', 'MemberLastName', 'MemberEmail', 'MemberPreferredPhone', 'MemberAddress1', 'MemberCity', 'MemberState', 'MemberPostalCode', 'MemberMlsId', 'OfficeName', 'OfficeKey', 'OfficeMlsId', 'MemberType'];

    private $agent_columns_mail_chimp = ['MemberEmail', 'MemberFirstName', 'MemberLastName', 'MemberCity', 'MemberState', 'OfficeKey', 'OfficeMlsId'];
    private $agent_columns_send_in_blue = ['MemberEmail', 'MemberFirstName', 'MemberLastName', 'OfficeKey', 'OfficeMlsId'];
    private $agent_columns_omni_send = ['MemberEmail', 'MemberFirstName', 'MemberLastName', 'MemberCity', 'MemberCountry', 'MemberState', 'OfficeKey', 'OfficeMlsId'];

    private $loan_officer_columns = ['id', 'first_name', 'last_name', 'full_name', 'email', 'phone', 'ext', 'city', 'state', 'county'];

    private $test_center_columns = ['id', 'first_name', 'last_name', 'full_name', 'email', 'phone', 'street', 'city', 'state', 'zip'];
    private $test_center_columns_send_in_blue = ['email', 'first_name', 'last_name'];

    public function address_database(Request $request)
    {
        $states = LocationData::ActiveStates();
        $states_loan_officers = LoanOfficerAddresses::select(['state'])
        -> groupBy('state')
        -> orderBy('state')
        -> pluck('state')
        -> toArray();

        $states_test_center = ['MD', 'VA'];

        $recently_added_emails = BrightAgentRoster::select(DB::raw('count(*) as added, date_format(created_at, "%Y-%m-%d") as date_added'))
        -> where('created_at' , '>', date('Y-m-d', strtotime('-6 month')))
        -> groupBy('date_added')
        -> orderBy('date_added', 'desc')
        -> get();

        $purged_emails = BrightAgentRoster::select(DB::raw('count(*) as purged, date_purged'))
        -> whereNotNull('date_purged')
        -> where('date_purged' , '>', date('Y-m-d', strtotime('-6 month')))
        -> groupBy('date_purged')
        -> orderBy('date_purged', 'desc')
        -> get();

        return view('/marketing/data/address_database', compact('states', 'states_loan_officers', 'states_test_center', 'recently_added_emails', 'purged_emails'));
    }

    public function get_results(Request $request)
    {
        $sender = $request -> sender;
        $list_group = $request -> list_group;
        $list_type = $request -> list_type;
        $states = $request -> states;
        $locations = $request -> counties;
        $office_codes = $request -> offices ?? null;
        $offices = null;
        $results_count = '0';
        $file_location = null;

        if($list_group == 'test_center') {

            $state = $request -> state;
            $location = null;

            if($list_type == 'email') {
                $agent_columns = $this -> test_center_columns_send_in_blue;
            } else if($list_type == 'address') {
                $agent_columns = $this -> test_center_columns;
            }

            $agents = TestCenterAddresses::select($agent_columns) -> where('state', $state) -> get();
            $results_count = 0;
            $file_name = 'test_center_list_'.time().'.csv';
            $file = Storage::path('/tmp/'.$file_name);
            $handle = fopen($file, 'w');
            fputcsv($handle, $agent_columns, ',');
            foreach ($agents as $agent) {
                $results_count += 1;
                fputcsv($handle, $agent -> toArray(), ',');
            }

        }

        if($list_type == 'email') {
            if($sender == 'mailchimp') {
                $this -> agent_columns = $this -> agent_columns_mail_chimp;
            } else if($sender == 'sendinblue') {
                $this -> agent_columns = $this -> agent_columns_send_in_blue;
            } else if($sender == 'omnisend') {
                $this -> agent_columns = $this -> agent_columns_omni_send;
            }
        }

        if ($locations) {
            $counties = [];
            foreach ($locations as $location) {
                $parts = explode('-', $location);
                $counties[] = ['state' => $parts[0], 'county' => $parts[1]];
            }
            $counties = json_decode(json_encode($counties));

            if ($list_group == 'agents') {
                if ($office_codes) {
                    $offices = $this -> get_offices('', $list_type, '', $office_codes);
                } else {
                    $offices = $this -> get_offices('', $list_type, $counties, null);
                }

                $results_count = 0;
                $file_name = 'agent_list_'.time().'.csv';
                $file = Storage::path('/tmp/'.$file_name);
                $handle = fopen($file, 'w');
                fputcsv($handle, $this -> agent_columns, ',');
                foreach ($offices as $office) {
                    foreach ($office -> agents as $agent) {
                        $results_count += 1;
                        fputcsv($handle, $agent -> toArray(), ',');
                    }
                }
            } elseif ($list_group == 'loan_officers') {
                $loan_officers = LoanOfficerAddresses::select($this -> loan_officer_columns)
                -> where(function ($query) use ($counties) {
                    foreach ($counties as $county) {
                        $query -> orWhere(function ($query) use ($county) {
                            $query -> where('state', $county -> state)
                            -> where(function ($query) use ($county) {
                                $query -> where('county', $county -> county);
                            });
                        });
                    }
                })
                -> orderBy('state')
                -> orderBy('county')
                -> get();

                $file_name = 'loan_officer_list_'.time().'.csv';
                $file = Storage::path('/tmp/'.$file_name);
                $handle = fopen($file, 'w');
                fputcsv($handle, $this -> loan_officer_columns, ',');

                $results_count = 0;
                foreach ($loan_officers as $loan_officer) {
                    fputcsv($handle, $loan_officer -> toArray(), ',');
                    $results_count += 1;
                }
            }

        }

        $file_location = '/storage/tmp/'.$file_name;

        return view('/marketing/data/get_results_html', compact('results_count', 'list_type', 'file_location'));
    }

    public function get_recently_added(Request $request)
    {
        $select = ['MemberKey', 'MemberFirstName', 'MemberLastName', 'MemberEmail', 'OfficeKey', 'OfficeName', 'created_at'];
        $export_cols = ['MemberKey', 'MemberFirstName', 'MemberLastName', 'MemberEmail', 'OfficeKey', 'OfficeName', 'created_at', 'office_street', 'office_street2', 'office_city', 'office_state', 'office_zip'];

        $start = $request -> start.' 00:00:00';
        $end = $request -> end ?? date('Y-m-d');
        $end .= ' 23:59:59';

        $recently_added = BrightAgentRoster::select($select)
        -> whereBetween('created_at', [$start, $end])
        -> whereHas('office')
        -> with(['office:OfficeKey,OfficeAddress1,OfficeAddress2,OfficeCity,OfficeStateOrProvince,OfficePostalCode,OfficeCounty'])
        -> get();

        $file_name = 'recently_added_list_'.time().'.csv';
        $file = Storage::path('/tmp/'.$file_name);
        $handle = fopen($file, 'w');
        fputcsv($handle, $export_cols, ',');

        $results_count = 0;
        foreach ($recently_added as $agent) {
            $office = $agent -> office;
            $agent -> office_street = $office -> OfficeAddress1;
            $agent -> office_street2 = $office -> OfficeAddress2;
            $agent -> office_city = $office -> OfficeCity;
            $agent -> office_state = $office -> OfficeStateOrProvince;
            $agent -> office_zip = $office -> OfficePostalCode;
            $agent = collect($agent);
            $agent -> forget(['office']);
            fputcsv($handle, $agent -> toArray(), ',');
            $results_count += 1;
        }

        $file_location = '/storage/tmp/'.$file_name;

        return response() -> json(['url' => $file_location]);
    }

    public function get_purged(Request $request)
    {
        $select = ['MemberKey', 'MemberFirstName', 'MemberLastName', 'MemberEmail', 'date_purged'];

        $start = $request -> start.' 00:00:00';
        $end = $request -> end ?? date('Y-m-d');
        $end .= ' 23:59:59';

        $purged = BrightAgentRoster::select($select)
        -> where('active', 'no')
        -> whereBetween('date_purged', [$start, $end])
        -> get();

        $file_name = 'purged_list_'.time().'.csv';
        $file = Storage::path('/tmp/'.$file_name);
        $handle = fopen($file, 'w');
        fputcsv($handle, $select, ',');

        $results_count = 0;
        foreach ($purged as $agent) {
            fputcsv($handle, $agent -> toArray(), ',');
            $results_count += 1;
        }

        $file_location = '/storage/tmp/'.$file_name;

        return response() -> json(['url' => $file_location]);
    }

    public function location_data(Request $request)
    {
        $states = $request -> states;
        //$counties_data = $request -> counties ?? [];
        $counties = [];

        if ($states) {
            if ($request -> list_group == 'agents') {
                $counties = LocationData::select(['county', 'state'])
                -> whereIn('state', $states)
                -> where('county', '!=', '')
                -> groupBy('state')
                -> groupBy('county')
                -> orderBy('state')
                -> orderBy('county')
                -> get();
            } elseif ($request -> list_group == 'loan_officers') {
                $counties = LoanOfficerAddresses::select(['county', 'state'])
                -> whereIn('state', $states)
                -> groupBy('state')
                -> groupBy('county')
                -> orderBy('state')
                -> orderBy('county')
                -> get();
            }
        }

        return compact('counties');
    }

    public function search_offices(Request $request)
    {
        $search_value = $request -> val;
        $list_type = $request -> list_type;
        $counties = json_decode($request -> counties);
        $offices = null;

        if ($search_value != '') {
            $offices = $this -> get_offices($search_value, $list_type, $counties, null);
        }

        return view('/marketing/data/office_search_results_html', compact('offices'));
    }

    public function get_offices($search_value, $list_type, $counties, $office_codes)
    {
        $offices = null;
        /* $state_and_county = [];
        $states = []; */

        //dd($search_value, $list_type, $counties, $office_codes);

        if ($office_codes) {
            $offices = BrightOffices::whereIn('OfficeMlsId', $office_codes)
            -> whereHas('agents', function (Builder $query) use ($list_type) {
                $query -> where('MemberType', 'Agent');
                if ($list_type == 'email') {
                    $query -> where('MemberEmail', '!=', '')
                    -> whereNotNull('MemberEmail');
                } elseif ($list_type == 'address') {
                    $query -> where('MemberAddress1', '!=', '')
                    -> whereNotNull('MemberAddress1');
                }
            })
            -> with(['agents' => function ($query) use ($list_type) {
                $query -> where('MemberType', 'Agent');
                if ($list_type == 'email') {
                    $query -> where('MemberEmail', '!=', '')
                    -> whereNotNull('MemberEmail');
                } elseif ($list_type == 'address') {
                    $query -> where('MemberAddress1', '!=', '')
                    -> whereNotNull('MemberAddress1');
                }
                $query -> select($this -> agent_columns);
            }])
            -> get();
        } else {
            $offices = BrightOffices::select(['OfficeKey', 'OfficeMlsId', 'OfficeName', 'OfficeAddress1', 'OfficeCity', 'OfficeStateOrProvince', 'OfficePostalCode'])
            -> where(function ($query) use ($search_value) {
                if ($search_value != '') {
                    $query -> where('OfficeName', 'like', '%'.$search_value.'%');
                }
            })
            -> where(function ($query) use ($counties) {
                foreach ($counties as $county) {
                    $query -> orWhere(function ($query) use ($county) {
                        $query -> where('OfficeStateOrProvince', $county -> state)
                        -> where(function ($query) use ($county) {
                            if ($county -> state != 'DC') {
                                $query -> where('OfficeCounty', $county -> county);
                            }
                        });
                    });
                }
            })
            -> whereHas('agents', function (Builder $query) use ($list_type) {
                $query -> where('MemberType', 'Agent');
                if ($list_type == 'email') {
                    $query -> where('MemberEmail', '!=', '')
                    -> whereNotNull('MemberEmail');
                } elseif ($list_type == 'address') {
                    $query -> where('MemberAddress1', '!=', '')
                    -> whereNotNull('MemberAddress1');
                }
            })
            -> with(['agents' => function ($query) use ($list_type) {
                $query -> where('MemberType', 'Agent');
                if ($list_type == 'email') {
                    $query -> where('MemberEmail', '!=', '')
                    -> whereNotNull('MemberEmail');
                } elseif ($list_type == 'address') {
                    $query -> where('MemberAddress1', '!=', '')
                    -> whereNotNull('MemberAddress1');
                }
                $query -> select($this -> agent_columns);
            }])
            -> get();

            /* foreach ($counties as $county) {
                $state = $county -> state;
                $states[] = $county -> state;
                $county = $county -> county;
                $state_and_county[] = ['state' => $state, 'county' => $county];
            }

            $states = array_unique($states);

            $locations = [];
            foreach ($states as $state) {
                $details['state'] = $state;
                $counties = [];
                foreach ($state_and_county as $data) {
                    if ($data['state'] == $state) {
                        if ($data['county'] != '') {
                            $counties[] = $data['county'];
                        }
                    }
                }
                $details['counties'] = $counties;
                $locations[] = $details;
            }

            $offices = BrightOffices::select(['OfficeKey', 'OfficeMlsId', 'OfficeName', 'OfficeAddress1', 'OfficeCity', 'OfficeStateOrProvince', 'OfficePostalCode'])
            -> where(function ($query) use ($search_value) {
                if ($search_value != '') {
                    $query -> where('OfficeName', 'like', '%'.$search_value.'%');
                }
            })
            -> where(function ($query) use ($locations) {
                foreach ($locations as $location) {
                    $query -> orWhere(function ($query) use ($location) {
                        $query -> where('OfficeStateOrProvince', $location['state'])
                        -> where(function ($query) use ($location) {
                            if ($location['state'] != 'DC') {
                                $query -> whereIn('OfficeCounty', $location['counties']);
                            }
                        });
                    });
                }
            })
            -> whereHas('agents', function (Builder $query) use ($list_type) {
                $query -> where('MemberType', 'Agent');
                if ($list_type == 'email') {
                    $query -> where('MemberEmail', '!=', '')
                    -> whereNotNull('MemberEmail');
                } elseif ($list_type == 'address') {
                    $query -> where('MemberAddress1', '!=', '')
                    -> whereNotNull('MemberAddress1');
                }
            })
            -> with(['agents' => function ($query) use ($list_type) {
                $query -> where('MemberType', 'Agent');
                if ($list_type == 'email') {
                    $query -> where('MemberEmail', '!=', '')
                    -> whereNotNull('MemberEmail');
                } elseif ($list_type == 'address') {
                    $query -> where('MemberAddress1', '!=', '')
                    -> whereNotNull('MemberAddress1');
                }
                $query -> select($this -> agent_columns);
            }])
            -> get(); */
        }

        return $offices;
    }

    public function upload_list(Request $request) {


        $states_test_center = ['MD', 'VA'];

        return view('/marketing/data/upload_list', compact('states_test_center'));

    }

    public function add_new_list(Request $request)
    {

        $validated = $request -> validate([
            'upload_input' => 'required',
        ],
        [
            'required' => 'Required',
        ]);

        $type = $request -> type;
        $file = $request -> file('upload_input');
        $state = $request -> state;

        if($type == 'in_house') {

            InHouseAddresses::truncate();
            Excel::import(new InHouseAddressesImport, $file);

        } else if($type == 'test_center') {

            // add new emails to temp table
            TestCenterAddressesTemp::truncate();
            Excel::import(new TestCenterAddressesImport, $file);

            sleep(1);

            // transfer agents from temp to table
            $agents = TestCenterAddressesTemp::get();

            foreach($agents as $agent) {
                $agent -> state = $state;
                $agent = collect($agent);
                $agent -> forget('id');

                $add_agent = TestCenterAddresses::firstOrNew(
                    ['email' => $agent['email']],
                    $agent -> toArray()
                );

                $add_agent -> save();

            }

            // remove any agents who are now in the bright mls roster
            $bright = BrightAgentRoster::select('MemberEmail') -> get() -> pluck('MemberEmail');
            $test_center = TestCenterAddresses::select('email') -> get();

            $delete = $test_center -> whereIn('email', $bright) -> pluck('email');
            TestCenterAddresses::whereIn('email', $delete) -> delete();

        }
    }
}
