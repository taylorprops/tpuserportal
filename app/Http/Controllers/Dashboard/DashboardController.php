<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\HeritageFinancial\Loans;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $group = auth()->user()->group;

        // Mortgage
        if ($group == 'mortgage' || $group == 'in_house') {
            $active_loans = Loans::where('loan_status', 'Open')
            ->orderBy('settlement_date', 'desc')
            ->get();

            $recent_commissions = Loans::where('loan_status', 'Closed')
            ->where('settlement_date', '>', date('Y-m-d', strtotime('-3 month')))
            ->orderBy('settlement_date', 'desc')
            ->get();

            $table_headers = [
                ['title' => 'Locked', 'db_field' => 'locked'],
                ['title' => 'Approved/Suspended', 'db_field' => 'time_line_conditions_received_status'],
                ['title' => 'Package Sent', 'db_field' => 'time_line_package_to_borrower'],
                ['title' => 'Sent To Processor', 'db_field' => 'time_line_sent_to_processing'],
                ['title' => 'Title Ordered', 'db_field' => 'time_line_title_ordered'],
                ['title' => 'Title Received', 'db_field' => 'time_line_title_received'],
                ['title' => 'Sent To UW', 'db_field' => 'time_line_submitted_to_uw'],
                ['title' => 'Appraisal Ordered', 'db_field' => 'time_line_appraisal_ordered'],
                ['title' => 'Appraisal Received', 'db_field' => 'time_line_appraisal_received'],
                ['title' => 'VOE Ordered', 'db_field' => 'time_line_voe_ordered'],
                ['title' => 'VOE Received', 'db_field' => 'time_line_voe_received'],
                ['title' => 'Conditions Sent', 'db_field' => 'time_line_conditions_submitted'],
                ['title' => 'Clear To Close', 'db_field' => 'time_line_clear_to_close'],
                ['title' => 'Settlement Scheduled', 'db_field' => 'time_line_scheduled_settlement'],
                ['title' => 'Closed', 'db_field' => 'time_line_closed'],
                ['title' => 'Funded', 'db_field' => 'time_line_funded'],
            ];

            return view('/dashboard/dashboard_'.$group, compact('group', 'active_loans', 'recent_commissions', 'table_headers'));
        } elseif ($group == 'in_house') {
            return view('/dashboard/dashboard_'.$group, compact('group'));
        }
    }
}
