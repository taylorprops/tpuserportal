<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Models\Employees\Mortgage;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;
use Illuminate\Database\Eloquent\Builder;

class ReportsController extends Controller
{

    public function reports(Request $request) {

        return view('/reports/reports');

    }

    public function loans_in_process(Request $request) {

        $loan_officers = Mortgage::where('active', 'yes')
        -> with(['loans' => function($query) {
            $query -> where('loan_status', 'open');
        }])
        -> whereHas('loans', function (Builder $query) {
            $query -> where('loan_status', 'open');
        })
        -> get();

        dd($loan_officers);

        $loans = Loans::where('loan_status', 'Open')
        -> with(['loan_officer'])
        -> get();

    }

}
