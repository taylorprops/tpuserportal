<?php

namespace App\Http\Controllers\Reports;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Models\Employees\Mortgage;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;
use Illuminate\Support\Facades\Storage;
use App\Models\HeritageFinancial\Lenders;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\DocManagement\Resources\LocationData;

class ReportsController extends Controller
{

    public function reports(Request $request) {

        $states = Loans::groupBy('state') -> pluck('state');
        $lenders = Lenders::where('active', 'yes') -> orderBy('company_name') -> get();

        return view('/reports/reports', compact('states', 'lenders'));

    }

    public function get_detailed_report(Request $request) {

        $report_type = $request -> report_type;

        return view('/reports/data/mortgage/get_detailed_report_html', compact('report_type'));

    }

    public function get_detailed_report_data(Request $request) {

        $direction = $request -> direction ? $request -> direction : 'desc';
        $sort = $request -> sort ? $request -> sort : 'settlement_date';
        $length = $request -> length ? $request -> length : 10;

        $search = $request -> search ?? null;
        $active = $request -> active ?? 'yes';

        $settlement_date_start = $request -> settlement_date_start ?? null;
        $settlement_date_end = $request -> settlement_date_end ?? null;
        $lender_uuid = $request -> lender_uuid ?? null;
        $state = $request -> state ?? null;
        $loan_type = $request -> loan_type ?? null;
        $loan_purpose = $request -> loan_purpose ?? null;
        $mortgage_type = $request -> mortgage_type ?? null;
        $reverse = $request -> reverse ?? null;
        $report_type = $request -> report_type ?? null;
        $to_excel = $request -> to_excel ?? 'false';

        $loans = Loans::where('loan_status', 'closed')
        -> where(function($query) use ($settlement_date_start, $settlement_date_end, $lender_uuid , $state, $loan_type, $loan_purpose, $mortgage_type, $reverse) {

            if($settlement_date_start) {
                $query -> where('settlement_date', '>=', $settlement_date_start);
            }
            if($settlement_date_end) {
                $query -> where('settlement_date', '<=', $settlement_date_end);
            }
            if($lender_uuid) {
                $query -> where('lender_uuid', $lender_uuid);
            }
            if($state) {
                $query -> where('state', $state);
            }
            if($loan_type) {
                $query -> where('loan_type', $loan_type);
            }
            if($loan_purpose) {
                $query -> where('loan_purpose', $loan_purpose);
            }
            if($mortgage_type) {
                $query -> where('mortgage_type', $mortgage_type);
            }
            if($reverse) {
                $query -> where('reverse', $reverse);
            }

        })
        -> with(['loan_officer_1', 'lender'])
        -> orderBy($sort, $direction);

        if ($to_excel == 'false') {

            if(!$report_type) {
                $loans = $loans -> paginate($length);
                return view('/reports/data/mortgage/get_detailed_report_data_html', compact('loans'));
            }

            $loans = $loans -> get();

            return view('/reports/data/mortgage/get_detailed_report_details_html', compact('loans'));



        } else {

            $loans = $loans -> get();

            $data = [];
            $select = ['Loan Officer', 'Borrowers', 'Address', 'Settlement Date', 'Loan Amount', 'Company Commission', 'Loan Type', 'Loan Purpose', 'Mortgage Type', 'Lender', 'State'];

            foreach($loans as $loan) {

                $borrower = $loan -> borrower_last.', '.$loan -> borrower_first;
                if($loan -> co_borrower_first != '') {
                    $borrower .= '<br>'.$loan -> co_borrower_last.', '.$loan -> co_borrower_first;
                }
                $address = $loan -> street.'<br>'.$loan -> city.' '.$loan -> state.' '.$loan -> zip;
                $lender = $loan -> lender -> company_name ?? null;

                $data[] = [
                    'loan_officer' => $loan -> loan_officer_1 -> fullname ?? null,
                    'borrower' => $borrower ?? null,
                    'address' => $address,
                    'settlement_date' => $loan -> settlement_date ?? null,
                    'loan_amount' => '$'.number_format($loan -> loan_amount),
                    'company_commission' => '$'.number_format($loan -> company_commission),
                    'loan_type' => ucwords($loan -> loan_type),
                    'loan_purpose' => ucwords($loan -> loan_purpose),
                    'mortgage_type' => ucwords($loan -> mortgage_type),
                    'lender' => $lender ?? null,
                    'state' => $loan -> state,
                ];

            }

            $filename = 'loans_'.time().'.xlsx';
            $file = Helper::to_excel($data, $filename, $select);

            return response() -> json(['file' => $file]);

        }




    }

    public function loans_in_process() {

        $report = 'loans_in_process';

        $loan_officers = Mortgage::select(['id', 'fullname'])
        -> where('active', 'yes')
        -> with(['loans' => function($query) {
            $query -> where('loan_status', 'open')
            -> select(
                'loan_officer_1_id',
                'borrower_fullname',
                'co_borrower_fullname',
                'street',
                'city',
                'state',
                'zip',
                'loan_purpose',
                'processor_id',
                'agent_name_seller',
                'agent_company_seller',
                'agent_name_buyer',
                'agent_company_buyer',
                'time_line_sent_to_processing',
                'time_line_conditions_received',
                'lock_expiration',
                'settlement_date',
                'loan_amount',
                'company_commission',
                'title_company'
            );
        }, 'loans.processor:id,fullname'])
        -> whereHas('loans', function (Builder $query) {
            $query -> where('loan_status', 'open');
        })
        -> withCount(['loans' => function ($query) {
            $query -> where('loan_status', 'open');
        }])
        -> orderBy('loans_count', 'desc')
        -> get();

        $report_name = 'Loans In Process';
        $file_name = $report.time().'.pdf';

        $table_headers = ['Borrower', 'Address', 'Type', 'Processor', 'Agent', 'Sent To Processing', 'Approved', 'Lock Expire', 'Settlement Date', 'Loan Amount', 'Commission', 'Title Company'];

        $pdf = App::make('dompdf.wrapper')
        -> setPaper('legal', 'landscape')
        -> loadView('/reports/data/mortgage/'.$report, compact('report_name', 'table_headers', 'loan_officers'))
        -> save(Storage::path('tmp/'.$file_name));

        return 'tmp/'.$file_name;

    }
/*
    public function get_closed_loans_by_month($year) {

        // $loans = Loans::join('heritage_financial_loans_deductions', 'heritage_financial_loans.uuid', '=', 'heritage_financial_loans_deductions.loan_uuid')
        // -> join('heritage_financial_loans_checks_in', 'heritage_financial_loans.uuid', '=', 'heritage_financial_loans_checks_in.loan_uuid')
        // -> select(DB::raw(
        //     'YEAR(heritage_financial_loans.settlement_date) as year,
        //     MONTH(heritage_financial_loans.settlement_date) as month,
        //     SUM(heritage_financial_loans_checks_in.amount) - SUM(heritage_financial_loans_deductions.amount) as checks_in,
        //     SUM(heritage_financial_loans.loan_amount) as total_loan_amount,
        //     SUM(heritage_financial_loans.loan_officer_1_commission_amount) as total_loan_officer_1_commission_amount,
        //     SUM(heritage_financial_loans.loan_officer_2_commission_amount) as total_loan_officer_2_commission_amount,
        //     SUM(heritage_financial_loans.company_commission) as total_company_commission,
        //     AVG(loan_amount) as average_loan_amount,
        //     count(*) as total'
        //     ))
        // -> whereRaw('YEAR(settlement_date) = '.$year)
        // -> where('loan_status', 'closed')
        // -> groupByRaw('year, month')
        // -> orderBy('month', 'desc')
        // -> get();

        $start = date('Y-01-01', strtotime('-1 year'));

        $loans = Loans:: where('settlement_date', '>=', $start)
        -> where('loan_status', 'closed')
        -> with(['checks_in', 'deductions'])
        -> orderBy('settlement_date', 'desc')
        -> get();

        dd($loans);
        return $loans;

    } */

    public function closed_loans_by_month() {

        $report = 'closed_loans_by_month';
        $start = date('Y-01-01', strtotime('-1 year'));

        $loans = Loans::select(DB::raw('*, YEAR(settlement_date) as year, MONTH(settlement_date) as month'))
        -> where('settlement_date', '>=', $start)
        -> where('loan_status', 'closed')
        -> with(['checks_in', 'deductions'])
        -> orderBy('settlement_date', 'desc')
        -> get();

        $years = [date('Y'), date('Y', strtotime('-1 year'))];
        $months = [];
        for($i = 12; $i >= 1; $i--) {
            $months[] = $i;
        }

        $report_name = 'Closed Loans By Month';
        $file_name = $report.time().'.pdf';

        $table_headers = ['Month', 'Count', 'Loan Amounts', 'Commission In', 'LO/LO2 Commission', 'Manager Bonus', 'Company Commission', 'Avg. Loan Amount'];

        $pdf = App::make('dompdf.wrapper')
        -> setPaper('legal', 'landscape')
        -> loadView('/reports/data/mortgage/'.$report, compact('report_name', 'table_headers', 'loans', 'years', 'months'))
        -> save(Storage::path('tmp/'.$file_name));

        return 'tmp/'.$file_name;

    }

    public function closed_loans_by_month_detailed() {

        $report = 'closed_loans_by_month_detailed';
        $start = date('Y-m-01', strtotime('-1 year'));

        $loans = Loans::select(DB::raw('*, YEAR(settlement_date) as year, MONTH(settlement_date) as month, MONTHNAME(settlement_date) as month_name'))
        -> where('settlement_date', '>=', $start)
        -> where('loan_status', 'closed')
        -> with(['checks_in', 'deductions', 'loan_officer_1'])
        -> orderBy('settlement_date', 'desc')
        -> get();

        $years = [date('Y'), date('Y', strtotime('-1 year'))];
        $months = [];
        for($i = 12; $i >= 1; $i--) {
            $months[] = $i;
        }

        $loan_officers = Mortgage::where('active', 'yes')
        -> where('emp_position', 'loan_officer')
        -> orderBy('last_name')
        -> get();

        $report_name = 'Closed Loans By Month Detailed';
        $file_name = $report.time().'.pdf';

        $table_headers = ['Month', 'Loan Officer', 'Loan Count', 'Loans Amount', 'Commission In', 'Commission Out', 'Company Commission', 'Avg. Loan Amount'];

        $pdf = App::make('dompdf.wrapper')
        -> setPaper('legal', 'landscape')
        -> loadView('/reports/data/mortgage/'.$report, compact('report_name', 'table_headers', 'loans', 'years', 'months', 'loan_officers'))
        -> save(Storage::path('tmp/'.$file_name));

        return 'tmp/'.$file_name;

    }

    public function closed_loans_by_loan_officer() {



    }

    public function closed_loans_by_loan_officer_summary() {



    }

    public function print(Request $request) {

        $reports_data = $request -> reports;

        $pdfs = [];

        foreach($reports_data as $report_data) {

            $pdf = $this -> $report_data();

            $pdfs[] = Storage::path($pdf);

        }

        $file_name = 'HeritageReport_'.date('Y-m-d').'_'.time();
        $report = Storage::path('tmp/'.$file_name);

        exec('pdftk '.implode(' ', $pdfs).' cat output '.$report);

        return'tmp/'.$file_name;

    }

}
