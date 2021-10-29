<?php

namespace App\Http\Controllers\Employees;

use App\Models\User;
use App\Helpers\Helper;
use App\Helpers\AuthNet;
use Illuminate\Http\Request;
use App\Models\Employees\Title;
use App\Models\Employees\Agents;
use App\Models\Employees\InHouse;
use App\Models\Billing\CreditCards;
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


        return view('/employees/agent/agents');

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


    public function in_house(Request $request) {


        return view('/employees/in_house/in_house');

    }

    public function get_in_house(Request $request) {

        $direction = $request -> direction ? $request -> direction : 'asc';
        $sort = $request -> sort ? $request -> sort : 'last_name';

        $search = $request -> search ?? null;
        $active = $request -> active;
        $employees = InHouse::select(['id', 'first_name', 'last_name', 'fullname', 'email', 'phone', 'active', 'emp_position'])
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
        -> orderBy($sort, $direction)
        -> paginate(25);

        return view('/employees/in_house/get_in_house_html', compact('employees'));

    }

    public function in_house_view(Request $request) {

        $id = $request -> id ? $request -> id : null;
        $employee = null;
        if($id) {
            $employee = InHouse::with(['docs', 'notes']) -> find($id);
        }
        $states = LocationData::getStates();

        return view('/employees/in_house/in_house_view', compact('employee', 'states'));

    }



    public function loan_officers(Request $request) {


        return view('/employees/loan_officer/loan_officers');

    }

    public function get_loan_officers(Request $request) {

        $direction = $request -> direction ? $request -> direction : 'asc';
        $sort = $request -> sort ? $request -> sort : 'last_name';

        $search = $request -> search ?? null;
        $active = $request -> active;
        $employees = LoanOfficers::select(['id', 'emp_type', 'first_name', 'last_name', 'fullname', 'email', 'phone', 'active', 'emp_position'])
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

        return view('/employees/loan_officer/get_loan_officers_html', compact('employees'));

    }

    public function loan_officer_view(Request $request) {

        $id = $request -> id ? $request -> id : null;
        $employee = null;
        if($id) {
            $employee = LoanOfficers::with(['docs', 'notes', 'licenses', 'user.credit_cards']) -> find($id);
        }

        $states = LocationData::getStates();

        return view('/employees/loan_officer/loan_officer_view', compact('employee', 'states'));

    }



    public function save_details(Request $request) {

        $emp_id = $request -> emp_id ?? null;
        $emp_type = $request -> emp_type;

        // if(!$request -> commission_percent) {
        //     $request -> merge(['commission_percent' => 'N/A']);
        // }
        // if(!$request -> folder) {
        //     $request -> merge(['folder' => 'N/A']);
        // }

        if($emp_type == 'loan_officer') {

            $employee = LoanOfficers::firstOrNew(['id' => $emp_id]);
            $ignore_cols = ['emp_id', 'license_state', 'license_number'];

            // $folder = LoanOfficers::where('folder', $request -> folder) -> where('id', '!=', $emp_id) -> first();
            // if($folder) {
            //     return response() -> json(['error' => 'The profile name is already in use']);
            // }

        } else if($emp_type == 'in_house') {

            $employee = InHouse::firstOrNew(['id' => $emp_id]);
            $ignore_cols = ['emp_id', 'commission_percent', 'license_state', 'license_number', 'folder'];

        }



        // get user email before it is changed to update user table
        $orig_email = $employee -> email;

        $user = User::where('email', $orig_email) -> first();
        $id = $user -> id ?? null;
        $user_id = $user -> user_id ?? null;

        $validator = $request -> validate([
            'emp_position' => 'required',
            'job_title' => 'required',
            'start_date' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'address_street' => 'required',
            'address_city' => 'required',
            'address_state' => 'required',
            'address_zip' => 'required',
            'soc_sec' => 'required|regex:/[0-9]{3}-[0-9]{2}-[0-9]{4}/',
            'dob' => 'required',
        ],
        [
            'required' => 'Required',
            'email' => 'You must enter a valid email address',
            'email.unique' => 'The email address is already in use',
            'regex' => 'The social security number must be in the format ###-##-####'
        ]);

        if ($emp_type == 'loan_officer') {

            $validator = $request -> validate(
                [
                'commission_percent' => 'required',
                'folder' => 'required|string|max:25|unique:emp_loan_officers,folder,'.$user_id,
            ],
                [
                'required' => 'Required',
                'folder.unique' => 'That profile name is already in use',
            ]
            );
        }

        foreach($request -> all() as $key => $val) {
            if(!in_array($key, $ignore_cols)) {
                if($key == 'soc_sec') {
                    $val = Crypt::encrypt($val);
                }
                $employee[$key] = $val;
            }
        }

        $employee -> fullname = $employee -> first_name . ' ' . $employee -> last_name;

        $employee -> save();
        $emp_id = $employee -> id;


        if($request -> license_state && count($request -> license_state) > 0) {

            EmployeesLicenses::where('emp_id', $emp_id) -> where('emp_type', $emp_type) -> delete();
            $license_states = $request -> license_state;
            $license_numbers = $request -> license_number;

            $count_licenses = count($license_states);
            for($i=0; $i<$count_licenses; $i++) {
                $license = new EmployeesLicenses();
                $license -> license_number = $license_numbers[$i];
                $license -> license_state = $license_states[$i];
                $license -> emp_id = $emp_id;
                $license -> emp_type = 'loan_officer';
                $license -> save();
            }

        }

        // add or update users table
        if($request -> emp_id) {
            $user = User::where('email', $orig_email) -> first();
        } else {
            $user = new User();
            $user -> user_id = $emp_id;
            $user -> group = $emp_type;
            $user -> active = 'yes';
            $user -> password = '$2y$10$P.O4F.rVfRRin81HksyCie0Wf0TEJQ9KlPYFoI2dMEzdtPFYD11FC';
        }
        $user -> name = $employee -> first_name.' '.$employee -> last_name;
        $user -> first_name = $employee -> first_name;
        $user -> last_name = $employee -> last_name;
        $user -> email = $request -> email;
        $user -> level = $request -> emp_position;
        $user -> save();

        if(!$request -> emp_id) {
            return response() -> json(['emp_id' => $emp_id]);
        }

        return response() -> json(['success' => true]);

    }


    public function profile(Request $request) {

        $emp_type = auth() -> user() -> group;
        $emp_id = auth() -> user() -> user_id;


        if($emp_type == 'agent') {
            $employee = Agents::find($emp_id);
        } else if($emp_type == 'loan_officer') {
            $employee = LoanOfficers::with(['licenses']) -> find($emp_id);
        } else if($emp_type == 'in_house') {
            $employee = InHouse::find($emp_id);
        } else if($emp_type == 'title') {
            $employee = Title::find($emp_id);
        } else if($emp_type == 'transaction_coordinator') {
            $employee = TransactionCoordinators::find($emp_id);
        }


        return view('/employees/'.$emp_type.'/profile', compact('employee'));

    }



    public function get_docs(Request $request) {

        $docs = EmployeesDocs::where('emp_id', $request -> emp_id) -> where('emp_type', $request -> emp_type) -> get();

        return compact('docs');

    }

    public function docs_upload(Request $request) {

        $file = $request -> file('employee_docs');
        $emp_type = $request -> emp_type;
        $emp_id = $request -> emp_id;

        $file_name_orig = $file -> getClientOriginalName();
        $file_name = Helper::clean_file_name($file, '', false, true);

        $dir = 'employees/'.$emp_type.'/docs/'.$emp_id;
        if(!is_dir($dir)) {
            Storage::makeDirectory($dir);
        }
        $file -> storeAs($dir, $file_name);
        $file_location = $dir.'/'.$file_name;
        $file_location_url = Storage::url($dir.'/'.$file_name);

        EmployeesDocs::create([
            'emp_id' => $emp_id,
            'emp_type' => $emp_type,
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

        $emp_id = $request -> emp_id;
        $emp_type = $request -> emp_type;
        $licenses = EmployeesLicenses::where('emp_id', $emp_id) -> where('emp_type', $emp_type) -> get();
        $states = LocationData::getStates();

        return view('/employees/get_licenses_html', compact('licenses', 'states'));

    }

    public function save_cropped_upload(Request $request) {

        $file = $request -> file('cropped_image');
        $emp_id = $request -> emp_id;
        $emp_type = $request -> emp_type;

        if($emp_type == 'agent') {
            $employee = Agents::find($emp_id);
        } else if($emp_type == 'loan_officer') {
            $employee = LoanOfficers::find($emp_id);
        } else if($emp_type == 'in_house') {
            $employee = InHouse::find($emp_id);
        } else if($emp_type == 'title') {
            $employee = Title::find($emp_id);
        } else if($emp_type == 'transaction_coordinator') {
            $employee = TransactionCoordinators::find($emp_id);
        }

        $filename = $employee -> id.'.'.$file -> extension();

        $image_resize = Image::make($file -> getRealPath());
        $image_resize -> resize(300, 400);
        $image_resize -> save(Storage::path('employees/'.$emp_type.'/photos/'.$filename));

        $location = 'employees/'.$emp_type.'/photos/'.$filename;
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

        $emp_id = $request -> emp_id;
        $emp_type = $request -> emp_type;

        if($emp_type == 'agent') {
            $employee = Agents::find($emp_id);
        } else if($emp_type == 'loan_officer') {
            $employee = LoanOfficers::find($emp_id);
        } else if($emp_type == 'in_house') {
            $employee = InHouse::find($emp_id);
        } else if($emp_type == 'title') {
            $employee = Title::find($emp_id);
        } else if($emp_type == 'transaction_coordinator') {
            $employee = TransactionCoordinators::find($emp_id);
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

        $emp_id = $request -> emp_id ? $request -> emp_id : auth() -> user() -> user_id;
        $emp_type = $request -> emp_type ? $request -> emp_type : auth() -> user() -> group;


        if($emp_type == 'agent') {
            Agents::find($emp_id) -> update([
                'bio' => $request -> bio
            ]);
        } else if($emp_type == 'loan_officer') {
            LoanOfficers::find($emp_id) -> update([
                'bio' => $request -> bio
            ]);
        } else if($emp_type == 'in_house') {
            InHouse::find($emp_id) -> update([
                'bio' => $request -> bio
            ]);
        } else if($emp_type == 'title') {
            Title::find($emp_id) -> update([
                'bio' => $request -> bio
            ]);
        } else if($emp_type == 'transaction_coordinator') {
            TransactionCoordinators::find($emp_id) -> update([
                'bio' => $request -> bio
            ]);
        }


        return response() -> json(['success' => true]);

    }

    public function save_signature(Request $request) {

        $emp_id = $request -> emp_id ? $request -> emp_id : auth() -> user() -> user_id;
        $emp_type = $request -> emp_type ? $request -> emp_type : auth() -> user() -> group;


        if($emp_type == 'agent') {
            Agents::find($emp_id) -> update([
                'signature' => $request -> signature
            ]);
        } else if($emp_type == 'loan_officer') {
            LoanOfficers::find($emp_id) -> update([
                'signature' => $request -> signature
            ]);
        } else if($emp_type == 'in_house') {
            InHouse::find($emp_id) -> update([
                'signature' => $request -> signature
            ]);
        } else if($emp_type == 'title') {
            Title::find($emp_id) -> update([
                'signature' => $request -> signature
            ]);
        } else if($emp_type == 'transaction_coordinator') {
            TransactionCoordinators::find($emp_id) -> update([
                'signature' => $request -> signature
            ]);
        }


        return response() -> json(['success' => true]);

    }


    public function get_credit_cards(Request $request) {

        $emp_id = $request -> emp_id;
        $emp_type = $request -> emp_type;

        if($emp_type == 'agent') {
            $employee = Agents::with(['user.credit_cards']) -> find($emp_id);
        } else if($emp_type == 'loan_officer') {
            $employee = LoanOfficers::with(['user.credit_cards']) -> find($emp_id);
        } else if($emp_type == 'in_house') {
            $employee = InHouse::with(['user.credit_cards']) -> find($emp_id);
        } else if($emp_type == 'title') {
            $employee = Title::with(['user.credit_cards']) -> find($emp_id);
        } else if($emp_type == 'transaction_coordinator') {
            $employee = TransactionCoordinators::with(['user.credit_cards']) -> find($emp_id);
        }

        $user = $employee -> user -> first();
        $credit_cards = $user -> credit_cards -> where('active', '1');

        return view('/employees/billing/get_credit_cards_html', compact('credit_cards'));

    }
    public function add_credit_card(Request $request) {


        $validator = $request -> validate([
            'first' => 'required',
            'last' => 'required',
            'number' => 'required',
            'expire_month' => 'required',
            'expire_year' => 'required',
            'street' => 'required',
            'zip' => 'required',
            'code' => 'required',
        ],
        [
            'required' => 'Required'
        ]);

        $user = User::where('group', $request -> emp_type) -> where('user_id', $request -> emp_id) -> first();
        $user_id = $user -> id;
        $email = $user -> email;
        $profile_id = $user -> profile_id ?? null;

        $result = AuthNet::AddCreditCard($user_id, $profile_id, $email, $request -> number, $request -> expire_month, $request -> expire_year, $request -> street, '', '', $request -> zip, $request -> first, $request -> last, $request -> code);

        if ($result != '') {
            return response() -> json(['error' => $result]);
        }
        return response() -> json(['success' => true]);

    }

    public function delete_credit_card(Request $request) {

        AuthNet::deleteCustomerPaymentProfile($request -> profile_id, $request -> payment_profile_id);

        return response() -> json(['success' => true]);

    }

    public function set_default_credit_card(Request $request) {

        CreditCards::where('profile_id', $request -> profile_id)
        -> update([
            'default' => 'no'
        ]);

        CreditCards::where('profile_id', $request -> profile_id)
        -> where('payment_profile_id', $request -> payment_profile_id)
        -> first()
        -> update([
            'default' => 'yes'
        ]);

        return response() -> json(['success' => true]);

    }



    //////////////// IMPORT DATA ////////////////
    public function import_los(Request $request) {

        LoanOfficers::truncate();
        EmployeesLicenses::where('emp_type', 'loan_officer') -> delete();
        EmployeesNotes::where('emp_type', 'loan_officer') -> delete();
        User::where('group', 'loan_officer') -> delete();

        $old_los = LoanOfficersOld::get();

        foreach($old_los as $lo) {

            $add_lo = new LoanOfficers();
            $add_lo -> id = $lo -> id;
            $add_lo -> emp_type = 'loan_officer';
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
            if($add_lo -> active == 'yes') {
                $add_lo -> folder = $lo -> first;
            }
            $add_lo -> commission_percent = (double) $lo -> comm_split * 100;
            if($lo -> start_date != '0000-00-00' && $lo -> start_date != '') {
                $add_lo -> start_date = $lo -> start_date;
            }
            if($lo -> term_date != '0000-00-00' && $lo -> term_date != '') {
                $add_lo -> term_date = $lo -> term_date;
            }
            if($lo -> dob != '0000-00-00' && $lo -> dob != '') {
                $add_lo -> dob = $lo -> dob;
            }
            $add_lo -> soc_sec = Crypt::encrypt($lo -> soc_sec);
            $add_lo -> nmls_id = $lo -> nmls_lic;
            $add_lo -> fullname = $lo -> fullname;
            $add_lo -> prev_company = $lo -> prev_company;
            $add_lo -> bio = $lo -> bio;
            if($lo -> manager == 'yes') {
                $add_lo -> emp_position = 'manager';
                $add_lo -> job_title = 'Manager';
            } else if($lo -> proc == 'yes') {
                $add_lo -> emp_position = 'processor';
                $add_lo -> job_title = 'Processor';
            } else {
                $add_lo -> emp_position = 'loan_officer';
                $add_lo -> job_title = 'Loan Officer';
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


            $email = $lo -> lo_email;
            // if($lo -> comp_email != '') {
            //     $email = $lo -> comp_email;
            // }
            $add_user = new User();
            $add_user -> user_id = $lo -> id;
            $add_user -> group = 'loan_officer';
            $add_user -> level = $add_lo -> emp_position;
            $add_user -> active = $lo -> active;
            $add_user -> name = $lo -> first.' '.$lo -> last;
            $add_user -> first_name = $lo -> first;
            $add_user -> last_name = $lo -> last;
            $add_user -> email = $email;
            $add_user -> password = '$2y$10$P.O4F.rVfRRin81HksyCie0Wf0TEJQ9KlPYFoI2dMEzdtPFYD11FC';
            $add_user -> save();

        }

    }

}
