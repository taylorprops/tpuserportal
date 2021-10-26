<?php

namespace App\Http\Controllers\HeritageFinancial;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employees\LoanOfficers;
use App\Models\HeritageFinancial\Loans;
use App\Models\HeritageFinancial\LoansDeductions;
use App\Models\DocManagement\Resources\LocationData;

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
        $deductions = null;
        if ($request -> uuid) {
            $loan = Loans::where('uuid', $request -> uuid)
            -> with(['deductions'])
            -> first();
            $deductions = $loan -> deductions;
        }

        $states = LocationData::getStates();

        $loan_officers = LoanOfficers::where('active', 'yes') -> orderBy('last_name') -> get();

        return view('heritage_financial/loans/view_loan_html', compact('loan', 'deductions', 'states', 'loan_officers'));

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

        $loan -> save();

    }


    public function save_commission(Request $request) {

        $request -> validate([
            'commission_check_amount' => 'required',
        ],
        [
            'required' => 'Required'
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


        $loan_uuid = $request -> uuid;
        $amounts = $request -> amount;
        $descriptions = $request -> description;
        $paid_tos = $request -> paid_to;
        $paid_to_others = $request -> paid_to_other;

        LoansDeductions::where('loan_uuid', $loan_uuid) -> delete();

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

}
