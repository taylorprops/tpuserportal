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

            $table_headers = [
                ['title' => 'Locked', 'db_field' => 'locked', 'type' => 'yes_no'],
                ['title' => 'Approved/Suspended', 'db_field' => 'time_line_conditions_received_status', 'type' => 'approved_suspended'],
                ['title' => 'Package Sent', 'db_field' => '', 'type' => 'date'],
                ['title' => 'Sent To Processor', 'db_field' => '', 'type' => 'date'],
                ['title' => 'Title Ordered', 'db_field' => '', 'type' => 'date'],
                ['title' => 'Title Received', 'db_field' => '', 'type' => 'date'],
                ['title' => 'Sent To UW', 'db_field' => '', 'type' => 'date'],
                ['title' => 'Appraisal Ordered', 'db_field' => '', 'type' => 'date'],
                ['title' => 'Appraisal Received', 'db_field' => '', 'type' => 'date'],
                ['title' => 'VOE Ordered', 'db_field' => '', 'type' => 'date'],
                ['title' => 'VOE Received', 'db_field' => '', 'type' => 'date'],
                ['title' => 'Conditions Sent', 'db_field' => '', 'type' => 'date'],
                ['title' => 'Clear To Close', 'db_field' => '', 'type' => 'date'],
                ['title' => 'Settlement Scheduled', 'db_field' => '', 'type' => 'date'],
                ['title' => 'Closed', 'db_field' => '', 'type' => 'date'],
                ['title' => 'Funded', 'db_field' => '', 'type' => 'date'],
            ];

            return view('/dashboard/dashboard_'.$group, compact('group', 'active_loans', 'recent_commissions', 'table_headers'));

        } else if ($group == 'in_house') {

            return view('/dashboard/dashboard_'.$group, compact('group'));

        }



    }
}
