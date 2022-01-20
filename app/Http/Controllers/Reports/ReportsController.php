<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\Employees\Mortgage;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ReportsController extends Controller
{

    public function reports(Request $request) {

        return view('/reports/reports');

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
                'agent_name',
                'agent_company',
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
