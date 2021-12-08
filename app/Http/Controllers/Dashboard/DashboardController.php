<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;

class DashboardController extends Controller
{
    public function dashboard(Request $request) {

        $group = auth() -> user() -> group;

        // Mortgage
        if ($group == 'mortgage') {

            $active_loans = Loans::where('loan_status', 'Open')
            -> orderBy('settlement_date', 'desc')
            -> get();

            $recent_commissions = Loans::where('loan_status', 'Closed')
            -> where('settlement_date', '>', date('Y-m-d', strtotime('-3 month')))
            -> orderBy('settlement_date', 'desc')
            -> get();

            return view('/dashboard/dashboard_'.$group, compact('group', 'active_loans', 'recent_commissions'));

        } else if ($group == 'in_house') {

            return view('/dashboard/dashboard_'.$group, compact('group'));

        }



    }
}
