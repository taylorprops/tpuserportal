<?php

namespace App\Http\Controllers\Reports;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Models\Employees\Mortgage;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class ReportsController extends Controller
{

    public function reports(Request $request) {

        return view('/reports/reports');

    }

    public function loans_in_process() {

        $loan_officers = Mortgage::select(['id', 'fullname'])
        -> where('active', 'yes')
        -> with(['loans' => function($query) {
            $query -> where('loan_status', 'open')
            -> select('loan_officer_1_id', 'borrower_fullname', 'co_borrower_fullname', 'street', 'city', 'state', 'zip', 'loan_purpose', 'processor_id', 'agent_name', 'agent_company', 'time_line_sent_to_processing', 'time_line_conditions_received', 'lock_expiration', 'settlement_date', 'loan_amount', 'company_commission', 'title_company');
        }, 'loans.processor:id,fullname'])
        -> whereHas('loans', function (Builder $query) {
            $query -> where('loan_status', 'open');
        })
        -> get();

        $report_name = 'Loans In Process';
        $file_name = 'loan_in_process_'.time().'.pdf';

        $table_headers = ['Borrower', 'Address', 'Type', 'Processor', 'Agent', 'Sent To Processing', 'Approved', 'Lock Expire', 'Settlement Date', 'Loan Amount', 'Commission', 'Title Company'];

        /* $pdf = PDF::loadView('/reports/data/mortgage/loans_in_process',  compact('report_name', 'table_headers', 'loan_officers'))
        -> save(Storage::path('tmp/'.$file_name))
        -> setPaper('legal', 'landscape')
        -> setOptions(['defaultFont' => 'Arial']); */

        $pdf = App::make('dompdf.wrapper')
        -> setPaper('legal', 'landscape')
        -> loadView('/reports/data/mortgage/loans_in_process', compact('report_name', 'table_headers', 'loan_officers'))
        -> save(Storage::path('tmp/'.$file_name));

        return 'tmp/'.$file_name;

    }

    public function print(Request $request) {

        $reports = $request -> reports;

        $pdfs = [];

        foreach($reports as $report) {

            $pdf = $this -> $report();

            $pdfs[] = $pdf;

        }

        dump($reports);


    }

}
