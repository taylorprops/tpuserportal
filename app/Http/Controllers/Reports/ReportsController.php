<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;

class ReportsController extends Controller
{

    public function reports(Request $request) {

        return view('/reports/reports');

    }

    public function report_loans_in_process(Request $request) {

        $loans = Loans::where('loan_status', 'Open')
        -> with(['loan_officer'])
        -> get();

    }

}
