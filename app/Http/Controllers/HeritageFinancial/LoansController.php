<?php

namespace App\Http\Controllers\HeritageFinancial;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employees\LoanOfficers;
use App\Models\HeritageFinancial\Loans;
use App\Models\HeritageFinancial\LoansChecksIn;
use App\Models\HeritageFinancial\LoansDeductions;
use App\Models\DocManagement\Resources\LocationData;
use App\Models\HeritageFinancial\LoansLoanOfficerDeductions;

class LoansController extends Controller
{

    public function loans(Request $request) {

        return view('heritage_financial/loans/loans');

    }

    public function get_loans(Request $request) {

        $loans = Loans::get();
        return view('heritage_financial/loans/get_loans_html', compact('loans'));

    }

    public function view_loan(Request $request) {

        $loan = null;
        $deductions = [];
        $checks_in = [];
        $loan_officer = null;
        $loan_officer_2 = null;
        $loan_officer_commission_type = null;
        $loan_officer_2_commission_type = null;
        $loan_officer_2_commission_sub_type = null;
        $loan_officer_active_commission_tab = null;
        $loan_officer_2_active_commission_tab = null;
        $loan_officer_deductions = null;

        if ($request -> uuid) {

            $loan = Loans::where('uuid', $request -> uuid)
            -> with(['deductions', 'checks_in', 'loan_officer_1', 'loan_officer_2', 'loan_officer_deductions'])
            -> first();

            $deductions = $loan -> deductions;
            $checks_in = $loan -> checks_in;

            $loan_officer = $loan -> loan_officer;
            $loan_officer_commission_type = $loan -> loan_officer_commission_type;
            $loan_officer_active_commission_tab = $loan -> loan_officer_commission_type  == 'loan_amount' ? '2' : '1';

            $loan_officer_2 = $loan -> loan_officer_2;
            $loan_officer_2_commission_type = $loan -> loan_officer_2_commission_type;
            $loan_officer_2_commission_sub_type = $loan -> loan_officer_2_commission_sub_type;
            $loan_officer_2_active_commission_tab = $loan -> loan_officer_2_commission_type  == 'loan_amount' ? '2' : '1';

            $loan_officer_deductions = $loan -> loan_officer_deductions;


        }



        $states = LocationData::getStates();

        $loan_officers = LoanOfficers::where('active', 'yes') -> orderBy('last_name') -> get();

        return view('heritage_financial/loans/view_loan_html', compact('loan', 'deductions', 'checks_in', 'loan_officer', 'loan_officer_2', 'loan_officer_commission_type', 'loan_officer_active_commission_tab', 'loan_officer_2_commission_type', 'loan_officer_2_commission_sub_type', 'loan_officer_2_active_commission_tab', 'loan_officer_deductions', 'states', 'loan_officers'));

    }

    public function save_details(Request $request) {

        $request -> validate([
            'loan_officer_id' => 'required',
            'processor_id' => 'required',
            'borrower_first' => 'required',
            'borrower_last' => 'required',
            'title_company_select' => 'required',
            'title_company' => 'required',
            'street' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'settlement_date' => 'required',
            'loan_amount' => 'required',
            'loan_number' => 'required',
        ],
        [
            'required' => 'Required'
        ]);

        $amounts = $request -> amount;


        if ($request -> uuid != '') {
            $loan = Loans::where('uuid', $request -> uuid) -> first();
            $loan -> uuid = $request -> uuid;
        } else {
            $loan = new Loans();
            $loan -> uuid = (string) Str::uuid();
        }

        $ignore = ['uuid', 'title_company_select'];
        foreach ($request -> all() as $key => $value) {

            if (!in_array($key, $ignore)) {

                if (preg_match('/^\$/', $value)) {
                    $value = preg_replace('/[\$,]+/', '', $value);
                }

                $loan -> $key = $value;

            }

        }

        $loan_officer = LoanOfficers::find($request -> loan_officer_id);
        $loan_officer_2 = LoanOfficers::find($request -> loan_officer_2_id);

        $loan -> loan_officer_commission_type = $loan_officer -> loan_amount_percent > 0 ? 'loan_amount' : 'commission';
        if ($loan_officer_2) {
            $loan -> loan_officer_2_commission_type = $loan_officer_2 -> loan_amount_percent > 0 ? 'loan_amount' : 'commission';
        }

        $loan -> save();

        return response() -> json([
            'success' => true,
            'uuid' => $loan -> uuid
        ]);

    }


    public function save_commission(Request $request) {

        $checks_in_amounts = $request -> check_in_amount;

        $request -> validate([
            'check_in_amount' => 'required|array',
            'check_in_amount.*'  => ['required' => 'regex:/^\$([1-9]+|0\.0[1-9]+|0\.[1-9]+)/'],
        ],
        [
            'required' => 'Required',
            'regex' => 'Must be greater than 0'
        ]);

        $deduction_amounts = $request -> amount;

        if ($deduction_amounts) {

            $request -> validate([
                'amount'    => 'required|array',
                'amount.*'  => 'required',
            ],
            [
                'required' => 'Required'
            ]);
            $request -> validate([
                'description'    => 'required|array',
                'description.*'  => 'required',
            ],
            [
                'required' => 'Required'
            ]);
            $request -> validate([
                'paid_to'    => 'required|array',
                'paid_to.*'  => 'required',
            ],
            [
                'required' => 'Required'
            ]);
            $request -> validate([
                'paid_to_other'    => 'required|array',
                'paid_to_other.*'  => 'required_if:paid_to.*,Other',
            ],
            [
                'required' => 'Required',
                'required_if' => 'Required'
            ]);

        }

        $loan_officer_deduction_amounts = $request -> loan_officer_deduction_amount;
        if ($loan_officer_deduction_amounts) {

            $request -> validate([
                'loan_officer_deduction_amount'    => 'required|array',
                'loan_officer_deduction_amount.*'  => 'required',
            ],
            [
                'required' => 'Required'
            ]);
            $request -> validate([
                'loan_officer_deduction_description'    => 'required|array',
                'loan_officer_deduction_description.*'  => 'required',
            ],
            [
                'required' => 'Required'
            ]);

        }


        $loan_uuid = $request -> uuid;
        $checks_in = $request -> check_in_amount;
        $amounts = $request -> amount;
        $descriptions = $request -> description;
        $paid_tos = $request -> paid_to;
        $paid_to_others = $request -> paid_to_other;
        $loan_officer_deduction_amounts = $request -> loan_officer_deduction_amount;
        $loan_officer_deduction_descriptions = $request -> loan_officer_deduction_description;


        LoansChecksIn::where('loan_uuid', $loan_uuid) -> delete();

        foreach ($checks_in as $check_in) {
            $check = new LoansChecksIn();
            $check -> loan_uuid  = $loan_uuid;
            $check -> amount = preg_replace('/[\$,]+/', '', $check_in);
            $check -> save();
        }

        LoansDeductions::where('loan_uuid', $loan_uuid) -> delete();

        if($amounts) {

            foreach ($amounts as $index => $amount) {

                if (preg_match('/^\$/', $amount)) {
                    $amount = preg_replace('/[\$,]+/', '', $amount);
                }

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

        LoansLoanOfficerDeductions::where('loan_uuid', $loan_uuid) -> delete();

        if($loan_officer_deduction_amounts) {

            foreach ($loan_officer_deduction_amounts as $index => $loan_officer_deduction_amount) {

                if (preg_match('/^\$/', $loan_officer_deduction_amount)) {
                    $loan_officer_deduction_amount = preg_replace('/[\$,]+/', '', $loan_officer_deduction_amount);
                }

                $deduction = new LoansLoanOfficerDeductions();
                $deduction -> loan_uuid = $loan_uuid;
                $deduction -> lo_index =  '1';
                $deduction -> amount = $loan_officer_deduction_amount;
                $deduction -> description = $loan_officer_deduction_descriptions[$index];
                $deduction -> save();
            }

        }

    }

}
