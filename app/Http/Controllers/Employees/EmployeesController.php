<?php

namespace App\Http\Controllers\Employees;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\Employees\Title;
use App\Models\Employees\Agents;
use App\Models\Employees\InHouse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Intervention\Image\Facades\Image;
use App\Models\Employees\LoanOfficers;
use App\Models\Employees\EmployeesDocs;
use Illuminate\Support\Facades\Storage;
use App\Models\Employees\EmployeesNotes;
use App\Models\Employees\EmployeesLicenses;
use App\Models\Employees\TransactionCoordinators;
use App\Models\DocManagement\Resources\LocationData;
use App\Models\OldDB\LoanOfficers as LoanOfficersOld;


class EmployeesController extends Controller
{

    public function agents(Request $request) {


        return view('/employees/agents/agents');

    }

    public function get_agents(Request $request) {

        $agents = Agents::select(['full_name', 'cell_phone', 'email']) -> get();

        $button_classes = 'px-3 py-2 text-sm bg-primary hover:bg-primary-dark active:bg-primary-dark focus:border-primary-dark ring-primary-dark inline-flex items-center rounded text-white shadow hover:shadow-lg outline-none tracking-wider focus:outline-none disabled:opacity-25 transition-all ease-in-out duration-150 shadow hover:shadow-md';

        return datatables() -> of($agents)
        -> addColumn('edit', function ($agents) use ($button_classes) {
            return '<a href="" class="'.$button_classes.'"><i class="fal fa-pencil fa-sm mr-2"></i> View/Edit</a>';
        })
        -> editColumn('full_name', function($agents) {
            return '<span class="text-red-500">'.$agents -> full_name.'</span>';
        })
        -> escapeColumns([])
        -> make(true);

    }



    public function loan_officers(Request $request) {


        return view('/employees/loan_officers/loan_officers');

    }

    public function get_loan_officers(Request $request) {

        $direction = $request -> direction ? $request -> direction : 'asc';
        $sort = $request -> sort ? $request -> sort : 'last_name';

        $search = $request -> search ?? null;
        $active = $request -> active;
        $loan_officers = LoanOfficers::select(['id', 'first_name', 'last_name', 'fullname', 'email', 'phone', 'active', 'position'])
        -> where(function($query) use ($search) {
            if($search) {
                $query -> where('fullname', 'like', '%'.$search.'%');
            }
        })
        -> where(function($query) use ($active) {
            if($active != '') {
                $query -> where('active', $active);
            }
        })
        -> with(['licenses'])
        -> orderBy($sort, $direction)
        -> paginate(25);

        return view('/employees/loan_officers/get_loan_officers_html', compact('loan_officers'));

    }

    public function loan_officer_view(Request $request) {

        $id = $request -> id;
        $loan_officer = LoanOfficers::with(['docs', 'notes', 'licenses']) -> find($id);
        $states = LocationData::getStates();

        return view('/employees/loan_officers/loan_officer_view', compact('loan_officer', 'states'));

    }

    public function save_details(Request $request) {

        $employee_id = $request -> employee_id;
        $employee_type = $request -> emp_type;

        if($employee_type == 'loan_officer') {

            $validator = $request -> validate([
                'start_date' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'address_street' => 'required',
                'address_city' => 'required',
                'address_state' => 'required',
                'address_zip' => 'required',
                'commission_split' => 'required',
                'soc_sec' => 'required',
            ],
            [
                'required' => 'Required Field',
                'required_if' => 'Required Field',
            ]);



            $loan_officer = LoanOfficers::find($employee_id);
            $ignore_cols = ['employee_id', 'license_state', 'license_number'];

            foreach($request -> all() as $key => $val) {
                if(!in_array($key, $ignore_cols)) {
                    if($key == 'soc_sec') {
                        $val = Crypt::encrypt($val);
                    }
                    $loan_officer[$key] = $val;
                }
            }
            $loan_officer -> save();

            EmployeesLicenses::where('emp_id', $employee_id) -> where('emp_type', 'loan_officer') -> delete();
            $license_states = $request -> license_state;
            $license_numbers = $request -> license_number;

            if($request -> license_state && count($request -> license_state) > 0) {
                $count_licenses = count($license_states);
                for($i=0; $i<$count_licenses; $i++) {
                    $license = new EmployeesLicenses();
                    $license -> license_number = $license_numbers[$i];
                    $license -> license_state = $license_states[$i];
                    $license -> emp_id = $employee_id;
                    $license -> emp_type = 'loan_officer';
                    $license -> save();
                }
            }

        }

    }

    /* public function get_licenses_loan_officer(Request $request) {

        $loan_officer_id = $request -> loan_officer_id;
        $licenses = LoanOfficers::find($loan_officer_id) -> licenses() -> get();
        $states = LocationData::getStates();

        return view('/employees/loan_officers/get_licenses_html', compact('licenses', 'states'));

    } */

    /* public function docs_upload_loan_officer(Request $request) {

        $file = $request -> file('loan_officer_docs');
        $loan_officer_id = $request -> loan_officer_id;

        $file_name_orig = $file -> getClientOriginalName();
        $file_name = Helper::clean_file_name($file, '', false, true);

        $dir = 'employees/loan_officers/docs/'.$loan_officer_id;
        if(!is_dir($dir)) {
            Storage::makeDirectory($dir);
        }
        $file -> storeAs($dir, $file_name);
        $file_location = $dir.'/'.$file_name;
        $file_location_url = Storage::url($dir.'/'.$file_name);

        EmployeesDocs::create([
            'emp_loan_officers_id' => $loan_officer_id,
            'file_name' => $file_name_orig,
            'file_location' => $file_location,
            'file_location_url' => $file_location_url
        ]);



    } */

    /* public function get_docs_loan_officer(Request $request) {

        $docs = LoanOfficers::find($request -> loan_officer_id) -> docs;

        return compact('docs');

    } */

    /* public function delete_doc_loan_officer(Request $request) {

        $id = $request -> id;

        EmployeesDocs::find($id) -> delete();

        return response() -> json(['status' => 'success']);

    } */

    /* public function save_cropped_upload_loan_officer(Request $request) {

        $file = $request -> file('cropped_image');
        $loan_officer_id = $request -> loan_officer_id;

        $loan_officer = LoanOfficers::find($loan_officer_id);

        $filename = $loan_officer -> first_name.'-'.$loan_officer -> last_name.'-'.$loan_officer -> id.'.'.$file -> extension();

        $image_resize = Image::make($file -> getRealPath());
        $image_resize -> resize(300, 400);
        $image_resize -> save(Storage::path('employees/loan_officers/photos/'.$filename));

        $location = 'employees/loan_officers/photos/'.$filename;
        $url = Storage::url($location);

        $loan_officer -> update([
            'photo_location' => $location,
            'photo_location_url' => $url
        ]);

        $user = User::where('email', $loan_officer -> email) -> first();
        $user -> photo_location_url = $url;
        $user -> save();

        return response() -> json(['status' => 'success', 'url' => $url]);


    } */

    /* public function delete_photo_loan_officer(Request $request) {

        $loan_officer_id = $request -> loan_officer_id;

        $loan_officer = LoanOfficers::find($loan_officer_id);
        $loan_officer -> update([
            'photo_location' => '',
            'photo_location_url' => ''
        ]);

        $user = User::where('email', $loan_officer -> email) -> first() -> update([
            'photo_location_url' => ''
        ]);

        Storage::delete($loan_officer -> photo_location);

    } */


    public function profile(Request $request) {

        $employee_type = auth() -> user() -> group;
        $employee_id = auth() -> user() -> user_id;

        if($employee_type == 'agent') {
            $employee = Agents::find($employee_id);
            $employee_type = 'agents';
        } else if($employee_type == 'loan_officer') {
            $employee = LoanOfficers::with(['licenses']) -> find($employee_id);
            $employee_type = 'loan_officers';
        } else if($employee_type == 'in_house') {
            $employee = InHouse::find($employee_id);
        } else if($employee_type == 'title') {
            $employee = Title::find($employee_id);
        } else if($employee_type == 'transaction_coordinator') {
            $employee = TransactionCoordinators::find($employee_id);
            $employee_type = 'transaction_coordinators';
        }


        return view('/employees/'.$employee_type.'/profile', compact('employee'));

    }



    public function get_docs(Request $request) {

        $docs = EmployeesDocs::where('emp_id', $request -> employee_id) -> where('emp_type', $request -> employee_type) -> get();

        return compact('docs');

    }

    public function docs_upload(Request $request) {

        $file = $request -> file('employee_docs');
        $employee_type = $request -> employee_type;
        $employee_id = $request -> employee_id;

        $file_name_orig = $file -> getClientOriginalName();
        $file_name = Helper::clean_file_name($file, '', false, true);

        $dir = 'employees/'.$employee_type.'/docs/'.$employee_id;
        if(!is_dir($dir)) {
            Storage::makeDirectory($dir);
        }
        $file -> storeAs($dir, $file_name);
        $file_location = $dir.'/'.$file_name;
        $file_location_url = Storage::url($dir.'/'.$file_name);

        EmployeesDocs::create([
            'emp_id' => $employee_id,
            'emp_type' => $employee_type,
            'file_name' => $file_name_orig,
            'file_location' => $file_location,
            'file_location_url' => $file_location_url,
        ]);



    }

    public function delete_doc(Request $request) {

        $id = $request -> id;

        EmployeesDocs::find($id) -> delete();

        return response() -> json(['status' => 'success']);

    }

    public function get_licenses(Request $request) {

        $employee_id = $request -> employee_id;
        $employee_type = $request -> employee_type;
        $licenses = EmployeesLicenses::where('emp_id', $employee_id) -> where('emp_type', $employee_type) -> get();
        $states = LocationData::getStates();

        return view('/employees/get_licenses_html', compact('licenses', 'states'));

    }

    public function save_cropped_upload(Request $request) {

        $file = $request -> file('cropped_image');
        $employee_id = $request -> employee_id;
        $employee_type = $request -> employee_type;

        if($employee_type == 'agent') {
            $employee = Agents::find($employee_id);
        } else if($employee_type == 'loan_officer') {
            $employee = LoanOfficers::find($employee_id);
        } else if($employee_type == 'in_house') {
            $employee = InHouse::find($employee_id);
        } else if($employee_type == 'title') {
            $employee = Title::find($employee_id);
        } else if($employee_type == 'transaction_coordinator') {
            $employee = TransactionCoordinators::find($employee_id);
        }

        $filename = $employee -> first_name.'-'.$employee -> last_name.'-'.$employee -> id.'.'.$file -> extension();

        $image_resize = Image::make($file -> getRealPath());
        $image_resize -> resize(300, 400);
        $image_resize -> save(Storage::path('employees/'.$employee_type.'/photos/'.$filename));

        $location = 'employees/'.$employee_type.'/photos/'.$filename;
        $url = Storage::url($location);

        $employee -> update([
            'photo_location' => $location,
            'photo_location_url' => $url
        ]);

        $user = User::where('email', $employee -> email) -> first();
        $user -> photo_location_url = $url;
        $user -> save();

        return response() -> json(['status' => 'success', 'url' => $url]);


    }

    public function delete_photo(Request $request) {

        $employee_id = $request -> employee_id;
        $employee_type = $request -> employee_type;

        if($employee_type == 'agent') {
            $employee = Agents::find($employee_id);
        } else if($employee_type == 'loan_officer') {
            $employee = LoanOfficers::find($employee_id);
        } else if($employee_type == 'in_house') {
            $employee = InHouse::find($employee_id);
        } else if($employee_type == 'title') {
            $employee = Title::find($employee_id);
        } else if($employee_type == 'transaction_coordinator') {
            $employee = TransactionCoordinators::find($employee_id);
        }

        Storage::delete($employee -> photo_location);

        $employee -> update([
            'photo_location' => '',
            'photo_location_url' => ''
        ]);

        $user = User::where('email', $employee -> email) -> first() -> update([
            'photo_location_url' => ''
        ]);

    }


    public function save_bio(Request $request) {

        $employee_id = $request -> employee_id ? $request -> employee_id : auth() -> user() -> user_id;
        $employee_type = $request -> employee_type ? $request -> employee_type : auth() -> user() -> group;

        if($employee_type == 'agent') {
            Agents::find($employee_id) -> update([
                'bio' => $request -> bio
            ]);
        } else if($employee_type == 'loan_officer') {
            LoanOfficers::find($employee_id) -> update([
                'bio' => $request -> bio
            ]);
        } else if($employee_type == 'in_house') {
            InHouse::find($employee_id) -> update([
                'bio' => $request -> bio
            ]);
        } else if($employee_type == 'title') {
            Title::find($employee_id) -> update([
                'bio' => $request -> bio
            ]);
        } else if($employee_type == 'transaction_coordinator') {
            TransactionCoordinators::find($employee_id) -> update([
                'bio' => $request -> bio
            ]);
        }


        return response() -> json(['success' => true]);

    }



    //////////////// IMPORT DATA ////////////////
    public function add_los(Request $request) {

        LoanOfficers::truncate();
        EmployeesLicenses::where('emp_type', 'loan_officer') -> delete();
        EmployeesNotes::where('emp_type', 'loan_officer') -> delete();
        User::where('id', '>', '3') -> where('group', 'loan_officer') -> delete();

        $old_los = LoanOfficersOld::get();

        foreach($old_los as $lo) {

            $add_lo = new LoanOfficers();
            $add_lo -> id = $lo -> id;
            $add_lo -> active = $lo -> active;
            $add_lo -> first_name = $lo -> first;
            $add_lo -> last_name = $lo -> last;
            $add_lo -> email = $lo -> lo_email;
            $add_lo -> phone = $lo -> cellphone;
            $add_lo -> address_street = $lo -> street;
            $add_lo -> address_city = $lo -> city;
            $add_lo -> address_state = $lo -> state;
            $add_lo -> address_zip = $lo -> zip;
            $add_lo -> photo_location = $lo -> photo_loc;
            $add_lo -> signature = $lo -> signature;
            $add_lo -> commission_split = (double) $lo -> comm_split * 100;
            if($lo -> start_date != '0000-00-00' && $lo -> start_date != '') {
                $add_lo -> start_date = $lo -> start_date;
            }
            if($lo -> term_date != '0000-00-00' && $lo -> term_date != '') {
                $add_lo -> term_date = $lo -> term_date;
            }
            $add_lo -> company_email = $lo -> comp_email;
            if($lo -> dob != '0000-00-00' && $lo -> dob != '') {
                $add_lo -> dob = $lo -> dob;
            }
            $add_lo -> soc_sec = Crypt::encrypt($lo -> soc_sec);
            $add_lo -> nmls_id = $lo -> nmls_lic;
            $add_lo -> fullname = $lo -> fullname;
            $add_lo -> prev_company = $lo -> prev_company;
            $add_lo -> bio = $lo -> bio;
            if($lo -> manager == 'yes') {
                $add_lo -> position = 'manager';
            } else if($lo -> proc == 'yes') {
                $add_lo -> position = 'processor';
            } else {
                $add_lo -> position = 'loan_officer';
            }
            $add_lo -> save();

            if($lo -> lic1_state != '') {
                $add_license = new EmployeesLicenses();
                $add_license -> emp_id = $lo -> id;
                $add_license -> emp_type = 'loan_officer';
                $add_license -> license_state = $lo -> lic1_state;
                $add_license -> license_number = $lo -> lic1_num;
                $add_license -> license_expiration_date = $lo -> lic1_expire;
                $add_license -> save();
            }
            if($lo -> lic2_state != '') {
                $add_license = new EmployeesLicenses();
                $add_license -> emp_id = $lo -> id;
                $add_license -> emp_type = 'loan_officer';
                $add_license -> license_state = $lo -> lic2_state;
                $add_license -> license_number = $lo -> lic2_num;
                $add_license -> license_expiration_date = $lo -> lic2_expire;
                $add_license -> save();
            }
            if($lo -> lic3_state != '') {
                $add_license = new EmployeesLicenses();
                $add_license -> emp_id = $lo -> id;
                $add_license -> emp_type = 'loan_officer';
                $add_license -> license_state = $lo -> lic3_state;
                $add_license -> license_number = $lo -> lic3_num;
                $add_license -> license_expiration_date = $lo -> lic3_expire;
                $add_license -> save();
            }
            if($lo -> notes != '') {
                $add_notes = new EmployeesNotes();
                $add_notes -> emp_id = $lo -> id;
                $add_notes -> emp_type = 'loan_officer';
                $add_notes -> emp_name = $lo -> fullname;
                $add_notes -> notes = $lo -> notes;
                $add_notes -> save();
            }


            $add_user = new User();
            $add_user -> user_id = $lo -> id;
            $add_user -> group = 'loan_officer';
            $add_user -> active = $lo -> active;
            $add_user -> name = $lo -> first.' '.$lo -> last;
            $add_user -> first_name = $lo -> first;
            $add_user -> last_name = $lo -> last;
            $add_user -> email = $lo -> lo_email;
            $add_user -> password = '$2y$10$P.O4F.rVfRRin81HksyCie0Wf0TEJQ9KlPYFoI2dMEzdtPFYD11FC';
            $add_user -> save();

        }

    }

}
