<?php

namespace App\Http\Controllers\HeritageFinancial;

use App\Classes\DatabaseChangeLog;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Mail\HeritageFinancial\ManagerBonuses;
use App\Models\Database\ChangeLog;
use App\Models\DocManagement\Resources\LocationData;
use App\Models\Employees\Mortgage;
use App\Models\HeritageFinancial\Lenders;
use App\Models\HeritageFinancial\Loans;
use App\Models\HeritageFinancial\LoansChecksIn;
use App\Models\HeritageFinancial\LoansDeductions;
use App\Models\HeritageFinancial\LoansDocuments;
use App\Models\HeritageFinancial\LoansLoanOfficerDeductions;
use App\Models\HeritageFinancial\LoansNotes;
use App\Models\OldDB\Company\Loans as LoansOld;
use App\Models\OldDB\Company\LoansInProcess as LoansInProcessOld;
use App\Models\OldDB\LoanOfficers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LoansController extends Controller
{
    public function loans(Request $request)
    {
        $processors = Mortgage::whereIn('emp_position', ['processor', 'manager'])
        -> where('active', 'yes')
        -> get();
        $loan_officers = Mortgage::where('emp_position', 'loan_officer')
        -> where('active', 'yes')
        -> get();

        return view('heritage_financial/loans/loans', compact('processors', 'loan_officers'));

    }

    public function get_loans(Request $request)
    {
        $direction = $request -> direction ? $request -> direction : 'desc';
        $sort = $request -> sort ? $request -> sort : 'settlement_date';
        $length = $request -> length ? $request -> length : 10;
        $start_date = $request -> start_date ?? null;
        $end_date = $request -> end_date ?? null;
        $date_col = $request -> date_col ?? null;

        $processor_id = $request -> processor_id ?? null;
        if (! $processor_id) {
            if (in_array(auth() -> user() -> level, ['manager', 'processor'])) {
                $processor_id = auth() -> user() -> user_id;
            }
        }
        $loan_officer_id = $request -> loan_officer_id ?? null;
        $loan_status = $request -> loan_status ?? 'Open';

        $search = $request -> search ?? null;

        $loans = Loans::select([
            'heritage_financial_loans.id',
            'uuid',
            'first_name as loan_officer_first',
            'last_name as loan_officer_last',
            'processor_id',
            'loan_officer_1_id',
            'loan_amount',
            'borrower_first',
            'borrower_last',
            'borrower_fullname',
            'co_borrower_first',
            'co_borrower_last',
            'co_borrower_fullname',
            'settlement_date',
            'loan_officer_1_id',
            'loan_status',
            'street',
            'city',
            'state',
            'zip',
            'heritage_financial_loans.created_at',
        ])
        -> where(function ($query) use ($search) {
            if ($search) {
                $query -> whereHas('loan_officer_1', function ($query) use ($search) {
                    $query -> where('fullname', 'like', '%'.$search.'%');
                })
                -> orWhere('street', 'like', '%'.$search.'%')
                -> orWhere('borrower_fullname', 'like', '%'.$search.'%')
                -> orWhere('co_borrower_fullname', 'like', '%'.$search.'%');
            }
        })
        -> where(function ($query) use ($processor_id) {
            if ($processor_id) {
                $query -> where('processor_id', $processor_id);
            }
        })
        -> where(function($query) use ($loan_officer_id) {
            if($loan_officer_id) {
                $query -> where('loan_officer_1_id', $loan_officer_id);
            }
        })
        -> where(function($query) use ($loan_status) {
            if($loan_status && $loan_status != 'all' && $loan_status != null) {
                $query -> where('loan_status', $loan_status);
            }
        })
        -> where(function ($query) use ($date_col, $start_date, $end_date) {
            if ($date_col != null) {
                if ($this -> validate_date($start_date)) {
                    $query -> where($date_col, '>=', $start_date);
                }
                if ($this -> validate_date($end_date)) {
                    $query -> where($date_col, '<=', $end_date);
                }
            }
        })
        -> leftJoin('emp_mortgage', 'loan_officer_1_id', '=', 'emp_mortgage.id')
        -> with(['processor'])
        -> orderBy($sort, $direction)
        -> paginate($length);

        return view('heritage_financial/loans/get_loans_html', compact('loans'));
    }

    public function validate_date($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d -> format($format) == $date;
    }

    public function view_loan(Request $request)
    {
        $loan = null;
        $deductions = [];
        $checks_in = [];
        $loan_officer_1 = null;
        $loan_officer_2 = null;
        $processor = null;
        $loan_officer_1_commission_type = null;
        $loan_officer_2_commission_type = null;
        $loan_officer_1_active_commission_tab = null;
        $loan_officer_2_active_commission_tab = null;
        $loan_officer_deductions = null;
        $manager = null;
        $manager_bonus = null;
        $manager_bonus_details = null;
        $history = null;

        if ($request -> uuid) {
            $loan = Loans::where('uuid', $request -> uuid)
            -> with(['deductions', 'checks_in', 'loan_officer_1', 'loan_officer_2', 'processor', 'loan_officer_deductions'])
            -> first();

            $deductions = $loan -> deductions;
            $checks_in = $loan -> checks_in;

            $loan_officer_1 = $loan -> loan_officer_1;
            $loan_officer_1_commission_type = $loan -> loan_officer_1_commission_type;
            $loan_officer_1_active_commission_tab = $loan -> loan_officer_1_commission_type == 'loan_amount' ? '2' : '1';

            $loan_officer_2 = $loan -> loan_officer_2;
            $loan_officer_2_commission_type = $loan -> loan_officer_2_commission_type;
            $loan_officer_2_active_commission_tab = $loan -> loan_officer_2_commission_type == 'loan_amount' ? '2' : '1';

            $loan_officer_deductions = $loan -> loan_officer_deductions;

            $processor = $loan -> processor;

            $manager = Mortgage::where('emp_position', 'manager') -> first();

            $manager = $manager -> fullname;

            $manager_bonus = $loan_officer_1-> manager_bonus;
            $manager_bonus_details = 'Manager Bonus is always '.$loan_officer_1-> manager_bonus.'% for all loans by '.$loan_officer_1-> fullname;
            if ($manager_bonus == '0.00' || $manager_bonus == '0') {
                $manager_bonus = '3';
                $manager_bonus_details = 'Manager Bonus is 3% for Loan Officer leads';
                if ($loan -> source == 'Office') {
                    $manager_bonus = '5';
                    $manager_bonus_details = 'Manager Bonus is 5% for Office leads';
                }
            }
        }

        $states = LocationData::getStates();

        $lenders = Lenders::where('active', 'yes') -> orderBy('company_name') -> get();

        $loan_officers = Mortgage::where('active', 'yes') -> orderBy('last_name') -> get();

        return view('heritage_financial/loans/view_loan_html', compact('loan', 'deductions', 'checks_in', 'loan_officer_1', 'loan_officer_2', 'processor', 'loan_officer_1_commission_type', 'loan_officer_1_active_commission_tab', 'loan_officer_2_commission_type', 'loan_officer_2_active_commission_tab', 'loan_officer_deductions', 'states', 'lenders', 'loan_officers', 'manager', 'manager_bonus', 'manager_bonus_details'));
    }

    public function get_changes(Request $request)
    {
        $loan = Loans::where('uuid', $request -> uuid) -> first();
        $changes = ChangeLog::where('model', 'Loans')
        -> where('model_id', $loan -> id)
        -> orderBy('created_at', 'desc')
        -> with(['changes', 'user'])
        -> get();

        return view('database_changes/get_changes_html', compact('changes'));
    }

    public function save_details(Request $request)
    {
        if ($request -> lo_form) {
            $validated = $request -> validate([
                'points_charged' => 'required',
            ],
            [
                'required' => 'Required',
            ]);
        } else {
            $validated = $request -> validate([
                'loan_officer_1_id' => 'required',
                'processor_id' => 'required',
                'borrower_first' => 'required',
                'borrower_last' => 'required',
                'title_company_select' => 'required_if:loan_status,Closed',
                'title_company' => 'required_if:loan_status,Closed',
                'street' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required',
                'points_charged' => 'required',
                'source' => 'required_if:loan_status,Closed',
                'mortgage_type' => 'required_if:loan_status,Closed',
                'reverse' => 'required_if:loan_status,Closed',
            ],
            [
                'required' => 'Required',
                'lender_uuid.required_if' => 'Lender is Required',
            ]);
        }

        //$amounts = $request -> amount;

        $original_loan_officer_1_id = null;
        if ($request -> uuid != '') {
            $loan = Loans::where('uuid', $request -> uuid) -> first();
            $db_log_data_before = $loan -> replicate();
            $db_log_data_before -> loan_officer_1_name = $loan -> loan_officer_one_name;
            $db_log_data_before -> loan_officer_2_name = $loan -> loan_officer_two_name;
            $db_log_data_before -> processor_name = $loan -> processor_name;
            $db_log_data_before -> lender_name = $loan -> lender_name;

            $original_loan_officer_1_id = $loan -> loan_officer_1_id;
            $loan -> uuid = $request -> uuid;
        } else {
            $loan = new Loans();
            $db_log_data_before = null;
            $loan -> uuid = (string) Str::uuid();
        }

        $ignore = ['uuid', 'title_company_select', 'lo_form'];
        foreach ($request -> all() as $key => $value) {
            if (! in_array($key, $ignore)) {
                if (preg_match('/^\$/', $value)) {
                    $value = preg_replace('/[\$,]+/', '', $value);
                }

                $loan -> $key = $value;
            }
        }

        $loan -> borrower_fullname = $loan -> borrower_first.' '.$loan -> borrower_last;
        $loan -> co_borrower_fullname = $loan -> co_borrower_first.' '.$loan -> co_borrower_last;

        if (! $original_loan_officer_1_id || $original_loan_officer_1_id != $loan -> loan_officer_1_id) {
            $loan_officer_1 = Mortgage::find($request -> loan_officer_1_id);
            $loan_officer_2 = Mortgage::find($request -> loan_officer_2_id);

            $loan -> loan_officer_1_commission_type = $loan_officer_1-> loan_amount_percent > 0 ? 'loan_amount' : 'commission';
            if ($loan_officer_2) {
                $loan -> loan_officer_2_commission_type = $loan_officer_2-> loan_amount_percent > 0 ? 'loan_amount' : 'commission';
            }

            $loan -> loan_officer_1_commission_percent = $loan_officer_1-> commission_percent;
            $loan -> loan_officer_1_loan_amount_percent = $loan_officer_1-> loan_amount_percent;
        }

        $loan -> save();

        $loan = Loans::find($loan -> id);
        $loan -> loan_officer_1_name = $loan -> loan_officer_one_name;
        $loan -> loan_officer_2_name = $loan -> loan_officer_two_name;
        $loan -> processor_name = $loan -> processor_name;
        $loan -> lender_name = $loan -> lender_name;
        $db_log_data_after = $loan;
        $changed_by = auth() -> user() -> id ?? 'system';
        $model = 'Loans';
        $model_id = $loan -> id;

        $db_log = new DatabaseChangeLog();
        $db_log -> log_changes($changed_by, $model, $model_id, $db_log_data_before, $db_log_data_after);

        return response() -> json([
            'success' => true,
            'uuid' => $loan -> uuid,
        ]);
    }

    public function save_time_line(Request $request)
    {
        $request -> validate([
            'lock_expiration' => 'required_if:locked,yes',

        ],
        [
            'required' => 'Required',
        ]);

        Loans::where('uuid', $request -> uuid) -> first() -> update($request -> all());

        return response() -> json(['status' => 'success']);
    }

    public function save_commission(Request $request)
    {
        if (! $request -> form_type) {
            $checks_in_amounts = $request -> check_in_amount;

            if ($checks_in_amounts) {
                $request -> validate([
                    'check_in_amount' => 'required|array',
                    'check_in_amount.*'  => ['required' => 'regex:/^\$([1-9]+|0\.0[1-9]+|0\.[1-9]+)/'], // cannot be 0.00
                ],
                [
                    'required' => 'Required',
                    'regex' => 'Must be greater than 0',
                ]);
            }
        }

        $deduction_amounts = $request -> amount;

        if ($deduction_amounts) {
            $request -> validate([
                'amount'    => 'required|array',
                'amount.*'  => 'required',
            ],
            [
                'required' => 'Required',
            ]);
            $request -> validate([
                'description'    => 'required|array',
                'description.*'  => 'required',
            ],
            [
                'required' => 'Required',
            ]);
            $request -> validate([
                'paid_to'    => 'required|array',
                'paid_to.*'  => 'required',
            ],
            [
                'required' => 'Required',
            ]);
            $request -> validate([
                'paid_to_other'    => 'required|array',
                'paid_to_other.*'  => 'required_if:paid_to.*,Other',
            ],
            [
                'required' => 'Required',
                'required_if' => 'Required',
            ]);
        }

        $db_log = new DatabaseChangeLog();

        if (! $request -> form_type) {
            $loan_officer_deduction_amounts = $request -> loan_officer_deduction_amount;
            if ($loan_officer_deduction_amounts) {
                $request -> validate([
                    'loan_officer_deduction_amount'    => 'required|array',
                    'loan_officer_deduction_amount.*'  => 'required',
                ],
                [
                    'required' => 'Required',
                ]);
                $request -> validate([
                    'loan_officer_deduction_description'    => 'required|array',
                    'loan_officer_deduction_description.*'  => 'required',
                ],
                [
                    'required' => 'Required',
                ]);
            }
        }

        $loan_uuid = $request -> uuid;
        $checks_in = $request -> check_in_amount;
        $deduction_amounts = $request -> amount;
        $descriptions = $request -> description;
        $paid_tos = $request -> paid_to;
        $paid_to_others = $request -> paid_to_other;
        $loan_officer_deduction_indexes = $request -> loan_officer_deduction_index;
        $loan_officer_deduction_amounts = $request -> loan_officer_deduction_amount;
        $loan_officer_deduction_descriptions = $request -> loan_officer_deduction_description;

        $loan = Loans::where('uuid', $request -> uuid) -> first();

        $db_log_data_before = $loan -> replicate();

        if (! $request -> form_type) {
            $loan -> loan_officer_1_commission_type = $request -> loan_officer_1_commission_type;
            $loan -> loan_officer_2_commission_type = $request -> loan_officer_2_commission_type ?? null;
            $loan -> loan_officer_1_commission_percent = $request -> loan_officer_1_commission_percent;
            $loan -> loan_officer_2_commission_percent = $request -> loan_officer_2_commission_percent ?? 0;
            $loan -> loan_officer_1_loan_amount_percent = $request -> loan_officer_1_loan_amount_percent;
            $loan -> loan_officer_2_loan_amount_percent = $request -> loan_officer_2_loan_amount_percent ?? 0;
            $loan -> loan_officer_1_commission_amount = preg_replace('/[\$,]+/', '', $request -> loan_officer_1_commission_amount);
            $loan -> loan_officer_2_commission_amount = preg_replace('/[\$,]+/', '', $request -> loan_officer_2_commission_amount ?? 0);
            $loan -> manager_bonus = preg_replace('/[\$,]+/', '', $request -> manager_bonus);
            $loan -> company_commission = preg_replace('/[\$,]+/', '', $request -> company_commission);
            $loan -> save();

            $db_log_data_after = $loan;
            $changed_by = auth() -> user() -> id ?? 'system';
            $model = 'Loans';
            $model_id = $loan -> id;

            $db_log -> log_changes($changed_by, $model, $model_id, $db_log_data_before, $db_log_data_after);

            $items_in_before_sum = LoansChecksIn::where('loan_uuid', $loan_uuid) -> sum('amount');
            $items_data_before = [
                'checks_in_total' => number_format($items_in_before_sum, 2),
            ];

            LoansChecksIn::where('loan_uuid', $loan_uuid) -> delete();

            $items_after_sum = 0;
            foreach ($checks_in as $check_in) {
                $amount = preg_replace('/[\$,]+/', '', $check_in);
                $items_after_sum += $amount;
                $check = new LoansChecksIn();
                $check -> loan_uuid = $loan_uuid;
                $check -> amount = $amount;
                $check -> save();
            }

            $items_data_after = [
                'checks_in_total' => number_format($items_after_sum, 2),
            ];
            $db_log -> log_changes($changed_by, $model, $model_id, $items_data_before, $items_data_after);

            LoansLoanOfficerDeductions::where('loan_uuid', $loan_uuid) -> delete();

            if ($loan_officer_deduction_amounts) {
                foreach ($loan_officer_deduction_amounts as $index => $loan_officer_deduction_amount) {
                    if (preg_match('/^\$/', $loan_officer_deduction_amount)) {
                        $loan_officer_deduction_amount = preg_replace('/[\$,]+/', '', $loan_officer_deduction_amount);
                    }

                    $deduction = new LoansLoanOfficerDeductions();
                    $deduction -> loan_uuid = $loan_uuid;
                    $deduction -> lo_index = $loan_officer_deduction_indexes[$index];
                    $deduction -> amount = $loan_officer_deduction_amount;
                    $deduction -> description = $loan_officer_deduction_descriptions[$index];
                    $deduction -> save();
                }
            }
        }

        $items_in_before_sum = LoansDeductions::where('loan_uuid', $loan_uuid) -> sum('amount');
        $items_data_before = [
            'deductions_total' => number_format($items_in_before_sum, 2),
        ];

        LoansDeductions::where('loan_uuid', $loan_uuid) -> delete();

        $items_after_sum = 0;

        if ($deduction_amounts) {
            foreach ($deduction_amounts as $index => $amount) {
                if (preg_match('/^\$/', $amount)) {
                    $amount = preg_replace('/[\$,]+/', '', $amount);
                }
                $items_after_sum += $amount;
                $deduction = new LoansDeductions();
                $deduction -> loan_uuid = $loan_uuid;
                $deduction -> amount = $amount;
                $deduction -> description = $descriptions[$index];
                $deduction -> paid_to = $paid_tos[$index];
                if ($deduction -> paid_to == 'Other') {
                    $deduction -> paid_to = $paid_to_others[$index];
                }
                $deduction -> save();
            }
        }

        $items_data_after = [
            'deductions_total' => number_format($items_after_sum, 2),
        ];
        $db_log -> log_changes($changed_by, $model, $model_id, $items_data_before, $items_data_after);
    }

    public function commission_reports(Request $request)
    {
        return view('/heritage_financial/loans/commission_reports');
    }

    public function get_commission_reports(Request $request)
    {
        $direction = $request -> direction ? $request -> direction : 'asc';
        $sort = $request -> sort ? $request -> sort : 'borrower_last';
        $length = $request -> length ? $request -> length : 10;

        $search = $request -> search ?? null;
        $active = $request -> active ?? 'yes';
        $start_date = $request -> start_date ?? null;
        $end_date = $request -> end_date ?? null;
        $date_col = $request -> date_col ?? null;

        $select = ['id', 'uuid', 'settlement_date', 'street', 'city', 'state', 'zip', 'loan_amount', 'loan_officer_1_commission_amount', 'borrower_first', 'borrower_last', 'co_borrower_first', 'co_borrower_last'];
        $select_excel = ['settlement_date', 'borrower_first', 'borrower_last', 'co_borrower_first', 'co_borrower_last', 'street', 'city', 'state', 'zip', 'loan_amount', 'loan_officer_1_commission_amount'];

        $loans = Loans::where(function ($query) use ($search) {
            if ($search) {
                $query -> orWhere('street', 'like', '%'.$search.'%')
                -> orWhere('borrower_fullname', 'like', '%'.$search.'%')
                -> orWhere('co_borrower_fullname', 'like', '%'.$search.'%');
            }
        })
        -> where(function ($query) use ($date_col, $start_date, $end_date) {
            if ($date_col) {
                if ($start_date != '') {
                    $query -> where($date_col, '>=', $start_date);
                }
                if ($end_date != '') {
                    $query -> where($date_col, '<=', $end_date);
                }
            }
        })
        -> orderBy($sort, $direction);

        if ($request -> to_excel == 'false') {
            $loans = $loans -> select($select) -> paginate($length);

            return view('/heritage_financial/loans/get_commission_reports_html', compact('loans'));
        } else {
            $loans = $loans -> select($select_excel) -> get()
            -> toArray();

            $filename = 'loans_'.time().'.xlsx';
            $file = Helper::to_excel($loans, $filename, $select_excel);

            return response() -> json(['file' => $file]);
        }

        $direction = $request -> direction ? $request -> direction : 'asc';
        $sort = $request -> sort ? $request -> sort : 'borrower_last';
        $length = $request -> length ? $request -> length : 10;

        $search = $request -> search ?? null;

        $loans = Loans::where(function ($query) use ($search) {
            if ($search) {
                $query -> whereHas('loan_officer_1', function ($query) use ($search) {
                    $query -> where('fullname', 'like', '%'.$search.'%');
                })
                -> orWhere('street', 'like', '%'.$search.'%')
                -> orWhere('borrower_fullname', 'like', '%'.$search.'%')
                -> orWhere('co_borrower_fullname', 'like', '%'.$search.'%');
            }
        })
        -> orderBy($sort, $direction)
        -> paginate($length);

        return view('/heritage_financial/loans/get_commission_reports_html', compact('loans'));
    }

    public function docs_upload(Request $request)
    {
        $file = $request -> file('loan_docs');
        $uuid = $request -> uuid;
        $size = Helper::get_mb($file -> getSize()).'MB';

        $file_name_orig = $file -> getClientOriginalName();
        $file_name = Helper::clean_file_name($file, '', false, true);

        $dir = 'mortgage/loans/'.$uuid;
        if (! is_dir($dir)) {
            Storage::makeDirectory($dir);
        }
        $file -> storeAs($dir, $file_name);
        $file_location = $dir.'/'.$file_name;
        $file_location_url = Storage::url($dir.'/'.$file_name);

        LoansDocuments::create([
            'loan_uuid' => $uuid,
            'file_name' => $file_name_orig,
            'file_location' => $file_location,
            'file_location_url' => $file_location_url,
            'file_size' => $size,
        ]);
    }

    public function get_docs(Request $request)
    {
        $docs = LoansDocuments::withTrashed()
        -> where('loan_uuid', $request -> uuid)
        -> orderBy('created_at', 'desc')
        -> get()
        -> map(function ($field) {
            $field['created'] = date('M jS, Y g:iA', strtotime($field['created_at']));
            $field['trashed'] = $field -> trashed();

            return $field;
        });

        return compact('docs');
    }

    public function delete_docs(Request $request)
    {
        $ids = explode(',', $request -> ids);

        LoansDocuments::whereIn('id', $ids) -> delete();

        return response() -> json(['status' => 'success']);
    }

    public function restore_docs(Request $request)
    {
        $ids = explode(',', $request -> ids);
        LoansDocuments::withTrashed()
        -> whereIn('id', $ids)
        -> restore();

        return response() -> json(['status' => 'success']);
    }

    public function get_notes(Request $request)
    {
        $print = $request -> print;
        $notes = LoansNotes::where('loan_uuid', $request -> uuid)
        -> with(['user'])
        -> orderBy('created_at', 'desc')
        -> get();

        return view('heritage_financial/loans/get_notes_html', compact('notes', 'print'));
    }

    public function add_notes(Request $request)
    {
        $request -> validate([
            'notes' => 'required',
        ],
        [
            'required' => 'Required',
        ]);

        LoansNotes::create([
            'loan_uuid' => $request -> uuid,
            'notes' => $request -> notes,
            'user_id' => auth() -> user() -> id,
        ]);
    }

    public function delete_note(Request $request)
    {
        LoansNotes::find($request -> id) -> delete();
    }

    public function manager_bonuses(Request $request)
    {
        $years = [];
        $months = [];
        $years_ago = 4;
        $year = date('Y');
        for ($i = $year; $i >= $year - $years_ago; $i--) {
            $years[] = $i;
        }
        for ($i = 12; $i >= 1; $i--) {
            $months[] = $i;
        }

        $loans = Loans::select(DB::raw('year(settlement_date) as year, month(settlement_date) as month, loan_officer_1_id, borrower_fullname, co_borrower_fullname, street, city, state, zip, settlement_date, manager_bonus'))
        -> where('loan_status', 'Closed')
        -> where('settlement_date', '>', date(($year - $years_ago).'-01-01'))
        -> with(['loan_officer_1'])
        -> orderBy('year', 'desc')
        -> orderBy('month', 'desc')
        -> get();

        return view('/heritage_financial/loans/manager_bonuses', compact('loans', 'years', 'months'));
    }

    public function email_manager_bonuses(Request $request)
    {
        $html = $request -> html;
        $to_email = $request -> to_email;
        $subject = 'Monthly Manager Bonus Report';

        Mail::html($html, function ($message) use ($to_email, $subject) {
            $message
            -> to($to_email)
            -> from(auth() -> user() -> email, auth() -> user() -> name)
            -> subject($subject);
        });
    }

    ////////// Import Loans from Old DB ////////////

    public function import_loans(Request $request)
    {
        $loans = LoansOld::get();
        $loans_in_process = LoansInProcessOld::where('did_not_settle_withdrawn', '!=', 'yes')
        -> where('did_not_settle_denied', '!=', 'yes')
        -> where('did_not_settle_inc', '!=', 'yes')
        -> where(function ($query) {
            $query -> where('funded', '0000-00-00')
            -> orWhereNull('funded');
        })
        -> get();

        Loans::truncate();
        LoansNotes::truncate();
        LoansChecksIn::truncate();
        LoansDeductions::truncate();
        LoansLoanOfficerDeductions::truncate();

        // Add Closed Loans
        foreach ($loans as $loan) {

            //$id = $loan -> loan_id;
            $uuid = (string) Str::uuid();
            $settlement_date = $loan -> settlement_date != '0000-00-00' ? $loan -> settlement_date : null;
            $commission_type = $loan -> lo_id == '204' ? 'loan_amount' : 'commission';
            $loan_amount_percent = $loan -> lo_id == '204' ? '.77' : '0';
            $source = 'Office';
            if ($loan -> source == 'From Loan Officer' || $loan -> source == 'From One Of Our LOs') {
                $source = 'Loan Officer';
            }

            $borrower_fullname = trim($loan -> borrower_first.' '.$loan -> borrower_last);
            $co_borrower_fullname = trim($loan -> co_borrower_first.' '.$loan -> co_borrower_last);

            $add_loan = new Loans();

            //$add_loan -> id = $id;
            $add_loan -> uuid = $uuid;
            $add_loan -> loan_status = 'Closed';
            $add_loan -> settlement_date = $settlement_date;
            $add_loan -> borrower_first = $loan -> borrower_first;
            $add_loan -> borrower_last = $loan -> borrower_last;
            $add_loan -> borrower_fullname = $borrower_fullname;
            $add_loan -> co_borrower_first = $loan -> co_borrower_first;
            $add_loan -> co_borrower_last = $loan -> co_borrower_last;
            $add_loan -> co_borrower_fullname = $co_borrower_fullname;
            $add_loan -> street = $loan -> prop_address;
            $add_loan -> city = $loan -> prop_city;
            $add_loan -> state = $loan -> prop_state;
            $add_loan -> county = $loan -> prop_county;
            $add_loan -> zip = $loan -> prop_zip;
            $add_loan -> source = $source;
            $add_loan -> loan_amount = $loan -> loan_amount;
            $add_loan -> points_charged = '2.5';
            $add_loan -> loan_officer_1_id = $loan -> lo_id;
            $add_loan -> loan_officer_2_id = $loan -> lo_2_id;
            $add_loan -> processor_id = $loan -> processor_id;
            $add_loan -> agent_id = $loan -> our_agent_id;
            $add_loan -> agent_name = $loan -> our_agent;
            $add_loan -> heritage_used = $loan -> heritage_used;
            $add_loan -> title_nation_used = $loan -> title_nation_used;
            $add_loan -> title_company = $loan -> title_co_used;
            $add_loan -> loan_officer_1_commission_type = $commission_type;
            $add_loan -> loan_officer_2_commission_type = 'commission';
            $add_loan -> loan_officer_1_commission_percent = (float) $loan -> lo_percent * 100;
            $add_loan -> loan_officer_2_commission_percent = (float) $loan -> lo_2_percent * 100;
            $add_loan -> loan_officer_1_loan_amount_percent = $loan_amount_percent;
            $add_loan -> loan_officer_2_loan_amount_percent = '0';
            $add_loan -> loan_officer_1_commission_amount = $loan -> amount_to_lo;
            $add_loan -> loan_officer_2_commission_amount = $loan -> amount_to_lo2;
            $add_loan -> manager_bonus = $loan -> manager_bonus;
            $add_loan -> company_commission = $loan -> company_net;
            $add_loan -> loan_number = $loan -> loan_number;

            $add_loan -> save();

            // add checks in
            $add_check = new LoansChecksIn();
            $add_check -> loan_uuid = $uuid;
            $add_check -> amount = $loan -> check_from_title;
            $add_check -> save();

            // add deductions
            for ($i = 1; $i < 7; $i++) {
                $deduct = 'deduct'.$i;
                $deduct_desc = 'deduct'.$i.'_desc';
                if ($loan ->{$deduct} > 0) {
                    $add_deduction = new LoansDeductions();
                    $add_deduction -> loan_uuid = $uuid;
                    $add_deduction -> description = $loan ->{$deduct_desc};
                    $add_deduction -> amount = $loan ->{$deduct};
                    $paid_to = 'Company';
                    if (stristr($loan ->{$deduct_desc}, 'Credit')) {
                        $paid_to = $loan -> lo;
                    }
                    $add_deduction -> paid_to = $paid_to;
                    $add_deduction -> save();
                }
            }

            // add lo deductions
            if ($loan -> lo_comm_deduction > 0) {
                $add_loan_officer_deduction = new LoansLoanOfficerDeductions();
                $add_loan_officer_deduction -> loan_uuid = $uuid;
                $add_loan_officer_deduction -> amount = $loan -> lo_comm_deduction;
                $add_loan_officer_deduction -> lo_index = '1';
                $add_loan_officer_deduction -> save();
            }

            // add notes
            if ($loan -> loan_notes != '') {
                $user = User::where('email', 'like', '%claure%') -> first();
                $user_id = $user -> id;
                $user_name = $user -> name;

                $add_notes = new LoansNotes();
                $add_notes -> loan_uuid = $uuid;
                $add_notes -> user_id = $user_id;
                $add_notes -> createdBy = $user_name;
                $add_notes -> notes = $loan -> loan_notes;
            }
        }

        // Add Loans In Process
        foreach ($loans_in_process as $loan_in_process) {
            $uuid = (string) Str::uuid();
            $settlement_date = $loan_in_process -> settlement_scheduled != '0000-00-00' ? $loan_in_process -> settlement_scheduled : null;
            $commission_type = $loan_in_process -> lo_id == '204' ? 'loan_amount' : 'commission';
            $loan_amount_percent = $loan_in_process -> lo_id == '204' ? '.77' : '0';
            $source = 'Office';
            if ($loan_in_process -> loan_source == 'From Loan Officer' || $loan_in_process -> loan_source == 'From One Of Our LOs') {
                $source = 'Loan Officer';
            }

            $borrower_fullname = trim($loan_in_process -> borrower_first.' '.$loan_in_process -> borrower_last);
            $co_borrower_fullname = trim($loan_in_process -> co_borrower_first.' '.$loan_in_process -> co_borrower_last);

            $heritage_used = 'no';
            if (stristr($loan_in_process -> title_company, 'heritage title')) {
                $heritage_used = 'yes';
            }
            $title_nation_used = 'no';
            if (stristr($loan_in_process -> title_company, 'title nation')) {
                $title_nation_used = 'yes';
            }

            $loan_officer = Mortgage::find($loan_in_process -> lo_id);

            $add_loan = new Loans();

            $add_loan -> uuid = $uuid;
            $add_loan -> loan_status = 'Open';
            $add_loan -> settlement_date = $settlement_date;
            $add_loan -> borrower_first = $loan_in_process -> borrower_first;
            $add_loan -> borrower_last = $loan_in_process -> borrower_last;
            $add_loan -> borrower_fullname = $borrower_fullname;
            $add_loan -> co_borrower_first = $loan_in_process -> co_borrower_first;
            $add_loan -> co_borrower_last = $loan_in_process -> co_borrower_last;
            $add_loan -> co_borrower_fullname = $co_borrower_fullname;
            $add_loan -> street = $loan_in_process -> street;
            $add_loan -> city = $loan_in_process -> city;
            $add_loan -> state = $loan_in_process -> state;
            $add_loan -> county = $loan_in_process -> county;
            $add_loan -> zip = $loan_in_process -> zip;
            $add_loan -> source = $source;
            $add_loan -> loan_number = $loan_in_process -> loan_number;
            $add_loan -> loan_amount = preg_replace('/[\$,]+/', '', $loan_in_process -> loan_amount);
            $add_loan -> points_charged = '2.5';
            $add_loan -> loan_officer_1_id = $loan_in_process -> lo_id;
            $add_loan -> processor_id = $loan_in_process -> processor_id;
            $add_loan -> agent_id = $loan_in_process -> agent_id;
            $add_loan -> agent_name = $loan_in_process -> agent;
            $add_loan -> heritage_used = $heritage_used;
            $add_loan -> title_nation_used = $title_nation_used;
            $add_loan -> title_company = $loan_in_process -> title_company;
            $add_loan -> loan_officer_1_commission_type = $commission_type;
            $add_loan -> loan_officer_2_commission_type = 'commission';
            $add_loan -> loan_officer_1_commission_percent = (float) $loan_officer -> commission_percent;
            $add_loan -> loan_officer_1_loan_amount_percent = $loan_amount_percent;
            $add_loan -> loan_officer_2_loan_amount_percent = '0';

            $add_loan -> save();

            $company_commission = $loan_in_process -> commission != '' ? preg_replace('/[\$,]+/', '', $loan_in_process -> commission) : '0.00';

            // add checks in
            $add_check = new LoansChecksIn();
            $add_check -> loan_uuid = $uuid;
            $add_check -> amount = $company_commission;
            $add_check -> save();
        }
    }

    public function add_time_line(Request $request)
    {
        $loans_old = LoansInProcessOld::get();

        foreach ($loans_old as $loan_old) {
            $loan_new = Loans::where('loan_number', $loan_old -> loan_number)
            -> first();

            if ($loan_new) {
                if ($loan_old -> package_sub == '0000-00-00') {
                    $loan_old -> package_sub = null;
                }
                if ($loan_old -> processor_sub == '0000-00-00') {
                    $loan_old -> processor_sub = null;
                }
                if ($loan_old -> cond_rec_date == '0000-00-00') {
                    $loan_old -> cond_rec_date = null;
                }
                if ($loan_old -> title_sub == '0000-00-00') {
                    $loan_old -> title_sub = null;
                }
                if ($loan_old -> title_rec == '0000-00-00') {
                    $loan_old -> title_rec = null;
                }
                if ($loan_old -> uw_sub == '0000-00-00') {
                    $loan_old -> uw_sub = null;
                }
                if ($loan_old -> appraisal_sub == '0000-00-00') {
                    $loan_old -> appraisal_sub = null;
                }
                if ($loan_old -> appraisal_rec == '0000-00-00') {
                    $loan_old -> appraisal_rec = null;
                }
                if ($loan_old -> voe_sub == '0000-00-00') {
                    $loan_old -> voe_sub = null;
                }
                if ($loan_old -> voe_rec == '0000-00-00') {
                    $loan_old -> voe_rec = null;
                }
                if ($loan_old -> cond_sub == '0000-00-00') {
                    $loan_old -> cond_sub = null;
                }
                if ($loan_old -> clear_to_close == '0000-00-00') {
                    $loan_old -> clear_to_close = null;
                }
                if ($loan_old -> settlement_scheduled == '0000-00-00') {
                    $loan_old -> settlement_scheduled = null;
                }
                if ($loan_old -> closed == '0000-00-00') {
                    $loan_old -> closed = null;
                }
                if ($loan_old -> funded == '0000-00-00') {
                    $loan_old -> funded = null;
                }

                $loan_new -> time_line_package_to_borrower = $loan_old -> package_sub;
                $loan_new -> time_line_sent_to_processing = $loan_old -> processor_sub;
                $loan_new -> time_line_conditions_received_status = $loan_old -> cond_rec_status;
                $loan_new -> time_line_conditions_received = $loan_old -> cond_rec_date;
                $loan_new -> time_line_title_ordered = $loan_old -> title_sub;
                $loan_new -> time_line_title_received = $loan_old -> title_rec;
                $loan_new -> time_line_submitted_to_uw = $loan_old -> uw_sub;
                $loan_new -> time_line_appraisal_ordered = $loan_old -> appraisal_sub;
                $loan_new -> time_line_appraisal_received = $loan_old -> appraisal_rec;
                $loan_new -> time_line_voe_ordered = $loan_old -> voe_sub;
                $loan_new -> time_line_voe_received = $loan_old -> voe_rec;
                $loan_new -> time_line_conditions_submitted = $loan_old -> cond_sub;
                $loan_new -> time_line_clear_to_close = $loan_old -> clear_to_close;
                $loan_new -> time_line_scheduled_settlement = $loan_old -> settlement_scheduled;
                $loan_new -> time_line_closed = $loan_old -> closed;
                $loan_new -> time_line_funded = $loan_old -> funded;
                $loan_new -> save();
            }
        }
    }

    public function add_lock_details(Request $request)
    {
        $loans_old = LoansInProcessOld::get();

        foreach ($loans_old as $loan_old) {
            $loan_new = Loans::where('loan_number', $loan_old -> loan_number)
            -> first();

            if ($loan_new) {
                if ($loan_old -> lock_expire == '0000-00-00') {
                    $loan_old -> lock_expire = null;
                }

                $loan_new -> locked = $loan_old -> locked;
                $loan_new -> lock_expiration = $loan_old -> lock_expire;
                $loan_new -> save();
            }
        }
    }

    public function add_agent_details(Request $request)
    {
        $loans_old = LoansOld::get();

        foreach ($loans_old as $loan_old) {
            $loan_new = Loans::where('loan_number', $loan_old -> loan_number)
            -> first();

            if ($loan_new) {
                $agent_company = null;
                if ($loan_old -> our_agent_id > 0) {
                    $agent_company = 'Taylor Properties';
                } elseif ($loan_old -> other_re_company != '') {
                    $agent_company = $loan_old -> other_re_company;
                }
                if (! $agent_company) {
                    dump($loan_old -> our_agent_id);
                }
                $loan_new -> agent_company = $agent_company;
                $loan_new -> save();
            }
        }
    }

    public function add_missing_details(Request $request)
    {
        $loans_old = LoansOld::where('lender', '!=', '') -> get();

        foreach ($loans_old as $loan_old) {
            $loan_new = Loans::where('loan_number', $loan_old -> loan_number)
            -> first();

            if ($loan_new) {
                $lender_name = $loan_old -> lender;
                if ($lender_name == 'Amtrust') {
                    $lender_name = 'AmTrust Bank';
                } elseif ($lender_name == 'Homecomings') {
                    $lender_name = 'Homecomings Financial';
                } elseif ($lender_name == 'Cardinal Financial Company') {
                    $lender_name = 'Cardinal Mortgage';
                } elseif ($lender_name == 'Pacific Union') {
                    $lender_name = 'Pacific Union Financial';
                } elseif ($lender_name == 'American Financial') {
                    $lender_name = 'American Financial Resources';
                } elseif ($lender_name == 'E Mortgage Management, LLC.') {
                    $lender_name = 'EMM Loans, LLC.';
                }

                $lender = Lenders::where('company_name', $lender_name) -> first();
                $lender_uuid = $lender ? $lender -> uuid : null;

                $loan_purpose = $loan_old -> loan_type;
                if ($loan_purpose == 'Buy A Home') {
                    $loan_purpose = 'purchase';
                } elseif ($loan_purpose == 'Refinance Mortgage') {
                    $loan_purpose = 'refi';
                }

                $loan_type = $loan_old -> loan_type2;
                if ($loan_type == 'First Mortgage') {
                    $loan_type = 'first';
                } elseif ($loan_type == 'Second Mortgage') {
                    $loan_type = 'second';
                }

                $loan_new -> mortgage_type = $loan_type;
                $loan_new -> loan_purpose = $loan_purpose;
                $loan_new -> loan_type = $loan_old -> loan_type3;
                $loan_new -> lender_uuid = $lender_uuid;
                $loan_new -> reverse = $loan_old -> reverse;

                $loan_new -> save();
            }
        }
    }
}
